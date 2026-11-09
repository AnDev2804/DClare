package com.andev.dclare.di

import com.andev.dclare.data.repository.AuthRepository
import com.andev.dclare.data.repository.AuthRepositoryImpl
import com.andev.dclare.data.repository.CurrencyRepository
import com.andev.dclare.data.repository.CurrencyRepositoryImpl
import com.andev.dclare.data.repository.FiscalRepository
import com.andev.dclare.data.repository.FiscalRepositoryImpl
import com.andev.dclare.domain.usecase.ConvertCurrencyUseCase
import com.andev.dclare.domain.usecase.GetFiscalObligationsUseCase
import com.andev.dclare.domain.usecase.GetNextDeadlineUseCase
import com.andev.dclare.domain.usecase.GetTaxBreakdownUseCase
import com.andev.dclare.domain.usecase.ToggleTaxPreferenceUseCase

object ServiceLocator {

    val authRepository: AuthRepository by lazy {
        AuthRepositoryImpl()
    }

    val currencyRepository: CurrencyRepository by lazy {
        CurrencyRepositoryImpl()
    }

    val fiscalRepository: FiscalRepository by lazy {
        FiscalRepositoryImpl(currencyRepository)
    }

    // Use cases
    val getNextDeadlineUseCase: GetNextDeadlineUseCase by lazy {
        GetNextDeadlineUseCase(fiscalRepository)
    }

    val getFiscalObligationsUseCase: GetFiscalObligationsUseCase by lazy {
        GetFiscalObligationsUseCase(fiscalRepository)
    }

    val toggleTaxPreferenceUseCase: ToggleTaxPreferenceUseCase by lazy {
        ToggleTaxPreferenceUseCase(fiscalRepository)
    }

    val convertCurrencyUseCase: ConvertCurrencyUseCase by lazy {
        ConvertCurrencyUseCase(currencyRepository)
    }

    val getTaxBreakdownUseCase: GetTaxBreakdownUseCase by lazy {
        GetTaxBreakdownUseCase(fiscalRepository)
    }
}
