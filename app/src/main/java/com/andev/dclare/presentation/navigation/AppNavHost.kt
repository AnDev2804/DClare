package com.andev.dclare.presentation.navigation

import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Scaffold
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.andev.dclare.presentation.auth.AuthViewModel
import com.andev.dclare.presentation.auth.LoginScreen
import com.andev.dclare.presentation.auth.RegisterScreen
import com.andev.dclare.presentation.calendar.CalendarScreen
import com.andev.dclare.presentation.calendar.CalendarViewModel
import com.andev.dclare.presentation.clients.ClientsScreen
import com.andev.dclare.presentation.clients.ClientsViewModel
import com.andev.dclare.presentation.common.DclareBottomNav
import com.andev.dclare.presentation.dashboard.DashboardScreen
import com.andev.dclare.presentation.dashboard.DashboardViewModel
import com.andev.dclare.presentation.shield.ShieldScreen
import com.andev.dclare.presentation.shield.ShieldViewModel
import com.andev.dclare.presentation.theme.SlateDarkBackground

@Composable
fun AppNavHost(
    modifier: Modifier = Modifier
) {
    val navController = rememberNavController()
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    val showBottomBar = currentRoute in listOf(
        Screen.Dashboard.route,
        Screen.Calendar.route,
        Screen.Clients.route,
        Screen.Shield.route
    )

    Scaffold(
        containerColor = SlateDarkBackground,
        bottomBar = {
            if (showBottomBar) {
                DclareBottomNav(
                    currentRoute = currentRoute,
                    onNavigate = { route ->
                        navController.navigate(route) {
                            popUpTo(navController.graph.findStartDestination().id) {
                                saveState = true
                            }
                            launchSingleTop = true
                            restoreState = true
                        }
                    }
                )
            }
        },
        modifier = modifier.fillMaxSize()
    ) { innerPadding ->
        NavHost(
            navController = navController,
            startDestination = Screen.Dashboard.route,
            modifier = Modifier.padding(innerPadding)
        ) {
            // Dashboard
            composable(Screen.Dashboard.route) {
                val viewModel: DashboardViewModel = viewModel()
                val state by viewModel.uiState.collectAsState()
                DashboardScreen(
                    state = state,
                    onEvent = viewModel::onEvent,
                    onNavigateToCalendar = { navController.navigate(Screen.Calendar.route) },
                    onNavigateToClients = { navController.navigate(Screen.Clients.route) }
                )
            }

            // Calendar
            composable(Screen.Calendar.route) {
                val viewModel: CalendarViewModel = viewModel()
                val state by viewModel.uiState.collectAsState()
                CalendarScreen(
                    state = state,
                    onEvent = viewModel::onEvent,
                    onNavigateToShield = { navController.navigate(Screen.Shield.route) }
                )
            }

            // Clients
            composable(Screen.Clients.route) {
                val viewModel: ClientsViewModel = viewModel()
                val state by viewModel.uiState.collectAsState()
                ClientsScreen(
                    state = state,
                    onEvent = viewModel::onEvent
                )
            }

            // Fiscal Shield
            composable(Screen.Shield.route) {
                val viewModel: ShieldViewModel = viewModel()
                val state by viewModel.uiState.collectAsState()
                ShieldScreen(
                    state = state,
                    onEvent = viewModel::onEvent
                )
            }

            // Login
            composable(Screen.Login.route) {
                val viewModel: AuthViewModel = viewModel()
                val state by viewModel.uiState.collectAsState()
                LoginScreen(
                    state = state,
                    onEvent = viewModel::onEvent,
                    onNavigateToRegister = { navController.navigate(Screen.Register.route) },
                    onLoginSuccess = {
                        navController.navigate(Screen.Dashboard.route) {
                            popUpTo(Screen.Login.route) { inclusive = true }
                        }
                    }
                )
            }

            // Register
            composable(Screen.Register.route) {
                val viewModel: AuthViewModel = viewModel()
                val state by viewModel.uiState.collectAsState()
                RegisterScreen(
                    state = state,
                    onEvent = viewModel::onEvent,
                    onNavigateToLogin = { navController.navigate(Screen.Login.route) },
                    onRegisterSuccess = {
                        navController.navigate(Screen.Dashboard.route) {
                            popUpTo(Screen.Register.route) { inclusive = true }
                        }
                    }
                )
            }
        }
    }
}

