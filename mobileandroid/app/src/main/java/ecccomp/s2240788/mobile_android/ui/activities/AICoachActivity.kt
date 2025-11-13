package ecccomp.s2240788.mobile_android.ui.activities

import android.graphics.Rect
import android.os.Build
import android.os.Bundle
import android.view.View
import android.view.ViewTreeObserver
import android.view.inputmethod.EditorInfo
import android.widget.Toast
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.snackbar.Snackbar
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityAiCoachBinding
import ecccomp.s2240788.mobile_android.ui.adapters.ChatMessageAdapter
import ecccomp.s2240788.mobile_android.ui.dialogs.ConversationHistoryDialog
import ecccomp.s2240788.mobile_android.ui.viewmodels.AICoachViewModel

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

        setupWindowInsets()

        // Initialize ViewModel
        viewModel = ViewModelProvider(this)[AICoachViewModel::class.java]

        setupRecyclerView()
        setupUI()
        setupClickListeners()
        setupKeyboardListener()
        setupRecyclerViewInsets()
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

        // Focus listener to ensure RecyclerView scrolls when keyboard appears
        binding.etMessage.setOnFocusChangeListener { _, hasFocus ->
            if (hasFocus) {
                // Scroll to bottom when input field gets focus
                scrollToBottom()
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
                // Update messages in adapter
                // Note: typing indicator is managed separately by isSending observer
                chatAdapter.updateMessages(messages)
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
                // Clear all messages
                chatAdapter.updateMessages(emptyList())
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
            // Always ensure input is enabled/disabled correctly
            binding.btnSend.isEnabled = !isSending
            binding.etMessage.isEnabled = !isSending

            if (isSending) {
                binding.btnSend.alpha = 0.5f
                // Show typing indicator immediately when AI is processing
                chatAdapter.showTypingIndicator()
                // Scroll to show typing indicator
                binding.rvSuggestions.postDelayed({
                    val itemCount = chatAdapter.itemCount
                    if (itemCount > 0) {
                        binding.rvSuggestions.smoothScrollToPosition(itemCount - 1)
                    }
                }, 100)
            } else {
                binding.btnSend.alpha = 1.0f
                // Hide typing indicator immediately when done
                chatAdapter.hideTypingIndicator()
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

        // Observe task suggestion
        viewModel.taskSuggestion.observe(this) { suggestion ->
            if (suggestion != null) {
                // Show suggestion card
                binding.taskSuggestionCard.visibility = View.VISIBLE

                // Populate suggestion data
                binding.tvSuggestionTitle.text = suggestion.title
                binding.tvSuggestionDescription.text = suggestion.description ?: ""

                // Format estimated time
                val timeText = if (suggestion.estimated_minutes != null) {
                    "${suggestion.estimated_minutes}分"
                } else {
                    "時間未設定"
                }
                binding.chipSuggestionTime.text = timeText

                // Format priority
                val priorityText = when (suggestion.priority.lowercase()) {
                    "high" -> "高優先度"
                    "medium" -> "中優先度"
                    "low" -> "低優先度"
                    else -> suggestion.priority
                }
                binding.chipSuggestionPriority.text = priorityText

                // Set priority chip color
                val priorityColor = when (suggestion.priority.lowercase()) {
                    "high" -> R.color.error
                    "medium" -> R.color.warning
                    "low" -> R.color.success
                    else -> R.color.text_secondary
                }
                binding.chipSuggestionPriority.setChipBackgroundColorResource(priorityColor)

                // Show reason
                binding.tvSuggestionReason.text = "💡 ${suggestion.reason}"

                // Setup button listeners
                binding.btnConfirmSuggestion.setOnClickListener {
                    viewModel.confirmTaskSuggestion(suggestion)
                }

                binding.btnDismissSuggestion.setOnClickListener {
                    viewModel.dismissTaskSuggestion()
                }
            } else {
                // Hide suggestion card
                binding.taskSuggestionCard.visibility = View.GONE
            }
        }
    }

    /**
     * Setup RecyclerView insets to handle keyboard and ensure messages are visible
     */
    private fun setupRecyclerViewInsets() {
        val recyclerView = binding.rvSuggestions
        val originalPaddingBottom = recyclerView.paddingBottom
        val originalPaddingLeft = recyclerView.paddingLeft
        val originalPaddingTop = recyclerView.paddingTop
        val originalPaddingRight = recyclerView.paddingRight
        
        ViewCompat.setOnApplyWindowInsetsListener(recyclerView) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            val imeInsets = insets.getInsets(WindowInsetsCompat.Type.ime())
            
            // When keyboard is visible, add bottom padding to RecyclerView
            // This ensures messages can scroll above the keyboard
            val bottomPadding = if (imeInsets.bottom > 0) {
                // Keyboard is visible: add padding equal to keyboard height
                // This allows RecyclerView content to scroll above the keyboard
                originalPaddingBottom + imeInsets.bottom
            } else {
                // Keyboard is hidden: use original padding
                originalPaddingBottom
            }
            
            v.setPadding(
                originalPaddingLeft,
                originalPaddingTop,
                originalPaddingRight,
                bottomPadding
            )
            
            // Scroll to bottom when keyboard appears
            if (imeInsets.bottom > 0) {
                scrollToBottom()
            }
            
            insets
        }
    }

    /**
     * Setup keyboard listener to handle RecyclerView scrolling when keyboard appears/disappears
     * Uses ViewTreeObserver to detect layout changes when keyboard appears
     */
    private fun setupKeyboardListener() {
        val rootView = binding.root
        
        rootView.viewTreeObserver.addOnGlobalLayoutListener(object : ViewTreeObserver.OnGlobalLayoutListener {
            private var wasKeyboardVisible = false
            
            override fun onGlobalLayout() {
                val rect = Rect()
                rootView.getWindowVisibleDisplayFrame(rect)
                val screenHeight = rootView.height
                val keypadHeight = screenHeight - rect.bottom
                
                // Consider keyboard visible if keypad height is more than 15% of screen height
                val isKeyboardVisible = keypadHeight > screenHeight * 0.15
                
                if (isKeyboardVisible && !wasKeyboardVisible) {
                    // Keyboard just appeared, scroll RecyclerView to bottom
                    scrollToBottom()
                }
                
                wasKeyboardVisible = isKeyboardVisible
            }
        })
    }

    /**
     * Scroll RecyclerView to the bottom
     */
    private fun scrollToBottom() {
        binding.rvSuggestions.postDelayed({
            val itemCount = chatAdapter.itemCount
            if (itemCount > 0) {
                binding.rvSuggestions.smoothScrollToPosition(itemCount - 1)
            }
        }, 100)
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
