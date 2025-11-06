package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CheatCodeLanguage
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class CheatCodeViewModel(private val apiService: ApiService = NetworkModule.provideApiService(
    NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
)) : ViewModel() {

    private val _languages = MutableLiveData<List<CheatCodeLanguage>>()
    val languages: LiveData<List<CheatCodeLanguage>> = _languages

    private val _filteredLanguages = MutableLiveData<List<CheatCodeLanguage>>()
    val filteredLanguages: LiveData<List<CheatCodeLanguage>> = _filteredLanguages

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private var allLanguages: List<CheatCodeLanguage> = emptyList()
    private var currentCategory: String = "all"
    private var currentSearchQuery: String = ""

    init {
        loadLanguages()
    }

    fun loadLanguages() {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null

            try {
                val response = apiService.getCheatCodeLanguages(
                    category = if (currentCategory != "all") currentCategory else null,
                    search = if (currentSearchQuery.isNotBlank()) currentSearchQuery else null
                )

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        allLanguages = apiResponse.data ?: emptyList()
                        _languages.value = allLanguages
                        applyFilters()
                    } else {
                        _error.value = apiResponse?.message ?: "Failed to load languages"
                        _languages.value = emptyList()
                    }
                } else {
                    _error.value = "API Error: ${response.message()}"
                    _languages.value = emptyList()
                }
            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                _languages.value = emptyList()
                android.util.Log.e("CheatCodeViewModel", "Error loading languages", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun filterByCategory(category: String) {
        currentCategory = category
        currentSearchQuery = "" // Reset search when filtering by category
        loadLanguages()
    }

    fun searchLanguages(query: String) {
        currentSearchQuery = query
        loadLanguages()
    }

    private fun applyFilters() {
        var filtered = allLanguages

        // Filter by category - use category field instead of hardcoded list
        filtered = if (currentCategory == "all") {
            filtered
        } else {
            filtered.filter { it.category == currentCategory }
        }

        // Filter by search query (already applied in API call, but keep for local filtering)
        if (currentSearchQuery.isNotBlank()) {
            filtered = filtered.filter { language ->
                language.displayName.contains(currentSearchQuery, ignoreCase = true) ||
                language.name.contains(currentSearchQuery, ignoreCase = true)
            }
        }

        _filteredLanguages.value = filtered
    }

    fun getLanguageById(languageId: Int): CheatCodeLanguage? {
        return allLanguages.find { it.id == languageId }
    }

    fun refresh() {
        loadLanguages()
    }
}
