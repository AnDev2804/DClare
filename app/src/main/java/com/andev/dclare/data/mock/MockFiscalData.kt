package com.andev.dclare.data.mock

import com.andev.dclare.domain.model.CompanyProfile
import com.andev.dclare.domain.model.TaxObligation
import com.andev.dclare.domain.model.TaxpayerType

object MockFiscalData {

    // 13 Official Taxes
    val ALL_TAX_CATALOG = listOf(
        TaxObligation(
            code = "ANTI_ISLR",
            shortName = "Anticipo ISLR",
            fullName = "Anticipo de Impuesto Sobre la Renta (Quincenal)",
            category = "Renta & Ganancias",
            applies = true,
            estimatedBaseAmountUsd = 120.0
        ),
        TaxObligation(
            code = "DEC_IVA",
            shortName = "Declaración IVA",
            fullName = "Declaración de Impuesto al Valor Agregado",
            category = "Consumo & Ventas",
            applies = true,
            estimatedBaseAmountUsd = 340.0
        ),
        TaxObligation(
            code = "RET_IVA",
            shortName = "Retenciones IVA",
            fullName = "Retenciones de Impuesto al Valor Agregado a Terceros",
            category = "Retenciones",
            applies = true,
            estimatedBaseAmountUsd = 180.0
        ),
        TaxObligation(
            code = "RET_ISLR_RENT",
            shortName = "Retenciones ISLR",
            fullName = "Retenciones de I.S.L.R. sobre Pagos y Honorarios",
            category = "Retenciones",
            applies = true,
            estimatedBaseAmountUsd = 95.0
        ),
        TaxObligation(
            code = "DEC_IGTF",
            shortName = "Declaración IGTF",
            fullName = "Grandes Transacciones Financieras en Divisas (3%)",
            category = "Transaccional",
            applies = true,
            estimatedBaseAmountUsd = 145.0
        ),
        TaxObligation(
            code = "DEC_PROT_PEN",
            shortName = "Protección Pensiones",
            fullName = "Contribución Especial de Protección de Pensiones",
            category = "Seguridad Social",
            applies = true,
            estimatedBaseAmountUsd = 65.0
        ),
        TaxObligation(
            code = "DD_ISLR",
            shortName = "Definitiva ISLR",
            fullName = "Declaración Definitiva Anual del I.S.L.R.",
            category = "Renta & Ganancias",
            applies = false,
            estimatedBaseAmountUsd = 850.0
        ),
        TaxObligation(
            code = "EST_ISLR_RENT",
            shortName = "Estimadas ISLR",
            fullName = "Declaración Estimada de I.S.L.R.",
            category = "Renta & Ganancias",
            applies = false,
            estimatedBaseAmountUsd = 210.0
        ),
        TaxObligation(
            code = "DEC_GRA_PATRI",
            shortName = "Grandes Patrimonios",
            fullName = "Impuesto a los Grandes Patrimonios (Sujetos Pasivos Especiales)",
            category = "Patrimonio",
            applies = false,
            estimatedBaseAmountUsd = 400.0
        ),
        TaxObligation(
            code = "DEC_AUTO_ENTDES",
            shortName = "Servicios Autónomos",
            fullName = "Declaraciones de Servicios y Entes Descentralizados",
            category = "Especial",
            applies = false,
            estimatedBaseAmountUsd = 75.0
        ),
        TaxObligation(
            code = "ENVITE_AZAR",
            shortName = "Envite y Azar",
            fullName = "Declaraciones de Juegos de Envite y Azar",
            category = "Especial",
            applies = false,
            estimatedBaseAmountUsd = 0.0
        ),
        TaxObligation(
            code = "RET_ISLR_LOT",
            shortName = "Retenciones Lotería",
            fullName = "Retenciones I.S.L.R. Premios de Lotería",
            category = "Retenciones",
            applies = false,
            estimatedBaseAmountUsd = 0.0
        ),
        TaxObligation(
            code = "DEC_DEF_ISLREI",
            shortName = "ISLR Irregulares",
            fullName = "Declaraciones Definitivas I.S.L.R. Ejercicios Irregulares",
            category = "Renta & Ganancias",
            applies = false,
            estimatedBaseAmountUsd = 0.0
        )
    )

