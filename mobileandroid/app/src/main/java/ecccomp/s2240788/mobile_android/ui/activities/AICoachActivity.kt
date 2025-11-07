package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.view.View
import android.view.inputmethod.EditorInfo
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.databinding.ActivityAiCoachBinding
import ecccomp.s2240788.mobile_android.ui.adapters.ChatMessageAdapter
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
    }

    private fun setupClickListeners() {
        // 戻るボタン
        binding.btnBack.setOnClickListener {
            finish()
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
                chatAdapter.submitList(messages)
                updateEmptyState(false)

                // Scroll to bottom when new message arrives
                binding.rvSuggestions.postDelayed({
                    binding.rvSuggestions.smoothScrollToPosition(messages.size - 1)
                }, 100)
            } else {
                updateEmptyState(true)
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
            } else {
                binding.btnSend.alpha = 1.0f
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

    override fun onDestroy() {
        super.onDestroy()
        // Optional: Save conversation state or perform cleanup
    }
}
