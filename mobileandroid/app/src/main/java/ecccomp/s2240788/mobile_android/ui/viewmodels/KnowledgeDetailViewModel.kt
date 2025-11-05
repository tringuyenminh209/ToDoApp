package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * KnowledgeDetailViewModel
 * 知識アイテムの詳細表示とCRUD操作
 */
class KnowledgeDetailViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _knowledgeItem = MutableLiveData<KnowledgeItem?>()
    val knowledgeItem: LiveData<KnowledgeItem?> = _knowledgeItem

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _toast = MutableLiveData<String?>()
    val toast: LiveData<String?> = _toast

    private val _finishActivity = MutableLiveData<Boolean>()
    val finishActivity: LiveData<Boolean> = _finishActivity

    /**
     * 知識アイテムを取得
     */
    fun loadKnowledgeItem(itemId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getKnowledgeItem(itemId)

                if (response.isSuccessful) {
                    val item = response.body()?.data
                    _knowledgeItem.postValue(item)
                } else {
                    _error.value = "Failed to load knowledge item: ${response.message()}"
                }
            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * お気に入りを切り替え
     */
    fun toggleFavorite() {
        val itemId = _knowledgeItem.value?.id ?: return

        viewModelScope.launch {
            try {
                val response = apiService.toggleKnowledgeFavorite(itemId)

                if (response.isSuccessful) {
                    // Update local state
                    _knowledgeItem.value?.let { item ->
                        _knowledgeItem.postValue(item.copy(is_favorite = !item.is_favorite))
                    }
                    _toast.value = "お気に入りを更新しました"
                } else {
                    _error.value = "お気に入りの更新に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            }
        }
    }

    /**
     * アーカイブを切り替え
     */
    fun toggleArchive() {
        val itemId = _knowledgeItem.value?.id ?: return

        viewModelScope.launch {
            try {
                val response = apiService.toggleKnowledgeArchive(itemId)

                if (response.isSuccessful) {
                    // Update local state
                    _knowledgeItem.value?.let { item ->
                        _knowledgeItem.postValue(item.copy(is_archived = !item.is_archived))
                    }
                    val message = if (_knowledgeItem.value?.is_archived == true) "アーカイブしました" else "復元しました"
                    _toast.value = message
                } else {
                    _error.value = "アーカイブの更新に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            }
        }
    }

    /**
     * アイテムを削除
     */
    fun deleteItem() {
        val itemId = _knowledgeItem.value?.id ?: return

        viewModelScope.launch {
            try {
                _isLoading.value = true
                val response = apiService.deleteKnowledgeItem(itemId)

                if (response.isSuccessful) {
                    _toast.value = "削除しました"
                    _finishActivity.value = true
                } else {
                    _error.value = "削除に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * レビュー済みとしてマーク
     */
    fun markAsReviewed() {
        val itemId = _knowledgeItem.value?.id ?: return

        viewModelScope.launch {
            try {
                val response = apiService.markKnowledgeReviewed(itemId)

                if (response.isSuccessful) {
                    val updatedItem = response.body()?.data
                    _knowledgeItem.postValue(updatedItem)
                    _toast.value = "復習済みにしました"
                } else {
                    _error.value = "レビューの更新に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            }
        }
    }

    fun clearError() {
        _error.value = null
    }

    fun clearToast() {
        _toast.value = null
    }
}
