package com.andev.dclare.data.repository

import com.andev.dclare.data.mappers.FiscalMappers
import com.andev.dclare.data.mock.MockFiscalData
import com.andev.dclare.domain.model.CompanyProfile
import com.andev.dclare.domain.model.FiscalDeadline
import com.andev.dclare.domain.model.MembershipStatus
import com.andev.dclare.domain.model.ShieldTier
import com.andev.dclare.domain.model.TaxDonutSlice
import com.andev.dclare.domain.model.TaxObligation
import com.andev.dclare.domain.model.TaxpayerType
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import java.util.Calendar

interface FiscalRepository {
    val companies: StateFlow<List<CompanyProfile>>
    val selectedCompany: StateFlow<CompanyProfile?>
    val membershipStatus: StateFlow<MembershipStatus>

    fun selectCompany(companyId: String)
    fun addCompany(taxId: String, businessName: String, type: TaxpayerType): Result<CompanyProfile>
    fun deleteCompany(companyId: String): Result<Unit>
    fun toggleTaxPreference(companyId: String, taxCode: String, applies: Boolean)
    fun getObligationsForCompany(companyId: String): List<TaxObligation>
    fun getNextFiscalDeadline(companyId: String? = null): FiscalDeadline
    fun getAllUpcomingDeadlines(companyId: String? = null): List<FiscalDeadline>
    fun getCalendarDueDays(taxCode: String, terminalDigit: Int, month: Int): List<Int>
    fun getDonutChartData(companyId: String? = null): List<TaxDonutSlice>
    fun upgradeToShieldPro(): Result<Unit>
}

