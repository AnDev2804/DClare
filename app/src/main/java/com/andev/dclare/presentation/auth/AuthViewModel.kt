package com.andev.dclare.presentation.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.andev.dclare.data.repository.AuthRepository
import com.andev.dclare.di.ServiceLocator
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

class AuthViewModel(
    private val authRepository: AuthRepository = ServiceLocator.authRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(AuthUiState())
    val uiState: StateFlow<AuthUiState> = _uiState.asStateFlow()

    init {
        viewModelScope.launch {
            authRepository.currentUser.collect { user ->
                _uiState.update { it.copy(user = user) }
            }
        }
    }

    fun onEvent(event: AuthUiEvent) {
        when (event) {
            is AuthUiEvent.EmailChanged -> _uiState.update { it.copy(email = event.value, errorMessage = null) }
            is AuthUiEvent.PasswordChanged -> _uiState.update { it.copy(pass = event.value, errorMessage = null) }
            is AuthUiEvent.FullNameChanged -> _uiState.update { it.copy(fullName = event.value, errorMessage = null) }
            AuthUiEvent.TogglePasswordVisibility -> _uiState.update { it.copy(isPasswordVisible = !it.isPasswordVisible) }
            AuthUiEvent.ClearError -> _uiState.update { it.copy(errorMessage = null) }
            AuthUiEvent.DemoLoginClicked -> handleDemoLogin()
            AuthUiEvent.SubmitLogin -> handleLogin()
            AuthUiEvent.SubmitRegister -> handleRegister()
        }
    }

    private fun handleDemoLogin() {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true) }
            val demoUser = authRepository.loginDemo()
            _uiState.update { it.copy(isLoading = false, user = demoUser) }
        }
    }

    private fun handleLogin() {
        viewModelScope.launch {
            val state = _uiState.value
            _uiState.update { it.copy(isLoading = true, errorMessage = null) }
            val result = authRepository.login(state.email, state.pass)
            result.onSuccess { user ->
                _uiState.update { it.copy(isLoading = false, user = user) }
            }.onFailure { err ->
                _uiState.update { it.copy(isLoading = false, errorMessage = err.message) }
            }
        }
    }

    private fun handleRegister() {
        viewModelScope.launch {
            val state = _uiState.value
            _uiState.update { it.copy(isLoading = true, errorMessage = null) }
            val result = authRepository.register(state.fullName, state.email, state.pass)
            result.onSuccess { user ->
                _uiState.update { it.copy(isLoading = false, user = user) }
            }.onFailure { err ->
                _uiState.update { it.copy(isLoading = false, errorMessage = err.message) }
            }
        }
    }
}

