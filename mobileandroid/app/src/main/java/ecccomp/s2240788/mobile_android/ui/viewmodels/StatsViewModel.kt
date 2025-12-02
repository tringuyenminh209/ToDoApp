package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.UserStats
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * StatsViewModel
 * 統計画面のビジネスロジック管理
 * - ユーザー統計データの取得と管理
 * - Various metrics の計算
 */
class StatsViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _stats = MutableLiveData<UserStats?>()
    val stats: LiveData<UserStats?> = _stats

    private val _goldenTimeData = MutableLiveData<ecccomp.s2240788.mobile_android.data.models.GoldenTimeData?>()
    val goldenTimeData: LiveData<ecccomp.s2240788.mobile_android.data.models.GoldenTimeData?> = _goldenTimeData

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    init {
        fetchStats()
        fetchGoldenTime()
    }

    /**
     * ユーザー統計を取得
     */
    fun fetchStats() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getUserStats()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _stats.value = apiResponse.data
                        android.util.Log.d("StatsViewModel", "Stats loaded successfully: completed_tasks=${apiResponse.data?.completed_tasks}")
                    } else {
                        _error.value = apiResponse?.message ?: "統計の取得に失敗しました"
                    }
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
                // Set default/empty stats on error
                _stats.value = null
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * ゴールデンタイムデータを取得
     */
    fun fetchGoldenTime() {
        viewModelScope.launch {
            try {
                val response = apiService.getGoldenTime()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _goldenTimeData.value = apiResponse.data
                        android.util.Log.d("StatsViewModel", "Golden time data loaded: max_minutes=${apiResponse.data?.max_minutes}")
                    } else {
                        android.util.Log.e("StatsViewModel", "Golden time取得失敗: ${apiResponse?.message}")
                    }
                } else {
                    android.util.Log.e("StatsViewModel", "Golden timeネットワークエラー: ${response.code()}")
                }
            } catch (e: Exception) {
                android.util.Log.e("StatsViewModel", "Golden timeエラー: ${e.message}")
                _goldenTimeData.value = null
            }
        }
    }

    /**
     * 統計を再読み込み
     */
    fun refreshStats() {
        fetchStats()
        fetchGoldenTime()
    }

    /**
     * Completion Rate を文字列として取得
     */
    fun getCompletionRateString(): String {
        val rate = _stats.value?.completion_rate ?: 0f
        return String.format("%.1f%%", rate)
    }

    /**
     * Focus Time を時間:分形式で取得
     */
    fun getFormattedFocusTime(): String {
        val minutes = _stats.value?.total_focus_time ?: 0
        val hours = minutes / 60
        val mins = minutes % 60
        return if (hours > 0) {
            "${hours}h ${mins}m"
        } else {
            "${mins}m"
        }
    }

    /**
     * Average Session Duration を分形式で取得
     */
    fun getFormattedAverageSession(): String {
        val minutes = _stats.value?.average_session_duration ?: 0
        return "${minutes} phút"
    }
}

