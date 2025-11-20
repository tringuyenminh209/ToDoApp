package ecccomp.s2240788.mobile_android.ui.dialogs

import android.app.Dialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.DialogFragment
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.DialogEditWeeklyContentBinding
import ecccomp.s2240788.mobile_android.data.models.TimetableClass
import ecccomp.s2240788.mobile_android.ui.viewmodels.TimetableViewModel

/**
 * Dialog Fragment for editing weekly content of a class
 * 授業の週別内容を編集するダイアログ
 */
class EditWeeklyContentDialogFragment : DialogFragment() {
    
    private var _binding: DialogEditWeeklyContentBinding? = null
    private val binding get() = _binding!!
    
    private lateinit var viewModel: TimetableViewModel
    private var timetableClass: TimetableClass? = null
    
    companion object {
        private const val ARG_CLASS = "class"
        
        fun newInstance(timetableClass: TimetableClass): EditWeeklyContentDialogFragment {
            return EditWeeklyContentDialogFragment().apply {
                arguments = Bundle().apply {
                    putSerializable(ARG_CLASS, timetableClass as java.io.Serializable)
                }
            }
        }
    }
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        @Suppress("DEPRECATION")
        timetableClass = arguments?.getSerializable(ARG_CLASS) as? TimetableClass
    }
    
    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = DialogEditWeeklyContentBinding.inflate(inflater, container, false)
        return binding.root
    }
    
    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        
        // Get ViewModel from parent activity
        viewModel = ViewModelProvider(requireActivity())[TimetableViewModel::class.java]
        
        setupUI()
        setupClickListeners()
        loadWeeklyContent()
    }
    
    private fun setupUI() {
        val classData = timetableClass ?: return

        // Set class name
        binding.tvClassName.text = classData.name

        // Set week info
        binding.tvWeekInfo.text = viewModel.getCurrentWeekInfo()

        // Pre-fill room and instructor from class data
        binding.etRoom.setText(classData.room ?: "")
        binding.etInstructor.setText(classData.instructor ?: "")
    }
    
    private fun setupClickListeners() {
        binding.btnCancel.setOnClickListener {
            dismiss()
        }
        
        binding.btnSave.setOnClickListener {
            saveWeeklyContent()
        }
    }
    
    private fun loadWeeklyContent() {
        val classData = timetableClass ?: return
        
        // Load existing weekly content if available
        viewModel.getWeeklyContent(classData.id) { weeklyContent ->
            if (weeklyContent != null) {
                binding.etWeeklyTitle.setText(weeklyContent.title)
                binding.etContent.setText(weeklyContent.content)
                binding.etHomework.setText(weeklyContent.homework)
                binding.etNotes.setText(weeklyContent.notes)
            } else {
                // If no weekly content exists, pre-fill with class info
                binding.etWeeklyTitle.setText(classData.name)
            }
        }
    }
    
    private fun saveWeeklyContent() {
        val classData = timetableClass ?: return

        val room = binding.etRoom.text?.toString()?.trim()
        val instructor = binding.etInstructor.text?.toString()?.trim()
        val title = binding.etWeeklyTitle.text?.toString()?.trim()
        val content = binding.etContent.text?.toString()?.trim()
        val homework = binding.etHomework.text?.toString()?.trim()
        val notes = binding.etNotes.text?.toString()?.trim()

        // First, update class info (room & instructor) if changed
        if (room != classData.room || instructor != classData.instructor) {
            val updateRequest = ecccomp.s2240788.mobile_android.data.models.CreateTimetableClassRequest(
                name = classData.name,
                description = classData.description,
                room = room,
                instructor = instructor,
                day = classData.day,
                period = classData.period,
                startTime = classData.startTime,
                endTime = classData.endTime,
                color = classData.color,
                icon = classData.icon
            )

            viewModel.updateClass(classData.id, updateRequest) {
                // After updating class, update weekly content
                saveWeeklyContentOnly(classData.id, title, content, homework, notes)
            }
        } else {
            // No class info changes, just update weekly content
            saveWeeklyContentOnly(classData.id, title, content, homework, notes)
        }
    }

    private fun saveWeeklyContentOnly(
        classId: Int,
        title: String?,
        content: String?,
        homework: String?,
        notes: String?
    ) {
        viewModel.updateWeeklyContent(
            classId = classId,
            title = title,
            content = content,
            homework = homework,
            notes = notes,
            onSuccess = {
                Toast.makeText(requireContext(), "週別内容を保存しました", Toast.LENGTH_SHORT).show()
                dismiss()
            }
        )
    }
    
    override fun onStart() {
        super.onStart()
        dialog?.window?.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )
    }
    
    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}

