package com.andev.dclare.presentation.common

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Alarm
import androidx.compose.material.icons.filled.ArrowForward
import androidx.compose.material.icons.filled.CalendarToday
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
import com.andev.dclare.domain.model.DeadlineUrgency
import com.andev.dclare.domain.model.FiscalDeadline
import com.andev.dclare.presentation.theme.AmberContainer
import com.andev.dclare.presentation.theme.AmberLight
import com.andev.dclare.presentation.theme.AmberWarning
import com.andev.dclare.presentation.theme.CrimsonAlert
import com.andev.dclare.presentation.theme.CrimsonContainer
import com.andev.dclare.presentation.theme.EmeraldContainer
import com.andev.dclare.presentation.theme.EmeraldLight
import com.andev.dclare.presentation.theme.EmeraldPrimary
import com.andev.dclare.presentation.theme.SlateBorder
import com.andev.dclare.presentation.theme.SlateCard
import com.andev.dclare.presentation.theme.SlateCardElevated
import com.andev.dclare.presentation.theme.TextMuted
import com.andev.dclare.presentation.theme.TextOnEmerald
import com.andev.dclare.presentation.theme.TextPrimary
import com.andev.dclare.presentation.theme.TextSecondary
import java.util.Locale

@Composable
fun FiscalCountdownCard(
    deadline: FiscalDeadline,
    onViewCalendarClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    val (badgeBg, badgeText, badgeBorder) = when (deadline.urgency) {
        DeadlineUrgency.CRITICAL -> Triple(CrimsonContainer, CrimsonAlert, CrimsonAlert.copy(alpha = 0.5f))
        DeadlineUrgency.UPCOMING -> Triple(AmberContainer, AmberLight, AmberWarning.copy(alpha = 0.5f))
        DeadlineUrgency.NORMAL -> Triple(EmeraldContainer, EmeraldLight, EmeraldPrimary.copy(alpha = 0.5f))
    }

    Box(
        modifier = modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(22.dp))
            .background(SlateCard)
            .border(1.dp, SlateBorder, RoundedCornerShape(22.dp))
            .padding(20.dp)
    ) {
        Column {
            // Header Row: Category + Urgency Pill
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .size(8.dp)
                            .clip(CircleShape)
                            .background(badgeText)
                    )
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(
                        text = "PRÓXIMO VENCIMIENTO",
                        style = MaterialTheme.typography.labelSmall,
                        color = TextMuted,
                        fontWeight = FontWeight.Bold,
                        letterSpacing = 1.sp
                    )
                }

                // Days Remaining Pill
                Box(
                    modifier = Modifier
                        .clip(RoundedCornerShape(20.dp))
                        .background(badgeBg)
                        .border(1.dp, badgeBorder, RoundedCornerShape(20.dp))
                        .padding(horizontal = 10.dp, vertical = 4.dp)
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(
                            imageVector = Icons.Default.Alarm,
                            contentDescription = null,
                            tint = badgeText,
                            modifier = Modifier.size(12.dp)
                        )
                        Spacer(modifier = Modifier.width(4.dp))
                        Text(
                            text = if (deadline.daysRemaining == 0) "¡VENCE HOY!" else "QUEDAN ${deadline.daysRemaining} DÍAS",
                            style = MaterialTheme.typography.labelSmall,
                            color = badgeText,
                            fontWeight = FontWeight.Bold,
                            fontSize = 10.sp
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(14.dp))

            // Main Tax Title
            Text(
                text = deadline.obligationName,
                style = MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold,
                color = TextPrimary
            )

            Spacer(modifier = Modifier.height(4.dp))

            // Company info & Date
            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(
                    imageVector = Icons.Default.CalendarToday,
                    contentDescription = null,
                    tint = TextMuted,
                    modifier = Modifier.size(14.dp)
                )
                Spacer(modifier = Modifier.width(6.dp))
                Text(
                    text = "Fecha límite: ${deadline.formattedDueDate}",
                    style = MaterialTheme.typography.bodyMedium,
                    color = TextSecondary,
                    fontWeight = FontWeight.Medium
                )
                Spacer(modifier = Modifier.width(8.dp))
                Text(
                    text = "• ${deadline.taxId}",
                    style = MaterialTheme.typography.bodyMedium,
                    color = TextMuted
                )
            }

            Spacer(modifier = Modifier.height(16.dp))

            // Estimation Box
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .clip(RoundedCornerShape(14.dp))
                    .background(SlateCardElevated)
                    .border(1.dp, SlateBorder, RoundedCornerShape(14.dp))
                    .padding(14.dp)
            ) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Column {
                        Text(
                            text = "Estimación Fiscal",
                            style = MaterialTheme.typography.bodySmall,
                            color = TextMuted
                        )
                        Row(verticalAlignment = Alignment.Bottom) {
                            Text(
                                text = String.format(Locale.US, "$%,.2f", deadline.estimatedAmountUsd),
                                style = MaterialTheme.typography.titleLarge,
                                fontWeight = FontWeight.Bold,
                                color = TextPrimary
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = "USD",
                                style = MaterialTheme.typography.labelSmall,
                                color = TextMuted
                            )
                        }
                    }

                    Column(horizontalAlignment = Alignment.End) {
                        Text(
                            text = "Equivalente VES",
                            style = MaterialTheme.typography.bodySmall,
                            color = TextMuted
                        )
                        Text(
                            text = String.format(Locale.US, "Bs. %,.2f", deadline.estimatedAmountVes),
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.SemiBold,
                            color = EmeraldLight
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            // Action Button
            Button(
                onClick = onViewCalendarClick,
                colors = ButtonDefaults.buttonColors(
                    containerColor = EmeraldPrimary,
                    contentColor = TextOnEmerald
                ),
                shape = RoundedCornerShape(12.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Row(
                    horizontalArrangement = Arrangement.Center,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "Gestionar en Calendario",
                        style = MaterialTheme.typography.labelLarge,
                        fontWeight = FontWeight.Bold
                    )
                    Spacer(modifier = Modifier.width(6.dp))
                    Icon(
                        imageVector = Icons.Default.ArrowForward,
                        contentDescription = null,
                        modifier = Modifier.size(16.dp)
                    )
                }
            }
        }
    }
}
