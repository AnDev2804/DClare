package com.andev.dclare.presentation.shield

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.statusBarsPadding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Security
import androidx.compose.material.icons.filled.Star
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.andev.dclare.domain.model.ShieldTier
import com.andev.dclare.presentation.theme.AmberLight
import com.andev.dclare.presentation.theme.AmberWarning
import com.andev.dclare.presentation.theme.EmeraldContainer
import com.andev.dclare.presentation.theme.EmeraldLight
import com.andev.dclare.presentation.theme.EmeraldPrimary
import com.andev.dclare.presentation.theme.SlateBorder
import com.andev.dclare.presentation.theme.SlateCard
import com.andev.dclare.presentation.theme.SlateCardElevated
import com.andev.dclare.presentation.theme.SlateDarkBackground
import com.andev.dclare.presentation.theme.TextMuted
import com.andev.dclare.presentation.theme.TextOnEmerald
import com.andev.dclare.presentation.theme.TextPrimary
import com.andev.dclare.presentation.theme.TextSecondary

@Composable
fun ShieldScreen(
    state: ShieldUiState,
    onEvent: (ShieldUiEvent) -> Unit,
    modifier: Modifier = Modifier
) {
    val isPro = state.status.tier != ShieldTier.FREE
    val features = listOf(
        "Acceso completo a todos los terminales de calendario (0 al 9)",
        "Registro ilimitado de empresas, sucursales y clientes",
        "Cálculo automatizado de la próxima declaración por RIF",
        "Cotización cambiaria oficial en tiempo real (USD / VES / EUR)",
        "Historial de auditoría tributaria y respaldo en la nube",
        "Alertas anticipadas de vencimientos tributarios SENIAT"
    )

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(SlateDarkBackground)
            .statusBarsPadding()
    ) {
        // Header
        Column(modifier = Modifier.padding(horizontal = 20.dp, vertical = 12.dp)) {
            Text(
                text = "Fiscal Shield",
                style = MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold,
                color = TextPrimary
            )
            Text(
                text = "Blindaje y niveles de protección tributaria",
                style = MaterialTheme.typography.bodySmall,
                color = TextMuted
            )
        }

        LazyColumn(
            modifier = Modifier.fillMaxSize(),
            contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // Current Plan Status Hero
            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clip(RoundedCornerShape(22.dp))
                        .background(SlateCard)
                        .border(
                            1.5.dp,
                            if (isPro) EmeraldPrimary else AmberWarning,
                            RoundedCornerShape(22.dp)
                        )
                        .padding(20.dp)
                ) {
                    Column {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Box(
                                    modifier = Modifier
                                        .size(40.dp)
                                        .clip(RoundedCornerShape(12.dp))
                                        .background(EmeraldContainer),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Icon(
                                        imageVector = Icons.Default.Security,
                                        contentDescription = null,
                                        tint = EmeraldLight,
                                        modifier = Modifier.size(22.dp)
                                    )
                                }
                                Spacer(modifier = Modifier.width(12.dp))
                                Column {
                                    Text(
                                        text = state.status.tier.displayName,
                                        style = MaterialTheme.typography.titleLarge,
                                        fontWeight = FontWeight.Bold,
                                        color = TextPrimary
                                    )
                                    Text(
                                        text = if (isPro) "Vigente hasta ${state.status.expiresAt ?: "Indefinido"}" else "Plan Gratuito Limitado",
                                        style = MaterialTheme.typography.bodySmall,
                                        color = TextSecondary
                                    )
                                }
                            }

                            Box(
                                modifier = Modifier
                                    .clip(RoundedCornerShape(20.dp))
                                    .background(if (isPro) EmeraldContainer else SlateCardElevated)
                                    .padding(horizontal = 10.dp, vertical = 4.dp)
                            ) {
                                Text(
                                    text = if (isPro) "ACTIVO" else "FREE",
                                    style = MaterialTheme.typography.labelSmall,
                                    color = if (isPro) EmeraldLight else AmberLight,
                                    fontWeight = FontWeight.Bold
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        // Stats Quotas
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            Box(
                                modifier = Modifier
                                    .weight(1f)
                                    .clip(RoundedCornerShape(12.dp))
                                    .background(SlateCardElevated)
                                    .padding(10.dp)
                            ) {
                                Column {
                                    Text("Empresas", style = MaterialTheme.typography.bodySmall, color = TextMuted)
                                    Text(
                                        text = "${state.status.currentCompaniesCount} / ${if (isPro) "∞" else "1"}",
                                        style = MaterialTheme.typography.titleMedium,
                                        fontWeight = FontWeight.Bold,
                                        color = TextPrimary
                                    )
                                }
                            }

                            Box(
                                modifier = Modifier
                                    .weight(1f)
                                    .clip(RoundedCornerShape(12.dp))
                                    .background(SlateCardElevated)
                                    .padding(10.dp)
                            ) {
                                Column {
                                    Text("Terminales", style = MaterialTheme.typography.bodySmall, color = TextMuted)
                                    Text(
                                        text = if (isPro) "0 al 9 (Todos)" else "0, 1 y 2",
                                        style = MaterialTheme.typography.titleMedium,
                                        fontWeight = FontWeight.Bold,
                                        color = TextPrimary
                                    )
                                }
                            }
                        }
                    }
                }
            }

            // Upgrade Success Pill
            if (state.isUpgradeSuccess) {
                item {
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .clip(RoundedCornerShape(14.dp))
                            .background(EmeraldContainer)
                            .border(1.dp, EmeraldPrimary, RoundedCornerShape(14.dp))
                            .padding(14.dp)
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Icon(
                                imageVector = Icons.Default.CheckCircle,
                                contentDescription = null,
                                tint = EmeraldLight
                            )
                            Spacer(modifier = Modifier.width(10.dp))
                            Text(
                                text = "¡Plan Fiscal Shield Pro activado exitosamente!",
                                style = MaterialTheme.typography.bodyMedium,
                                color = TextPrimary,
                                fontWeight = FontWeight.Bold
                            )
                        }
                    }
                }
            }

            // Plan Feature Comparisons
            item {
                Text(
                    text = "Beneficios del Ecosistema Dclare",
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.Bold,
                    color = TextPrimary
                )
            }

            items(features) { feature ->
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    modifier = Modifier.padding(vertical = 4.dp)
                ) {
                    Icon(
                        imageVector = Icons.Default.CheckCircle,
                        contentDescription = null,
                        tint = EmeraldLight,
                        modifier = Modifier.size(18.dp)
                    )
                    Spacer(modifier = Modifier.width(10.dp))
                    Text(
                        text = feature,
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary
                    )
                }
            }

            // Upgrade Button
            item {
                Spacer(modifier = Modifier.height(10.dp))
                Button(
                    onClick = { onEvent(ShieldUiEvent.UpgradeToPro) },
                    colors = ButtonDefaults.buttonColors(
                        containerColor = EmeraldPrimary,
                        contentColor = TextOnEmerald
                    ),
                    shape = RoundedCornerShape(14.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(52.dp)
                ) {
                    Row(
                        horizontalArrangement = Arrangement.Center,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Icon(
                            imageVector = Icons.Default.Star,
                            contentDescription = null,
                            modifier = Modifier.size(18.dp)
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        Text(
                            text = if (isPro) "Renovar Fiscal Shield ($1.99 USD)" else "Activar Fiscal Shield Pro ($1.99 USD)",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.Bold
                        )
                    }
                }
            }

            item {
                Spacer(modifier = Modifier.height(70.dp))
            }
        }
    }
}
