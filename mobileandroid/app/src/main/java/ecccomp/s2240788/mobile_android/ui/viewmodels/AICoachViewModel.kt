package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.ChatConversation
import ecccomp.s2240788.mobile_android.data.models.ChatMessage
import ecccomp.s2240788.mobile_android.data.repository.ChatRepository
import ecccomp.s2240788.mobile_android.data.result.ChatResult
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * AICoachViewModel
 * Chat AI機能のデータ管理
 */
class AICoachViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val chatRepository: ChatRepository = ChatRepository(apiService)

    // Current conversation
    private val _currentConversation = MutableLiveData<ChatConversation?>()
    val currentConversation: LiveData<ChatConversation?> = _currentConversation

    // Messages in current conversation
    private val _messages = MutableLiveData<List<ChatMessage>>()
    val messages: LiveData<List<ChatMessage>> = _messages

    // Loading state
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    // Sending message state
    private val _isSending = MutableLiveData<Boolean>()
    val isSending: LiveData<Boolean> = _isSending

    // Error messages
    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    // Success messages
    private val _successMessage = MutableLiveData<String?>()
    val successMessage: LiveData<String?> = _successMessage

    // Conversations list
    private val _conversations = MutableLiveData<List<ChatConversation>>()
    val conversations: LiveData<List<ChatConversation>> = _conversations

    // Loading conversations state
    private val _isLoadingConversations = MutableLiveData<Boolean>()
    val isLoadingConversations: LiveData<Boolean> = _isLoadingConversations

    /**
     * Start a new conversation with initial message
     */
    fun startNewConversation(message: String) {
        if (message.isBlank()) {
            _error.value = "メッセージを入力してください"
            return
        }

        viewModelScope.launch {
            try {
                _isLoading.value = true
                _isSending.value = true
                _error.value = null

                val result = chatRepository.createConversation(message)

                when (result) {
                    is ChatResult.Success -> {
                        _currentConversation.value = result.data
                        _messages.value = result.data.messages ?: emptyList()
                        _successMessage.value = "会話を開始しました"
                    }
                    is ChatResult.Error -> {
                        _error.value = result.message
                    }
                    is ChatResult.Loading -> {
                        // Already handled by _isLoading
                    }
                }

            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isLoading.value = false
                _isSending.value = false
            }
        }
    }

    /**
     * Send message in existing conversation
     */
    fun sendMessage(message: String) {
        if (message.isBlank()) {
            _error.value = "メッセージを入力してください"
            return
        }

        val conversationId = _currentConversation.value?.id
        if (conversationId == null) {
            // If no conversation exists, start a new one
            startNewConversation(message)
            return
        }

        viewModelScope.launch {
            try {
                _isSending.value = true
                _error.value = null

                val result = chatRepository.sendMessage(conversationId, message)

                when (result) {
                    is ChatResult.Success -> {
                        // Add both user and assistant messages to the list
                        val currentMessages = _messages.value?.toMutableList() ?: mutableListOf()
                        currentMessages.add(result.data.user_message)
                        currentMessages.add(result.data.assistant_message)
                        _messages.value = currentMessages

                        // Update conversation
                        _currentConversation.value = _currentConversation.value?.copy(
                            message_count = currentMessages.size,
                            last_message_at = result.data.assistant_message.created_at
                        )
                    }
                    is ChatResult.Error -> {
                        _error.value = result.message
                    }
                    is ChatResult.Loading -> {
                        // Already handled by _isSending
                    }
                }

            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isSending.value = false
            }
        }
    }

    /**
     * Load existing conversation
     */
    fun loadConversation(conversationId: Long) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val result = chatRepository.getConversation(conversationId)

                when (result) {
                    is ChatResult.Success -> {
                        _currentConversation.value = result.data
                        _messages.value = result.data.messages ?: emptyList()
                    }
                    is ChatResult.Error -> {
                        _error.value = result.message
                    }
                    is ChatResult.Loading -> {
                        // Already handled by _isLoading
                    }
                }

            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Handle quick action button click
     */
    fun sendQuickAction(actionMessage: String) {
        sendMessage(actionMessage)
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
     * Reset conversation (start fresh)
     */
    fun resetConversation() {
        _currentConversation.value = null
        _messages.value = emptyList()
        _error.value = null
        _successMessage.value = null
    }

    /**
     * Check if there's an active conversation
     */
    fun hasActiveConversation(): Boolean {
        return _currentConversation.value != null
    }

    /**
     * Get current conversation ID
     */
    fun getCurrentConversationId(): Long? {
        return _currentConversation.value?.id
    }

    /**
     * Load conversations list
     */
    fun loadConversations() {
        viewModelScope.launch {
            try {
                _isLoadingConversations.value = true
                _error.value = null

                val result = chatRepository.getConversations(
                    status = "active",
                    sortBy = "last_message_at",
                    sortOrder = "desc",
                    perPage = 50
                )

                when (result) {
                    is ChatResult.Success -> {
                        _conversations.value = result.data.data
                    }
                    is ChatResult.Error -> {
                        _error.value = result.message
                    }
                    is ChatResult.Loading -> {
                        // Already handled by _isLoadingConversations
                    }
                }

            } catch (e: Exception) {
                _error.value = "エラーが発生しました: ${e.message}"
            } finally {
                _isLoadingConversations.value = false
            }
        }
    }
}
