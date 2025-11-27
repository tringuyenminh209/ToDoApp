package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Notification
import ecccomp.s2240788.mobile_android.databinding.ActivityNotificationBinding
import ecccomp.s2240788.mobile_android.ui.adapters.NotificationAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.NotificationViewModel

class NotificationActivity : BaseActivity() {

    private lateinit var binding: ActivityNotificationBinding
    private lateinit var viewModel: NotificationViewModel
    private lateinit var adapter: NotificationAdapter

    private var currentFilter: NotificationFilter = NotificationFilter.ALL

    enum class NotificationFilter {
        ALL, UNREAD, REMINDER, ACHIEVEMENT
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityNotificationBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()
        setupViewModel()
        setupUI()
        setupObservers()
        setupClickListeners()
        loadData()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[NotificationViewModel::class.java]
    }

    private fun setupUI() {
        // Setup adapter
        adapter = NotificationAdapter(
            onItemClick = { notification ->
                handleNotificationClick(notification)
            },
            onMoreClick = { notification ->
                showNotificationOptions(notification)
            }
        )

        // Setup RecyclerView
        binding.rvNotifications.apply {
            adapter = this@NotificationActivity.adapter
            layoutManager = LinearLayoutManager(this@NotificationActivity)
            setHasFixedSize(false)
        }
    }

    private fun setupObservers() {
        // Notifications list
        viewModel.notifications.observe(this) { notifications ->
            adapter.submitList(notifications)
            binding.emptyState.visibility = if (notifications.isEmpty()) View.VISIBLE else View.GONE
            binding.rvNotifications.visibility = if (notifications.isEmpty()) View.GONE else View.VISIBLE
        }

        // Loading state
        viewModel.isLoading.observe(this) { isLoading ->
            // Could add progress indicator here
        }

        // Error messages
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
            }
        }

        // Success messages
        viewModel.successMessage.observe(this) { message ->
            message?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
            }
        }

        // Unread count
        viewModel.unreadCount.observe(this) { count ->
            // Update badge or UI if needed
        }
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }

        // Mark all read button
        binding.btnMarkAllRead.setOnClickListener {
            viewModel.markAllAsRead()
        }

        // Filter chips
        binding.chipAll.setOnClickListener {
            currentFilter = NotificationFilter.ALL
            applyFilter()
        }

        binding.chipUnread.setOnClickListener {
            currentFilter = NotificationFilter.UNREAD
            applyFilter()
        }

        binding.chipReminder.setOnClickListener {
            currentFilter = NotificationFilter.REMINDER
            applyFilter()
        }

        binding.chipAchievement.setOnClickListener {
            currentFilter = NotificationFilter.ACHIEVEMENT
            applyFilter()
        }
    }

    private fun loadData() {
        viewModel.loadNotifications()
        viewModel.loadUnreadCount()
    }

    private fun applyFilter() {
        when (currentFilter) {
            NotificationFilter.ALL -> viewModel.loadNotifications()
            NotificationFilter.UNREAD -> viewModel.loadNotifications(readFilter = false)
            NotificationFilter.REMINDER -> viewModel.loadNotifications(type = "reminder")
            NotificationFilter.ACHIEVEMENT -> viewModel.loadNotifications(type = "achievement")
        }
    }

    private fun handleNotificationClick(notification: Notification) {
        // Mark as read if unread
        if (!notification.read) {
            viewModel.markAsRead(notification.id)
        }

        // Navigate to task detail if task_id exists
        notification.task_id?.let { taskId ->
            val intent = Intent(this, TaskDetailActivity::class.java)
            intent.putExtra("task_id", taskId)
            startActivity(intent)
        }
    }

    private fun showNotificationOptions(notification: Notification) {
        val options = mutableListOf<String>()

        if (!notification.read) {
            options.add(getString(R.string.mark_as_read))
        }
        options.add(getString(R.string.delete_notification))

        MaterialAlertDialogBuilder(this)
            .setTitle(notification.title)
            .setItems(options.toTypedArray()) { _, which ->
                when (options[which]) {
                    getString(R.string.mark_as_read) -> viewModel.markAsRead(notification.id)
                    getString(R.string.delete_notification) -> {
                        MaterialAlertDialogBuilder(this)
                            .setTitle(getString(R.string.delete_notification_title))
                            .setMessage(getString(R.string.delete_notification_message))
                            .setPositiveButton(getString(R.string.delete)) { _, _ ->
                                viewModel.deleteNotification(notification.id)
                            }
                            .setNegativeButton(getString(R.string.cancel), null)
                            .show()
                    }
                }
            }
            .show()
    }

    override fun onResume() {
        super.onResume()
        // Refresh data when returning to screen
        loadData()
    }
}
