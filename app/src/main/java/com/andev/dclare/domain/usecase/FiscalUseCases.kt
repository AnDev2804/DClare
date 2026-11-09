package com.andev.dclare.domain.usecase

import com.andev.dclare.data.repository.CurrencyRepository
import com.andev.dclare.data.repository.FiscalRepository
import com.andev.dclare.domain.model.CompanyProfile
import com.andev.dclare.domain.model.FiscalDeadline
import com.andev.dclare.domain.model.TaxDonutSlice
import com.andev.dclare.domain.model.TaxObligation
import com.andev.dclare.domain.model.TaxpayerType
import kotlinx.coroutines.flow.StateFlow

class GetNextDeadlineUseCase(
    private val fiscalRepository: FiscalRepository
) {
    operator fun invoke(companyId: String? = null): FiscalDeadline {
        return fiscalRepository.getNextFiscalDeadline(companyId)
    }
}

class GetFiscalObligationsUseCase(
    private val fiscalRepository: FiscalRepository
) {
    operator fun invoke(companyId: String): List<TaxObligation> {
        return fiscalRepository.getObligationsForCompany(companyId)
    }
}

class ToggleTaxPreferenceUseCase(
    private val fiscalRepository: FiscalRepository
) {
    operator fun invoke(companyId: String, taxCode: String, applies: Boolean) {
        fiscalRepository.toggleTaxPreference(companyId, taxCode, applies)
    }
}

class ConvertCurrencyUseCase(
    private val currencyRepository: CurrencyRepository
) {
    fun toVes(usd: Double): Double = currencyRepository.convertUsdToVes(usd)
    fun toEur(usd: Double): Double = currencyRepository.convertUsdToEur(usd)
}

class GetTaxBreakdownUseCase(
    private val fiscalRepository: FiscalRepository
) {
    operator fun invoke(companyId: String? = null): List<TaxDonutSlice> {
        return fiscalRepository.getDonutChartData(companyId)
    }
}

