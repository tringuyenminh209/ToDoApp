package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CheatCodeLanguage
import ecccomp.s2240788.mobile_android.data.models.CheatCodeSection
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class CheatCodeDetailViewModel(private val apiService: ApiService = NetworkModule.provideApiService(
    NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
)) : ViewModel() {

    private val _sections = MutableLiveData<List<CheatCodeSection>>()
    val sections: LiveData<List<CheatCodeSection>> = _sections

    private val _language = MutableLiveData<CheatCodeLanguage?>()
    val language: LiveData<CheatCodeLanguage?> = _language

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    fun loadSections(languageId: Int) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null

            try {
                val response = apiService.getCheatCodeSections(languageId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val sectionsResponse = apiResponse.data
                        _language.value = sectionsResponse?.language
                        _sections.value = sectionsResponse?.sections ?: emptyList()
                    } else {
                        _error.value = apiResponse?.message ?: "Failed to load sections"
                        _sections.value = emptyList()
                    }
                } else {
                    _error.value = "API Error: ${response.message()}"
                    _sections.value = emptyList()
                }
            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                _sections.value = emptyList()
                android.util.Log.e("CheatCodeDetailViewModel", "Error loading sections", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun refresh(languageId: Int) {
        loadSections(languageId)
    }
}
