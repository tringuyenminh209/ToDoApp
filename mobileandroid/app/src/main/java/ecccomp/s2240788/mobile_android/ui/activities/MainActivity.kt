package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.databinding.ActivityMainBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.MainViewModel

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var viewModel: MainViewModel

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
    }

    private fun setupUI() {
        // Set initial progress (45%)
        binding.progressRing.progress = 45
    }

    private fun setupClickListeners() {
        // Header actions
        binding.btnLanguage.setOnClickListener {
            Toast.makeText(this, "Language selection", Toast.LENGTH_SHORT).show()
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
        binding.fabFocus.setOnClickListener {
            Toast.makeText(this, "Start focus session", Toast.LENGTH_SHORT).show()
        }
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
}