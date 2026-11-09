package com.andev.dclare.presentation.shield

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.andev.dclare.data.repository.FiscalRepository
import com.andev.dclare.di.ServiceLocator
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

class ShieldViewModel(
    private val fiscalRepository: FiscalRepository = ServiceLocator.fiscalRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(ShieldUiState())
    val uiState: StateFlow<ShieldUiState> = _uiState.asStateFlow()

    init {
        viewModelScope.launch {
            fiscalRepository.membershipStatus.collect { status ->
                _uiState.update { it.copy(status = status) }
            }
        }
    }

    fun onEvent(event: ShieldUiEvent) {
        when (event) {
            ShieldUiEvent.UpgradeToPro -> {
                viewModelScope.launch {
                    _uiState.update { it.copy(isProcessing = true) }
                    fiscalRepository.upgradeToShieldPro()
                    _uiState.update { it.copy(isProcessing = false, isUpgradeSuccess = true) }
                }
            }
            ShieldUiEvent.DismissSuccessMessage -> {
                _uiState.update { it.copy(isUpgradeSuccess = false) }
            }
        }
    }
}

