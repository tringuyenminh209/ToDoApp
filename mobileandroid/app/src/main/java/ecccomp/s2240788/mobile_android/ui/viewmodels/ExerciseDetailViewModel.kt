package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class ExerciseDetailViewModel(
    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )
) : ViewModel() {

    private val _exercise = MutableLiveData<ExerciseDetail?>()
    val exercise: LiveData<ExerciseDetail?> = _exercise

    private val _submitResult = MutableLiveData<SubmitSolutionResponse?>()
    val submitResult: LiveData<SubmitSolutionResponse?> = _submitResult

    private val _solution = MutableLiveData<String?>()
    val solution: LiveData<String?> = _solution

    private val _statistics = MutableLiveData<ExerciseStatistics?>()
    val statistics: LiveData<ExerciseStatistics?> = _statistics

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _isSubmitting = MutableLiveData<Boolean>()
    val isSubmitting: LiveData<Boolean> = _isSubmitting

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _submitError = MutableLiveData<String?>()
    val submitError: LiveData<String?> = _submitError

    fun loadExercise(languageId: Int, exerciseId: Int) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null

            try {
                val response = apiService.getExercise(languageId, exerciseId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _exercise.value = apiResponse.data
                    } else {
                        _error.value = apiResponse?.message ?: "Failed to load exercise"
                        _exercise.value = null
                    }
                } else {
                    _error.value = "API Error: ${response.message()}"
                    _exercise.value = null
                }
            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                _exercise.value = null
                android.util.Log.e("ExerciseDetailViewModel", "Error loading exercise", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun submitSolution(languageId: Int, exerciseId: Int, code: String) {
        viewModelScope.launch {
            _isSubmitting.value = true
            _submitError.value = null
            _submitResult.value = null

            try {
                val request = SubmitSolutionRequest(code)
                val response = apiService.submitExerciseSolution(languageId, exerciseId, request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _submitResult.value = apiResponse.data
                    } else {
                        _submitError.value = apiResponse?.message ?: "Failed to submit solution"
                    }
                } else {
                    _submitError.value = "API Error: ${response.message()}"
                }
            } catch (e: Exception) {
                _submitError.value = "Network error: ${e.message}"
                android.util.Log.e("ExerciseDetailViewModel", "Error submitting solution", e)
            } finally {
                _isSubmitting.value = false
            }
        }
    }

    fun loadSolution(languageId: Int, exerciseId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.getExerciseSolution(languageId, exerciseId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _solution.value = apiResponse.data?.solution
                    } else {
                        _error.value = apiResponse?.message ?: "Failed to load solution"
                    }
                } else {
                    _error.value = "API Error: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                android.util.Log.e("ExerciseDetailViewModel", "Error loading solution", e)
            }
        }
    }

    fun loadStatistics(languageId: Int, exerciseId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.getExerciseStatistics(languageId, exerciseId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _statistics.value = apiResponse.data
                    } else {
                        _error.value = apiResponse?.message ?: "Failed to load statistics"
                    }
                } else {
                    _error.value = "API Error: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                android.util.Log.e("ExerciseDetailViewModel", "Error loading statistics", e)
            }
        }
    }

    fun clearSubmitResult() {
        _submitResult.value = null
        _submitError.value = null
    }

    fun refresh(languageId: Int, exerciseId: Int) {
        loadExercise(languageId, exerciseId)
        loadStatistics(languageId, exerciseId)
    }
}
