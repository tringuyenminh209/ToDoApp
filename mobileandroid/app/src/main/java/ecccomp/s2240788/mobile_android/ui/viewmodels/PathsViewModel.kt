package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.LearningPath
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * PathsViewModel
 * Learning Paths 画面のビジネスロジック管理
 * - Paths の取得とフィルタリング
 * - Progress 統計の計算
 */
class PathsViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _allPaths = MutableLiveData<List<LearningPath>>()
    val allPaths: LiveData<List<LearningPath>> = _allPaths

    private val _filteredPaths = MutableLiveData<List<LearningPath>>()
    val filteredPaths: LiveData<List<LearningPath>> = _filteredPaths

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _activePathsCount = MutableLiveData<Int>()
    val activePathsCount: LiveData<Int> = _activePathsCount

    private val _completedPathsCount = MutableLiveData<Int>()
    val completedPathsCount: LiveData<Int> = _completedPathsCount

    private val _overallProgress = MutableLiveData<Int>()
    val overallProgress: LiveData<Int> = _overallProgress

    private var currentFilter: FilterType = FilterType.ALL

    enum class FilterType {
        ALL, ACTIVE, COMPLETED
    }

    init {
        fetchPaths()
    }

    /**
     * Learning Paths を取得
     */
    fun fetchPaths() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getLearningPaths()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val paths = parsePathsFromResponse(apiResponse.data)
                        _allPaths.value = paths
                        updateStatistics(paths)
                        applyFilter()
                    } else {
                        _error.value = apiResponse?.message ?: "パスの取得に失敗しました"
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
     * フィルターを設定
     */
    fun setFilter(filterType: FilterType) {
        currentFilter = filterType
        applyFilter()
    }

    /**
     * フィルターを適用
     */
    private fun applyFilter() {
        val paths = _allPaths.value ?: emptyList()

        val filtered = when (currentFilter) {
            FilterType.ALL -> paths
            FilterType.ACTIVE -> paths.filter { it.status == "active" || it.status == "in_progress" }
            FilterType.COMPLETED -> paths.filter { it.status == "completed" }
        }

        _filteredPaths.value = filtered
    }

    /**
     * 統計情報を更新
     */
    private fun updateStatistics(paths: List<LearningPath>) {
        val activeCount = paths.count { it.status == "active" || it.status == "in_progress" }
        val completedCount = paths.count { it.status == "completed" }
        
        _activePathsCount.value = activeCount
        _completedPathsCount.value = completedCount

        // 全体進捗率を計算
        if (paths.isNotEmpty()) {
            val totalProgress = paths.sumOf { it.progress_percentage }
            val averageProgress = totalProgress / paths.size
            _overallProgress.value = averageProgress
        } else {
            _overallProgress.value = 0
        }
    }

    /**
     * Learning Path を削除
     */
    fun deletePath(pathId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.deleteLearningPath(pathId)
                
                if (response.isSuccessful && response.body()?.success == true) {
                    fetchPaths() // Refresh list
                } else {
                    _error.value = "削除に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            }
        }
    }

    /**
     * Learning Path を完了
     */
    fun completePath(pathId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.completeLearningPath(pathId)
                
                if (response.isSuccessful && response.body()?.success == true) {
                    fetchPaths() // Refresh list
                } else {
                    _error.value = "完了処理に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            }
        }
    }

    /**
     * API レスポンスからパースする
     */
    private fun parsePathsFromResponse(data: Any?): List<LearningPath> {
        return try {
            when (data) {
                is List<*> -> data.mapNotNull { item ->
                    when (item) {
                        is Map<*, *> -> convertMapToPath(item)
                        is LearningPath -> item
                        else -> null
                    }
                }
                else -> emptyList()
            }
        } catch (e: Exception) {
            emptyList()
        }
    }

    /**
     * Map を LearningPath に変換
     */
    private fun convertMapToPath(map: Map<*, *>): LearningPath? {
        return try {
            LearningPath(
                id = (map["id"] as? Number)?.toInt() ?: 0,
                user_id = (map["user_id"] as? Number)?.toInt() ?: 0,
                title = map["title"] as? String ?: "",
                description = map["description"] as? String,
                category = map["category"] as? String,
                status = map["status"] as? String ?: "active",
                progress_percentage = (map["progress_percentage"] as? Number)?.toInt() ?: 0,
                total_milestones = (map["total_milestones"] as? Number)?.toInt() ?: 0,
                completed_milestones = (map["completed_milestones"] as? Number)?.toInt() ?: 0,
                target_date = map["target_date"] as? String,
                created_at = map["created_at"] as? String ?: "",
                updated_at = map["updated_at"] as? String ?: ""
            )
        } catch (e: Exception) {
            null
        }
    }

    /**
     * Paths を再読み込み
     */
    fun refreshPaths() {
        fetchPaths()
    }
}

