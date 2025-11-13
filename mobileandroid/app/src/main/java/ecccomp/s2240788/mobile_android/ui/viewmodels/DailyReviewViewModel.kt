package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * DailyReviewViewModel
 * デイリーレビュー画面のビジネスロジック管理
 * - デイリーレビューの作成・取得・更新・削除
 * - 統計データとトレンドの管理
 * - インサイトの取得
 */
class DailyReviewViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    // Daily Review Data
    private val _currentReview = MutableLiveData<DailyReview?>()
    val currentReview: LiveData<DailyReview?> = _currentReview

    private val _reviews = MutableLiveData<List<DailyReview>>()
    val reviews: LiveData<List<DailyReview>> = _reviews

    // Stats & Analytics
    private val _stats = MutableLiveData<DailyReviewStats?>()
    val stats: LiveData<DailyReviewStats?> = _stats

    private val _trends = MutableLiveData<List<DailyReviewTrend>>()
    val trends: LiveData<List<DailyReviewTrend>> = _trends

    private val _insights = MutableLiveData<DailyReviewInsights?>()
    val insights: LiveData<DailyReviewInsights?> = _insights

    // UI State
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _successMessage = MutableLiveData<String?>()
    val successMessage: LiveData<String?> = _successMessage

    /**
     * 今日のレビューを取得
     */
    fun fetchTodayReview() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getTodayReview()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _currentReview.value = apiResponse.data
                    } else {
                        _currentReview.value = null
                        _error.value = apiResponse?.message
                    }
                } else if (response.code() == 404) {
                    // 今日のレビューがまだない
                    _currentReview.value = null
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
                _currentReview.value = null
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * 指定日のレビューを取得
     */
    fun fetchReviewByDate(date: String) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getReviewByDate(date)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _currentReview.value = apiResponse.data
                    } else {
                        _currentReview.value = null
                        _error.value = apiResponse?.message
                    }
                } else if (response.code() == 404) {
                    _currentReview.value = null
                    _error.value = "指定日のレビューが見つかりません"
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
                _currentReview.value = null
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * デイリーレビューを作成
     */
    fun createDailyReview(request: CreateDailyReviewRequest) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.createDailyReview(request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _currentReview.value = apiResponse.data
                        _successMessage.value = "デイリーレビューを完了しました！"
                    } else {
                        _error.value = apiResponse?.message ?: "レビューの作成に失敗しました"
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
     * デイリーレビューを更新
     */
    fun updateDailyReview(id: Int, request: UpdateDailyReviewRequest) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.updateDailyReview(id, request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _currentReview.value = apiResponse.data
                        _successMessage.value = "レビューを更新しました"
                    } else {
                        _error.value = apiResponse?.message ?: "レビューの更新に失敗しました"
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
     * デイリーレビューを削除
     */
    fun deleteDailyReview(id: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.deleteDailyReview(id)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _currentReview.value = null
                        _successMessage.value = "レビューを削除しました"
                    } else {
                        _error.value = apiResponse?.message ?: "レビューの削除に失敗しました"
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
     * 統計データを取得
     */
    fun fetchStats(period: String = "month") {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getDailyReviewStats(period)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _stats.value = apiResponse.data
                    } else {
                        _error.value = apiResponse?.message ?: "統計の取得に失敗しました"
                    }
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
                _stats.value = null
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * トレンドデータを取得
     */
    fun fetchTrends(period: String, metric: String = "productivity") {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getDailyReviewTrends(period, metric)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _trends.value = apiResponse.data ?: emptyList()
                    } else {
                        _error.value = apiResponse?.message ?: "トレンドの取得に失敗しました"
                    }
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
                _trends.value = emptyList()
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * インサイトを取得
     */
    fun fetchInsights(period: String = "month") {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getDailyReviewInsights(period)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        _insights.value = apiResponse.data
                    } else {
                        _error.value = apiResponse?.message ?: "インサイトの取得に失敗しました"
                    }
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
                _insights.value = null
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * エラーメッセージをクリア
     */
    fun clearError() {
        _error.value = null
    }

    /**
     * 成功メッセージをクリア
     */
    fun clearSuccessMessage() {
        _successMessage.value = null
    }

    /**
     * レビュー履歴を取得
     */
    fun fetchReviewHistory(
        startDate: String? = null,
        endDate: String? = null,
        mood: String? = null
    ) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getDailyReviews(
                    startDate = startDate,
                    endDate = endDate,
                    mood = mood,
                    perPage = 50
                )

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        // Parse paginated data
                        val data = apiResponse.data
                        if (data is Map<*, *>) {
                            @Suppress("UNCHECKED_CAST")
                            val dataList = (data["data"] as? List<*>)?.mapNotNull { item ->
                                // Convert to DailyReview - this is a simplification
                                // In production you'd want proper deserialization
                                null // TODO: Implement proper deserialization
                            } ?: emptyList()
                            _reviews.value = dataList
                        }
                    } else {
                        _error.value = apiResponse?.message ?: "レビュー履歴の取得に失敗しました"
                    }
                } else {
                    _error.value = "ネットワークエラー: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
                _reviews.value = emptyList()
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Mood を日本語表示に変換
     */
    fun getMoodDisplay(mood: String): String {
        return when (mood) {
            "excellent" -> "最高"
            "good" -> "良い"
            "average" -> "普通"
            "poor" -> "悪い"
            "terrible" -> "最悪"
            else -> "不明"
        }
    }

    /**
     * スコアの色を取得
     */
    fun getScoreColor(score: Float): Int {
        return when {
            score >= 8 -> android.graphics.Color.parseColor("#4CAF50") // Green
            score >= 6 -> android.graphics.Color.parseColor("#FFC107") // Yellow
            score >= 4 -> android.graphics.Color.parseColor("#FF9800") // Orange
            else -> android.graphics.Color.parseColor("#F44336") // Red
        }
    }
}
