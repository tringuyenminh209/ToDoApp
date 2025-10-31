package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import kotlinx.coroutines.launch

/**
 * KnowledgeViewModel
 * 知識アイテムの管理とCRUD操作
 */
class KnowledgeViewModel : ViewModel() {

    private val _knowledgeItems = MutableLiveData<List<KnowledgeItem>>()
    val knowledgeItems: LiveData<List<KnowledgeItem>> = _knowledgeItems

    private val _filteredItems = MutableLiveData<List<KnowledgeItem>>()
    val filteredItems: LiveData<List<KnowledgeItem>> = _filteredItems

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private var currentFilter: FilterType = FilterType.ALL
    private var currentQuery: String = ""

    enum class FilterType {
        ALL, NOTES, CODE, EXERCISES, LINKS, ATTACHMENTS, FAVORITES, ARCHIVED, DUE_REVIEW
    }

    /**
     * 知識アイテムを取得
     */
    fun loadKnowledgeItems() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                
                // TODO: API call to fetch knowledge items
                // For now, use empty list as placeholder
                val items = emptyList<KnowledgeItem>()
                
                _knowledgeItems.postValue(items)
                applyFilterAndSearch()
                
            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
                _knowledgeItems.postValue(emptyList())
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
        
        // Apply filter
        var filtered = when (currentFilter) {
            FilterType.ALL -> allItems
            FilterType.NOTES -> allItems.filter { it.item_type == "note" }
            FilterType.CODE -> allItems.filter { it.item_type == "code_snippet" }
            FilterType.EXERCISES -> allItems.filter { it.item_type == "exercise" }
            FilterType.LINKS -> allItems.filter { it.item_type == "resource_link" }
            FilterType.ATTACHMENTS -> allItems.filter { it.item_type == "attachment" }
            FilterType.FAVORITES -> allItems.filter { it.is_favorite }
            FilterType.ARCHIVED -> allItems.filter { it.is_archived }
            FilterType.DUE_REVIEW -> allItems.filter { 
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
        _knowledgeItems.postValue(filtered)
    }

    /**
     * アイテムをお気に入りに追加/削除
     */
    fun toggleFavorite(itemId: Int) {
        viewModelScope.launch {
            try {
                // TODO: API call to toggle favorite
                loadKnowledgeItems()
            } catch (e: Exception) {
                _error.value = "お気に入りの更新に失敗しました: ${e.message}"
            }
        }
    }

    /**
     * アイテムをアーカイブ/復元
     */
    fun toggleArchive(itemId: Int) {
        viewModelScope.launch {
            try {
                // TODO: API call to toggle archive
                loadKnowledgeItems()
            } catch (e: Exception) {
                _error.value = "アーカイブの更新に失敗しました: ${e.message}"
            }
        }
    }

    /**
     * アイテムを削除
     */
    fun deleteItem(itemId: Int) {
        viewModelScope.launch {
            try {
                // TODO: API call to delete item
                loadKnowledgeItems()
            } catch (e: Exception) {
                _error.value = "削除に失敗しました: ${e.message}"
            }
        }
    }

    /**
     * レビュー済みとしてマーク
     */
    fun markAsReviewed(itemId: Int) {
        viewModelScope.launch {
            try {
                // TODO: API call to mark as reviewed
                loadKnowledgeItems()
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
}

