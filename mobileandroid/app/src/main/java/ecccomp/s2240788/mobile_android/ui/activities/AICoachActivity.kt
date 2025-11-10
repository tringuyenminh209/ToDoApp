package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.view.View
import android.view.inputmethod.EditorInfo
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.snackbar.Snackbar
import ecccomp.s2240788.mobile_android.databinding.ActivityAiCoachBinding
import ecccomp.s2240788.mobile_android.ui.adapters.ChatMessageAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.AICoachViewModel
import ecccomp.s2240788.mobile_android.ui.dialogs.ConversationHistoryDialog
import ecccomp.s2240788.mobile_android.ui.adapters.ChatMessageAdapter.Companion.TYPING_INDICATOR

/**
 * AICoachActivity
 * AIコーチ画面 - AIとのチャット機能
 * - クイックアクション（1日の計画、集中力のヘルプ、モチベーション、休憩提案）
 * - AIとのチャット機能
 * - メッセージ履歴の表示
 */
class AICoachActivity : BaseActivity() {

    private lateinit var binding: ActivityAiCoachBinding
    private lateinit var viewModel: AICoachViewModel
    private lateinit var chatAdapter: ChatMessageAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityAiCoachBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Initialize ViewModel
        viewModel = ViewModelProvider(this)[AICoachViewModel::class.java]

