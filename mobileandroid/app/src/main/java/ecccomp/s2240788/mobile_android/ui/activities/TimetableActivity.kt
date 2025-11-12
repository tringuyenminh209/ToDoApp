package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.GestureDetector
import android.view.MotionEvent
import androidx.core.view.GestureDetectorCompat
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityTimetableBinding
import ecccomp.s2240788.mobile_android.data.models.toClassModel
import ecccomp.s2240788.mobile_android.data.models.toStudyModel
import ecccomp.s2240788.mobile_android.ui.adapters.StudyAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.TimetableAdapter
import ecccomp.s2240788.mobile_android.ui.dialogs.AddClassDialogFragment
import ecccomp.s2240788.mobile_android.ui.dialogs.EditWeeklyContentDialogFragment
import ecccomp.s2240788.mobile_android.ui.viewmodels.TimetableViewModel
import android.widget.Toast
import kotlin.math.abs

class TimetableActivity : BaseActivity() {

    private lateinit var binding: ActivityTimetableBinding
    private lateinit var viewModel: TimetableViewModel
    private lateinit var timetableAdapter: TimetableAdapter
    private lateinit var studyAdapter: StudyAdapter
    private lateinit var gestureDetector: GestureDetectorCompat

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityTimetableBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        setupViewModel()
        setupUI()
        setupObservers()
        setupClickListeners()
        setupSwipeGesture()
        setupBottomNavigation()

