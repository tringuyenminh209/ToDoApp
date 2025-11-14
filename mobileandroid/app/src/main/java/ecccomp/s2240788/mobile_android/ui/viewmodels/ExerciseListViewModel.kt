package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CheatCodeLanguage
import ecccomp.s2240788.mobile_android.data.models.ExerciseSummary
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class ExerciseListViewModel(
    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )
) : ViewModel() {

    private val _exercises = MutableLiveData<List<ExerciseSummary>>()
    val exercises: LiveData<List<ExerciseSummary>> = _exercises

    private val _language = MutableLiveData<CheatCodeLanguage?>()
    val language: LiveData<CheatCodeLanguage?> = _language

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private var allExercises: List<ExerciseSummary> = emptyList()
    private var currentFilter: String? = null

    fun loadExercises(languageId: Int, difficulty: String? = null, search: String? = null) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null

            try {
                val response = apiService.getExercises(
                    languageId = languageId,
                    difficulty = difficulty,
                    search = search,
                    sortBy = "sort_order",
                    sortOrder = "asc"
                )

                if (response.isSuccessful) {
                    val exerciseResponse = response.body()
                    if (exerciseResponse?.success == true) {
                        val data = exerciseResponse.data
                        _language.value = data.language
                        allExercises = data.exercises
                        _exercises.value = allExercises
                    } else {
                        _error.value = exerciseResponse?.message ?: "Failed to load exercises"
                        _exercises.value = emptyList()
                    }
                } else {
                    _error.value = "API Error: ${response.message()}"
                    _exercises.value = emptyList()
                }
            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                _exercises.value = emptyList()
                android.util.Log.e("ExerciseListViewModel", "Error loading exercises", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun filterByDifficulty(difficulty: String?) {
        currentFilter = difficulty
        if (difficulty == null) {
            _exercises.value = allExercises
        } else {
            _exercises.value = allExercises.filter { it.difficulty == difficulty }
        }
    }

    fun searchExercises(query: String) {
        if (query.isEmpty()) {
            _exercises.value = if (currentFilter == null) {
                allExercises
            } else {
                allExercises.filter { it.difficulty == currentFilter }
            }
        } else {
            val filtered = allExercises.filter {
                it.title.contains(query, ignoreCase = true) ||
                        it.description.contains(query, ignoreCase = true) ||
                        it.tags?.any { tag -> tag.contains(query, ignoreCase = true) } == true
            }
            _exercises.value = if (currentFilter == null) {
                filtered
            } else {
                filtered.filter { it.difficulty == currentFilter }
            }
        }
    }

    fun refresh(languageId: Int) {
        loadExercises(languageId, currentFilter)
    }
}
