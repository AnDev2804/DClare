package com.andev.dclare.presentation.calendar

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.andev.dclare.data.mock.MockFiscalData
import com.andev.dclare.data.repository.FiscalRepository
import com.andev.dclare.di.ServiceLocator
import com.andev.dclare.domain.model.ShieldTier
import com.andev.dclare.domain.model.TaxObligation
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch
import java.util.Calendar

class CalendarViewModel(
    private val fiscalRepository: FiscalRepository = ServiceLocator.fiscalRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(CalendarUiState())
    val uiState: StateFlow<CalendarUiState> = _uiState.asStateFlow()

    init {
        val cal = Calendar.getInstance()
        val currentMonth = cal.get(Calendar.MONTH) + 1
        val currentYear = cal.get(Calendar.YEAR)
        val currentDay = cal.get(Calendar.DAY_OF_MONTH)

        viewModelScope.launch {
            fiscalRepository.membershipStatus.collect { status ->
                val isPro = status.tier != ShieldTier.FREE
                val defaultDigit = fiscalRepository.selectedCompany.value?.terminalDigit ?: 1
                _uiState.update {
                    it.copy(
                        selectedTerminalDigit = defaultDigit,
                        selectedMonth = currentMonth,
                        selectedYear = currentYear,
                        selectedDay = currentDay,
                        isProShield = isPro,
                        allowedTerminalDigits = if (isPro) (0..9).toList() else listOf(0, 1, 2),
                        selectedCompany = fiscalRepository.selectedCompany.value
                    )
                }
                recalculateCalendar()
            }
        }
    }

    fun onEvent(event: CalendarUiEvent) {
        when (event) {
            is CalendarUiEvent.SelectTerminalDigit -> {
                _uiState.update { it.copy(selectedTerminalDigit = event.digit) }
                recalculateCalendar()
            }
            is CalendarUiEvent.SelectMonth -> {
                _uiState.update { it.copy(selectedMonth = event.month) }
                recalculateCalendar()
            }
            is CalendarUiEvent.SelectDay -> {
                _uiState.update { it.copy(selectedDay = event.day) }
                updateSelectedDayObligations()
            }
        }
    }

    private fun recalculateCalendar() {
        val state = _uiState.value
        val month = state.selectedMonth
        val digit = state.selectedTerminalDigit

        // Days in month
        val totalDays = when (month) {
            2 -> 28
            4, 6, 9, 11 -> 30
            else -> 31
        }

        // Collect all due days for all 13 taxes for this digit and month
        val dueDayCounts = mutableMapOf<Int, Int>()
        MockFiscalData.ALL_TAX_CATALOG.forEach { tax ->
            val days = fiscalRepository.getCalendarDueDays(tax.code, digit, month)
            days.forEach { d ->
                dueDayCounts[d] = (dueDayCounts[d] ?: 0) + 1
            }
        }

        val daysList = (1..totalDays).map { day ->
            val count = dueDayCounts[day] ?: 0
            CalendarDayInfo(
                dayOfMonth = day,
                isDueDay = count > 0,
                dueObligationsCount = count,
                isSelected = day == state.selectedDay,
                isToday = day == 8 && month == 11 // Mock reference date or today
            )
        }

        _uiState.update { it.copy(daysInMonth = daysList) }
        updateSelectedDayObligations()
    }

    private fun updateSelectedDayObligations() {
        val state = _uiState.value
        val month = state.selectedMonth
        val digit = state.selectedTerminalDigit
        val selectedDay = state.selectedDay

        val matchingTaxes = mutableListOf<TaxObligation>()
        MockFiscalData.ALL_TAX_CATALOG.forEach { tax ->
            val days = fiscalRepository.getCalendarDueDays(tax.code, digit, month)
            if (days.contains(selectedDay)) {
                matchingTaxes.add(
                    tax.copy(
                        nextDueDate = String.format("%02d/%02d/%04d", selectedDay, month, state.selectedYear)
                    )
                )
            }
        }

        _uiState.update { it.copy(obligationsForSelectedDay = matchingTaxes) }
    }
}