        // Load timetable data
        viewModel.loadTimetable()
    }

    override fun onResume() {
        super.onResume()
        // Reload data when returning to activity
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
                    Toast.makeText(this, "Edit: ${classModel.name}", Toast.LENGTH_SHORT).show()
                } else {
                    // Show add dialog
                    showAddClassDialog(day, period)
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
                viewModel.toggleStudyCompletion(study.id.toInt())
            }
        )

        binding.rvStudyList.apply {
            layoutManager = LinearLayoutManager(this@TimetableActivity)
            adapter = studyAdapter
        }
    }

    private fun setupObservers() {
        // Observe classes
        viewModel.classes.observe(this) { classes ->
            // Convert classes list to map (need to convert TimetableClass to ClassModel)
            val timetableMap = classes.associate { timetableClass ->
                // Map day string to int
                val dayInt = when (timetableClass.day.lowercase()) {
                    "sunday" -> 0
                    "monday" -> 1
                    "tuesday" -> 2
                    "wednesday" -> 3
                    "thursday" -> 4
                    "friday" -> 5
                    "saturday" -> 6
                    else -> 0
                }
                "${dayInt}-${timetableClass.period}" to timetableClass.toClassModel()
            }
            
            // Create new adapter with data
            timetableAdapter = TimetableAdapter(
                timetableData = timetableMap,
                onCellClick = { day, period, classModel ->
                    if (classModel != null) {
                        // Find the TimetableClass from the classes list
                        val timetableClass = classes.find { 
                            val dayInt = when (it.day.lowercase()) {
                                "sunday" -> 0
                                "monday" -> 1
                                "tuesday" -> 2
                                "wednesday" -> 3
                                "thursday" -> 4
                                "friday" -> 5
                                "saturday" -> 6
                                else -> 0
                            }
                            dayInt == day && it.period == period
                        }
                        
                        if (timetableClass != null) {
                            showEditWeeklyContentDialog(timetableClass)
                        }
                    } else {
                        // Show add class dialog
                        showAddClassDialog(day, period)
                    }
                }
            )
            binding.rvTimetableGrid.adapter = timetableAdapter
        }

        // Observe studies
        viewModel.studies.observe(this) { studies ->
            val studyModels = studies.map { it.toStudyModel() }
            studyAdapter.updateStudies(studyModels)
        }

        // Observe current status
        viewModel.currentStatus.observe(this) { status ->
            updateStatusCard(status)
        }

        // Observe current class
        viewModel.currentClass.observe(this) { currentClass ->
            updateCurrentClassUI(currentClass?.toClassModel())
        }

        // Observe next class
        viewModel.nextClass.observe(this) { nextClass ->
            updateNextClassUI(nextClass?.toClassModel())
        }
        
        // Observe week info
        viewModel.weekInfo.observe(this) { weekInfo ->
            updateWeekInfoDisplay(weekInfo)
        }
    }

    private fun updateStatusCard(status: Any?) {
        // Update status display if view exists
        // TODO: Add status views to layout when ready
    }

    private fun updateCurrentClassUI(currentClass: ecccomp.s2240788.mobile_android.data.models.ClassModel?) {
        // Update current class UI if views exist
        // TODO: Add current class views to layout when ready
    }

    private fun updateNextClassUI(nextClass: ecccomp.s2240788.mobile_android.data.models.ClassModel?) {
        // Update next class UI if views exist
        // TODO: Add next class views to layout when ready
    }

    private fun updateWeekDisplay() {
        val weekInfo = viewModel.getCurrentWeekInfo()
        updateWeekInfoDisplay(weekInfo)
    }
    
    private fun updateWeekInfoDisplay(weekInfo: String) {
        // Split weekInfo into title and date range
        val lines = weekInfo.split("\n")
        if (lines.size >= 2) {
            binding.tvWeekTitle.text = lines[0]
            binding.tvWeekDates.text = lines[1]
        } else {
            binding.tvWeekTitle.text = weekInfo
        }
    }
    
    private fun setupSwipeGesture() {
        gestureDetector = GestureDetectorCompat(this, object : GestureDetector.SimpleOnGestureListener() {
            private val SWIPE_THRESHOLD = 100
            private val SWIPE_VELOCITY_THRESHOLD = 100
            
            override fun onFling(
                e1: MotionEvent?,
                e2: MotionEvent,
                velocityX: Float,
                velocityY: Float
            ): Boolean {
                if (e1 == null) return false
                
                val diffX = e2.x - e1.x
                val diffY = e2.y - e1.y
                
                if (abs(diffX) > abs(diffY)) {
                    if (abs(diffX) > SWIPE_THRESHOLD && abs(velocityX) > SWIPE_VELOCITY_THRESHOLD) {
                        if (diffX > 0) {
                            // Swipe right - previous week
                            viewModel.navigateToPreviousWeek()
                        } else {
                            // Swipe left - next week
                            viewModel.navigateToNextWeek()
                        }
                        return true
                    }
                }
                return false
            }
        })
        
        // Apply gesture detector to timetable card
        binding.timetableGridCard.setOnTouchListener { _, event ->
            gestureDetector.onTouchEvent(event)
            false
        }
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }

        // Add class button
        binding.btnAddClass.setOnClickListener {
            showAddClassDialog()
        }

        // Add study button
        binding.btnAddStudy.setOnClickListener {
            // TODO: Show add study dialog
        }

        // Previous week
        binding.btnPrevWeek.setOnClickListener {
            viewModel.navigateToPreviousWeek()
            updateWeekDisplay()
        }

        // Next week
        binding.btnNextWeek.setOnClickListener {
            viewModel.navigateToNextWeek()
            updateWeekDisplay()
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
    
    /**
     * Show add class dialog
     * 授業追加ダイアログを表示
     */
    private fun showAddClassDialog(day: Int? = null, period: Int? = null) {
        val dialog = AddClassDialogFragment.newInstance(day, period)
        dialog.show(supportFragmentManager, "AddClassDialog")
    }
    
    private fun showEditWeeklyContentDialog(timetableClass: ecccomp.s2240788.mobile_android.data.models.TimetableClass) {
        val dialog = EditWeeklyContentDialogFragment.newInstance(timetableClass)
        dialog.show(supportFragmentManager, "EditWeeklyContentDialog")
    }
}
