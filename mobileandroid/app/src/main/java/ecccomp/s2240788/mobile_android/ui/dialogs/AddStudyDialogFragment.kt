package ecccomp.s2240788.mobile_android.ui.dialogs

import android.app.DatePickerDialog
import android.app.Dialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.fragment.app.DialogFragment
import androidx.lifecycle.ViewModelProvider
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.DialogAddStudyBinding
import ecccomp.s2240788.mobile_android.data.models.CreateTimetableStudyRequest
import ecccomp.s2240788.mobile_android.data.models.TimetableStudy
import ecccomp.s2240788.mobile_android.ui.viewmodels.TimetableViewModel
import java.text.SimpleDateFormat
import java.util.*

/**
 * Dialog for adding/editing study items (homework, review, exam)
 * 宿題・復習・試験を追加・編集するダイアログ
 */
class AddStudyDialogFragment : DialogFragment() {

    private var _binding: DialogAddStudyBinding? = null
    private val binding get() = _binding!!

    private lateinit var viewModel: TimetableViewModel

    private var editStudy: TimetableStudy? = null
    private var selectedDueDate: String? = null
    private val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
    private val displayDateFormat = SimpleDateFormat("MM月dd日 (E)", Locale.JAPANESE)

    companion object {
        private const val ARG_STUDY = "study"

        fun newInstance(study: TimetableStudy? = null): AddStudyDialogFragment {
            val fragment = AddStudyDialogFragment()
            val args = Bundle()
            study?.let {
                // Manually serialize study data
                args.putInt("study_id", it.id)
                args.putString("study_title", it.title)
                args.putString("study_type", it.type)
                args.putString("study_subject", it.subject)
                args.putString("study_due_date", it.dueDate)
                args.putInt("study_priority", it.priority)
                args.putString("study_status", it.status)
                args.putString("study_description", it.description)
            }
            fragment.arguments = args
            return fragment
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        // Reconstruct TimetableStudy from arguments
        arguments?.let { args ->
            if (args.containsKey("study_id")) {
                editStudy = TimetableStudy(
                    id = args.getInt("study_id"),
                    userId = 0,
                    timetableClassId = null,
                    title = args.getString("study_title") ?: "",
                    description = args.getString("study_description"),
                    type = args.getString("study_type") ?: "homework",
                    subject = args.getString("study_subject"),
                    dueDate = args.getString("study_due_date"),
                    priority = args.getInt("study_priority", 3),
                    status = args.getString("study_status") ?: "pending",
                    completedAt = null,
                    taskId = null,
                    createdAt = "",
                    updatedAt = ""
                )
            }
        }
        viewModel = ViewModelProvider(requireActivity())[TimetableViewModel::class.java]
    }

    override fun onCreateDialog(savedInstanceState: Bundle?): Dialog {
        _binding = DialogAddStudyBinding.inflate(layoutInflater)

        setupUI()
        setupClickListeners()

        // Fill data if editing
        editStudy?.let { fillStudyData(it) }

        return MaterialAlertDialogBuilder(requireContext())
            .setView(binding.root)
            .create()
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        return binding.root
    }

    private fun setupUI() {
        // Set dialog title
        binding.tvDialogTitle.text = if (editStudy != null) {
            getString(R.string.timetable_edit_study_title)
        } else {
            getString(R.string.timetable_add_study_title)
        }

        // Setup Type Spinner
        val types = arrayOf(
            getString(R.string.timetable_study_type_homework),
            getString(R.string.timetable_study_type_review),
            getString(R.string.timetable_study_type_exam),
            getString(R.string.timetable_study_type_project)
        )
        val typeAdapter = ArrayAdapter(requireContext(), android.R.layout.simple_dropdown_item_1line, types)
        binding.spinnerType.setAdapter(typeAdapter)
        binding.spinnerType.setText(types[0], false)

        // Setup Priority Spinner
        val priorities = arrayOf("低", "中", "高", "緊急")
        val priorityAdapter = ArrayAdapter(requireContext(), android.R.layout.simple_dropdown_item_1line, priorities)
        binding.spinnerPriority.setAdapter(priorityAdapter)
        binding.spinnerPriority.setText(priorities[1], false) // Default: 中

        // Show delete button only in edit mode
        binding.btnDelete.visibility = if (editStudy != null) View.VISIBLE else View.GONE
    }

    private fun setupClickListeners() {
        // Due Date Picker
        binding.etStudyDueDate.setOnClickListener {
            showDatePicker()
        }

        // Save Button
        binding.btnSave.setOnClickListener {
            saveStudy()
        }

        // Cancel Button
        binding.btnCancel.setOnClickListener {
            dismiss()
        }

        // Delete Button
        binding.btnDelete.setOnClickListener {
            deleteStudy()
        }
    }

    private fun showDatePicker() {
        val calendar = Calendar.getInstance()

        // Set initial date if exists
        selectedDueDate?.let {
            try {
                calendar.time = dateFormat.parse(it) ?: Date()
            } catch (e: Exception) {
                // Use current date
            }
        }

        val datePickerDialog = DatePickerDialog(
            requireContext(),
            { _, year, month, dayOfMonth ->
                calendar.set(year, month, dayOfMonth)
                selectedDueDate = dateFormat.format(calendar.time)
                binding.etStudyDueDate.setText(displayDateFormat.format(calendar.time))
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        )

        datePickerDialog.show()
    }

    private fun fillStudyData(study: TimetableStudy) {
        binding.etStudyTitle.setText(study.title)

        // Set type
        val typeIndex = when (study.type.lowercase()) {
            "homework" -> 0
            "review" -> 1
            "exam" -> 2
            "project" -> 3
            else -> 0
        }
        val types = arrayOf(
            getString(R.string.timetable_study_type_homework),
            getString(R.string.timetable_study_type_review),
            getString(R.string.timetable_study_type_exam),
            getString(R.string.timetable_study_type_project)
        )
        binding.spinnerType.setText(types[typeIndex], false)

        // Set subject
        study.subject?.let {
            binding.etStudySubject.setText(it)
        }

        // Set due date
        study.dueDate?.let {
            selectedDueDate = it
            try {
                val date = dateFormat.parse(it)
                date?.let { d ->
                    binding.etStudyDueDate.setText(displayDateFormat.format(d))
                }
            } catch (e: Exception) {
                binding.etStudyDueDate.setText(it)
            }
        }

        // Set priority (1-5 scale mapped to display)
        val priorityIndex = when (study.priority) {
            1 -> 0  // 低
            2 -> 1  // 中
            3 -> 1  // 中 (default)
            4 -> 2  // 高
            5 -> 3  // 緊急
            else -> 1  // Default to 中
        }
        val priorities = arrayOf("低", "中", "高", "緊急")
        binding.spinnerPriority.setText(priorities[priorityIndex], false)

        // Set description
        study.description?.let {
            binding.etStudyDescription.setText(it)
        }
    }

    private fun saveStudy() {
        val title = binding.etStudyTitle.text.toString().trim()

        // Validation
        if (title.isEmpty()) {
            binding.tilStudyTitle.error = "タイトルを入力してください"
            return
        }

        if (selectedDueDate == null) {
            binding.tilStudyDueDate.error = "提出期限を選択してください"
            return
        }

        // Clear errors
        binding.tilStudyTitle.error = null
        binding.tilStudyDueDate.error = null

        // Get type
        val typeText = binding.spinnerType.text.toString()
        val type = when (typeText) {
            getString(R.string.timetable_study_type_homework) -> "homework"
            getString(R.string.timetable_study_type_review) -> "review"
            getString(R.string.timetable_study_type_exam) -> "exam"
            getString(R.string.timetable_study_type_project) -> "project"
            else -> "homework"
        }

        // Get priority (map display to 1-5 scale)
        val priorityText = binding.spinnerPriority.text.toString()
        val priority = when (priorityText) {
            "低" -> 1
            "中" -> 3
            "高" -> 4
            "緊急" -> 5
            else -> 3  // Default to medium
        }

        val subject = binding.etStudySubject.text.toString().trim()
        val description = binding.etStudyDescription.text.toString().trim()

        // Create request
        val request = CreateTimetableStudyRequest(
            timetableClassId = editStudy?.timetableClassId,
            title = title,
            type = type,
            dueDate = selectedDueDate!!,
            priority = priority,
            description = if (description.isNotEmpty()) description else null,
            status = editStudy?.status ?: "pending"
        )

        if (editStudy != null) {
            // Update existing study
            viewModel.updateStudy(editStudy!!.id, request) {
                Toast.makeText(requireContext(), "宿題を更新しました", Toast.LENGTH_SHORT).show()
                dismiss()
            }
        } else {
            // Create new study
            viewModel.addStudy(request) {
                Toast.makeText(requireContext(), "宿題を追加しました", Toast.LENGTH_SHORT).show()
                dismiss()
            }
        }
    }

    private fun deleteStudy() {
        editStudy?.let { study ->
            MaterialAlertDialogBuilder(requireContext())
                .setTitle("削除確認")
                .setMessage("この宿題を削除しますか？")
                .setPositiveButton("削除") { _, _ ->
                    viewModel.deleteStudy(study.id) {
                        Toast.makeText(requireContext(), "宿題を削除しました", Toast.LENGTH_SHORT).show()
                        dismiss()
                    }
                }
                .setNegativeButton("キャンセル", null)
                .show()
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
