package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.data.repository.RoadmapRepository
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * RoadmapViewModel
 * Roadmap機能のデータ管理
 */
class RoadmapViewModel : ViewModel() {

    private val apiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )
    private val roadmapRepository = RoadmapRepository(apiService)

    // Popular roadmaps
    private val _popularRoadmaps = MutableLiveData<List<PopularRoadmap>>()
    val popularRoadmaps: LiveData<List<PopularRoadmap>> = _popularRoadmaps

    // Generated roadmap
    private val _generatedRoadmap = MutableLiveData<GeneratedRoadmap?>()
    val generatedRoadmap: LiveData<GeneratedRoadmap?> = _generatedRoadmap

    // Imported roadmap data (contains template and learning_path_id)
    private val _importedRoadmapData = MutableLiveData<ImportRoadmapData?>()
    val importedRoadmapData: LiveData<ImportRoadmapData?> = _importedRoadmapData
    
    // Learning path ID after import (for navigation)
    private val _importedLearningPathId = MutableLiveData<Long?>()
    val importedLearningPathId: LiveData<Long?> = _importedLearningPathId

    // Loading states
    private val _isLoadingPopular = MutableLiveData<Boolean>()
    val isLoadingPopular: LiveData<Boolean> = _isLoadingPopular

    private val _isGenerating = MutableLiveData<Boolean>()
    val isGenerating: LiveData<Boolean> = _isGenerating

    private val _isImporting = MutableLiveData<Boolean>()
    val isImporting: LiveData<Boolean> = _isImporting

    // Error messages
    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    // Success messages
    private val _successMessage = MutableLiveData<String?>()
    val successMessage: LiveData<String?> = _successMessage

    /**
     * 人気のロードマップを取得
     */
    fun loadPopularRoadmaps() {
        viewModelScope.launch {
            try {
                _isLoadingPopular.value = true
                _error.value = null

                val result = roadmapRepository.getPopularRoadmaps()

                result.onSuccess { roadmaps ->
                    _popularRoadmaps.value = roadmaps
                }.onFailure { exception ->
                    _error.value = exception.message ?: "ロードマップの取得に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isLoadingPopular.value = false
            }
        }
    }

    /**
     * AIでロードマップを生成
     */
    fun generateRoadmap(topic: String, level: String = "beginner") {
        if (topic.isBlank()) {
            _error.value = "トピックを入力してください"
            return
        }

        viewModelScope.launch {
            try {
                _isGenerating.value = true
                _error.value = null
                _generatedRoadmap.value = null

                val result = roadmapRepository.generateRoadmap(topic, level)

                result.onSuccess { roadmap ->
                    _generatedRoadmap.value = roadmap
                    _successMessage.value = "ロードマップを生成しました！"
                }.onFailure { exception ->
                    _error.value = exception.message ?: "ロードマップの生成に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isGenerating.value = false
            }
        }
    }

    /**
     * 人気のロードマップをインポート（自動的に学習パスにクローン）
     */
    fun importPopularRoadmap(roadmapId: String, studySchedules: List<StudyScheduleInput>) {
        viewModelScope.launch {
            try {
                _isImporting.value = true
                _error.value = null
                _importedRoadmapData.value = null
                _importedLearningPathId.value = null

                val result = roadmapRepository.importRoadmap(
                    source = "popular",
                    roadmapId = roadmapId,
                    autoClone = true,
                    studySchedules = studySchedules
                )

                result.onSuccess { data ->
                    _importedRoadmapData.value = data
                    _importedLearningPathId.value = data.learning_path_id
                    _successMessage.value = if (data.learning_path_id != null) {
                        "ロードマップを学習パスとして追加しました！"
                    } else {
                        "ロードマップをインポートしました！"
                    }
                }.onFailure { exception ->
                    _error.value = exception.message ?: "ロードマップのインポートに失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isImporting.value = false
            }
        }
    }

    /**
     * AI生成ロードマップをインポート（自動的に学習パスにクローン）
     */
    fun importAIGeneratedRoadmap(topic: String, level: String = "beginner", studySchedules: List<StudyScheduleInput>) {
        viewModelScope.launch {
            try {
                _isImporting.value = true
                _error.value = null
                _importedRoadmapData.value = null
                _importedLearningPathId.value = null

                val result = roadmapRepository.importRoadmap(
                    source = "ai",
                    topic = topic,
                    level = level,
                    autoClone = true,
                    studySchedules = studySchedules
                )

                result.onSuccess { data ->
                    _importedRoadmapData.value = data
                    _importedLearningPathId.value = data.learning_path_id
                    _successMessage.value = if (data.learning_path_id != null) {
                        "ロードマップを学習パスとして追加しました！"
                    } else {
                        "ロードマップをインポートしました！"
                    }
                }.onFailure { exception ->
                    _error.value = exception.message ?: "ロードマップのインポートに失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isImporting.value = false
            }
        }
    }

    /**
     * Clear error message
     */
    fun clearError() {
        _error.value = null
    }

    /**
     * Clear success message
     */
    fun clearSuccessMessage() {
        _successMessage.value = null
    }

    /**
     * Reset generated roadmap
     */
    fun resetGeneratedRoadmap() {
        _generatedRoadmap.value = null
    }

    /**
     * Reset imported roadmap data
     */
    fun resetImportedRoadmapData() {
        _importedRoadmapData.value = null
        _importedLearningPathId.value = null
    }
}

