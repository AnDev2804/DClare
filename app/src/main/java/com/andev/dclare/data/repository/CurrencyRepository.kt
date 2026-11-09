package com.andev.dclare.data.repository

import com.andev.dclare.data.api.ApiClient
import com.andev.dclare.data.dto.FrankfurterResponseDto
import com.andev.dclare.data.mappers.FiscalMappers
import com.andev.dclare.domain.model.CurrencyRates
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.withContext

interface CurrencyRepository {
    val rates: StateFlow<CurrencyRates>
    suspend fun refreshRates(): Result<CurrencyRates>
    fun convertUsdToVes(usdAmount: Double): Double
    fun convertUsdToEur(usdAmount: Double): Double
}

class CurrencyRepositoryImpl : CurrencyRepository {

    private val _rates = MutableStateFlow(
        CurrencyRates(
            base = "USD",
            date = "2026-11-08",
            eurRate = 0.925,
            vesRate = 49.35,
            lastUpdated = "Benchmark Offline / Live Fallback"
        )
    )
    override val rates: StateFlow<CurrencyRates> = _rates.asStateFlow()

    override suspend fun refreshRates(): Result<CurrencyRates> = withContext(Dispatchers.IO) {
        try {
            val responseString = ApiClient.currencyApi.getLatestRates(from = "USD", to = "EUR")
            val dto = ApiClient.json.decodeFromString<FrankfurterResponseDto>(responseString)
            val mappedRates = FiscalMappers.mapFrankfurterDtoToCurrencyRates(dto, fallbackVesRate = 49.35)
            _rates.value = mappedRates
            Result.success(mappedRates)
        } catch (e: Exception) {
            // Keep existing or fallback gracefully without throwing
            val fallback = _rates.value.copy(lastUpdated = "Cache local (Offline)")
            _rates.value = fallback
            Result.success(fallback)
        }
    }

    override fun convertUsdToVes(usdAmount: Double): Double {
        return usdAmount * _rates.value.vesRate
    }

    override fun convertUsdToEur(usdAmount: Double): Double {
        return usdAmount * _rates.value.eurRate
    }
}
