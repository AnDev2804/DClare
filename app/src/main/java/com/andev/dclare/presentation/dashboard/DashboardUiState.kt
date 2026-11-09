package com.andev.dclare.presentation.dashboard

import com.andev.dclare.domain.model.CompanyProfile
import com.andev.dclare.domain.model.CurrencyRates
import com.andev.dclare.domain.model.FiscalDeadline
import com.andev.dclare.domain.model.TaxDonutSlice
import com.andev.dclare.domain.model.TaxObligation

data class DashboardUiState(
    val isLoading: Boolean = false,
    val selectedCompany: CompanyProfile? = null,
    val allCompanies: List<CompanyProfile> = emptyList(),
    val nextDeadline: FiscalDeadline? = null,
    val upcomingDeadlines: List<FiscalDeadline> = emptyList(),
    val activeObligations: List<TaxObligation> = emptyList(),
    val donutSlices: List<TaxDonutSlice> = emptyList(),
    val currencyRates: CurrencyRates = CurrencyRates(),
    val totalEstimatedUsd: Double = 0.0,
    val totalEstimatedVes: Double = 0.0
)

sealed interface DashboardUiEvent {
    data class SelectCompany(val companyId: String) : DashboardUiEvent
    data class ToggleTax(val taxCode: String, val applies: Boolean) : DashboardUiEvent
    object RefreshCurrencyRates : DashboardUiEvent
}
