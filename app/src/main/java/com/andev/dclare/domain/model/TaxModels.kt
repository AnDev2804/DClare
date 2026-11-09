package com.andev.dclare.domain.model

enum class TaxpayerType(val label: String) {
    ORDINARIO("Contribuyente Ordinario"),
    ESPECIAL("Contribuyente Especial")
}

enum class DeadlineUrgency {
    CRITICAL, // <= 3 days
    UPCOMING, // <= 7 days
    NORMAL    // > 7 days
}

data class TaxObligation(
    val code: String,
    val shortName: String,
    val fullName: String,
    val category: String,
    val applies: Boolean,
    val estimatedBaseAmountUsd: Double,
    val nextDueDate: String? = null,
    val allDueDates: List<String> = emptyList()
)

data class CompanyProfile(
    val id: String,
    val taxId: String, // e.g. "J-29837192-0" or "TAX-00123-X"
    val businessName: String,
    val type: TaxpayerType,
    val terminalDigit: Int,
    val activeTaxesCount: Int = 0,
    val isPrimary: Boolean = false,
    val taxPreferences: Map<String, Boolean> = emptyMap()
)

data class FiscalDeadline(
    val id: String,
    val obligationCode: String,
    val obligationName: String,
    val companyName: String,
    val taxId: String,
    val formattedDueDate: String,
    val daysRemaining: Int,
    val urgency: DeadlineUrgency,
    val estimatedAmountUsd: Double,
    val estimatedAmountVes: Double
)

data class CurrencyRates(
    val base: String = "USD",
    val date: String = "",
    val eurRate: Double = 0.92,
    val vesRate: Double = 48.50, // Real-time or official benchmark
    val lastUpdated: String = ""
)

data class TaxDonutSlice(
    val label: String,
    val percentage: Float,
    val amountUsd: Double,
    val colorArgb: Long
)

enum class ShieldTier(val displayName: String, val priceUsd: Double) {
    FREE("Fiscal Free Shield", 0.0),
    PRO("Fiscal Shield Pro", 1.99),
    ENTERPRISE("Enterprise Shield", 9.99)
}

data class MembershipStatus(
    val tier: ShieldTier,
    val expiresAt: String?,
    val daysRemaining: Int?,
    val maxCompanies: Int,
    val currentCompaniesCount: Int,
    val allowedTerminalDigits: List<Int>,
    val canAccessAllTerminals: Boolean
)

data class AuthUser(
    val id: String,
    val email: String,
    val fullName: String,
    val token: String,
    val isPremium: Boolean,
    val roleName: String
)
