package com.andev.dclare.data.api

import retrofit2.http.GET
import retrofit2.http.Query

interface CurrencyApi {
    @GET("latest")
    suspend fun getLatestRates(
        @Query("from") from: String = "USD",
        @Query("to") to: String = "EUR"
    ): String
}
