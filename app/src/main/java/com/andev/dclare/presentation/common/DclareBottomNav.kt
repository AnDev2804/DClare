package com.andev.dclare.presentation.common

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.navigationBarsPadding
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.Groups
import androidx.compose.material.icons.filled.Security
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.remember
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.andev.dclare.presentation.navigation.Screen
import com.andev.dclare.presentation.theme.EmeraldLight
import com.andev.dclare.presentation.theme.EmeraldPrimary
import com.andev.dclare.presentation.theme.SlateBorder
import com.andev.dclare.presentation.theme.SlateCard
import com.andev.dclare.presentation.theme.SlateDarkBackground
import com.andev.dclare.presentation.theme.TextMuted
import com.andev.dclare.presentation.theme.TextPrimary
import com.andev.dclare.presentation.theme.TextSecondary

sealed class NavItem(
    val route: String,
    val title: String,
    val icon: ImageVector
) {
    object Dashboard : NavItem(Screen.Dashboard.route, "Resumen", Icons.Default.Dashboard)
    object Calendar : NavItem(Screen.Calendar.route, "Calendario", Icons.Default.CalendarMonth)
    object Clients : NavItem(Screen.Clients.route, "Empresas", Icons.Default.Groups)
    object Shield : NavItem(Screen.Shield.route, "Shield", Icons.Default.Security)
}

@Composable
fun DclareBottomNav(
    currentRoute: String?,
    onNavigate: (String) -> Unit,
    modifier: Modifier = Modifier
) {
    val items = listOf(
        NavItem.Dashboard,
        NavItem.Calendar,
        NavItem.Clients,
        NavItem.Shield
    )

    Box(
        modifier = modifier
            .fillMaxWidth()
            .background(SlateDarkBackground)
            .navigationBarsPadding()
            .padding(horizontal = 16.dp, vertical = 8.dp)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .clip(RoundedCornerShape(22.dp))
                .background(SlateCard)
                .border(1.dp, SlateBorder, RoundedCornerShape(22.dp))
                .padding(vertical = 8.dp, horizontal = 6.dp),
            horizontalArrangement = Arrangement.SpaceAround,
            verticalAlignment = Alignment.CenterVertically
        ) {
            items.forEach { item ->
                val isSelected = currentRoute == item.route
                val interactionSource = remember { MutableInteractionSource() }

                Column(
                    horizontalAlignment = Alignment.CenterHorizontally,
                    modifier = Modifier
                        .clip(RoundedCornerShape(14.dp))
                        .clickable(interactionSource = interactionSource, indication = null) {
                            if (!isSelected) onNavigate(item.route)
                        }
                        .padding(horizontal = 14.dp, vertical = 6.dp)
                ) {
                    Icon(
                        imageVector = item.icon,
                        contentDescription = item.title,
                        tint = if (isSelected) EmeraldLight else TextMuted,
                        modifier = Modifier.size(22.dp)
                    )
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = item.title,
                        style = MaterialTheme.typography.labelSmall,
                        color = if (isSelected) TextPrimary else TextMuted,
                        fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal,
                        fontSize = 10.sp
                    )
                }
            }
        }
    }
}
