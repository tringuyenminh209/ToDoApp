package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.databinding.ActivityMainBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.LogoutViewModel
import ecccomp.s2240788.mobile_android.ui.viewmodels.MainViewModel

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var viewModel: MainViewModel
    private lateinit var logoutViewModel: LogoutViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupViewModel()
        setupUI()
        setupClickListeners()
        setupObservers()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[MainViewModel::class.java]
        logoutViewModel = ViewModelProvider(this)[LogoutViewModel::class.java]
        observeLogoutViewModel()
    }

    private fun setupUI() {
        // Set initial progress (45%)
        binding.progressRing.progress = 45
    }

    private fun setupClickListeners() {
        // Header actions
        binding.btnLanguage.setOnClickListener {
            // Long press to show logout option
            showLogoutDialog()
        }

        binding.btnNotification.setOnClickListener {
            Toast.makeText(this, "Notifications", Toast.LENGTH_SHORT).show()
        }

        binding.btnAddTask.setOnClickListener {
            Toast.makeText(this, "Add task", Toast.LENGTH_SHORT).show()
        }

        // Daily actions
        binding.btnCheckin.setOnClickListener {
            Toast.makeText(this, "Daily check-in", Toast.LENGTH_SHORT).show()
        }

        binding.btnReview.setOnClickListener {
            Toast.makeText(this, "Daily review", Toast.LENGTH_SHORT).show()
        }

        binding.btnAiCoach.setOnClickListener {
            Toast.makeText(this, "AI Coach", Toast.LENGTH_SHORT).show()
        }

        // Quick actions
        binding.btnAiArrange.setOnClickListener {
            Toast.makeText(this, "AI Auto-arrange", Toast.LENGTH_SHORT).show()
        }

        binding.btnStart5min.setOnClickListener {
            Toast.makeText(this, "Start 5-min task", Toast.LENGTH_SHORT).show()
        }

        binding.btnReschedule.setOnClickListener {
            Toast.makeText(this, "Reschedule tasks", Toast.LENGTH_SHORT).show()
        }

        // Progress links
        binding.btnViewStats.setOnClickListener {
            Toast.makeText(this, "View statistics", Toast.LENGTH_SHORT).show()
        }

        binding.btnViewTimetable.setOnClickListener {
            Toast.makeText(this, "View timetable", Toast.LENGTH_SHORT).show()
        }

        // FAB
//        binding.fabFocus.setOnClickListener {
//            Toast.makeText(this, "Start focus session", Toast.LENGTH_SHORT).show()
//        }
    }

    private fun setupObservers() {
        viewModel.error.observe(this) { error ->
            if (error != null) {
                Toast.makeText(this, error, Toast.LENGTH_LONG).show()
            }
        }

        viewModel.tasks.observe(this) { tasks ->
            if (tasks != null) {
                Toast.makeText(this, "Got ${tasks.size} tasks!", Toast.LENGTH_SHORT).show()
                // TODO: Update RecyclerView with tasks
            }
        }
    }

    /**
     * ログアウト処理のObserverを設定
     */
    private fun observeLogoutViewModel() {
        logoutViewModel.logoutSuccess.observe(this) { success ->
            if (success) {
                // LoginActivityに遷移（バックスタックをクリア）
                val intent = Intent(this, LoginActivity::class.java).apply {
                    flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                }
                startActivity(intent)
                finish()
            }
        }

        logoutViewModel.isLoading.observe(this) { isLoading ->
            // ローディング表示は必要に応じて実装
        }

        logoutViewModel.error.observe(this) { error ->
            if (error != null) {
                Toast.makeText(this, error, Toast.LENGTH_SHORT).show()
            }
        }
    }

    /**
     * ログアウト確認ダイアログを表示
     */
    private fun showLogoutDialog() {
        AlertDialog.Builder(this)
            .setTitle("ログアウト")
            .setMessage("ログアウトしますか？")
            .setPositiveButton("はい") { _, _ ->
                logoutViewModel.logout()
            }
            .setNegativeButton("いいえ", null)
            .show()
    }
}