package com.andev.dclare.data.dto

import kotlinx.serialization.Serializable

@Serializable
data class DolarApiResponseDto(
    val moneda: String = "USD",
    val fuente: String = "oficial",
    val nombre: String = "Dólar",
    val compra: Double? = null,
    val venta: Double? = null,
    val promedio: Double = 897.54,
    val fechaActualizacion: String = ""
)

@Serializable
data class FrankfurterResponseDto(
    val amount: Double = 1.0,
    val base: String = "USD",
    val date: String = "",
    val rates: Map<String, Double> = emptyMap()
)

@Serializable
data class LoginRequestDto(
    val email: String,
    val pass: String
)

@Serializable
data class LoginResponseDto(
    val success: Boolean,
    val token: String? = null,
    val userId: String? = null,
    val fullName: String? = null,
    val email: String? = null,
    val isPremium: Boolean = false,
    val message: String? = null
)
