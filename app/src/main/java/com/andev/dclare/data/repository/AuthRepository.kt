package com.andev.dclare.data.repository

import com.andev.dclare.domain.model.AuthUser
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow

interface AuthRepository {
    val currentUser: StateFlow<AuthUser?>
    suspend fun login(email: String, pass: String): Result<AuthUser>
    suspend fun loginDemo(): AuthUser
    suspend fun register(fullName: String, email: String, pass: String): Result<AuthUser>
    suspend fun logout()
}

class AuthRepositoryImpl : AuthRepository {

    private val _currentUser = MutableStateFlow<AuthUser?>(
        // Start logged in with a demo user for seamless portfolio review
        AuthUser(
            id = "user_demo_01",
            email = "senior.dev@dclare.portfolio",
            fullName = "Alejandro Mendoza (Lead Dev)",
            token = "dclare_token_senior_audited_998877",
            isPremium = true,
            roleName = "Fiscal Shield Pro"
        )
    )
    override val currentUser: StateFlow<AuthUser?> = _currentUser.asStateFlow()

    override suspend fun login(email: String, pass: String): Result<AuthUser> {
        return if (email.contains("@") && pass.length >= 4) {
            val isPro = email.contains("pro") || email.contains("admin")
            val user = AuthUser(
                id = "usr_${System.currentTimeMillis() % 10000}",
                email = email,
                fullName = email.substringBefore("@").replace(".", " ").replaceFirstChar { it.uppercase() },
                token = "tk_${System.currentTimeMillis()}",
                isPremium = isPro,
                roleName = if (isPro) "Fiscal Shield Pro" else "Free Plan (0, 1, 2)"
            )
            _currentUser.value = user
            Result.success(user)
        } else {
            Result.failure(IllegalArgumentException("Credenciales inválidas. Revisa el correo y la contraseña."))
        }
    }

    override suspend fun loginDemo(): AuthUser {
        val demoUser = AuthUser(
            id = "demo_reviewer",
            email = "evaluator@github.portfolio",
            fullName = "GitHub Tech Evaluator",
            token = "demo_session_token_12345",
            isPremium = true,
            roleName = "Fiscal Shield Pro (Reviewer)"
        )
        _currentUser.value = demoUser
        return demoUser
    }

    override suspend fun register(fullName: String, email: String, pass: String): Result<AuthUser> {
        return if (fullName.isNotBlank() && email.contains("@") && pass.length >= 6) {
            val user = AuthUser(
                id = "usr_new_${System.currentTimeMillis() % 1000}",
                email = email,
                fullName = fullName,
                token = "tk_new_${System.currentTimeMillis()}",
                isPremium = false,
                roleName = "Free Plan"
            )
            _currentUser.value = user
            Result.success(user)
        } else {
            Result.failure(IllegalArgumentException("Por favor completa todos los campos requeridos (contraseña mín 6 caracteres)."))
        }
    }

    override suspend fun logout() {
        _currentUser.value = null
    }
}
