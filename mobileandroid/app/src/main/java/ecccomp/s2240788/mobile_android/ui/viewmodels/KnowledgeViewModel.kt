package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateKnowledgeItemRequest
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * KnowledgeViewModel
 * 知識アイテムの管理とCRUD操作
 */
class KnowledgeViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _knowledgeItems = MutableLiveData<List<KnowledgeItem>>()
    val knowledgeItems: LiveData<List<KnowledgeItem>> = _knowledgeItems

    private val _filteredItems = MutableLiveData<List<KnowledgeItem>>()
    val filteredItems: LiveData<List<KnowledgeItem>> = _filteredItems

    private val _learningPaths = MutableLiveData<List<ecccomp.s2240788.mobile_android.data.models.LearningPath>>()
    val learningPaths: LiveData<List<ecccomp.s2240788.mobile_android.data.models.LearningPath>> = _learningPaths

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private var currentFilter: FilterType = FilterType.ALL
    private var currentQuery: String = ""
    private var currentLearningPathId: Int? = null  // null = all paths

    enum class FilterType {
        ALL, NOTES, CODE, EXERCISES, LINKS, ATTACHMENTS, FAVORITES, ARCHIVED, DUE_REVIEW
    }

    /**
     * 知識アイテムを取得
     */
    fun loadKnowledgeItems(filter: String? = null) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.getKnowledgeItems(filter)
                
                if (response.isSuccessful) {
                    val items = response.body()?.data ?: emptyList()
                    _knowledgeItems.postValue(items)
                    applyFilterAndSearch()
                } else {
                    _error.value = "Failed to load knowledge items: ${response.message()}"
                    _knowledgeItems.postValue(emptyList())
                }
            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
                _knowledgeItems.postValue(emptyList())
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * 知識アイテムを作成
     */
    fun createKnowledgeItem(request: CreateKnowledgeItemRequest, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.createKnowledgeItem(request)
                
                if (response.isSuccessful) {
                    loadKnowledgeItems() // Reload list
                    onSuccess()
                } else {
                    _error.value = "Failed to create item: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error creating item: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * 知識アイテムを更新
     */
    fun updateKnowledgeItem(id: Int, request: CreateKnowledgeItemRequest, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.updateKnowledgeItem(id, request)
                
                if (response.isSuccessful) {
                    loadKnowledgeItems() // Reload list
                    onSuccess()
                } else {
                    _error.value = "Failed to update item: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "Error updating item: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * フィルターを設定
     */
    fun setFilter(filter: FilterType) {
        currentFilter = filter
        applyFilterAndSearch()
    }

    /**
     * 検索クエリを設定
     */
    fun setQuery(query: String) {
        currentQuery = query
        applyFilterAndSearch()
    }

    /**
     * フィルターと検索を適用
     */
    private fun applyFilterAndSearch() {
        val allItems = _knowledgeItems.value ?: emptyList()

        // Filter by learning path first
        var filtered = if (currentLearningPathId != null) {
            allItems.filter { it.learning_path_id == currentLearningPathId }
        } else {
            allItems
        }

        // Apply type filter
        filtered = when (currentFilter) {
            FilterType.ALL -> filtered
            FilterType.NOTES -> filtered.filter { it.item_type == "note" }
            FilterType.CODE -> filtered.filter { it.item_type == "code_snippet" }
            FilterType.EXERCISES -> filtered.filter { it.item_type == "exercise" }
            FilterType.LINKS -> filtered.filter { it.item_type == "resource_link" }
            FilterType.ATTACHMENTS -> filtered.filter { it.item_type == "attachment" }
            FilterType.FAVORITES -> filtered.filter { it.is_favorite }
            FilterType.ARCHIVED -> filtered.filter { it.is_archived }
            FilterType.DUE_REVIEW -> filtered.filter {
                it.next_review_date != null && it.next_review_date <= getCurrentDate()
            }
        }

        // Apply search
        if (currentQuery.isNotEmpty()) {
            filtered = filtered.filter { item ->
                item.title.contains(currentQuery, ignoreCase = true) ||
                item.content?.contains(currentQuery, ignoreCase = true) == true ||
                item.tags?.any { it.contains(currentQuery, ignoreCase = true) } == true
            }
        }

        _filteredItems.postValue(filtered)
    }

    /**
     * アイテムをお気に入りに追加/削除
     */
    fun toggleFavorite(itemId: Int, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _error.value = null
                
                val response = apiService.toggleKnowledgeFavorite(itemId)
                
                if (response.isSuccessful) {
                    loadKnowledgeItems()
                    onSuccess()
                } else {
                    _error.value = "お気に入りの更新に失敗しました: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "お気に入りの更新に失敗しました: ${e.message}"
            }
        }
    }

    /**
     * アイテムをアーカイブ/復元
     */
    fun toggleArchive(itemId: Int, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _error.value = null
                
                val response = apiService.toggleKnowledgeArchive(itemId)
                
                if (response.isSuccessful) {
                    loadKnowledgeItems()
                    onSuccess()
                } else {
                    _error.value = "アーカイブの更新に失敗しました: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "アーカイブの更新に失敗しました: ${e.message}"
            }
        }
    }

    /**
     * アイテムを削除
     */
    fun deleteItem(itemId: Int, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null
                
                val response = apiService.deleteKnowledgeItem(itemId)
                
                if (response.isSuccessful) {
                    loadKnowledgeItems()
                    onSuccess()
                } else {
                    _error.value = "削除に失敗しました: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "削除に失敗しました: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * レビュー済みとしてマーク
     */
    fun markAsReviewed(itemId: Int, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _error.value = null
                
                val response = apiService.markKnowledgeReviewed(itemId)
                
                if (response.isSuccessful) {
                    loadKnowledgeItems()
                    onSuccess()
                } else {
                    _error.value = "レビューの更新に失敗しました: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "レビューの更新に失敗しました: ${e.message}"
            }
        }
    }

    /**
     * Get current date in yyyy-MM-dd format
     */
    private fun getCurrentDate(): String {
        val sdf = java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.getDefault())
        return sdf.format(java.util.Date())
    }

    fun clearError() {
        _error.value = null
    }
    
    /**
     * Refresh knowledge items
     */
    fun refreshKnowledgeItems() {
        loadKnowledgeItems()
    }

    /**
     * Load knowledge items for a specific task
     */
    fun loadKnowledgeItemsByTask(taskId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                // Load all knowledge items and filter by source_task_id
                val response = apiService.getKnowledgeItems(null)

                if (response.isSuccessful) {
                    val allItems = response.body()?.data ?: emptyList()
                    val taskItems = allItems.filter { it.source_task_id == taskId }
                    _knowledgeItems.postValue(taskItems)
                    applyFilterAndSearch()
                } else {
                    _error.value = "Failed to load knowledge items: ${response.message()}"
                    _knowledgeItems.postValue(emptyList())
                }
            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
                _knowledgeItems.postValue(emptyList())
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Load user's learning paths
     */
    fun loadLearningPaths() {
        viewModelScope.launch {
            try {
                val response = apiService.getLearningPaths()

                if (response.isSuccessful) {
                    val paths = response.body()?.data ?: emptyList()
                    _learningPaths.postValue(paths)
                } else {
                    _learningPaths.postValue(emptyList())
                }
            } catch (e: Exception) {
                _learningPaths.postValue(emptyList())
            }
        }
    }

    /**
     * Filter knowledge items by learning path
     */
    fun filterByLearningPath(learningPathId: Int?) {
        currentLearningPathId = learningPathId
        applyFilterAndSearch()
    }

    /**
     * Load knowledge items for a specific milestone
     * This loads items by getting all knowledge items whose source_task_id belongs to the milestone's tasks
     */
    fun loadKnowledgeItemsByMilestone(milestoneId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                // Load all knowledge items
                val response = apiService.getKnowledgeItems(null)

                if (response.isSuccessful) {
                    val allItems = response.body()?.data ?: emptyList()
                    // For now, filter by learning_path_id since knowledge items belong to learning paths
                    // In a real scenario, we would need to get milestone's task IDs first
                    // and filter by source_task_id
                    // Temporary: just show all items for the learning path
                    _knowledgeItems.postValue(allItems)
                    applyFilterAndSearch()
                } else {
                    _error.value = "Failed to load knowledge items: ${response.message()}"
                    _knowledgeItems.postValue(emptyList())
                }
            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
                _knowledgeItems.postValue(emptyList())
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Load knowledge items by task IDs
     */
    fun loadKnowledgeItemsByTaskIds(taskIds: List<Int>) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                // Load all knowledge items and filter by task IDs
                val response = apiService.getKnowledgeItems(null)

                if (response.isSuccessful) {
                    val allItems = response.body()?.data ?: emptyList()
                    val filteredItems = allItems.filter { item ->
                        item.source_task_id != null && taskIds.contains(item.source_task_id)
                    }
                    _knowledgeItems.postValue(filteredItems)
                    applyFilterAndSearch()
                } else {
                    _error.value = "Failed to load knowledge items: ${response.message()}"
                    _knowledgeItems.postValue(emptyList())
                }
            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
                _knowledgeItems.postValue(emptyList())
            } finally {
                _isLoading.value = false
            }
        }
    }
}
