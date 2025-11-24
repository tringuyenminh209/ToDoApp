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

    // Additional Phase 1 LiveData
    private val _categories = MutableLiveData<List<KnowledgeCategory>>()
    val categories: LiveData<List<KnowledgeCategory>> = _categories

    private val _categoryTree = MutableLiveData<List<KnowledgeCategory>>()
    val categoryTree: LiveData<List<KnowledgeCategory>> = _categoryTree

    private val _knowledgeStats = MutableLiveData<KnowledgeStats?>()
    val knowledgeStats: LiveData<KnowledgeStats?> = _knowledgeStats

    private val _categoryStats = MutableLiveData<KnowledgeCategoryStats?>()
    val categoryStats: LiveData<KnowledgeCategoryStats?> = _categoryStats

    private val _dueReviewItems = MutableLiveData<List<KnowledgeItem>>()
    val dueReviewItems: LiveData<List<KnowledgeItem>> = _dueReviewItems

    private val _relatedItems = MutableLiveData<List<KnowledgeItem>>()
    val relatedItems: LiveData<List<KnowledgeItem>> = _relatedItems

    private val _quickCaptureResponse = MutableLiveData<QuickCaptureResponse?>()
    val quickCaptureResponse: LiveData<QuickCaptureResponse?> = _quickCaptureResponse

    private val _suggestCategoryResponse = MutableLiveData<SuggestCategoryResponse?>()
    val suggestCategoryResponse: LiveData<SuggestCategoryResponse?> = _suggestCategoryResponse

    private val _suggestTagsResponse = MutableLiveData<SuggestTagsResponse?>()
    val suggestTagsResponse: LiveData<SuggestTagsResponse?> = _suggestTagsResponse

    private val _successMessage = MutableLiveData<String?>()
    val successMessage: LiveData<String?> = _successMessage

    private var currentFilter: FilterType = FilterType.ALL
    private var currentQuery: String = ""
    private var currentLearningPathId: Int? = null  // null = all paths
    private var currentCategoryId: Int? = null  // null = all categories

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

                android.util.Log.d("KnowledgeViewModel", "Starting to load knowledge items...")

                // Use the new API with multiple parameters
                val response = apiService.getKnowledgeItems(
                    categoryId = null,
                    itemType = null,
                    isFavorite = null,
                    isArchived = null,
                    search = null,
                    tags = null
                )

                android.util.Log.d("KnowledgeViewModel", "Response code: ${response.code()}")
                android.util.Log.d("KnowledgeViewModel", "Response successful: ${response.isSuccessful}")

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    android.util.Log.d("KnowledgeViewModel", "API Response: success=${apiResponse?.success}, data type=${apiResponse?.data?.javaClass?.simpleName}")

                    if (apiResponse?.success == true) {
                        val items = parseItemsFromResponse(apiResponse.data)
                        android.util.Log.d("KnowledgeViewModel", "Parsed ${items.size} items")
                        items.forEachIndexed { index, item ->
                            android.util.Log.d("KnowledgeViewModel", "Item $index: id=${item.id}, title=${item.title}, type=${item.item_type}")
                        }
                        _knowledgeItems.postValue(items)
                        applyFilterAndSearch()
                    } else {
                        android.util.Log.e("KnowledgeViewModel", "API returned success=false, message=${apiResponse?.message}")
                        _error.value = "Failed to load knowledge items: ${apiResponse?.message}"
                        _knowledgeItems.postValue(emptyList())
                    }
                } else {
                    val errorBody = response.errorBody()?.string()
                    android.util.Log.e("KnowledgeViewModel", "HTTP error: ${response.code()}, message: ${response.message()}, body: $errorBody")
                    _error.value = "Failed to load knowledge items: ${response.message()}"
                    _knowledgeItems.postValue(emptyList())
                }
            } catch (e: Exception) {
                android.util.Log.e("KnowledgeViewModel", "Exception loading knowledge items", e)
                _error.value = "ネットワークエラー: ${e.message}"
                _knowledgeItems.postValue(emptyList())
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Parse Items from API Response
     */
    private fun parseItemsFromResponse(data: Any?): List<KnowledgeItem> {
        return try {
            android.util.Log.d("KnowledgeViewModel", "parseItemsFromResponse - data class: ${data?.javaClass?.name}")
            android.util.Log.d("KnowledgeViewModel", "parseItemsFromResponse - data: $data")

            when (data) {
                is List<*> -> {
                    android.util.Log.d("KnowledgeViewModel", "Data is List with ${(data as List<*>).size} items")
                    val items = data.mapNotNull { item ->
                        android.util.Log.d("KnowledgeViewModel", "List item class: ${item?.javaClass?.name}")
                        android.util.Log.d("KnowledgeViewModel", "List item: $item")

                        // Check if it's already a KnowledgeItem
                        when (item) {
                            is KnowledgeItem -> item
                            is Map<*, *> -> {
                                android.util.Log.d("KnowledgeViewModel", "Item is Map, attempting to parse as KnowledgeItem")
                                // Try to parse map as KnowledgeItem using Gson
                                try {
                                    val gson = com.google.gson.Gson()
                                    val json = gson.toJson(item)
                                    gson.fromJson(json, KnowledgeItem::class.java)
                                } catch (e: Exception) {
                                    android.util.Log.e("KnowledgeViewModel", "Failed to parse map item", e)
                                    null
                                }
                            }
                            else -> {
                                android.util.Log.w("KnowledgeViewModel", "Unknown item type, skipping")
                                null
                            }
                        }
                    }
                    android.util.Log.d("KnowledgeViewModel", "Parsed ${items.size} items from list")
                    items
                }
                is Map<*, *> -> {
                    android.util.Log.d("KnowledgeViewModel", "Data is Map with keys: ${data.keys}")
                    // Handle paginated response
                    val dataList = data["data"] as? List<*>
                    dataList?.mapNotNull { it as? KnowledgeItem } ?: emptyList()
                }
                else -> {
                    android.util.Log.w("KnowledgeViewModel", "Unknown data type, returning empty list")
                    emptyList()
                }
            }
        } catch (e: Exception) {
            android.util.Log.e("KnowledgeViewModel", "Exception in parseItemsFromResponse", e)
            emptyList()
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
        android.util.Log.d("KnowledgeViewModel", "applyFilterAndSearch: all items count=${allItems.size}")

        // Filter by learning path first
        var filtered = if (currentLearningPathId != null) {
            allItems.filter { it.learning_path_id == currentLearningPathId }
        } else {
            allItems
        }
        android.util.Log.d("KnowledgeViewModel", "After learning path filter: ${filtered.size} items")

        // Filter by category
        if (currentCategoryId != null) {
            filtered = filtered.filter { it.category_id == currentCategoryId }
            android.util.Log.d("KnowledgeViewModel", "After category filter (${currentCategoryId}): ${filtered.size} items")
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
        android.util.Log.d("KnowledgeViewModel", "After type filter (${currentFilter}): ${filtered.size} items")

        // Apply search
        if (currentQuery.isNotEmpty()) {
            filtered = filtered.filter { item ->
                item.title.contains(currentQuery, ignoreCase = true) ||
                item.content?.contains(currentQuery, ignoreCase = true) == true ||
                item.tags?.any { it.contains(currentQuery, ignoreCase = true) } == true
            }
            android.util.Log.d("KnowledgeViewModel", "After search filter ('$currentQuery'): ${filtered.size} items")
        }

        android.util.Log.d("KnowledgeViewModel", "Final filtered items: ${filtered.size}")
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

                // Load all knowledge items
                val response = apiService.getKnowledgeItems(
                    categoryId = null,
                    itemType = null,
                    isFavorite = null,
                    isArchived = null,
                    search = null,
                    tags = null
                )

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val allItems = parseItemsFromResponse(apiResponse.data)
                        val taskItems = allItems.filter { it.source_task_id == taskId }
                        _knowledgeItems.postValue(taskItems)
                        applyFilterAndSearch()
                    } else {
                        _error.value = "Failed to load knowledge items: ${apiResponse?.message}"
                        _knowledgeItems.postValue(emptyList())
                    }
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
     * カテゴリでフィルタリング
     */
    fun filterByCategory(categoryId: Int) {
        currentCategoryId = categoryId
        applyFilterAndSearch()
    }

    /**
     * カテゴリフィルターをクリア
     */
    fun clearCategoryFilter() {
        currentCategoryId = null
        applyFilterAndSearch()
    }

    /**
     * 現在選択中のカテゴリIDを取得
     */
    fun getCurrentCategoryId(): Int? {
        return currentCategoryId
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
                val response = apiService.getKnowledgeItems(
                    categoryId = null,
                    itemType = null,
                    isFavorite = null,
                    isArchived = null,
                    search = null,
                    tags = null
                )

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val allItems = parseItemsFromResponse(apiResponse.data)
                        // For now, just show all items
                        _knowledgeItems.postValue(allItems)
                        applyFilterAndSearch()
                    } else {
                        _error.value = "Failed to load knowledge items: ${apiResponse?.message}"
                        _knowledgeItems.postValue(emptyList())
                    }
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

                // Load all knowledge items
                val response = apiService.getKnowledgeItems(
                    categoryId = null,
                    itemType = null,
                    isFavorite = null,
                    isArchived = null,
                    search = null,
                    tags = null
                )

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val allItems = parseItemsFromResponse(apiResponse.data)
                        val filteredItems = allItems.filter { item ->
                            item.source_task_id != null && taskIds.contains(item.source_task_id)
                        }
                        _knowledgeItems.postValue(filteredItems)
                        applyFilterAndSearch()
                    } else {
                        _error.value = "Failed to load knowledge items: ${apiResponse?.message}"
                        _knowledgeItems.postValue(emptyList())
                    }
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

    // ==================== Phase 1 Knowledge Base Features ====================

    /**
     * Load categories
     */
    fun loadCategories() {
        viewModelScope.launch {
            try {
                val response = apiService.getKnowledgeCategories()
                if (response.isSuccessful && response.body()?.success == true) {
                    _categories.postValue(response.body()?.data ?: emptyList())
                }
            } catch (e: Exception) {
                // Silently fail
            }
        }
    }

    /**
     * Load category tree
     */
    fun loadCategoryTree() {
        viewModelScope.launch {
            try {
                val response = apiService.getKnowledgeCategoryTree()
                if (response.isSuccessful && response.body()?.success == true) {
                    _categoryTree.postValue(response.body()?.data ?: emptyList())
                }
            } catch (e: Exception) {
                _error.value = "カテゴリーツリーの読み込みに失敗しました"
            }
        }
    }

    /**
     * Load knowledge stats
     */
    fun loadKnowledgeStats() {
        viewModelScope.launch {
            try {
                val response = apiService.getKnowledgeStats()
                if (response.isSuccessful && response.body()?.success == true) {
                    _knowledgeStats.postValue(response.body()?.data)
                }
            } catch (e: Exception) {
                // Silently fail
            }
        }
    }

    /**
     * Load category stats
     */
    fun loadCategoryStats() {
        viewModelScope.launch {
            try {
                val response = apiService.getKnowledgeCategoryStats()
                if (response.isSuccessful && response.body()?.success == true) {
                    _categoryStats.postValue(response.body()?.data)
                }
            } catch (e: Exception) {
                // Silently fail
            }
        }
    }

    /**
     * Load due review items
     */
    fun loadDueReviewItems() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val response = apiService.getKnowledgeDueReview()
                if (response.isSuccessful && response.body()?.success == true) {
                    _dueReviewItems.postValue(response.body()?.data ?: emptyList())
                } else {
                    _error.value = "復習項目の読み込みに失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Load related items
     */
    fun loadRelatedItems(itemId: Int, limit: Int = 5) {
        viewModelScope.launch {
            try {
                val response = apiService.getRelatedKnowledgeItems(itemId, limit)
                if (response.isSuccessful && response.body()?.success == true) {
                    _relatedItems.postValue(response.body()?.data ?: emptyList())
                }
            } catch (e: Exception) {
                // Silently fail
            }
        }
    }

    /**
     * Quick Capture knowledge
     */
    fun quickCapture(content: String, itemType: String, categoryId: Int? = null) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val request = QuickCaptureRequest(content, itemType, categoryId)
                val response = apiService.quickCaptureKnowledge(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _quickCaptureResponse.postValue(response.body()?.data)
                    _successMessage.postValue("保存しました")
                    loadKnowledgeItems()
                } else {
                    _error.value = "保存に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Suggest category based on content
     */
    fun suggestCategory(title: String?, content: String, itemType: String) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val request = SuggestCategoryRequest(title, content, itemType)
                val response = apiService.suggestCategory(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _suggestCategoryResponse.postValue(response.body()?.data)
                } else {
                    _error.value = "カテゴリの提案に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Suggest tags based on content
     */
    fun suggestTags(content: String, itemType: String = "note") {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val request = SuggestTagsRequest(content, itemType)
                val response = apiService.suggestTags(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _suggestTagsResponse.postValue(response.body()?.data)
                } else {
                    _error.value = "タグの提案に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Bulk tag items
     */
    fun bulkTagItems(itemIds: List<Int>, tags: List<String>) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val request = BulkTagRequest(itemIds, tags)
                val response = apiService.bulkTagKnowledgeItems(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _successMessage.postValue("${response.body()?.data?.affected_count}件にタグを追加しました")
                    loadKnowledgeItems()
                } else {
                    _error.value = "タグ追加に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Bulk move items
     */
    fun bulkMoveItems(itemIds: List<Int>, categoryId: Int?) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val request = BulkMoveRequest(itemIds, categoryId)
                val response = apiService.bulkMoveKnowledgeItems(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _successMessage.postValue("${response.body()?.data?.affected_count}件を移動しました")
                    loadKnowledgeItems()
                } else {
                    _error.value = "移動に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Bulk delete items
     */
    fun bulkDeleteItems(itemIds: List<Int>) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val request = BulkDeleteRequest(itemIds)
                val response = apiService.bulkDeleteKnowledgeItems(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _successMessage.postValue("${response.body()?.data?.affected_count}件を削除しました")
                    loadKnowledgeItems()
                } else {
                    _error.value = "削除に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Clone knowledge item
     */
    fun cloneKnowledgeItem(itemId: Int, title: String? = null, categoryId: Int? = null, onSuccess: () -> Unit = {}) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val request = CloneKnowledgeRequest(title, categoryId)
                val response = apiService.cloneKnowledgeItem(itemId, request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _successMessage.postValue("複製しました")
                    loadKnowledgeItems()
                    onSuccess()
                } else {
                    _error.value = "複製に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Create category
     */
    fun createCategory(request: CreateKnowledgeCategoryRequest) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val response = apiService.createKnowledgeCategory(request)

                if (response.isSuccessful && response.body()?.success == true) {
                    _successMessage.postValue("カテゴリーを作成しました")
                    loadCategories()
                    loadCategoryTree()
                } else {
                    _error.value = "作成に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Delete category
     */
    fun deleteCategory(categoryId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val response = apiService.deleteKnowledgeCategory(categoryId)

                if (response.isSuccessful && response.body()?.success == true) {
                    _successMessage.postValue("カテゴリーを削除しました")
                    loadCategories()
                    loadCategoryTree()
                } else {
                    _error.value = "削除に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Clear success message
     */
    fun clearSuccessMessage() {
        _successMessage.value = null
    }

    /**
     * Clear quick capture response
     */
    fun clearQuickCaptureResponse() {
        _quickCaptureResponse.value = null
    }
}
