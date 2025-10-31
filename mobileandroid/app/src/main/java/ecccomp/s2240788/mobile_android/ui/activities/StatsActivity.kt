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
        // Today Focus (placeholder - would need daily stats from API)
        // binding.tvTodayFocus.text = "0"
        
        // Week Focus
        binding.tvWeekFocus.text = formatFocusTime(stats.weekly_stats.focus_time)

        // Stats Grid - Row 2
        // Tasks Completed
        binding.tvTasksCompleted.text = stats.completed_tasks.toString()
        
        // Completion Rate
        binding.tvCompletionRate.text = String.format("%.1f%%", stats.completion_rate)

        // TODO: Update other sections as needed (charts, heatmap, etc.)
        // The layout has many more TextViews for detailed statistics
        // that would require additional API endpoints or calculations
    }

    /**
     * デフォルト状態を表示
     */
    private fun showDefaultState() {
        binding.tvStreakValue.text = "0"
        binding.tvStreakRecord.text = "Kỷ lục: 0 ngày"
        binding.tvWeekFocus.text = "0m"
        binding.tvTasksCompleted.text = "0"
        binding.tvCompletionRate.text = "0%"
    }

    /**
     * Focus Time をフォーマット
     */
    private fun formatFocusTime(minutes: Int): String {
        val hours = minutes / 60
        val mins = minutes % 60
        return if (hours > 0) {
            "${hours}h ${mins}m"
        } else {
            "${mins}m"
        }
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

