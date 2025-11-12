package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.activity.viewModels
import com.google.android.material.datepicker.MaterialDatePicker
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityCreateLearningPathBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.CreateLearningPathViewModel
import java.text.SimpleDateFormat
import java.util.*

/**
 * CreateLearningPathActivity
 * 手動で学習パスを作成する画面
 */
class CreateLearningPathActivity : BaseActivity() {

    private lateinit var binding: ActivityCreateLearningPathBinding
    private val viewModel: CreateLearningPathViewModel by viewModels()
    
    private var selectedStartDate: Date? = null
    private var selectedEndDate: Date? = null
    private val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCreateLearningPathBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        setupToolbar()
        setupGoalTypeSpinner()
        setupClickListeners()
        observeViewModel()
    }

    private fun setupToolbar() {
        // Header card với back button thay vì toolbar
        binding.btnBack.setOnClickListener {
            finish()
        }
    }

    private fun setupGoalTypeSpinner() {
        val goalTypes = listOf(
            "スキル習得" to "skill",
            "キャリア開発" to "career",
            "資格取得" to "certification",
            "趣味・興味" to "hobby"
        )
        
        val adapter = ArrayAdapter(
            this,
            android.R.layout.simple_spinner_item,
            goalTypes.map { it.first }
        )
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        binding.spinnerGoalType.adapter = adapter
    }

    private fun setupClickListeners() {
        // Start Date Picker
        binding.etStartDate.setOnClickListener {
            showDatePicker(isStartDate = true)
        }

        // End Date Picker
        binding.etEndDate.setOnClickListener {
            showDatePicker(isStartDate = false)
        }

        // Create Button
        binding.btnCreate.setOnClickListener {
            validateAndCreate()
        }

        // Cancel Button
        binding.btnCancel.setOnClickListener {
            finish()
        }
    }

    private fun showDatePicker(isStartDate: Boolean) {
        val datePicker = MaterialDatePicker.Builder.datePicker()
            .setTitleText(if (isStartDate) "開始日を選択" else "目標完了日を選択")
            .setSelection(MaterialDatePicker.todayInUtcMilliseconds())
            .build()

        datePicker.addOnPositiveButtonClickListener { selection ->
            val date = Date(selection)
            if (isStartDate) {
                selectedStartDate = date
                binding.etStartDate.setText(dateFormat.format(date))
            } else {
                selectedEndDate = date
                binding.etEndDate.setText(dateFormat.format(date))
            }
        }

        datePicker.show(supportFragmentManager, "DATE_PICKER")
    }

    private fun validateAndCreate() {
        val title = binding.etTitle.text.toString().trim()
        val description = binding.etDescription.text.toString().trim()
        val goalTypePosition = binding.spinnerGoalType.selectedItemPosition
        val estimatedHours = binding.etEstimatedHours.text.toString().toIntOrNull()

        // Validation
        if (title.isEmpty()) {
            binding.etTitle.error = "タイトルを入力してください"
            return
        }

        val goalTypes = listOf("skill", "career", "certification", "hobby")
        val goalType = goalTypes[goalTypePosition]

        // Create Learning Path
        viewModel.createLearningPath(
            title = title,
            description = description.ifEmpty { null },
            goalType = goalType,
            targetStartDate = selectedStartDate?.let { dateFormat.format(it) },
            targetEndDate = selectedEndDate?.let { dateFormat.format(it) },
            estimatedHoursTotal = estimatedHours
        )
    }

    private fun observeViewModel() {
        // Loading State
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnCreate.isEnabled = !isLoading
        }

        // Success
        viewModel.createdLearningPath.observe(this) { learningPath ->
            learningPath?.let {
                Toast.makeText(this, "ロードマップを作成しました！", Toast.LENGTH_LONG).show()
                setResult(RESULT_OK)
                finish()
            }
        }

        // Error
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearError()
            }
        }
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }
}

