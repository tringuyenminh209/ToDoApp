package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityStatsBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.StatsViewModel

/**
 * StatsActivity
 * 統計画面 - ユーザーの進捗とパフォーマンスを表示
 * - Task completion stats
 * - Focus time tracking
 * - Streaks and habits
 * - Weekly/Monthly productivity
 */
class StatsActivity : BaseActivity() {

    private lateinit var binding: ActivityStatsBinding
    private lateinit var viewModel: StatsViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityStatsBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[StatsViewModel::class.java]

        setupClickListeners()
        observeViewModel()
        setupBottomNavigation()
    }

    /**
     * クリックリスナーのセットアップ
     */
    private fun setupClickListeners() {
        binding.btnBack.setOnClickListener {
            finish()
        }
    }

    /**
     * ViewModelの監視
     */
    private fun observeViewModel() {
        viewModel.stats.observe(this) { stats ->
            stats?.let {
                updateUI(it)
            } ?: run {
                // Show default/empty state
                showDefaultState()
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            // TODO: Show loading indicator if needed
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
            }
        }
    }

    /**
     * UI を更新
     */
    private fun updateUI(stats: ecccomp.s2240788.mobile_android.data.models.UserStats) {
        // Streak Section (top card)
        binding.tvStreakValue.text = stats.current_streak.toString()
        binding.tvStreakRecord.text = "Kỷ lục: ${stats.longest_streak} ngày"

        // Stats Grid - Row 1
        // Today Focus - Get from monthly_productivity for today
        val todayFocusMinutes = getTodayFocusMinutes(stats)
        binding.tvTodayFocus.text = formatFocusTimeValue(todayFocusMinutes)
        binding.tvTodayChange.text = "%s 昨日比" // Placeholder for comparison

        // Week Focus
        val weekFocusMinutes = stats.weekly_stats.focus_time
        binding.tvWeekFocus.text = formatFocusTimeValue(weekFocusMinutes)
        binding.tvWeekChange.text = "%s 先週比" // Placeholder for comparison

        // Stats Grid - Row 2
        // Tasks Completed (lifetime)
        binding.tvTasksCompleted.text = stats.completed_tasks.toString()
        binding.tvTasksChange.text = "%s 先週比" // Placeholder for comparison

        // Completion Rate
        binding.tvCompletionRate.text = String.format("%.1f%%", stats.completion_rate)
        binding.tvCompletionChange.text = "%s 先週比" // Placeholder for comparison

        // Update Deep Work Chart
        updateDeepWorkChart(stats)
    }

    /**
     * 今日の集中時間を取得
     */
    private fun getTodayFocusMinutes(stats: ecccomp.s2240788.mobile_android.data.models.UserStats): Int {
        val today = java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.getDefault())
            .format(java.util.Date())

        return stats.monthly_productivity
            .firstOrNull { it.date == today }
            ?.focus_minutes ?: 0
    }

    /**
     * Focus Time の値のみをフォーマット (単位なし)
     */
    private fun formatFocusTimeValue(minutes: Int): String {
        val hours = minutes / 60
        val mins = minutes % 60
        return if (hours > 0) {
            "${hours}h ${mins}m"
        } else {
            "${mins}m"
        }
    }

    /**
     * Deep Work Chart を更新
     */
    private fun updateDeepWorkChart(stats: ecccomp.s2240788.mobile_android.data.models.UserStats) {
        // Get last 7 days of data
        val last7Days = getLastSevenDaysData(stats)

        // Bar IDs
        val barIds = listOf(
            ecccomp.s2240788.mobile_android.R.id.bar_1,
            ecccomp.s2240788.mobile_android.R.id.bar_2,
            ecccomp.s2240788.mobile_android.R.id.bar_3,
            ecccomp.s2240788.mobile_android.R.id.bar_4,
            ecccomp.s2240788.mobile_android.R.id.bar_5,
            ecccomp.s2240788.mobile_android.R.id.bar_6,
            ecccomp.s2240788.mobile_android.R.id.bar_7
        )

        // Find max value for scaling
        val maxMinutes = last7Days.maxOrNull() ?: 1
        val maxHeight = 120 // Max height in dp

        // Update each bar
        last7Days.forEachIndexed { index, minutes ->
            val bar = binding.root.findViewById<android.view.View>(barIds[index])
            val barParent = bar.parent as? android.widget.LinearLayout

            // Calculate bar height (scale to max height)
            val heightDp = if (maxMinutes > 0) {
                ((minutes.toFloat() / maxMinutes) * maxHeight).toInt().coerceAtLeast(10)
            } else {
                10
            }

            // Update bar height
            val heightPx = (heightDp * resources.displayMetrics.density).toInt()
            val params = bar.layoutParams
            params.height = heightPx
            bar.layoutParams = params

            // Update value TextView (first child of parent LinearLayout)
            barParent?.let { parent ->
                val valueTextView = parent.getChildAt(0) as? android.widget.TextView
                valueTextView?.text = minutes.toString()
            }
        }
    }

    /**
     * 過去7日間のデータを取得
     */
    private fun getLastSevenDaysData(stats: ecccomp.s2240788.mobile_android.data.models.UserStats): List<Int> {
        val calendar = java.util.Calendar.getInstance()
        val dateFormat = java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.getDefault())
        val last7Days = mutableListOf<Int>()

        // Get last 7 days starting from 6 days ago to today
        for (i in 6 downTo 0) {
            calendar.time = java.util.Date()
            calendar.add(java.util.Calendar.DAY_OF_YEAR, -i)
            val dateStr = dateFormat.format(calendar.time)

            val focusMinutes = stats.monthly_productivity
                .firstOrNull { it.date == dateStr }
                ?.focus_minutes ?: 0

            last7Days.add(focusMinutes)
        }

        return last7Days
    }

    /**
     * デフォルト状態を表示
     */
    private fun showDefaultState() {
        binding.tvStreakValue.text = "0"
        binding.tvStreakRecord.text = "Kỷ lục: 0 ngày"
        binding.tvTodayFocus.text = "0m"
        binding.tvTodayChange.text = "%s 昨日比"
        binding.tvWeekFocus.text = "0m"
        binding.tvWeekChange.text = "%s 先週比"
        binding.tvTasksCompleted.text = "0"
        binding.tvTasksChange.text = "%s 先週比"
        binding.tvCompletionRate.text = "0.0%"
        binding.tvCompletionChange.text = "%s 先週比"
    }

    /**
     * ボトムナビゲーションのセットアップ
     */
    private fun setupBottomNavigation() {
        // StatsActivity is not a main navigation destination, so don't select any item
        binding.bottomNavigation.menu.setGroupCheckable(0, true, false)
        for (i in 0 until binding.bottomNavigation.menu.size()) {
            binding.bottomNavigation.menu.getItem(i).isChecked = false
        }
        binding.bottomNavigation.menu.setGroupCheckable(0, true, true)

        binding.bottomNavigation.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_home -> {
                    startActivity(Intent(this, MainActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_calendar -> {
                    startActivity(Intent(this, CalendarActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_paths -> {
                    startActivity(Intent(this, PathsActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_knowledge -> {
                    startActivity(Intent(this, KnowledgeActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_settings -> {
                    startActivity(Intent(this, SettingsActivity::class.java))
                    finish()
                    true
                }
                else -> false
            }
        }
    }

    override fun onResume() {
        super.onResume()
        viewModel.refreshStats()
    }
}

