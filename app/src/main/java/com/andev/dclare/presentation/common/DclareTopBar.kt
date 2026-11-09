package com.andev.dclare.presentation.common

import androidx.compose.animation.AnimatedVisibility
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.statusBarsPadding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowDropDown
import androidx.compose.material.icons.filled.Business
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Security
import androidx.compose.material3.DropdownMenu
import androidx.compose.material3.DropdownMenuItem
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.andev.dclare.domain.model.CompanyProfile
import com.andev.dclare.domain.model.CurrencyRates
import com.andev.dclare.presentation.theme.CyanAccent
import com.andev.dclare.presentation.theme.EmeraldLight
import com.andev.dclare.presentation.theme.EmeraldPrimary
import com.andev.dclare.presentation.theme.SlateBorder
import com.andev.dclare.presentation.theme.SlateCard
import com.andev.dclare.presentation.theme.SlateCardElevated
import com.andev.dclare.presentation.theme.SlateDarkBackground
import com.andev.dclare.presentation.theme.TextMuted
import com.andev.dclare.presentation.theme.TextPrimary
import com.andev.dclare.presentation.theme.TextSecondary
import java.util.Locale

@Composable
fun DclareTopBar(
    selectedCompany: CompanyProfile?,
    allCompanies: List<CompanyProfile>,
    currencyRates: CurrencyRates,
    onSelectCompany: (String) -> Unit,
    onRefreshRates: () -> Unit,
    modifier: Modifier = Modifier
) {
    var showCompanyDropdown by remember { mutableStateOf(false) }

    Column(
        modifier = modifier
            .fillMaxWidth()
            .background(SlateDarkBackground)
            .statusBarsPadding()
            .padding(horizontal = 16.dp, vertical = 10.dp)
    ) {
        // Upper row: Brand Logo + Currency Ticker + Refresh
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Brand
            Row(verticalAlignment = Alignment.CenterVertically) {
                Box(
                    modifier = Modifier
                        .size(34.dp)
                        .clip(RoundedCornerShape(10.dp))
                        .background(EmeraldPrimary.copy(alpha = 0.15f))
                        .border(1.dp, EmeraldPrimary.copy(alpha = 0.4f), RoundedCornerShape(10.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.Security,
                        contentDescription = "Dclare Shield",
                        tint = EmeraldLight,
                        modifier = Modifier.size(20.dp)
                    )
                }
                Spacer(modifier = Modifier.width(10.dp))
                Column {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Text(
                            text = "Dclare",
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.Bold,
                            color = TextPrimary
                        )
                        Spacer(modifier = Modifier.width(6.dp))
                        Box(
                            modifier = Modifier
                                .clip(RoundedCornerShape(4.dp))
                                .background(EmeraldPrimary.copy(alpha = 0.2f))
                                .padding(horizontal = 5.dp, vertical = 1.dp)
                        ) {
                            Text(
                                text = "PRO",
                                style = MaterialTheme.typography.labelSmall,
                                color = EmeraldLight,
                                fontWeight = FontWeight.Bold,
                                fontSize = 9.sp
                            )
                        }
                    }
                    Text(
                        text = "Fiscal Shield & Calendar",
                        style = MaterialTheme.typography.bodySmall,
                        color = TextMuted,
                        fontSize = 10.sp
                    )
                }
            }

            // Public Currency Rate Ticker Pill + Refresh
            Row(verticalAlignment = Alignment.CenterVertically) {
                Box(
                    modifier = Modifier
                        .clip(RoundedCornerShape(20.dp))
                        .background(SlateCard)
                        .border(1.dp, SlateBorder, RoundedCornerShape(20.dp))
                        .padding(horizontal = 10.dp, vertical = 5.dp)
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Box(
                            modifier = Modifier
                                .size(6.dp)
                                .clip(CircleShape)
                                .background(CyanAccent)
                        )
                        Spacer(modifier = Modifier.width(6.dp))
                        Text(
                            text = String.format(Locale.US, "1 USD = %.2f VES", currencyRates.vesRate),
                            style = MaterialTheme.typography.labelSmall,
                            color = TextPrimary,
                            fontWeight = FontWeight.Medium
                        )
                    }
                }
                Spacer(modifier = Modifier.width(4.dp))
                IconButton(
                    onClick = onRefreshRates,
                    modifier = Modifier.size(32.dp)
                ) {
                    Icon(
                        imageVector = Icons.Default.Refresh,
                        contentDescription = "Actualizar Tasa",
                        tint = TextSecondary,
                        modifier = Modifier.size(18.dp)
                    )
                }
            }
        }

        Spacer(modifier = Modifier.height(10.dp))

        // Lower row: Active Company Selector Pill
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .clip(RoundedCornerShape(12.dp))
                .background(SlateCard)
                .border(1.dp, SlateBorder, RoundedCornerShape(12.dp))
                .clickable { showCompanyDropdown = true }
                .padding(horizontal = 12.dp, vertical = 8.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(
                        imageVector = Icons.Default.Business,
                        contentDescription = null,
                        tint = EmeraldLight,
                        modifier = Modifier.size(16.dp)
                    )
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(
                        text = selectedCompany?.businessName ?: "Seleccionar Empresa",
                        style = MaterialTheme.typography.titleMedium,
                        color = TextPrimary,
                        maxLines = 1,
                        fontWeight = FontWeight.Medium
                    )
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(
                        text = "(${selectedCompany?.taxId ?: ""})",
                        style = MaterialTheme.typography.bodySmall,
                        color = TextMuted
                    )
                }
                Icon(
                    imageVector = Icons.Default.ArrowDropDown,
                    contentDescription = null,
                    tint = TextSecondary
                )
            }

            DropdownMenu(
                expanded = showCompanyDropdown,
                onDismissRequest = { showCompanyDropdown = false },
                modifier = Modifier
                    .background(SlateCardElevated)
                    .border(1.dp, SlateBorder, RoundedCornerShape(8.dp))
            ) {
                allCompanies.forEach { comp ->
                    DropdownMenuItem(
                        text = {
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Column {
                                    Text(
                                        text = comp.businessName,
                                        style = MaterialTheme.typography.bodyMedium,
                                        color = TextPrimary,
                                        fontWeight = if (comp.id == selectedCompany?.id) FontWeight.Bold else FontWeight.Normal
                                    )
                                    Text(
                                        text = "${comp.taxId} • Term. ${comp.terminalDigit} • ${comp.type.label}",
                                        style = MaterialTheme.typography.bodySmall,
                                        color = TextMuted
                                    )
                                }
                                if (comp.id == selectedCompany?.id) {
                                    Icon(
                                        imageVector = Icons.Default.Check,
                                        contentDescription = null,
                                        tint = EmeraldPrimary,
                                        modifier = Modifier.size(18.dp)
                                    )
                                }
                            }
                        },
                        onClick = {
                            onSelectCompany(comp.id)
                            showCompanyDropdown = false
                        }
                    )
                }
            }
        }
    }
}
