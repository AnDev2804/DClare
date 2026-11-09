package com.andev.dclare.presentation.clients

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.andev.dclare.data.repository.FiscalRepository
import com.andev.dclare.di.ServiceLocator
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.combine
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

class ClientsViewModel(
    private val fiscalRepository: FiscalRepository = ServiceLocator.fiscalRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(ClientsUiState())
    val uiState: StateFlow<ClientsUiState> = _uiState.asStateFlow()

    init {
        viewModelScope.launch {
            combine(
                fiscalRepository.companies,
                fiscalRepository.selectedCompany
            ) { companies, selected ->
                val comp = selected ?: companies.firstOrNull()
                val obligations = comp?.let { fiscalRepository.getObligationsForCompany(it.id) } ?: emptyList()
                _uiState.update {
                    it.copy(
                        companies = companies,
                        selectedCompany = comp,
                        obligations = obligations
                    )
                }
            }.collect {}
        }
    }

    fun onEvent(event: ClientsUiEvent) {
        when (event) {
            is ClientsUiEvent.SelectCompany -> {
                fiscalRepository.selectCompany(event.companyId)
            }
            is ClientsUiEvent.ToggleTaxPreference -> {
                val comp = _uiState.value.selectedCompany ?: return
                fiscalRepository.toggleTaxPreference(comp.id, event.taxCode, event.applies)
            }
            is ClientsUiEvent.DeleteCompany -> {
                fiscalRepository.deleteCompany(event.companyId)
            }
            ClientsUiEvent.OpenAddDialog -> {
                _uiState.update { it.copy(isAddDialogOpen = true, errorMessage = null) }
            }
            ClientsUiEvent.CloseAddDialog -> {
                _uiState.update { it.copy(isAddDialogOpen = false, errorMessage = null) }
            }
            is ClientsUiEvent.NewTaxIdChanged -> {
                _uiState.update { it.copy(newTaxId = event.value) }
            }
            is ClientsUiEvent.NewBusinessNameChanged -> {
                _uiState.update { it.copy(newBusinessName = event.value) }
            }
            is ClientsUiEvent.NewTaxpayerTypeChanged -> {
                _uiState.update { it.copy(newTaxpayerType = event.type) }
            }
            ClientsUiEvent.SubmitAddCompany -> {
                handleAddCompany()
            }
        }
    }

    private fun handleAddCompany() {
        val state = _uiState.value
        if (state.newTaxId.isBlank() || state.newBusinessName.isBlank()) {
            _uiState.update { it.copy(errorMessage = "Por favor ingresa el RIF / Tax ID y el Nombre.") }
            return
        }

        val result = fiscalRepository.addCompany(
            taxId = state.newTaxId.trim().uppercase(),
            businessName = state.newBusinessName.trim(),
            type = state.newTaxpayerType
        )

        result.onSuccess {
            _uiState.update {
                it.copy(
                    isAddDialogOpen = false,
                    newTaxId = "",
                    newBusinessName = "",
                    errorMessage = null
                )
            }
        }.onFailure { err ->
            _uiState.update { it.copy(errorMessage = err.message) }
        }
    }
}

