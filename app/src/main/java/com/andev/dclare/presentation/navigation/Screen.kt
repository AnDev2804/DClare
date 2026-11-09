package com.andev.dclare.presentation.navigation

sealed class Screen(val route: String) {
    object Login : Screen("login")
    object Register : Screen("register")
    object Dashboard : Screen("dashboard")
    object Calendar : Screen("calendar")
    object Clients : Screen("clients")
    object Shield : Screen("shield")
}

