package com.andev.dclare

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import com.andev.dclare.presentation.navigation.AppNavHost
import com.andev.dclare.presentation.theme.DclareTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            DclareTheme {
                AppNavHost()
            }
        }
    }
}

