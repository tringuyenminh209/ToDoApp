package ecccomp.s2240788.mobile_android.ui.adapters

import android.animation.ObjectAnimator
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.animation.AccelerateDecelerateInterpolator
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
 * Typing indicatorをサポート
 */
class ChatMessageAdapter : ListAdapter<Any, RecyclerView.ViewHolder>(ChatMessageDiffCallback()) {

    companion object {
        private const val VIEW_TYPE_USER = 1
        private const val VIEW_TYPE_AI = 2
        private const val VIEW_TYPE_TYPING = 3
        
        // Typing indicator object
        val TYPING_INDICATOR = Any()
    }

    override fun getItemViewType(position: Int): Int {
        val item = getItem(position)
        return when {
            item === TYPING_INDICATOR -> VIEW_TYPE_TYPING
            item is ChatMessage && item.role == "user" -> VIEW_TYPE_USER
            item is ChatMessage && item.role == "assistant" -> VIEW_TYPE_AI
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
            VIEW_TYPE_TYPING -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.item_chat_typing_indicator, parent, false)
                TypingIndicatorViewHolder(view)
            }
            else -> {
                val view = LayoutInflater.from(parent.context)
                    .inflate(R.layout.item_chat_message_ai, parent, false)
                AIMessageViewHolder(view)
            }
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        val item = getItem(position)
        when (holder) {
            is UserMessageViewHolder -> {
                if (item is ChatMessage) {
                    holder.bind(item)
                }
            }
            is AIMessageViewHolder -> {
                if (item is ChatMessage) {
                    holder.bind(item)
                }
            }
            is TypingIndicatorViewHolder -> holder.startAnimation()
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
     * ViewHolder for Typing Indicator
     */
    inner class TypingIndicatorViewHolder(
        itemView: View
    ) : RecyclerView.ViewHolder(itemView) {
        private val dot1: View = itemView.findViewById(R.id.dot1)
        private val dot2: View = itemView.findViewById(R.id.dot2)
        private val dot3: View = itemView.findViewById(R.id.dot3)
        
        private var animators: List<ObjectAnimator>? = null

        fun startAnimation() {
            stopAnimation()
            
            // Create animations for each dot
            val anim1 = ObjectAnimator.ofFloat(dot1, "alpha", 0.3f, 1f, 0.3f).apply {
                duration = 1000
                repeatCount = ObjectAnimator.INFINITE
                startDelay = 0
            }
            
            val anim2 = ObjectAnimator.ofFloat(dot2, "alpha", 0.3f, 1f, 0.3f).apply {
                duration = 1000
                repeatCount = ObjectAnimator.INFINITE
                startDelay = 200
            }
            
            val anim3 = ObjectAnimator.ofFloat(dot3, "alpha", 0.3f, 1f, 0.3f).apply {
                duration = 1000
                repeatCount = ObjectAnimator.INFINITE
                startDelay = 400
            }
            
            animators = listOf(anim1, anim2, anim3)
            animators?.forEach { 
                it.interpolator = AccelerateDecelerateInterpolator()
                it.start()
            }
        }
        
        fun stopAnimation() {
            animators?.forEach { it.cancel() }
            animators = null
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
    class ChatMessageDiffCallback : DiffUtil.ItemCallback<Any>() {
        override fun areItemsTheSame(oldItem: Any, newItem: Any): Boolean {
            return when {
                oldItem === ChatMessageAdapter.TYPING_INDICATOR && newItem === ChatMessageAdapter.TYPING_INDICATOR -> true
                oldItem is ChatMessage && newItem is ChatMessage -> oldItem.id == newItem.id
                else -> false
            }
        }

        override fun areContentsTheSame(oldItem: Any, newItem: Any): Boolean {
            return when {
                oldItem === ChatMessageAdapter.TYPING_INDICATOR && newItem === ChatMessageAdapter.TYPING_INDICATOR -> true
                oldItem is ChatMessage && newItem is ChatMessage -> oldItem == newItem
                else -> false
            }
        }
    }
    
    /**
     * Show typing indicator
     */
    fun showTypingIndicator() {
        val currentList = currentList.toMutableList()
        // Remove existing typing indicator if any
        currentList.removeAll { it === TYPING_INDICATOR }
        // Add typing indicator at the end
        currentList.add(TYPING_INDICATOR)
        submitList(currentList)
    }
    
    /**
     * Hide typing indicator
     */
    fun hideTypingIndicator() {
        val currentList = currentList.toMutableList()
        val removed = currentList.removeAll { it === TYPING_INDICATOR }
        if (removed) {
            submitList(currentList)
        }
    }
}
