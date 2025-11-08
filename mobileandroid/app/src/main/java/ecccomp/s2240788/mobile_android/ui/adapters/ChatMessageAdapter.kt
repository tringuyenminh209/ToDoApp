package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.ChatMessage
import java.text.SimpleDateFormat
import java.util.*

/**
 * ChatMessageAdapter
 * Chat AI メッセージリスト表示用RecyclerViewアダプター
 * ユーザーとAIのメッセージを区別して表示
 */
class ChatMessageAdapter : ListAdapter<ChatMessage, RecyclerView.ViewHolder>(ChatMessageDiffCallback()) {

    companion object {
        private const val VIEW_TYPE_USER = 1
        private const val VIEW_TYPE_AI = 2
    }

    override fun getItemViewType(position: Int): Int {
        return when (getItem(position).role) {
            "user" -> VIEW_TYPE_USER
            "assistant" -> VIEW_TYPE_AI
            else -> VIEW_TYPE_AI // system messages shown as AI
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        return when (viewType) {
            VIEW_TYPE_USER -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.item_chat_message_user, parent, false)
                UserMessageViewHolder(view)
            }
            else -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.item_chat_message_ai, parent, false)
                AIMessageViewHolder(view)
            }
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        val message = getItem(position)
        when (holder) {
            is UserMessageViewHolder -> holder.bind(message)
            is AIMessageViewHolder -> holder.bind(message)
        }
    }

    /**
     * ViewHolder for User Messages (right-aligned)
     */
    inner class UserMessageViewHolder(
        itemView: View
    ) : RecyclerView.ViewHolder(itemView) {
        private val tvMessage: TextView = itemView.findViewById(R.id.tvMessage)
        private val tvTimestamp: TextView = itemView.findViewById(R.id.tvTimestamp)

        fun bind(message: ChatMessage) {
            tvMessage.text = message.content
            tvTimestamp.text = formatTimestamp(message.created_at)
        }
    }

    /**
     * ViewHolder for AI Messages (left-aligned)
     */
    inner class AIMessageViewHolder(
        itemView: View
    ) : RecyclerView.ViewHolder(itemView) {
        private val tvMessage: TextView = itemView.findViewById(R.id.tvMessage)
        private val tvTimestamp: TextView = itemView.findViewById(R.id.tvTimestamp)

        fun bind(message: ChatMessage) {
            tvMessage.text = message.content
            tvTimestamp.text = formatTimestamp(message.created_at)
        }
    }

    /**
     * Format timestamp for display
     */
    private fun formatTimestamp(timestamp: String): String {
        return try {
            // Parse ISO 8601 timestamp from API
            val inputFormat = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", Locale.getDefault())
            inputFormat.timeZone = TimeZone.getTimeZone("UTC")
            val date = inputFormat.parse(timestamp)

            // Format to readable time
            val outputFormat = SimpleDateFormat("HH:mm", Locale.getDefault())
            date?.let { outputFormat.format(it) } ?: timestamp
        } catch (e: Exception) {
            // Fallback to showing just time if parsing fails
            try {
                timestamp.substring(11, 16) // Extract HH:mm from timestamp
            } catch (e: Exception) {
                ""
            }
        }
    }

    /**
     * DiffUtil for efficient list updates
     */
    class ChatMessageDiffCallback : DiffUtil.ItemCallback<ChatMessage>() {
        override fun areItemsTheSame(oldItem: ChatMessage, newItem: ChatMessage): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: ChatMessage, newItem: ChatMessage): Boolean {
            return oldItem == newItem
        }
    }
}
