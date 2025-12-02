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

        viewModel.goldenTimeData.observe(this) { goldenTimeData ->
            goldenTimeData?.let {
                updateGoldenTimeHeatmap(it)
            } ?: run {
                android.util.Log.w("StatsActivity", "Golden time data is null")
            }
        }
    }

    /**
     * UI を更新
     */
    private fun updateUI(stats: ecccomp.s2240788.mobile_android.data.models.UserStats) {
        android.util.Log.d("StatsActivity", "updateUI called with stats: " +
            "total_tasks=${stats.total_tasks}, " +
            "completed_tasks=${stats.completed_tasks}, " +
            "completion_rate=${stats.completion_rate}, " +
            "total_focus_time=${stats.total_focus_time}, " +
            "weekly_focus_time=${stats.weekly_stats.focus_time}")

        // Streak Section (top card)
        binding.tvStreakValue.text = stats.current_streak.toString()
        binding.tvStreakRecord.text = getString(R.string.streak_record, stats.longest_streak)

        // Stats Grid - Row 1
        // Today Focus - Get from monthly_productivity for today
        val todayFocusMinutes = getTodayFocusMinutes(stats)
        android.util.Log.d("StatsActivity", "Today focus minutes: $todayFocusMinutes")
        binding.tvTodayFocus.text = formatFocusTimeValue(todayFocusMinutes)
        binding.tvTodayChange.text = getString(R.string.compared_to_yesterday, "-")

        // Week Focus
        val weekFocusMinutes = stats.weekly_stats.focus_time
        android.util.Log.d("StatsActivity", "Week focus minutes: $weekFocusMinutes")
        binding.tvWeekFocus.text = formatFocusTimeValue(weekFocusMinutes)
        binding.tvWeekChange.text = getString(R.string.compared_to_last_week, "-")

        // Stats Grid - Row 2
        // Tasks Completed (lifetime)
        binding.tvTasksCompleted.text = stats.completed_tasks.toString()
        binding.tvTasksChange.text = getString(R.string.compared_to_last_week, "-")

        // Completion Rate
        binding.tvCompletionRate.text = String.format("%.1f%%", stats.completion_rate)
        binding.tvCompletionChange.text = getString(R.string.compared_to_last_week, "-")

        // Set Deep Work title with day count
        binding.tvDeepWorkTitle.text = getString(R.string.deep_work_past_days, 7)

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
     * 今週（月曜〜日曜）の曜日別データを取得
     * bar_1=T2(月), bar_2=T3(火), bar_3=T4(水), bar_4=T5(木), bar_5=T6(金), bar_6=T7(土), bar_7=CN(日)
     */
    private fun getLastSevenDaysData(stats: ecccomp.s2240788.mobile_android.data.models.UserStats): List<Int> {
        val calendar = java.util.Calendar.getInstance()
        val dateFormat = java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.getDefault())
        val weekData = mutableListOf<Int>()

        android.util.Log.d("StatsActivity", "Getting this week data (Mon-Sun) from monthly_productivity")

        // Set first day of week to Monday
        calendar.firstDayOfWeek = java.util.Calendar.MONDAY
        
        // Get Monday of this week
        calendar.time = java.util.Date()
        val currentDayOfWeek = calendar.get(java.util.Calendar.DAY_OF_WEEK)
        
        // Calculate days to subtract to get to Monday
        val daysFromMonday = when(currentDayOfWeek) {
            java.util.Calendar.SUNDAY -> 6      // Sunday is 6 days after Monday
            java.util.Calendar.MONDAY -> 0
            java.util.Calendar.TUESDAY -> 1
            java.util.Calendar.WEDNESDAY -> 2
            java.util.Calendar.THURSDAY -> 3
            java.util.Calendar.FRIDAY -> 4
            java.util.Calendar.SATURDAY -> 5
            else -> 0
        }
        
        calendar.add(java.util.Calendar.DAY_OF_YEAR, -daysFromMonday)
        android.util.Log.d("StatsActivity", "This week starts on: ${dateFormat.format(calendar.time)}")
        
        // Get data for Monday to Sunday (7 days)
        for (i in 0 until 7) {
            val dateStr = dateFormat.format(calendar.time)
            val dayOfWeek = calendar.get(java.util.Calendar.DAY_OF_WEEK)
            
            val focusMinutes = stats.monthly_productivity
                .firstOrNull { it.date == dateStr }
                ?.focus_minutes ?: 0

            val dayName = when(dayOfWeek) {
                java.util.Calendar.MONDAY -> "Mon(T2)"
                java.util.Calendar.TUESDAY -> "Tue(T3)"
                java.util.Calendar.WEDNESDAY -> "Wed(T4)"
                java.util.Calendar.THURSDAY -> "Thu(T5)"
                java.util.Calendar.FRIDAY -> "Fri(T6)"
                java.util.Calendar.SATURDAY -> "Sat(T7)"
                java.util.Calendar.SUNDAY -> "Sun(CN)"
                else -> "Day$i"
            }
            
            android.util.Log.d("StatsActivity", "$dayName ($dateStr): $focusMinutes minutes")
            weekData.add(focusMinutes)
            
            // Move to next day
            calendar.add(java.util.Calendar.DAY_OF_YEAR, 1)
        }

        android.util.Log.d("StatsActivity", "This week data (Mon-Sun): $weekData")
        return weekData
    }

    /**
     * ゴールデンタイムヒートマップを更新
     */
    private fun updateGoldenTimeHeatmap(goldenTimeData: ecccomp.s2240788.mobile_android.data.models.GoldenTimeData) {
        android.util.Log.d("StatsActivity", "Updating golden time heatmap: ${goldenTimeData.heatmap.size} rows x ${goldenTimeData.days} cols, max_minutes=${goldenTimeData.max_minutes}")

        val heatmapContainer = binding.heatmapContainer
        
        // Get all row LinearLayouts (skip the header row at index 0)
        val rows = mutableListOf<android.view.ViewGroup>()
        for (i in 1 until heatmapContainer.childCount) {
            val child = heatmapContainer.getChildAt(i)
            if (child is android.view.ViewGroup) {
                rows.add(child)
            }
        }

        android.util.Log.d("StatsActivity", "Found ${rows.size} rows in heatmap container")

        // Update each cell based on intensity
        goldenTimeData.heatmap.forEachIndexed { rowIndex, rowData ->
            if (rowIndex < rows.size) {
                val row = rows[rowIndex]
                rowData.forEachIndexed { colIndex, cellData ->
                    if (colIndex < row.childCount) {
                        val cell = row.getChildAt(colIndex) as? com.google.android.material.card.MaterialCardView
                        cell?.let {
                            // Set background color based on intensity
                            val colorRes = when (cellData.intensity) {
                                0 -> R.color.heatmap_level_0  // #E0E0E0 (no activity)
                                1 -> R.color.heatmap_level_1  // #C8E6C9 (light)
                                2 -> R.color.heatmap_level_2  // #81C784 (medium)
                                3 -> R.color.heatmap_level_3  // #4CAF50 (heavy)
                                4 -> R.color.heatmap_level_4  // #2E7D32 (very heavy)
                                else -> R.color.heatmap_level_0
                            }
                            it.setCardBackgroundColor(androidx.core.content.ContextCompat.getColor(this, colorRes))
                            
                            if (cellData.minutes > 0) {
                                android.util.Log.d("StatsActivity", "Cell[$rowIndex][$colIndex]: ${cellData.minutes}m, intensity=${cellData.intensity}")
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * デフォルト状態を表示
     */
    private fun showDefaultState() {
        binding.tvStreakValue.text = "0"
        binding.tvStreakRecord.text = getString(R.string.streak_record, 0)
        binding.tvTodayFocus.text = "0m"
        binding.tvTodayChange.text = getString(R.string.compared_to_yesterday, "-")
        binding.tvWeekFocus.text = "0m"
        binding.tvWeekChange.text = getString(R.string.compared_to_last_week, "-")
        binding.tvTasksCompleted.text = "0"
        binding.tvTasksChange.text = getString(R.string.compared_to_last_week, "-")
        binding.tvCompletionRate.text = "0.0%"
        binding.tvCompletionChange.text = getString(R.string.compared_to_last_week, "-")

        // Set Deep Work title with day count
        binding.tvDeepWorkTitle.text = getString(R.string.deep_work_past_days, 7)
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

