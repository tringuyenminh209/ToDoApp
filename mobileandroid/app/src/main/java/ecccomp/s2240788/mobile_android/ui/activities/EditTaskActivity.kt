package ecccomp.s2240788.mobile_android.ui.activities

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.databinding.ActivityEditTaskBinding
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskInput
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskInputAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.EditTaskViewModel
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale
import java.util.UUID

/**
 * EditTaskActivity
 * タスク編集画面
 */
class EditTaskActivity : BaseActivity() {

    private lateinit var binding: ActivityEditTaskBinding
    private lateinit var viewModel: EditTaskViewModel
    private var taskId: Int = -1
    private var selectedPriority = 3 // Default: medium (1-5)
    private var selectedEnergy = "medium"
    private var selectedCategory = "study" // Default: study
    private var selectedDeadline: String? = null
    private var calendar: Calendar = Calendar.getInstance()
    private lateinit var subtaskAdapter: SubtaskInputAdapter
    private val subtasks = mutableListOf<SubtaskInput>()

    // Deep Work Mode fields
    private var requiresDeepFocus = false
    private var allowInterruptions = true
    private var focusDifficulty = 3 // Default: medium (1-5)
    private var warmupMinutes: Int? = null
    private var cooldownMinutes: Int? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityEditTaskBinding.inflate(layoutInflater)
        setContentView(binding.root)

        taskId = intent.getIntExtra("task_id", -1)
        if (taskId == -1) {
            showError("タスクIDが無効です")
            finish()
            return
        }

        viewModel = ViewModelProvider(this)[EditTaskViewModel::class.java]

        setupSubtaskRecyclerView()
        setupClickListeners()
        setupObservers()
        setupDeepWorkMode()

        // Load task data
        viewModel.loadTask(taskId)
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack?.setOnClickListener { finish() }

        // Priority selection (1-5)
        binding.chipPriority1?.setOnClickListener {
            selectedPriority = 1
            updatePrioritySelection()
        }

        binding.chipPriority2?.setOnClickListener {
            selectedPriority = 2
            updatePrioritySelection()
        }

        binding.chipPriority3?.setOnClickListener {
            selectedPriority = 3
            updatePrioritySelection()
        }

        binding.chipPriority4?.setOnClickListener {
            selectedPriority = 4
            updatePrioritySelection()
        }

        binding.chipPriority5?.setOnClickListener {
            selectedPriority = 5
            updatePrioritySelection()
        }

        // Energy selection
        binding.chipEnergyHigh?.setOnClickListener { selectedEnergy = "high" }
        binding.chipEnergyMedium?.setOnClickListener { selectedEnergy = "medium" }
        binding.chipEnergyLow?.setOnClickListener { selectedEnergy = "low" }

        // Category/Type selection
        binding.chipTypeStudy?.setOnClickListener { selectedCategory = "study" }
        binding.chipTypeWork?.setOnClickListener { selectedCategory = "work" }

        // Deadline quick buttons
        binding.btnToday?.setOnClickListener {
            calendar = Calendar.getInstance()
            updateDeadlineDisplay()
        }
        binding.btnTomorrow?.setOnClickListener {
            calendar = Calendar.getInstance(); calendar.add(Calendar.DAY_OF_MONTH, 1)
            updateDeadlineDisplay()
        }
        binding.btnNextWeek?.setOnClickListener {
            calendar = Calendar.getInstance(); calendar.add(Calendar.DAY_OF_MONTH, 7)
            updateDeadlineDisplay()
        }

        // Deadline input - show date picker
        binding.etDeadline?.setOnClickListener { showDatePicker() }

        // Time quick select buttons
        binding.btnTime15?.setOnClickListener {
            binding.etHours?.setText("0")
            binding.etMinutes?.setText("15")
        }

        binding.btnTime30?.setOnClickListener {
            binding.etHours?.setText("0")
            binding.etMinutes?.setText("30")
        }

        binding.btnTime60?.setOnClickListener {
            binding.etHours?.setText("1")
            binding.etMinutes?.setText("0")
        }

        binding.btnTime120?.setOnClickListener {
            binding.etHours?.setText("2")
            binding.etMinutes?.setText("0")
        }

        // Add subtask button
        binding.btnAddSubtask?.setOnClickListener {
            addNewSubtask()
        }

