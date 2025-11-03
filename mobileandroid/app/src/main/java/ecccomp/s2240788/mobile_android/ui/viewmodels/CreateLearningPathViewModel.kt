package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateLearningPathRequest
import ecccomp.s2240788.mobile_android.data.models.LearningPath
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * CreateLearningPathViewModel
 * 学習パス作成画面のビジネスロジック
 */
class CreateLearningPathViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _createdLearningPath = MutableLiveData<LearningPath?>()
    val createdLearningPath: LiveData<LearningPath?> = _createdLearningPath

    /**
     * 学習パスを作成
     */
    fun createLearningPath(
        title: String,
        description: String?,
        goalType: String,
        targetStartDate: String?,
        targetEndDate: String?,
        estimatedHoursTotal: Int?
    ) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val request = CreateLearningPathRequest(
                    title = title,
                    description = description,
                    goal_type = goalType,
                    target_start_date = targetStartDate,
                    target_end_date = targetEndDate,
                    estimated_hours_total = estimatedHoursTotal
                )

                val response = apiService.createLearningPath(request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _createdLearningPath.value = apiResponse.data
                    } else {
                        _error.value = apiResponse?.message ?: "作成に失敗しました"
                    }
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * エラーをクリア
     */
    fun clearError() {
        _error.value = null
    }
}

