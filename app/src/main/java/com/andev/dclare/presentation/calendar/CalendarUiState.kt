package com.andev.dclare.presentation.calendar

import com.andev.dclare.domain.model.CompanyProfile
import com.andev.dclare.domain.model.TaxObligation

data class CalendarDayInfo(
    val dayOfMonth: Int,
    val isDueDay: Boolean,
    val dueObligationsCount: Int = 0,
    val isSelected: Boolean = false,
    val isToday: Boolean = false
)

data class CalendarUiState(
    val selectedTerminalDigit: Int = 1,
    val selectedMonth: Int = 11, // November default
    val selectedYear: Int = 2026,
    val selectedDay: Int = 15,
    val daysInMonth: List<CalendarDayInfo> = emptyList(),
    val obligationsForSelectedDay: List<TaxObligation> = emptyList(),
    val isProShield: Boolean = true,
    val allowedTerminalDigits: List<Int> = (0..9).toList(),
    val selectedCompany: CompanyProfile? = null
)

sealed interface CalendarUiEvent {
    data class SelectTerminalDigit(val digit: Int) : CalendarUiEvent
    data class SelectMonth(val month: Int) : CalendarUiEvent
    data class SelectDay(val day: Int) : CalendarUiEvent
}