    // Fictional Profiles
    val MOCK_COMPANIES = listOf(
        CompanyProfile(
            id = "comp_1",
            taxId = "TAX-00123-X",
            businessName = "Hyperion Digital Solutions LLC",
            type = TaxpayerType.ESPECIAL,
            terminalDigit = 1,
            activeTaxesCount = 6,
            isPrimary = true,
            taxPreferences = mapOf(
                "ANTI_ISLR" to true,
                "DEC_IVA" to true,
                "RET_IVA" to true,
                "RET_ISLR_RENT" to true,
                "DEC_IGTF" to true,
                "DEC_PROT_PEN" to true,
                "DD_ISLR" to false,
                "EST_ISLR_RENT" to false,
                "DEC_GRA_PATRI" to false,
                "DEC_AUTO_ENTDES" to false,
                "ENVITE_AZAR" to false,
                "RET_ISLR_LOT" to false,
                "DEC_DEF_ISLREI" to false
            )
        ),
        CompanyProfile(
            id = "comp_2",
            taxId = "J-29837192-2",
            businessName = "NovaTech Cloud Consulting C.A.",
            type = TaxpayerType.ESPECIAL,
            terminalDigit = 2,
            activeTaxesCount = 5,
            isPrimary = false,
            taxPreferences = mapOf(
                "ANTI_ISLR" to true,
                "DEC_IVA" to true,
                "RET_IVA" to true,
                "DEC_IGTF" to true,
                "DEC_PROT_PEN" to true
            )
        ),
        CompanyProfile(
            id = "comp_3",
            taxId = "TAX-00789-F",
            businessName = "Apex Studio Freelance",
            type = TaxpayerType.ORDINARIO,
            terminalDigit = 0,
            activeTaxesCount = 3,
            isPrimary = false,
            taxPreferences = mapOf(
                "DEC_IVA" to true,
                "RET_ISLR_RENT" to true,
                "DD_ISLR" to true
            )
        ),
        CompanyProfile(
            id = "comp_4",
            taxId = "V-18492041-5",
            businessName = "Carlos Mendoza (Consultor IT)",
            type = TaxpayerType.ORDINARIO,
            terminalDigit = 5,
            activeTaxesCount = 2,
            isPrimary = false,
            taxPreferences = mapOf(
                "DEC_IVA" to true,
                "RET_ISLR_RENT" to true
            )
        )
    )

    // Calendar Matrix by Terminal Digit (0 to 9) for current months (days of month)
    // Key: Pair(taxCode, terminalDigit) -> Map of month (1..12) to list of day numbers
    val CALENDAR_DUE_DAYS: Map<Pair<String, Int>, Map<Int, List<Int>>> = run {
        val map = mutableMapOf<Pair<String, Int>, Map<Int, List<Int>>>()

        // Generate consistent, realistic calendar schedule for each digit (0..9)
        for (digit in 0..9) {
            val baseOffset = (digit * 2) % 7
            for (tax in ALL_TAX_CATALOG) {
                val monthsMap = mutableMapOf<Int, List<Int>>()
                for (month in 1..12) {
                    when (tax.code) {
                        "ANTI_ISLR", "DEC_IVA", "RET_IVA", "DEC_IGTF" -> {
                            // Bi-weekly declarations (e.g., 6th and 20th + offset)
                            val day1 = ((5 + baseOffset) % 12) + 4
                            val day2 = ((18 + baseOffset) % 10) + 18
                            monthsMap[month] = listOf(day1, day2)
                        }
                        "DEC_PROT_PEN", "RET_ISLR_RENT", "DEC_AUTO_ENTDES" -> {
                            // Monthly single declaration (e.g., 15th)
                            val day = ((10 + baseOffset) % 15) + 8
                            monthsMap[month] = listOf(day)
                        }
                        "DD_ISLR" -> {
                            // Definitive declaration only in months 1 to 3 (Q1)
                            if (month in 1..3) {
                                val day = 15 + (digit % 14)
                                monthsMap[month] = listOf(day)
                            }
                        }
                        "DEC_GRA_PATRI" -> {
                            // Large fortunes declaration only in months 10 and 11
                            if (month in 10..11) {
                                val day = 10 + (digit % 15)
                                monthsMap[month] = listOf(day)
                            }
                        }
                        else -> {
                            val day = ((7 + baseOffset) % 18) + 6
                            monthsMap[month] = listOf(day)
                        }
                    }
                }
                map[Pair(tax.code, digit)] = monthsMap
            }
        }
        map
    }
}

