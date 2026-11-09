package com.andev.dclare.data.mappers

import com.andev.dclare.data.dto.FrankfurterResponseDto
import com.andev.dclare.domain.model.CurrencyRates
import com.andev.dclare.domain.model.DeadlineUrgency
import com.andev.dclare.domain.model.FiscalDeadline
import com.andev.dclare.domain.model.TaxDonutSlice
import com.andev.dclare.domain.model.TaxObligation
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

object FiscalMappers {

    fun mapFrankfurterDtoToCurrencyRates(dto: FrankfurterResponseDto, fallbackVesRate: Double = 49.20): CurrencyRates {
        val eur = dto.rates["EUR"] ?: 0.92
        val sdf = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
        return CurrencyRates(
            base = dto.base,
            date = dto.date,
            eurRate = eur,
            vesRate = fallbackVesRate,
            lastUpdated = sdf.format(Date())
        )
    }

    fun calculateUrgency(daysRemaining: Int): DeadlineUrgency {
        return when {
            daysRemaining <= 3 -> DeadlineUrgency.CRITICAL
            daysRemaining <= 7 -> DeadlineUrgency.UPCOMING
            else -> DeadlineUrgency.NORMAL
        }
    }

    fun mapToFiscalDeadline(
        obligation: TaxObligation,
        companyName: String,
        taxId: String,
        dueDateFormatted: String,
        daysRemaining: Int,
        vesExchangeRate: Double
    ): FiscalDeadline {
        val amountUsd = obligation.estimatedBaseAmountUsd
        val amountVes = amountUsd * vesExchangeRate

        return FiscalDeadline(
            id = "${obligation.code}_${taxId}",
            obligationCode = obligation.code,
            obligationName = obligation.shortName,
            companyName = companyName,
            taxId = taxId,
            formattedDueDate = dueDateFormatted,
            daysRemaining = daysRemaining,
            urgency = calculateUrgency(daysRemaining),
            estimatedAmountUsd = amountUsd,
            estimatedAmountVes = amountVes
        )
    }

    fun computeTaxDonutSlices(activeObligations: List<TaxObligation>): List<TaxDonutSlice> {
        val totalUsd = activeObligations.sumOf { it.estimatedBaseAmountUsd }.coerceAtLeast(1.0)
        val palette = listOf(
            0xFF10B981, // Emerald Green
            0xFF06B6D4, // Cyan Accent
            0xFFF59E0B, // Amber Warning
            0xFF8B5CF6, // Purple Accent
            0xFF3B82F6, // Blue
            0xFFEC4899  // Pink
        )

        return activeObligations
            .filter { it.applies }
            .take(6)
            .mapIndexed { index, tax ->
                val slicePercentage = ((tax.estimatedBaseAmountUsd / totalUsd) * 100).toFloat()
                val color = palette[index % palette.size]
                TaxDonutSlice(
                    label = tax.shortName,
                    percentage = slicePercentage,
                    amountUsd = tax.estimatedBaseAmountUsd,
                    colorArgb = color
                )
            }
    }
}