class FiscalRepositoryImpl(
    private val currencyRepository: CurrencyRepository
) : FiscalRepository {

    private val _companies = MutableStateFlow(MockFiscalData.MOCK_COMPANIES)
    override val companies: StateFlow<List<CompanyProfile>> = _companies.asStateFlow()

    private val _selectedCompany = MutableStateFlow<CompanyProfile?>(MockFiscalData.MOCK_COMPANIES.first())
    override val selectedCompany: StateFlow<CompanyProfile?> = _selectedCompany.asStateFlow()

    private val _membershipStatus = MutableStateFlow(
        MembershipStatus(
            tier = ShieldTier.PRO,
            expiresAt = "15/12/2026",
            daysRemaining = 37,
            maxCompanies = 999,
            currentCompaniesCount = MockFiscalData.MOCK_COMPANIES.size,
            allowedTerminalDigits = (0..9).toList(),
            canAccessAllTerminals = true
        )
    )
    override val membershipStatus: StateFlow<MembershipStatus> = _membershipStatus.asStateFlow()

    override fun selectCompany(companyId: String) {
        val found = _companies.value.firstOrNull { it.id == companyId }
        if (found != null) {
            _selectedCompany.value = found
        }
    }

    override fun addCompany(taxId: String, businessName: String, type: TaxpayerType): Result<CompanyProfile> {
        val isFree = _membershipStatus.value.tier == ShieldTier.FREE
        if (isFree && _companies.value.size >= 1) {
            return Result.failure(IllegalStateException("El plan gratuito permite únicamente 1 empresa activa. Pásate a Fiscal Shield Pro para ilimitadas."))
        }

        val lastDigitChar = taxId.filter { it.isDigit() }.lastOrNull() ?: '0'
        val terminalDigit = lastDigitChar.digitToInt()

        val defaultPrefs = MockFiscalData.ALL_TAX_CATALOG.associate { it.code to (it.applies) }

        val newCompany = CompanyProfile(
            id = "comp_${System.currentTimeMillis()}",
            taxId = taxId,
            businessName = businessName,
            type = type,
            terminalDigit = terminalDigit,
            activeTaxesCount = defaultPrefs.count { it.value },
            isPrimary = _companies.value.isEmpty(),
            taxPreferences = defaultPrefs
        )

        val updatedList = _companies.value + newCompany
        _companies.value = updatedList
        _selectedCompany.value = newCompany
        _membershipStatus.value = _membershipStatus.value.copy(currentCompaniesCount = updatedList.size)

        return Result.success(newCompany)
    }

    override fun deleteCompany(companyId: String): Result<Unit> {
        val updated = _companies.value.filterNot { it.id == companyId }
        _companies.value = updated
        if (_selectedCompany.value?.id == companyId) {
            _selectedCompany.value = updated.firstOrNull()
        }
        _membershipStatus.value = _membershipStatus.value.copy(currentCompaniesCount = updated.size)
        return Result.success(Unit)
    }

    override fun toggleTaxPreference(companyId: String, taxCode: String, applies: Boolean) {
        val list = _companies.value.map { comp ->
            if (comp.id == companyId) {
                val newPrefs = comp.taxPreferences.toMutableMap().apply {
                    put(taxCode, applies)
                }
                comp.copy(
                    taxPreferences = newPrefs,
                    activeTaxesCount = newPrefs.count { it.value }
                )
            } else {
                comp
            }
        }
        _companies.value = list
        if (_selectedCompany.value?.id == companyId) {
            _selectedCompany.value = list.firstOrNull { it.id == companyId }
        }
    }

    override fun getObligationsForCompany(companyId: String): List<TaxObligation> {
        val company = _companies.value.firstOrNull { it.id == companyId } ?: _selectedCompany.value
        val prefs = company?.taxPreferences ?: emptyMap()

        val calendar = Calendar.getInstance()
        val currentMonth = calendar.get(Calendar.MONTH) + 1
        val currentYear = calendar.get(Calendar.YEAR)
        val digit = company?.terminalDigit ?: 0

        return MockFiscalData.ALL_TAX_CATALOG.map { catalogItem ->
            val applies = prefs[catalogItem.code] ?: catalogItem.applies
            val daysList = MockFiscalData.CALENDAR_DUE_DAYS[Pair(catalogItem.code, digit)]?.get(currentMonth) ?: listOf(15)
            val dateStrings = daysList.map { d -> String.format("%02d/%02d/%04d", d, currentMonth, currentYear) }

            catalogItem.copy(
                applies = applies,
                nextDueDate = dateStrings.firstOrNull(),
                allDueDates = dateStrings
            )
        }
    }

    override fun getNextFiscalDeadline(companyId: String?): FiscalDeadline {
        val upcoming = getAllUpcomingDeadlines(companyId)
        return upcoming.minByOrNull { it.daysRemaining } ?: run {
            FiscalDeadline(
                id = "empty_due",
                obligationCode = "DEC_IVA",
                obligationName = "Declaración de I.V.A.",
                companyName = _selectedCompany.value?.businessName ?: "Dclare Sandbox",
                taxId = _selectedCompany.value?.taxId ?: "TAX-00123-X",
                formattedDueDate = "15/11/2026",
                daysRemaining = 3,
                urgency = com.andev.dclare.domain.model.DeadlineUrgency.CRITICAL,
                estimatedAmountUsd = 340.0,
                estimatedAmountVes = 340.0 * currencyRepository.rates.value.vesRate
            )
        }
    }

    override fun getAllUpcomingDeadlines(companyId: String?): List<FiscalDeadline> {
        val targetCompany = if (companyId != null) {
            _companies.value.firstOrNull { it.id == companyId } ?: _selectedCompany.value
        } else {
            _selectedCompany.value
        } ?: return emptyList()

        val obligations = getObligationsForCompany(targetCompany.id)
        val calendar = Calendar.getInstance()
        val currentDay = calendar.get(Calendar.DAY_OF_MONTH)
        val currentMonth = calendar.get(Calendar.MONTH) + 1
        val currentYear = calendar.get(Calendar.YEAR)
        val vesRate = currencyRepository.rates.value.vesRate

        val deadlines = mutableListOf<FiscalDeadline>()

        obligations.filter { it.applies }.forEach { tax ->
            tax.allDueDates.forEach { dateStr ->
                val day = dateStr.split("/").firstOrNull()?.toIntOrNull() ?: 15
                // Days remaining calculation
                val daysRemaining = if (day >= currentDay) {
                    day - currentDay
                } else {
                    // Next month rollover
                    (30 - currentDay) + day
                }

                deadlines.add(
                    FiscalMappers.mapToFiscalDeadline(
                        obligation = tax,
                        companyName = targetCompany.businessName,
                        taxId = targetCompany.taxId,
                        dueDateFormatted = dateStr,
                        daysRemaining = daysRemaining,
                        vesExchangeRate = vesRate
                    )
                )
            }
        }

        return deadlines.sortedBy { it.daysRemaining }
    }

    override fun getCalendarDueDays(taxCode: String, terminalDigit: Int, month: Int): List<Int> {
        return MockFiscalData.CALENDAR_DUE_DAYS[Pair(taxCode, terminalDigit)]?.get(month) ?: emptyList()
    }

    override fun getDonutChartData(companyId: String?): List<TaxDonutSlice> {
        val targetCompany = if (companyId != null) {
            _companies.value.firstOrNull { it.id == companyId } ?: _selectedCompany.value
        } else {
            _selectedCompany.value
        } ?: return emptyList()

        val activeObligations = getObligationsForCompany(targetCompany.id).filter { it.applies }
        return FiscalMappers.computeTaxDonutSlices(activeObligations)
    }

    override fun upgradeToShieldPro(): Result<Unit> {
        _membershipStatus.value = _membershipStatus.value.copy(
            tier = ShieldTier.PRO,
            expiresAt = "31/12/2026",
            daysRemaining = 90,
            maxCompanies = 999,
            canAccessAllTerminals = true
        )
        return Result.success(Unit)
    }
}

