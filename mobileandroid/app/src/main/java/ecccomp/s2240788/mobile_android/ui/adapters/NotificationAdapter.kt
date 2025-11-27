package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Notification
import ecccomp.s2240788.mobile_android.databinding.ItemNotificationBinding
import java.text.SimpleDateFormat
import java.util.Locale
import java.util.Date
import java.util.concurrent.TimeUnit

/**
 * NotificationAdapter
 * Adapter for notifications list with DiffUtil and ViewBinding
 * Follows app's standard adapter pattern
 */
class NotificationAdapter(
    private val onItemClick: (Notification) -> Unit,
    private val onMoreClick: (Notification) -> Unit
) : ListAdapter<Notification, NotificationAdapter.ViewHolder>(NotificationDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemNotificationBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(
        private val binding: ItemNotificationBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(notification: Notification) {
            binding.apply {
                // Title and message
                tvNotificationTitle.text = notification.title
                tvNotificationMessage.text = notification.message

                // Type badge and icon
                val (icon, badgeText, backgroundColor) = when (notification.type) {
                    "reminder" -> Triple(
                        R.drawable.ic_clock,
                        root.context.getString(R.string.notification_type_reminder),
                        R.color.warning_light
                    )
                    "achievement" -> Triple(
                        R.drawable.ic_star,
                        root.context.getString(R.string.notification_type_achievement),
                        R.color.success_light
                    )
                    "motivational" -> Triple(
                        R.drawable.ic_heart,
                        root.context.getString(R.string.notification_type_motivational),
                        R.color.info_light
                    )
                    else -> Triple(
                        R.drawable.ic_info,
                        root.context.getString(R.string.notification_type_system),
                        R.color.accent_light
                    )
                }

                ivNotificationIcon.setImageResource(icon)
                tvNotificationType.text = badgeText
                tvNotificationType.setBackgroundResource(backgroundColor)

                // Unread indicator
                unreadIndicator.visibility = if (notification.read) View.GONE else View.VISIBLE

                // Read/unread visual state
                if (notification.read) {
                    notificationCard.alpha = 0.7f
                } else {
                    notificationCard.alpha = 1.0f
                }

                // Time ago
                tvNotificationTime.text = getTimeAgo(notification.created_at)

                // Click listeners
                root.setOnClickListener { onItemClick(notification) }
                btnMore.setOnClickListener { onMoreClick(notification) }
            }
        }

        private fun getTimeAgo(timestamp: String): String {
            return try {
                val context = binding.root.context
                val sdf = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS'Z'", Locale.getDefault())
                val date = sdf.parse(timestamp) ?: return context.getString(R.string.time_ago_recently)
                val now = Date()
                val diff = now.time - date.time

                when {
                    diff < TimeUnit.MINUTES.toMillis(1) -> context.getString(R.string.time_ago_just_now)
                    diff < TimeUnit.HOURS.toMillis(1) -> {
                        val minutes = TimeUnit.MILLISECONDS.toMinutes(diff)
                        context.getString(R.string.time_ago_minutes_ago, minutes.toInt())
                    }
                    diff < TimeUnit.DAYS.toMillis(1) -> {
                        val hours = TimeUnit.MILLISECONDS.toHours(diff)
                        context.getString(R.string.time_ago_hours_ago, hours.toInt())
                    }
                    diff < TimeUnit.DAYS.toMillis(7) -> {
                        val days = TimeUnit.MILLISECONDS.toDays(diff)
                        context.getString(R.string.time_ago_days_ago, days.toInt())
                    }
                    else -> {
                        val displayFormat = SimpleDateFormat("MMM dd", Locale.getDefault())
                        displayFormat.format(date)
                    }
                }
            } catch (e: Exception) {
                binding.root.context.getString(R.string.time_ago_recently)
            }
        }
    }

    class NotificationDiffCallback : DiffUtil.ItemCallback<Notification>() {
        override fun areItemsTheSame(oldItem: Notification, newItem: Notification): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: Notification, newItem: Notification): Boolean {
            return oldItem == newItem
        }
    }
}
