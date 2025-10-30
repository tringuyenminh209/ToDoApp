package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateTaskRequest
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * AddTaskViewModel
 * 新規タスク作成のビジネスロジック
 */
class AddTaskViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _taskCreated = MutableLiveData<Boolean>()
    val taskCreated: LiveData<Boolean> = _taskCreated

    /**
     * タスクを作成
     * Adapts priority string (low/medium/high) to Int (1-5) and sets default energy_level
     */
    fun createTask(
        title: String,
        description: String?,
        priority: String, // "low", "medium", "high"
        dueDate: String?,
        energyLevel: String, // "low" | "medium" | "high"
        estimatedMinutes: Int?
    ) {
        viewModelScope.launch {
            try {
                // Validation
                if (title.trim().isEmpty()) {
                    _error.value = "タイトルは必須です"
                    return@launch
                }

                _isLoading.value = true
                _error.value = null

                // Map priority string to Int (1-5)
                val priorityInt = when (priority) {
                    "low" -> 2
                    "high" -> 5
                    else -> 3  // medium
                }

                val request = CreateTaskRequest(
                    title = title.trim(),
                    description = if (description.isNullOrBlank()) null else description.trim(),
                    priority = priorityInt,
                    energy_level = energyLevel,
                    estimated_minutes = estimatedMinutes,
                    deadline = dueDate
                )

                val response = apiService.createTask(request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        _taskCreated.value = true
                    } else {
                        _error.value = "タスクの作成に失敗しました"
                    }
                } else {
                    _error.value = when (response.code()) {
                        422 -> "入力データが無効です"
                        500 -> "サーバーエラーが発生しました"
                        else -> "タスクの作成に失敗しました: ${response.message()}"
                    }
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearError() {
        _error.value = null
    }

}