        setupRecyclerView()
        setupUI()
        setupClickListeners()
        observeViewModel()
    }

    private fun setupRecyclerView() {
        chatAdapter = ChatMessageAdapter()

        binding.rvSuggestions.apply {
            adapter = chatAdapter
            layoutManager = LinearLayoutManager(this@AICoachActivity).apply {
                stackFromEnd = true // Start from bottom (most recent message)
            }
            // Hide empty state when we have messages
            visibility = View.GONE
        }
    }

    private fun setupUI() {
        // 初期状態: 空の状態を表示
        updateEmptyState(true)
        // Show quick actions initially
        updateQuickActionsVisibility(true)
    }

    private fun setupClickListeners() {
        // 戻るボタン
        binding.btnBack.setOnClickListener {
            finish()
        }

        // History button
        binding.btnHistory.setOnClickListener {
            showConversationHistory()
        }

        // 送信ボタン
        binding.btnSend.setOnClickListener {
            sendMessage()
        }

        // Enter key in EditText
        binding.etMessage.setOnEditorActionListener { _, actionId, _ ->
            if (actionId == EditorInfo.IME_ACTION_SEND) {
                sendMessage()
                true
            } else {
                false
            }
        }

        // クイックアクション - 自動送信
        binding.chipPlanDay.setOnClickListener {
            sendQuickAction("今日の計画を立ててください")
        }

        binding.chipFocusHelp.setOnClickListener {
            sendQuickAction("集中力を高める方法を教えてください")
        }

        binding.chipMotivation.setOnClickListener {
            sendQuickAction("モチベーションを上げる方法を教えてください")
        }

        binding.chipBreakSuggestion.setOnClickListener {
            sendQuickAction("休憩のタイミングを教えてください")
        }
    }

    private fun observeViewModel() {
        // Observe messages
        viewModel.messages.observe(this) { messages ->
            if (messages.isNotEmpty()) {
                // Convert List<ChatMessage> to List<Any> for adapter
                val adapterList = messages.map { it as Any }.toMutableList()
                // Don't add typing indicator here - it's handled by isSending observer
                // Remove any existing typing indicator to avoid duplicates
                adapterList.removeAll { it === ChatMessageAdapter.TYPING_INDICATOR }
                chatAdapter.submitList(adapterList)
                updateEmptyState(false)
                // Hide quick actions when messages exist
                updateQuickActionsVisibility(false)

                // Scroll to bottom when new message arrives
                binding.rvSuggestions.postDelayed({
                    val itemCount = chatAdapter.itemCount
                    if (itemCount > 0) {
                        binding.rvSuggestions.smoothScrollToPosition(itemCount - 1)
                    }
                }, 100)
            } else {
                // Clear typing indicator when no messages
                chatAdapter.hideTypingIndicator()
                chatAdapter.submitList(emptyList())
                updateEmptyState(true)
                // Show quick actions when no messages
                updateQuickActionsVisibility(true)
            }
        }

        // Observe loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }

        // Observe sending state
        viewModel.isSending.observe(this) { isSending ->
            binding.btnSend.isEnabled = !isSending
            binding.etMessage.isEnabled = !isSending

            if (isSending) {
                binding.btnSend.alpha = 0.5f
                // Show typing indicator when AI is processing
                // Delay để đảm bảo messages đã được update trước
                binding.rvSuggestions.postDelayed({
                    chatAdapter.showTypingIndicator()
                    // Scroll to show typing indicator
                    val itemCount = chatAdapter.itemCount
                    if (itemCount > 0) {
                        binding.rvSuggestions.smoothScrollToPosition(itemCount - 1)
                    }
                }, 150)
            } else {
                binding.btnSend.alpha = 1.0f
                // Hide typing indicator when done
                // Delay một chút để đảm bảo messages đã được update
                binding.rvSuggestions.postDelayed({
                    chatAdapter.hideTypingIndicator()
                }, 100)
            }
        }

        // Observe errors
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        // Observe success messages
        viewModel.successMessage.observe(this) { message ->
            message?.let {
                // Optionally show success toast (can be removed if too noisy)
                // Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearSuccessMessage()
            }
        }

        // Observe conversations list
        viewModel.conversations.observe(this) { conversations ->
            // Update dialog if it's showing
            updateConversationHistoryDialog(conversations)
        }

        // Observe loading conversations
        viewModel.isLoadingConversations.observe(this) { isLoading ->
            // Can show loading indicator if needed
        }

        // Observe created task
        viewModel.createdTask.observe(this) { task ->
            task?.let {
                // Show snackbar with task info
                val subtaskInfo = if (!it.subtasks.isNullOrEmpty()) {
                    " (サブタスク: ${it.subtasks?.size}個)"
                } else {
                    ""
                }
                val message = "✅ タスクを作成しました: 「${it.title}」$subtaskInfo"

                Snackbar.make(binding.root, message, Snackbar.LENGTH_LONG)
                    .setAction("表示") {
                        // Could navigate to task detail screen here
                        Toast.makeText(this, "タスク ID: ${it.id}", Toast.LENGTH_SHORT).show()
                    }
                    .show()

                viewModel.clearCreatedTask()
            }
        }
    }

    private fun sendMessage() {
        val message = binding.etMessage.text?.toString()?.trim()

        if (message.isNullOrEmpty()) {
            Toast.makeText(this, "メッセージを入力してください", Toast.LENGTH_SHORT).show()
            return
        }

        // Send message via ViewModel
        viewModel.sendMessage(message)

        // Clear input field
        binding.etMessage.text?.clear()

        // Hide keyboard
        binding.etMessage.clearFocus()
    }

    private fun sendQuickAction(actionMessage: String) {
        // Send quick action directly
        viewModel.sendQuickAction(actionMessage)
    }

    private fun updateEmptyState(isEmpty: Boolean) {
        if (isEmpty) {
            binding.emptyState.visibility = View.VISIBLE
            binding.rvSuggestions.visibility = View.GONE
        } else {
            binding.emptyState.visibility = View.GONE
            binding.rvSuggestions.visibility = View.VISIBLE
        }
    }

    /**
     * Update quick actions visibility
     * Only show when there are no messages (initial state)
     */
    private fun updateQuickActionsVisibility(show: Boolean) {
        binding.quickActionsCard.visibility = if (show) View.VISIBLE else View.GONE
    }

    /**
     * Show conversation history dialog
     */
    private fun showConversationHistory() {
        // Load conversations first
        viewModel.loadConversations()

        // Show dialog with current conversations (will be updated when loaded)
        val currentConversations = viewModel.conversations.value ?: emptyList()
        val dialog = ConversationHistoryDialog.newInstance(currentConversations)
        
        dialog.setOnConversationSelectedListener { conversation ->
            // Load selected conversation
            viewModel.loadConversation(conversation.id)
        }
        
        dialog.show(supportFragmentManager, "conversation_history")
    }

    /**
     * Update conversation history dialog if it's showing
     */
    private fun updateConversationHistoryDialog(conversations: List<ecccomp.s2240788.mobile_android.data.models.ChatConversation>) {
        val dialog = supportFragmentManager.findFragmentByTag("conversation_history") as? ConversationHistoryDialog
        dialog?.updateConversations(conversations)
    }

    override fun onDestroy() {
        super.onDestroy()
        // Optional: Save conversation state or perform cleanup
    }
}
