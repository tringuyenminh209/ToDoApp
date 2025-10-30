package ecccomp.s2240788.mobile_android.ui.activities

import android.app.DatePickerDialog
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.databinding.ActivityEditTaskBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.EditTaskViewModel
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale

/**
 * EditTaskActivity
 * タスク編集画面
 */
class EditTaskActivity : AppCompatActivity() {

    private lateinit var binding: ActivityEditTaskBinding
    private lateinit var viewModel: EditTaskViewModel
    private var taskId: Int = -1
    private var selectedPriority = "medium"
    private var selectedEnergy = "medium"
    private var selectedDeadline: String? = null
    private var calendar: Calendar = Calendar.getInstance()

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

        setupClickListeners()
        setupObservers()

        // Load task data
        viewModel.loadTask(taskId)
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack?.setOnClickListener { finish() }

        // Priority selection (chips)
        binding.chipPriorityHigh?.setOnClickListener {
            selectedPriority = "high"
            updatePrioritySelection()
        }

        binding.chipPriorityMedium?.setOnClickListener {
            selectedPriority = "medium"
            updatePrioritySelection()
        }

        binding.chipPriorityLow?.setOnClickListener {
            selectedPriority = "low"
            updatePrioritySelection()
        }

        // Energy selection
        binding.chipEnergyHigh?.setOnClickListener { selectedEnergy = "high" }
        binding.chipEnergyMedium?.setOnClickListener { selectedEnergy = "medium" }
        binding.chipEnergyLow?.setOnClickListener { selectedEnergy = "low" }

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

                // Map priority Int to string
                selectedPriority = when (it.priority) {
                    4, 5 -> "high"
                    1, 2 -> "low"
                    else -> "medium"
                }
                updatePrioritySelection()

                // Energy level
                selectedEnergy = it.energy_level
                when (selectedEnergy) {
                    "high" -> binding.chipEnergyHigh?.isChecked = true
                    "low" -> binding.chipEnergyLow?.isChecked = true
                    else -> binding.chipEnergyMedium?.isChecked = true
                }

                // Estimated minutes
                it.estimated_minutes?.let { mins ->
                    binding.etEstimatedTime?.setText(mins.toString())
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
        // Ensure single selection among chips
        val high = binding.chipPriorityHigh
        val medium = binding.chipPriorityMedium
        val low = binding.chipPriorityLow

        when (selectedPriority) {
            "high" -> {
                high?.isChecked = true
                medium?.isChecked = false
                low?.isChecked = false
            }
            "low" -> {
                high?.isChecked = false
                medium?.isChecked = false
                low?.isChecked = true
            }
            else -> {
                high?.isChecked = false
                medium?.isChecked = true
                low?.isChecked = false
            }
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

        var estimated: Int? = null
        try {
            val valStr = binding.etEstimatedTime?.text?.toString()?.trim() ?: ""
            if (valStr.isNotEmpty()) {
                var base = valStr.toInt()
                val unitIndex = binding.spinnerTimeUnit?.selectedItemPosition ?: 0
                if (unitIndex == 1) base *= 60
                estimated = base
            }
        } catch (e: Exception) { }

        viewModel.updateTask(taskId, title, description, selectedPriority, selectedDeadline, selectedEnergy, estimated)
    }

    private fun showError(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    }
}
