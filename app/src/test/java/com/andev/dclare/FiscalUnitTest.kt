package com.andev.dclare

import com.andev.dclare.data.dto.FrankfurterResponseDto
import com.andev.dclare.data.mappers.FiscalMappers
import com.andev.dclare.data.repository.AuthRepositoryImpl
import com.andev.dclare.data.repository.CurrencyRepositoryImpl
import com.andev.dclare.data.repository.FiscalRepositoryImpl
import com.andev.dclare.domain.model.DeadlineUrgency
import com.andev.dclare.domain.model.TaxpayerType
import kotlinx.coroutines.runBlocking
import org.junit.Assert.assertEquals
import org.junit.Assert.assertNotNull
import org.junit.Assert.assertTrue
import org.junit.Test

class FiscalUnitTest {

    @Test
    fun urgencyCalculation_correctThresholds() {
        assertEquals(DeadlineUrgency.CRITICAL, FiscalMappers.calculateUrgency(1))
        assertEquals(DeadlineUrgency.CRITICAL, FiscalMappers.calculateUrgency(3))
        assertEquals(DeadlineUrgency.UPCOMING, FiscalMappers.calculateUrgency(5))
        assertEquals(DeadlineUrgency.UPCOMING, FiscalMappers.calculateUrgency(7))
        assertEquals(DeadlineUrgency.NORMAL, FiscalMappers.calculateUrgency(10))
    }

    @Test
    fun currencyRatesMapping_convertsProperly() {
        val dto = FrankfurterResponseDto(
            amount = 1.0,
            base = "USD",
            date = "2026-11-08",
            rates = mapOf("EUR" to 0.92)
        )
        val rates = FiscalMappers.mapFrankfurterDtoToCurrencyRates(dto, fallbackVesRate = 49.35)
        assertEquals("USD", rates.base)
        assertEquals(0.92, rates.eurRate, 0.001)
        assertEquals(49.35, rates.vesRate, 0.001)
    }

    @Test
    fun currencyRepository_convertsUsdToVesCorrectly() {
        val repo = CurrencyRepositoryImpl()
        val usdAmount = 100.0
        val vesAmount = repo.convertUsdToVes(usdAmount)
        assertEquals(4935.0, vesAmount, 0.1)
    }

    @Test
    fun fiscalRepository_addCompanyAndCalculateTerminalDigit() {
        val currencyRepo = CurrencyRepositoryImpl()
        val fiscalRepo = FiscalRepositoryImpl(currencyRepo)

        val result = fiscalRepo.addCompany(
            taxId = "TAX-99887-4",
            businessName = "Test Fiscal Corp",
            type = TaxpayerType.ESPECIAL
        )

        assertTrue(result.isSuccess)
        val company = result.getOrNull()
        assertNotNull(company)
        assertEquals(4, company?.terminalDigit)
        assertEquals(TaxpayerType.ESPECIAL, company?.type)
    }

    @Test
    fun fiscalRepository_toggleTaxPreferenceUpdatesReactively() {
        val currencyRepo = CurrencyRepositoryImpl()
        val fiscalRepo = FiscalRepositoryImpl(currencyRepo)
        val company = fiscalRepo.companies.value.first()

        fiscalRepo.toggleTaxPreference(company.id, "ANTI_ISLR", false)
        val updatedCompany = fiscalRepo.companies.value.first { it.id == company.id }
        assertEquals(false, updatedCompany.taxPreferences["ANTI_ISLR"])

        fiscalRepo.toggleTaxPreference(company.id, "ANTI_ISLR", true)
        val revertedCompany = fiscalRepo.companies.value.first { it.id == company.id }
        assertEquals(true, revertedCompany.taxPreferences["ANTI_ISLR"])
    }

    @Test
    fun authRepository_demoLoginReturnsValidSession() = runBlocking {
        val authRepo = AuthRepositoryImpl()
        val user = authRepo.loginDemo()
        assertEquals("demo_reviewer", user.id)
        assertTrue(user.isPremium)
        assertNotNull(authRepo.currentUser.value)
    }
}
