package com.andev.dclare.presentation.dashboard

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.andev.dclare.data.repository.CurrencyRepository
import com.andev.dclare.data.repository.FiscalRepository
import com.andev.dclare.di.ServiceLocator
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.combine
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

class DashboardViewModel(
    private val fiscalRepository: FiscalRepository = ServiceLocator.fiscalRepository,
    private val currencyRepository: CurrencyRepository = ServiceLocator.currencyRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(DashboardUiState())
    val uiState: StateFlow<DashboardUiState> = _uiState.asStateFlow()

    init {
        observeData()
        refreshRates()
    }

    private fun observeData() {
        viewModelScope.launch {
            combine(
                fiscalRepository.companies,
                fiscalRepository.selectedCompany,
                currencyRepository.rates
            ) { companies, selectedComp, rates ->
                val currentComp = selectedComp ?: companies.firstOrNull()
                val nextDue = currentComp?.let { fiscalRepository.getNextFiscalDeadline(it.id) }
                val upcoming = currentComp?.let { fiscalRepository.getAllUpcomingDeadlines(it.id) } ?: emptyList()
                val obligations = currentComp?.let { fiscalRepository.getObligationsForCompany(it.id) } ?: emptyList()
                val donut = currentComp?.let { fiscalRepository.getDonutChartData(it.id) } ?: emptyList()
                val totalUsd = obligations.filter { it.applies }.sumOf { it.estimatedBaseAmountUsd }
                val totalVes = totalUsd * rates.vesRate

                _uiState.update {
                    it.copy(
                        allCompanies = companies,
                        selectedCompany = currentComp,
                        nextDeadline = nextDue,
                        upcomingDeadlines = upcoming,
                        activeObligations = obligations,
                        donutSlices = donut,
                        currencyRates = rates,
                        totalEstimatedUsd = totalUsd,
                        totalEstimatedVes = totalVes
                    )
                }
            }.collect {}
        }
    }

    fun onEvent(event: DashboardUiEvent) {
        when (event) {
            is DashboardUiEvent.SelectCompany -> {
                fiscalRepository.selectCompany(event.companyId)
            }
            is DashboardUiEvent.ToggleTax -> {
                val comp = _uiState.value.selectedCompany ?: return
                fiscalRepository.toggleTaxPreference(comp.id, event.taxCode, event.applies)
            }
            DashboardUiEvent.RefreshCurrencyRates -> {
                refreshRates()
            }
        }
    }

    private fun refreshRates() {
        viewModelScope.launch {
            currencyRepository.refreshRates()
        }
    }
}
