package ecccomp.s2240788.mobile_android.ui.dialogs

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.DialogFragment
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.ChatConversation
import ecccomp.s2240788.mobile_android.databinding.DialogConversationHistoryBinding
import ecccomp.s2240788.mobile_android.ui.adapters.ConversationListAdapter
import java.text.SimpleDateFormat
import java.util.*

/**
 * ConversationHistoryDialog
 * 会話履歴ダイアログ
 * 過去の会話を表示・選択
 */
class ConversationHistoryDialog : DialogFragment() {

    private var _binding: DialogConversationHistoryBinding? = null
    private val binding get() = _binding!!

    private var onConversationSelectedListener: ((ChatConversation) -> Unit)? = null
    private var conversations: List<ChatConversation> = emptyList()

    private lateinit var adapter: ConversationListAdapter

    companion object {
        @JvmStatic
        fun newInstance(conversations: List<ChatConversation>): ConversationHistoryDialog {
            val dialog = ConversationHistoryDialog()
            dialog.conversations = conversations
            return dialog
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setStyle(STYLE_NORMAL, android.R.style.Theme_Material_Light_Dialog)
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = DialogConversationHistoryBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        // Setup RecyclerView
        adapter = ConversationListAdapter { conversation ->
            onConversationSelectedListener?.invoke(conversation)
            dismiss()
        }
        binding.rvConversations.layoutManager = LinearLayoutManager(requireContext())
        binding.rvConversations.adapter = adapter

        // Update conversations
        updateConversations(conversations)

        // Close button
        binding.btnClose.setOnClickListener {
            dismiss()
        }

        // Update empty state
        updateEmptyState()
    }

    fun setOnConversationSelectedListener(listener: (ChatConversation) -> Unit) {
        onConversationSelectedListener = listener
    }

    fun updateConversations(newConversations: List<ChatConversation>) {
        conversations = newConversations
        adapter.submitList(conversations)
        updateEmptyState()
    }

    private fun updateEmptyState() {
        if (conversations.isEmpty()) {
            binding.rvConversations.visibility = View.GONE
            binding.emptyState.visibility = View.VISIBLE
        } else {
            binding.rvConversations.visibility = View.VISIBLE
            binding.emptyState.visibility = View.GONE
        }
    }

    override fun onStart() {
        super.onStart()
        dialog?.window?.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}

