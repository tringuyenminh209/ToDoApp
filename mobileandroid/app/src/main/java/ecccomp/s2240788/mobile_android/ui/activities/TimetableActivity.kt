package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityTimetableBinding
import ecccomp.s2240788.mobile_android.ui.adapters.StudyAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.TimetableAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.TimetableViewModel

class TimetableActivity : BaseActivity() {

    private lateinit var binding: ActivityTimetableBinding
    private lateinit var viewModel: TimetableViewModel
    private lateinit var timetableAdapter: TimetableAdapter
    private lateinit var studyAdapter: StudyAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityTimetableBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupViewModel()
        setupUI()
        setupObservers()
        setupClickListeners()
        setupBottomNavigation()

        // Load timetable data
        viewModel.loadTimetable()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[TimetableViewModel::class.java]
    }

    private fun setupUI() {
        // Setup Timetable RecyclerView
        timetableAdapter = TimetableAdapter(
            timetableData = emptyMap(),
            onCellClick = { day, period, classModel ->
                if (classModel != null) {
                    // Show edit dialog
                    // TODO: Show edit class dialog
                } else {
                    // Show add dialog
                    // TODO: Show add class dialog
                }
            }
        )

        binding.rvTimetableGrid.apply {
            layoutManager = LinearLayoutManager(this@TimetableActivity)
            adapter = timetableAdapter
        }

        // Setup Study RecyclerView
        studyAdapter = StudyAdapter(
            studies = emptyList(),
            onStudyClick = { study ->
                // TODO: Show edit study dialog
            },
            onCheckboxClick = { study ->
                viewModel.toggleStudyCompletion(study.id)
            }
        )

        binding.rvStudyList.apply {
            layoutManager = LinearLayoutManager(this@TimetableActivity)
            adapter = studyAdapter
        }
    }

    private fun setupObservers() {
        // Observe timetable
        viewModel.timetable.observe(this) { timetable ->
            // Convert classes list to map
            val timetableMap = timetable.classes.associateBy { 
                "${it.day}-${it.period}" 
            }
            
            // Create new adapter with data
            timetableAdapter = TimetableAdapter(
                timetableData = timetableMap,
                onCellClick = { day, period, classModel ->
                    if (classModel != null) {
                        // TODO: Show edit class dialog
                    } else {
                        // TODO: Show add class dialog
                    }
                }
            )
            binding.rvTimetableGrid.adapter = timetableAdapter
        }

        // Observe studies
        viewModel.studies.observe(this) { studies ->
            studyAdapter.updateStudies(studies)
        }

        // Observe current status
        viewModel.currentStatus.observe(this) { status ->
            updateStatusCard(status)
        }

        // Observe current class
        viewModel.currentClass.observe(this) { currentClass ->
            updateCurrentClassUI(currentClass)
        }

        // Observe next class
        viewModel.nextClass.observe(this) { nextClass ->
            updateNextClassUI(nextClass)
        }
    }

    private fun updateStatusCard(status: Any?) {
        // TODO: Update status display when layout is ready
    }

    private fun updateCurrentClassUI(currentClass: ecccomp.s2240788.mobile_android.data.models.ClassModel?) {
        // TODO: Update current class display when layout is ready
        // if (currentClass != null) {
        //     binding.tvCurrentClass.text = currentClass.name
        //     binding.tvCurrentRoom.text = currentClass.room
        // }
    }

    private fun updateNextClassUI(nextClass: ecccomp.s2240788.mobile_android.data.models.ClassModel?) {
        // TODO: Update next class display when layout is ready
        // if (nextClass != null) {
        //     binding.tvNextClass.text = nextClass.name
        // }
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }

        // Add class button
        binding.btnAddClass.setOnClickListener {
            // TODO: Show add class dialog
        }

        // Add study button
        binding.btnAddStudy.setOnClickListener {
            // TODO: Show add study dialog
        }

        // Previous week
        binding.btnPrevWeek.setOnClickListener {
            // TODO: Navigate to previous week
        }

        // Next week
        binding.btnNextWeek.setOnClickListener {
            // TODO: Navigate to next week
        }
    }

    private fun setupBottomNavigation() {
        binding.bottomNavigation.selectedItemId = R.id.nav_home

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
}
