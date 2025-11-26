package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.ChatConversation
import ecccomp.s2240788.mobile_android.data.models.ChatMessage
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.data.models.TaskSuggestion
import ecccomp.s2240788.mobile_android.data.models.TimetableClass
import ecccomp.s2240788.mobile_android.data.models.TimetableClassSuggestion
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

    // Created task from AI chat
    private val _createdTask = MutableLiveData<Task?>()
    val createdTask: LiveData<Task?> = _createdTask

    // Task suggestion from AI (not auto-created, requires user confirmation)
    private val _taskSuggestion = MutableLiveData<TaskSuggestion?>()
    val taskSuggestion: LiveData<TaskSuggestion?> = _taskSuggestion

    // Timetable class suggestion from AI (not auto-created, requires user confirmation)
    private val _timetableSuggestion = MutableLiveData<TimetableClassSuggestion?>()
    val timetableSuggestion: LiveData<TimetableClassSuggestion?> = _timetableSuggestion

    // Created timetable class from AI chat
    private val _createdTimetableClass = MutableLiveData<TimetableClass?>()
    val createdTimetableClass: LiveData<TimetableClass?> = _createdTimetableClass

    // Knowledge creation result from AI (requires user confirmation to view)
    private val _knowledgeCreationResult = MutableLiveData<ecccomp.s2240788.mobile_android.data.models.KnowledgeCreationResult?>()
    val knowledgeCreationResult: LiveData<ecccomp.s2240788.mobile_android.data.models.KnowledgeCreationResult?> = _knowledgeCreationResult

    /**
     * Start a new conversation with initial message
     */
    fun startNewConversation(message: String) {
        if (message.isBlank()) {
            _error.value = "メッセージを入力してください"
            return
        }

        // Create temporary user message to show immediately
        val tempUserMessage = ChatMessage(
            id = -1, // Temporary ID
            conversation_id = -1,
            user_id = null,
            role = "user",
            content = message,
            metadata = null,
            token_count = null,
            created_at = java.text.SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", java.util.Locale.getDefault()).apply {
                timeZone = java.util.TimeZone.getTimeZone("UTC")
            }.format(java.util.Date()),
            updated_at = null
        )

        // Add user message immediately for better UX
        _messages.value = listOf(tempUserMessage)

        viewModelScope.launch {
            try {
                _isLoading.value = true
                _isSending.value = true
                _error.value = null

                val result = chatRepository.createConversation(message)

                when (result) {
                    is ChatResult.Success -> {
                        try {
                            _currentConversation.value = result.data.conversation
                            // Replace temporary message with real messages
                            val realMessages = result.data.conversation.messages ?: emptyList()
                            _messages.value = realMessages

                            // Check if task was created
                            if (result.data.created_task != null) {
                                _createdTask.value = result.data.created_task
                                _successMessage.value = "会話を開始し、タスクを作成しました！"
                            } else if (result.data.created_timetable_class != null) {
                                _createdTimetableClass.value = result.data.created_timetable_class
                                _successMessage.value = "会話を開始し、授業を登録しました！"
                            } else {
                                _successMessage.value = "会話を開始しました"
                            }
                        } catch (e: Exception) {
                            android.util.Log.e("AICoachViewModel", "Error processing conversation response", e)
                            _error.value = "レスポンスの処理に失敗しました: ${e.message}"
                            // Keep temporary message on parse error
                        }
                    }
                    is ChatResult.Error -> {
                        // Remove temporary message on error
                        _messages.value = emptyList()
                        _error.value = result.message
                    }
                    is ChatResult.Loading -> {
                        // Already handled by _isLoading
                    }
                }

            } catch (e: Exception) {
                android.util.Log.e("AICoachViewModel", "Exception in startNewConversation", e)
                // Remove temporary message on exception
                _messages.value = emptyList()
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

        // Create temporary user message to show immediately
        val tempUserMessage = ChatMessage(
            id = -1, // Temporary ID
            conversation_id = conversationId,
            user_id = null,
            role = "user",
            content = message,
            metadata = null,
            token_count = null,
            created_at = java.text.SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", java.util.Locale.getDefault()).apply {
                timeZone = java.util.TimeZone.getTimeZone("UTC")
            }.format(java.util.Date()),
            updated_at = null
        )

        // Add user message immediately for better UX
        val currentMessages = _messages.value?.toMutableList() ?: mutableListOf()
        currentMessages.add(tempUserMessage)
        _messages.value = currentMessages

        viewModelScope.launch {
            try {
                _isSending.value = true
                _error.value = null

                // Use context-aware endpoint instead of regular sendMessage
                val result = chatRepository.sendMessageWithContext(conversationId, message)

                when (result) {
                    is ChatResult.Success -> {
                        try {
                            // Replace temporary user message with real one and add assistant message
                            val updatedMessages = _messages.value?.toMutableList() ?: mutableListOf()
                            // Remove temporary message (last one should be the temp user message)
                            if (updatedMessages.isNotEmpty() && updatedMessages.last().id == -1L) {
                                updatedMessages.removeAt(updatedMessages.size - 1)
                            }
                            // Add real messages
                            updatedMessages.add(result.data.user_message)
                            updatedMessages.add(result.data.assistant_message)
                            _messages.value = updatedMessages

                            // Update conversation
                            _currentConversation.value = _currentConversation.value?.copy(
                                message_count = updatedMessages.size,
                                last_message_at = result.data.assistant_message.created_at
                            )

                            // Check if task was created (auto-created task)
                            if (result.data.created_task != null) {
                                _createdTask.value = result.data.created_task
                                _successMessage.value = "タスクを作成しました！"
                            }

                            // Check if there's a task suggestion (requires user confirmation)
                            if (result.data.task_suggestion != null) {
                                _taskSuggestion.value = result.data.task_suggestion
                            }

                            // Check if there's a timetable suggestion (requires user confirmation)
                            if (result.data.timetable_suggestion != null) {
                                _timetableSuggestion.value = result.data.timetable_suggestion
                            }

                            // Check if knowledge was created via AI
                            if (result.data.knowledge_creation != null && result.data.knowledge_creation.success) {
                                _knowledgeCreationResult.value = result.data.knowledge_creation
                            }
                        } catch (e: Exception) {
                            android.util.Log.e("AICoachViewModel", "Error processing sendMessage response", e)
                            _error.value = "メッセージの処理に失敗しました: ${e.message}"
                        }
                    }
                    is ChatResult.Error -> {
                        // Remove temporary message on error
                        val updatedMessages = _messages.value?.toMutableList() ?: mutableListOf()
                        if (updatedMessages.isNotEmpty() && updatedMessages.last().id == -1L) {
                            updatedMessages.removeAt(updatedMessages.size - 1)
                            _messages.value = updatedMessages
                        }
                        _error.value = result.message
                    }
                    is ChatResult.Loading -> {
                        // Already handled by _isSending
                    }
                }

            } catch (e: Exception) {
                // Remove temporary message on exception
                val updatedMessages = _messages.value?.toMutableList() ?: mutableListOf()
                if (updatedMessages.isNotEmpty() && updatedMessages.last().id == -1L) {
                    updatedMessages.removeAt(updatedMessages.size - 1)
                    _messages.value = updatedMessages
                }
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
     * Clear created task
     */
    fun clearCreatedTask() {
        _createdTask.value = null
    }

    /**
     * Clear created timetable class
     */
    fun clearCreatedTimetableClass() {
        _createdTimetableClass.value = null
    }

    /**
     * Confirm and create task from AI suggestion
     */
    fun confirmTaskSuggestion(suggestion: TaskSuggestion) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val result = chatRepository.confirmTaskSuggestion(suggestion)

                when (result) {
                    is ChatResult.Success -> {
                        _createdTask.value = result.data
                        _taskSuggestion.value = null // Clear suggestion after confirmation
                        _successMessage.value = "タスクを作成しました！"
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
     * Dismiss task suggestion without creating
     */
    fun dismissTaskSuggestion() {
        _taskSuggestion.value = null
    }

    /**
     * Confirm and create timetable class from AI suggestion
     */
    fun confirmTimetableSuggestion(suggestion: TimetableClassSuggestion) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                val response = apiService.confirmTimetableSuggestion(suggestion)

                if (response.isSuccessful && response.body()?.success == true) {
                    _createdTimetableClass.value = response.body()?.data
                    _timetableSuggestion.value = null // Clear suggestion after confirmation
                    _successMessage.value = "授業を登録しました！"
                } else {
                    _error.value = "授業の登録に失敗しました"
                }
            } catch (e: Exception) {
                _error.value = "授業の登録に失敗しました: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Dismiss timetable suggestion without creating
     */
    fun dismissTimetableSuggestion() {
        _timetableSuggestion.value = null
    }

    /**
     * Dismiss knowledge creation result after viewing
     */
    fun dismissKnowledgeCreation() {
        _knowledgeCreationResult.value = null
    }

    /**
     * Reset conversation (start fresh)
     */
    fun resetConversation() {
        _currentConversation.value = null
        _messages.value = emptyList()
        _error.value = null
        _successMessage.value = null
        _taskSuggestion.value = null
        _timetableSuggestion.value = null
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
