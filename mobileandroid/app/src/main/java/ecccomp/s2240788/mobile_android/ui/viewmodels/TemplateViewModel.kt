package ecccomp.s2240788.mobile_android.ui.viewmodels

import android.util.Log
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.google.gson.Gson
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * ViewModel for Template Library
 * テンプレートライブラリ用のViewModel
 */
class TemplateViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    // Templates List
    private val _templates = MutableLiveData<List<LearningPathTemplate>>()
    val templates: LiveData<List<LearningPathTemplate>> = _templates

    // Featured Templates
    private val _featuredTemplates = MutableLiveData<List<LearningPathTemplate>>()
    val featuredTemplates: LiveData<List<LearningPathTemplate>> = _featuredTemplates

    // Popular Templates
    private val _popularTemplates = MutableLiveData<List<LearningPathTemplate>>()
    val popularTemplates: LiveData<List<LearningPathTemplate>> = _popularTemplates

    // Template Detail
    private val _templateDetail = MutableLiveData<LearningPathTemplate>()
    val templateDetail: LiveData<LearningPathTemplate> = _templateDetail

    // Categories
    private val _categories = MutableLiveData<List<TemplateCategoryCount>>()
    val categories: LiveData<List<TemplateCategoryCount>> = _categories

    // Loading State
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    // Error Message
    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    // Success Message
    private val _message = MutableLiveData<String?>()
    val message: LiveData<String?> = _message

    // Cloned Learning Path ID
    private val _clonedLearningPathId = MutableLiveData<Long?>()
    val clonedLearningPathId: LiveData<Long?> = _clonedLearningPathId

    // Current Filter
    private val _currentFilter = MutableLiveData(TemplateFilter())
    val currentFilter: LiveData<TemplateFilter> = _currentFilter

    /**
     * Get all templates with optional filters
     */
    fun getTemplates(filter: TemplateFilter? = null) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val queryMap = filter?.toQueryMap()
                val response = apiService.getTemplates(queryMap)

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true) {
                        _templates.value = body.data
                        Log.d(TAG, "Loaded ${body.data.size} templates")
                    } else {
                        _error.value = "テンプレートの取得に失敗しました"
                    }
                } else {
                    _error.value = "エラー: ${response.code()}"
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error loading templates", e)
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Get featured templates
     */
    fun getFeaturedTemplates() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getFeaturedTemplates()

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true) {
                        _featuredTemplates.value = body.data
                        Log.d(TAG, "Loaded ${body.data.size} featured templates")
                    } else {
                        _error.value = "おすすめテンプレートの取得に失敗しました"
                    }
                } else {
                    _error.value = "エラー: ${response.code()}"
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error loading featured templates", e)
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Get popular templates
     */
    fun getPopularTemplates() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getPopularTemplates()

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true) {
                        _popularTemplates.value = body.data
                        Log.d(TAG, "Loaded ${body.data.size} popular templates")
                    } else {
                        _error.value = "人気テンプレートの取得に失敗しました"
                    }
                } else {
                    _error.value = "エラー: ${response.code()}"
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error loading popular templates", e)
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Get template detail
     */
    fun getTemplateDetail(id: Long) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getTemplateDetail(id)

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true) {
                        _templateDetail.value = body.data
                        Log.d(TAG, "Loaded template detail: ${body.data.title}")
                    } else {
                        _error.value = "テンプレート詳細の取得に失敗しました"
                    }
                } else {
                    _error.value = "エラー: ${response.code()}"
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error loading template detail", e)
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Get templates by category
     */
    fun getTemplatesByCategory(category: TemplateCategory) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getTemplatesByCategory(category.value)

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true) {
                        _templates.value = body.data
                        Log.d(TAG, "Loaded ${body.data.size} templates for category: ${category.displayName}")
                    } else {
                        _error.value = "カテゴリー別テンプレートの取得に失敗しました"
                    }
                } else {
                    _error.value = "エラー: ${response.code()}"
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error loading templates by category", e)
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Get categories
     */
    fun getCategories() {
        viewModelScope.launch {
            try {
                _error.value = null

                val response = apiService.getTemplateCategories()

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true) {
                        _categories.value = body.data
                        Log.d(TAG, "Loaded ${body.data.size} categories")
                    } else {
                        _error.value = "カテゴリーの取得に失敗しました"
                    }
                } else {
                    _error.value = "エラー: ${response.code()}"
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error loading categories", e)
                _error.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    /**
     * Clone template to user's learning path with study schedules
     */
    fun cloneTemplate(templateId: Long, studySchedules: List<StudyScheduleInput>) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val request = CloneTemplateRequest(studySchedules = studySchedules)
                val response = apiService.cloneTemplate(templateId, request)

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true) {
                        _clonedLearningPathId.value = body.data.learningPathId
                        _message.value = body.message
                        Log.d(TAG, "Template cloned successfully: ${body.data.learningPathId}")
                    } else {
                        _error.value = body?.message ?: "テンプレートのクローンに失敗しました"
                    }
                } else {
                    // Try to parse error message from response body
                    val errorMessage = try {
                        val errorBody = response.errorBody()?.string()
                        if (errorBody != null) {
                            // Try to parse as JSON to get message
                            val gson = Gson()
                            val errorJson = gson.fromJson(errorBody, Map::class.java)
                            (errorJson["message"] as? String) ?: "テンプレートのクローンに失敗しました"
                        } else {
                            "エラー: ${response.code()}"
                        }
                    } catch (e: Exception) {
                        Log.e(TAG, "Error parsing error body", e)
                        "エラー: ${response.code()}"
                    }
                    _error.value = errorMessage
                    Log.e(TAG, "Clone template failed: ${response.code()} - $errorMessage")
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error cloning template", e)
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Apply filter
     */
    fun applyFilter(filter: TemplateFilter) {
        _currentFilter.value = filter
        getTemplates(filter)
    }

    /**
     * Clear filter
     */
    fun clearFilter() {
        _currentFilter.value = TemplateFilter()
        getTemplates()
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
    fun clearMessage() {
        _message.value = null
    }

    /**
     * Clear cloned learning path ID
     */
    fun clearClonedLearningPathId() {
        _clonedLearningPathId.value = null
    }

    companion object {
        private const val TAG = "TemplateViewModel"
    }
}