        // Save button
        binding.btnSave?.setOnClickListener {
            if (validateInputs()) {
                updateTask()
            }
        }
    }

    private fun setupObservers() {
        // Task data loaded
        viewModel.task.observe(this) { task ->
            task?.let {
                binding.etTaskTitle?.setText(it.title)
                binding.etTaskDescription?.setText(it.description)

                // Category
                selectedCategory = it.category ?: "study"
                when (selectedCategory) {
                    "work" -> binding.chipTypeWork?.isChecked = true
                    else -> binding.chipTypeStudy?.isChecked = true
                }

                // Priority (1-5)
                selectedPriority = it.priority.coerceIn(1, 5)
                updatePrioritySelection()

                // Energy level
                selectedEnergy = it.energy_level
                when (selectedEnergy) {
                    "high" -> binding.chipEnergyHigh?.isChecked = true
                    "low" -> binding.chipEnergyLow?.isChecked = true
                    else -> binding.chipEnergyMedium?.isChecked = true
                }

                // Estimated minutes - convert to hours and minutes
                it.estimated_minutes?.let { mins ->
                    val hours = mins / 60
                    val minutes = mins % 60
                    if (hours > 0) {
                        binding.etHours?.setText(hours.toString())
                    }
                    if (minutes > 0) {
                        binding.etMinutes?.setText(minutes.toString())
                    }
                }

                // Deadline
                it.deadline?.let { dateStr ->
                    try {
                        val inFmt = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                        val outFmt = SimpleDateFormat("yyyy/MM/dd", Locale.getDefault())
                        val date = inFmt.parse(dateStr)
                        binding.etDeadline?.setText(if (date != null) outFmt.format(date) else dateStr)
                        selectedDeadline = dateStr
                    } catch (e: Exception) {
                        binding.etDeadline?.setText(dateStr)
                        selectedDeadline = dateStr
                    }
                }

                // Load subtasks
                it.subtasks?.let { taskSubtasks ->
                    subtasks.clear()
                    taskSubtasks.forEach { subtask ->
                        subtasks.add(
                            SubtaskInput(
                                id = subtask.id.toString(),
                                title = subtask.title,
                                estimatedMinutes = subtask.estimated_minutes
                            )
                        )
                    }
                    subtaskAdapter.submitList(subtasks.toList())
                    updateEmptyState()
                }

                // Load Deep Work fields
                requiresDeepFocus = it.requires_deep_focus
                allowInterruptions = it.allow_interruptions
                focusDifficulty = it.focus_difficulty.coerceIn(1, 5)
                warmupMinutes = it.warmup_minutes
                cooldownMinutes = it.cooldown_minutes

                // Update UI
                binding.switchDeepWork?.isChecked = requiresDeepFocus
                binding.sliderFocusDifficulty?.value = focusDifficulty.toFloat()
                binding.etWarmup?.setText(warmupMinutes?.toString() ?: "")
                binding.etCooldown?.setText(cooldownMinutes?.toString() ?: "")
            }
        }

        // Loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.btnSave?.isEnabled = !isLoading
        }

        // Error handling
        viewModel.error.observe(this) { error ->
            error?.let {
                showError(it)
                viewModel.clearError()
            }
        }

        // Update success
        viewModel.taskUpdated.observe(this) { success ->
            if (success) {
                Toast.makeText(this, "タスクを更新しました！", Toast.LENGTH_SHORT).show()
                finish()
            }
        }
    }

    private fun updatePrioritySelection() {
        // Reset all chips
        binding.chipPriority1?.isChecked = false
        binding.chipPriority2?.isChecked = false
        binding.chipPriority3?.isChecked = false
        binding.chipPriority4?.isChecked = false
        binding.chipPriority5?.isChecked = false

        // Set selected chip
        when (selectedPriority) {
            1 -> binding.chipPriority1?.isChecked = true
            2 -> binding.chipPriority2?.isChecked = true
            3 -> binding.chipPriority3?.isChecked = true
            4 -> binding.chipPriority4?.isChecked = true
            5 -> binding.chipPriority5?.isChecked = true
        }
    }

    private fun showDatePicker() {
        val dlg = DatePickerDialog(
            this,
            { _, year, month, day ->
                calendar.set(Calendar.YEAR, year)
                calendar.set(Calendar.MONTH, month)
                calendar.set(Calendar.DAY_OF_MONTH, day)
                updateDeadlineDisplay()
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        )
        dlg.show()
    }

    private fun updateDeadlineDisplay() {
        val viewFmt = SimpleDateFormat("yyyy/MM/dd", Locale.getDefault())
        binding.etDeadline?.setText(viewFmt.format(calendar.time))
        val apiFmt = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        selectedDeadline = apiFmt.format(calendar.time)
    }

    private fun validateInputs(): Boolean {
        val title = binding.etTaskTitle?.text?.toString()?.trim() ?: ""

        if (title.isEmpty()) {
            binding.tilTaskTitle?.error = "タイトルは必須です"
            return false
        }

        binding.tilTaskTitle?.error = null
        return true
    }

    private fun updateTask() {
        val title = binding.etTaskTitle?.text?.toString()?.trim() ?: ""
        val description = binding.etTaskDescription?.text?.toString()?.trim() ?: ""

        // Calculate estimated minutes from hours and minutes inputs
        var estimated: Int? = null
        try {
            val hoursStr = binding.etHours?.text?.toString()?.trim() ?: ""
            val minsStr = binding.etMinutes?.text?.toString()?.trim() ?: ""
            
            val hours = if (hoursStr.isNotEmpty()) hoursStr.toInt() else 0
            val mins = if (minsStr.isNotEmpty()) minsStr.toInt() else 0
            
            if (hours > 0 || mins > 0) {
                estimated = hours * 60 + mins
            }
        } catch (e: Exception) { }

        // Get warmup/cooldown times
        try {
            val warmupStr = binding.etWarmup?.text?.toString()?.trim() ?: ""
            warmupMinutes = if (warmupStr.isEmpty()) null else warmupStr.toIntOrNull()

            val cooldownStr = binding.etCooldown?.text?.toString()?.trim() ?: ""
            cooldownMinutes = if (cooldownStr.isEmpty()) null else cooldownStr.toIntOrNull()
        } catch (e: Exception) {
            // Keep existing values
        }

        // Get subtasks from adapter
        val currentSubtasks = getSubtasks()

        viewModel.updateTask(
            taskId,
            title,
            description,
            selectedPriority,
            selectedDeadline,
            selectedEnergy,
            estimated,
            selectedCategory,
            currentSubtasks,
            requiresDeepFocus,
            allowInterruptions,
            focusDifficulty,
            warmupMinutes,
            cooldownMinutes
        )
    }

    private fun showError(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    }

    private fun setupSubtaskRecyclerView() {
        subtaskAdapter = SubtaskInputAdapter(this::removeSubtask)
        binding.rvSubtasks?.layoutManager = LinearLayoutManager(this)
        binding.rvSubtasks?.adapter = subtaskAdapter

        // Show/hide empty state
        updateEmptyState()
    }

    private fun addNewSubtask() {
        val newSubtask = SubtaskInput(
            id = UUID.randomUUID().toString(),
            title = "",
            estimatedMinutes = null
        )
        subtasks.add(newSubtask)
        subtaskAdapter.submitList(subtasks.toList())
        updateEmptyState()

        // Scroll to new subtask
        binding.rvSubtasks?.post {
            binding.rvSubtasks?.smoothScrollToPosition(subtasks.size - 1)
        }
    }

    private fun removeSubtask(subtask: SubtaskInput) {
        subtasks.remove(subtask)
        subtaskAdapter.submitList(subtasks.toList())
        updateEmptyState()
        Toast.makeText(this, "サブタスクを削除しました", Toast.LENGTH_SHORT).show()
    }

    private fun updateEmptyState() {
        if (subtasks.isEmpty()) {
            binding.emptySubtasks?.visibility = View.VISIBLE
            binding.rvSubtasks?.visibility = View.GONE
        } else {
            binding.emptySubtasks?.visibility = View.GONE
            binding.rvSubtasks?.visibility = View.VISIBLE
        }
    }

    private fun getSubtasks(): List<SubtaskInput> {
        return subtaskAdapter.getSubtasks()
    }

    private fun setupDeepWorkMode() {
        // Deep Work Mode toggle
        binding.switchDeepWork?.setOnCheckedChangeListener { _, isChecked ->
            requiresDeepFocus = isChecked
            allowInterruptions = !isChecked // Inverse logic

            if (isChecked) {
                // Auto-set focus difficulty to 4 when deep work enabled
                binding.sliderFocusDifficulty?.value = 4f
                focusDifficulty = 4

                // Suggest warmup/cooldown times if empty
                if (binding.etWarmup?.text?.toString()?.trim().isNullOrEmpty()) {
                    binding.etWarmup?.setText("5")
                    warmupMinutes = 5
                }
                if (binding.etCooldown?.text?.toString()?.trim().isNullOrEmpty()) {
                    binding.etCooldown?.setText("10")
                    cooldownMinutes = 10
                }
            }
        }

        // Focus Difficulty slider (1-5)
        binding.sliderFocusDifficulty?.addOnChangeListener { _, value, _ ->
            focusDifficulty = value.toInt()
        }

        // Set default value
        binding.sliderFocusDifficulty?.value = 3f
    }
}
