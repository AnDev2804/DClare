package com.andev.dclare.presentation.clients

import com.andev.dclare.domain.model.CompanyProfile
import com.andev.dclare.domain.model.TaxObligation
import com.andev.dclare.domain.model.TaxpayerType

data class ClientsUiState(
    val companies: List<CompanyProfile> = emptyList(),
    val selectedCompany: CompanyProfile? = null,
    val obligations: List<TaxObligation> = emptyList(),
    val isAddDialogOpen: Boolean = false,
    val newTaxId: String = "",
    val newBusinessName: String = "",
    val newTaxpayerType: TaxpayerType = TaxpayerType.ORDINARIO,
    val errorMessage: String? = null
)

sealed interface ClientsUiEvent {
    data class SelectCompany(val companyId: String) : ClientsUiEvent
    data class ToggleTaxPreference(val taxCode: String, val applies: Boolean) : ClientsUiEvent
    data class DeleteCompany(val companyId: String) : ClientsUiEvent
    object OpenAddDialog : ClientsUiEvent
    object CloseAddDialog : ClientsUiEvent
    data class NewTaxIdChanged(val value: String) : ClientsUiEvent
    data class NewBusinessNameChanged(val value: String) : ClientsUiEvent
    data class NewTaxpayerTypeChanged(val type: TaxpayerType) : ClientsUiEvent
    object SubmitAddCompany : ClientsUiEvent
}
