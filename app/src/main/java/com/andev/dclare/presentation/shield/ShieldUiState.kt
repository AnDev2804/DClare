package com.andev.dclare.presentation.shield

import com.andev.dclare.domain.model.MembershipStatus
import com.andev.dclare.domain.model.ShieldTier

data class ShieldUiState(
    val status: MembershipStatus = MembershipStatus(
        tier = ShieldTier.PRO,
        expiresAt = "15/12/2026",
        daysRemaining = 37,
        maxCompanies = 999,
        currentCompaniesCount = 4,
        allowedTerminalDigits = (0..9).toList(),
        canAccessAllTerminals = true
    ),
    val isUpgradeSuccess: Boolean = false,
    val isProcessing: Boolean = false
)

sealed interface ShieldUiEvent {
    object UpgradeToPro : ShieldUiEvent
    object DismissSuccessMessage : ShieldUiEvent
}

