package com.andev.dclare.data.repository

import com.andev.dclare.data.api.ApiClient
import com.andev.dclare.data.dto.DolarApiResponseDto
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
            date = "2026-11-09",
            eurRate = 0.92,
            vesRate = 897.54, // Oficial BCV benchmark from DolarApi
            lastUpdated = "BCV Oficial (DolarApi)"
        )
    )
    override val rates: StateFlow<CurrencyRates> = _rates.asStateFlow()

    override suspend fun refreshRates(): Result<CurrencyRates> = withContext(Dispatchers.IO) {
        try {
            // Primary: Fetch official Venezuelan BCV dollar rate from DolarApi
            val responseString = ApiClient.dolarApi.getDolarOficial()
            val dto = ApiClient.json.decodeFromString<DolarApiResponseDto>(responseString)
            val mappedRates = FiscalMappers.mapDolarApiDtoToCurrencyRates(dto)
            _rates.value = mappedRates
            Result.success(mappedRates)
        } catch (e: Exception) {
            // Graceful fallback to offline cache/benchmark if network is unavailable
            val fallback = _rates.value.copy(
                lastUpdated = "Cache local (DolarApi Offline)"
            )
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
