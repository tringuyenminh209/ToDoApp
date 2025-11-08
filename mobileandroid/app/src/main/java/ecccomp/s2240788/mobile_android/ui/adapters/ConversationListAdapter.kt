package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.data.models.ChatConversation
import ecccomp.s2240788.mobile_android.databinding.ItemConversationBinding
import java.text.SimpleDateFormat
import java.util.*

/**
 * ConversationListAdapter
 * 会話リストのアダプター
 */
class ConversationListAdapter(
    private val onConversationClick: (ChatConversation) -> Unit
) : ListAdapter<ChatConversation, ConversationListAdapter.ConversationViewHolder>(ConversationDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ConversationViewHolder {
        val binding = ItemConversationBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return ConversationViewHolder(binding, onConversationClick)
    }

    override fun onBindViewHolder(holder: ConversationViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class ConversationViewHolder(
        private val binding: ItemConversationBinding,
        private val onConversationClick: (ChatConversation) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(conversation: ChatConversation) {
            binding.apply {
                // Title
                tvTitle.text = conversation.title ?: "会話"

                // Message count
                tvMessageCount.text = "${conversation.message_count}件のメッセージ"

                // Last message time
                conversation.last_message_at?.let { lastMessageAt ->
                    tvLastMessageTime.text = formatTimestamp(lastMessageAt)
                } ?: run {
                    tvLastMessageTime.text = formatTimestamp(conversation.created_at)
                }

                // Status indicator
                if (conversation.status == "active") {
                    ivStatus.visibility = View.VISIBLE
                } else {
                    ivStatus.visibility = View.GONE
                }

                // Click listener
                root.setOnClickListener {
                    onConversationClick(conversation)
                }
            }
        }

        private fun formatTimestamp(timestamp: String): String {
            return try {
                val inputFormat = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", Locale.getDefault())
                inputFormat.timeZone = TimeZone.getTimeZone("UTC")
                val date = inputFormat.parse(timestamp)

                val now = Calendar.getInstance()
                val messageDate = Calendar.getInstance()
                date?.let { messageDate.time = it }

                val outputFormat = when {
                    // Same day
                    now.get(Calendar.YEAR) == messageDate.get(Calendar.YEAR) &&
                    now.get(Calendar.DAY_OF_YEAR) == messageDate.get(Calendar.DAY_OF_YEAR) -> {
                        SimpleDateFormat("HH:mm", Locale.getDefault())
                    }
                    // Same year
                    now.get(Calendar.YEAR) == messageDate.get(Calendar.YEAR) -> {
                        SimpleDateFormat("MM/dd HH:mm", Locale.getDefault())
                    }
                    // Different year
                    else -> {
                        SimpleDateFormat("yyyy/MM/dd", Locale.getDefault())
                    }
                }

                date?.let { outputFormat.format(it) } ?: timestamp
            } catch (e: Exception) {
                // Fallback
                try {
                    timestamp.substring(0, 10) // YYYY-MM-DD
                } catch (e: Exception) {
                    timestamp
                }
            }
        }
    }

    class ConversationDiffCallback : DiffUtil.ItemCallback<ChatConversation>() {
        override fun areItemsTheSame(oldItem: ChatConversation, newItem: ChatConversation): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: ChatConversation, newItem: ChatConversation): Boolean {
            return oldItem == newItem
        }
    }
}

