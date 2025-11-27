package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Notification
import ecccomp.s2240788.mobile_android.data.repository.NotificationRepository
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class NotificationViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )
    private val repository = NotificationRepository(apiService)

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> = _error

    private val _notifications = MutableLiveData<List<Notification>>()
    val notifications: LiveData<List<Notification>> = _notifications

    private val _unreadCount = MutableLiveData<Int>()
    val unreadCount: LiveData<Int> = _unreadCount

    private val _successMessage = MutableLiveData<String>()
    val successMessage: LiveData<String> = _successMessage

    /**
     * Load notifications with optional filters
     */
    fun loadNotifications(type: String? = null, readFilter: Boolean? = null) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val result = repository.getNotifications(type, readFilter, 100)

                result.fold(
                    onSuccess = { response ->
                        _notifications.value = response.data
                        _isLoading.value = false
                    },
                    onError = { errorMessage ->
                        _error.value = errorMessage
                        _isLoading.value = false
                    }
                )
            } catch (e: IllegalStateException) {
                _error.value = "Network error: ${e.message ?: "Response body already consumed"}"
                _isLoading.value = false
            } catch (e: Exception) {
                _error.value = "Failed to load notifications: ${e.message ?: e.javaClass.simpleName}"
                _isLoading.value = false
            }
        }
    }

    /**
     * Load unread count
     */
    fun loadUnreadCount() {
        viewModelScope.launch {
            try {
                val result = repository.getUnreadCount()
                result.fold(
                    onSuccess = { count ->
                        _unreadCount.value = count
                    },
                    onError = { /* Ignore errors for count */ }
                )
            } catch (e: Exception) {
                // Ignore errors for count
            }
        }
    }

    /**
     * Mark notification as read
     */
    fun markAsRead(notificationId: Int) {
        viewModelScope.launch {
            try {
                val result = repository.markAsRead(notificationId)
                result.fold(
                    onSuccess = {
                        // Update local list
                        val updated = _notifications.value?.map {
                            if (it.id == notificationId) it.copy(is_read = true) else it
                        }
                        _notifications.value = updated ?: emptyList()
                        loadUnreadCount()
                    },
                    onError = { errorMessage ->
                        _error.value = errorMessage
                    }
                )
            } catch (e: Exception) {
                _error.value = "Failed to mark as read: ${e.message}"
            }
        }
    }

    /**
     * Mark all notifications as read
     */
    fun markAllAsRead() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val result = repository.markAllAsRead()

                result.fold(
                    onSuccess = {
                        // Update local list
                        val updated = _notifications.value?.map { it.copy(is_read = true) }
                        _notifications.value = updated ?: emptyList()
                        _unreadCount.value = 0
                        _successMessage.value = "All notifications marked as read"
                        _isLoading.value = false
                    },
                    onError = { errorMessage ->
                        _error.value = errorMessage
                        _isLoading.value = false
                    }
                )
            } catch (e: Exception) {
                _error.value = "Failed to mark all as read: ${e.message}"
                _isLoading.value = false
            }
        }
    }

    /**
     * Delete notification
     */
    fun deleteNotification(notificationId: Int) {
        viewModelScope.launch {
            try {
                val result = repository.deleteNotification(notificationId)
                result.fold(
                    onSuccess = {
                        // Remove from local list
                        val updated = _notifications.value?.filter { it.id != notificationId }
                        _notifications.value = updated ?: emptyList()
                        _successMessage.value = "Notification deleted"
                        loadUnreadCount()
                    },
                    onError = { errorMessage ->
                        _error.value = errorMessage
                    }
                )
            } catch (e: Exception) {
                _error.value = "Failed to delete notification: ${e.message}"
            }
        }
    }

    /**
     * Clear all read notifications
     */
    fun clearReadNotifications() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val result = repository.clearReadNotifications()

                result.fold(
                    onSuccess = {
                        // Keep only unread in local list
                        val updated = _notifications.value?.filter { !it.read }
                        _notifications.value = updated ?: emptyList()
                        _successMessage.value = "Read notifications cleared"
                        _isLoading.value = false
                    },
                    onError = { errorMessage ->
                        _error.value = errorMessage
                        _isLoading.value = false
                    }
                )
            } catch (e: Exception) {
                _error.value = "Failed to clear notifications: ${e.message}"
                _isLoading.value = false
            }
        }
    }
}
