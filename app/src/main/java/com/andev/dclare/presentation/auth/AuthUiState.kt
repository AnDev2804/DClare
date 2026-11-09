package com.andev.dclare.presentation.auth

import com.andev.dclare.domain.model.AuthUser

data class AuthUiState(
    val email: String = "",
    val pass: String = "",
    val fullName: String = "",
    val isPasswordVisible: Boolean = false,
    val isLoading: Boolean = false,
    val errorMessage: String? = null,
    val user: AuthUser? = null
)

sealed interface AuthUiEvent {
    data class EmailChanged(val value: String) : AuthUiEvent
    data class PasswordChanged(val value: String) : AuthUiEvent
    data class FullNameChanged(val value: String) : AuthUiEvent
    object TogglePasswordVisibility : AuthUiEvent
    object SubmitLogin : AuthUiEvent
    object SubmitRegister : AuthUiEvent
    object DemoLoginClicked : AuthUiEvent
    object ClearError : AuthUiEvent
}
