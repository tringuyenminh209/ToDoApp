package ecccomp.s2240788.mobile_android.ui.dialogs

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
import com.google.android.material.timepicker.MaterialTimePicker
import com.google.android.material.timepicker.TimeFormat
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.DialogAddClassBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.TimetableViewModel
import java.util.Locale

/**
 * AddClassDialogFragment
 * 時間割に授業を追加するダイアログ
 */
class AddClassDialogFragment : DialogFragment() {

    private var _binding: DialogAddClassBinding? = null
    private val binding get() = _binding!!
    
    private lateinit var viewModel: TimetableViewModel
    
    private var selectedDay: String? = null
    private var selectedPeriod: Int? = null
    private var selectedStartTime: String? = null
    private var selectedEndTime: String? = null
    private var selectedColor: String = "#4F46E5" // Default indigo
    
    companion object {
        private const val ARG_DAY = "day"
        private const val ARG_PERIOD = "period"
        
        fun newInstance(day: Int? = null, period: Int? = null): AddClassDialogFragment {
            return AddClassDialogFragment().apply {
                arguments = Bundle().apply {
                    day?.let { putInt(ARG_DAY, it) }
                    period?.let { putInt(ARG_PERIOD, it) }
                }
            }
        }
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = DialogAddClassBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        
        viewModel = ViewModelProvider(requireActivity())[TimetableViewModel::class.java]
        
        setupUI()
        setupClickListeners()
        setupObservers()
        
        // Pre-fill day and period if provided
        arguments?.let { args ->
            if (args.containsKey(ARG_DAY)) {
                val dayInt = args.getInt(ARG_DAY)
                binding.spinnerDay.setSelection(dayInt)
            }
            if (args.containsKey(ARG_PERIOD)) {
                val period = args.getInt(ARG_PERIOD)
                binding.spinnerPeriod.setSelection(period - 1) // Period 1-10, index 0-9
            }
        }
    }
    
    private fun setupUI() {
        // Setup day spinner
        val days = resources.getStringArray(R.array.days_of_week)
        val dayAdapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, days)
        dayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        binding.spinnerDay.adapter = dayAdapter
        
        // Setup period spinner
        val periods = (1..10).map { getString(R.string.timetable_period_format, it) }.toTypedArray()
        val periodAdapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, periods)
        periodAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        binding.spinnerPeriod.adapter = periodAdapter
        
        // Setup color chips
        setupColorChips()
    }
    
    private fun setupColorChips() {
        // Default selection
        binding.chipColorBlue.isChecked = true
        
        binding.chipGroupColor.setOnCheckedStateChangeListener { _, checkedIds ->
            if (checkedIds.isNotEmpty()) {
                selectedColor = when (checkedIds[0]) {
                    R.id.chip_color_blue -> "#4F46E5"
                    R.id.chip_color_green -> "#10B981"
                    R.id.chip_color_red -> "#EF4444"
                    R.id.chip_color_orange -> "#F59E0B"
                    R.id.chip_color_purple -> "#8B5CF6"
                    else -> "#4F46E5"
                }
            }
        }
    }
    
    private fun setupClickListeners() {
        // Close button
        binding.btnClose.setOnClickListener {
            dismiss()
        }
        
        // Start time picker
        binding.etStartTime.setOnClickListener {
            showTimePicker(true)
        }
        
        // End time picker
        binding.etEndTime.setOnClickListener {
            showTimePicker(false)
        }
        
        // Save button
        binding.btnSave.setOnClickListener {
            saveClass()
        }
        
        // Cancel button
        binding.btnCancel.setOnClickListener {
            dismiss()
        }
    }
    
    private fun setupObservers() {
        viewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            binding.btnSave.isEnabled = !isLoading
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }
        
        viewModel.error.observe(viewLifecycleOwner) { error ->
            error?.let {
                Toast.makeText(requireContext(), it, Toast.LENGTH_LONG).show()
            }
        }
    }
    
    private fun showTimePicker(isStartTime: Boolean) {
        val picker = MaterialTimePicker.Builder()
            .setTimeFormat(TimeFormat.CLOCK_24H)
            .setHour(9)
            .setMinute(0)
            .setTitleText(if (isStartTime) getString(R.string.timetable_select_start_time) else getString(R.string.timetable_select_end_time))
            .build()
        
        picker.addOnPositiveButtonClickListener {
            val hour = picker.hour
            val minute = picker.minute
            val timeString = String.format(Locale.getDefault(), "%02d:%02d", hour, minute)
            
            if (isStartTime) {
                selectedStartTime = timeString
                binding.etStartTime.setText(timeString)
            } else {
                selectedEndTime = timeString
                binding.etEndTime.setText(timeString)
            }
        }
        
        picker.show(parentFragmentManager, "time_picker")
    }
    
    private fun saveClass() {
        // Validate inputs
        val name = binding.etClassName.text.toString().trim()
        if (name.isEmpty()) {
            binding.etClassName.error = getString(R.string.timetable_error_name_required)
            return
        }
        
        if (selectedStartTime == null) {
            Toast.makeText(requireContext(), getString(R.string.timetable_error_start_time_required), Toast.LENGTH_SHORT).show()
            return
        }
        
        if (selectedEndTime == null) {
            Toast.makeText(requireContext(), getString(R.string.timetable_error_end_time_required), Toast.LENGTH_SHORT).show()
            return
        }
        
        // Get selected day
        val dayIndex = binding.spinnerDay.selectedItemPosition
        selectedDay = when (dayIndex) {
            0 -> "sunday"
            1 -> "monday"
            2 -> "tuesday"
            3 -> "wednesday"
            4 -> "thursday"
            5 -> "friday"
            6 -> "saturday"
            else -> "monday"
        }
        
        // Get selected period
        selectedPeriod = binding.spinnerPeriod.selectedItemPosition + 1 // Convert index to period (1-10)
        
        // Get optional fields
        val room = binding.etRoom.text.toString().trim().ifEmpty { null }
        val instructor = binding.etInstructor.text.toString().trim().ifEmpty { null }
        val description = binding.etDescription.text.toString().trim().ifEmpty { null }
        val notes = binding.etNotes.text.toString().trim().ifEmpty { null }
        
        // Create class via ViewModel
        viewModel.createClass(
            name = name,
            description = description,
            room = room,
            instructor = instructor,
            day = selectedDay!!,
            period = selectedPeriod!!,
            startTime = selectedStartTime!!,
            endTime = selectedEndTime!!,
            color = selectedColor,
            notes = notes,
            onSuccess = {
                // Show success message
                Toast.makeText(requireContext(), "授業を追加しました", Toast.LENGTH_SHORT).show()
                // Reload timetable
                viewModel.loadTimetable()
                // Dismiss dialog
                dismiss()
            }
        )
    }
    
    override fun onStart() {
        super.onStart()
        // Make dialog fullscreen width with some margin
        dialog?.window?.setLayout(
            (resources.displayMetrics.widthPixels * 0.95).toInt(),
            ViewGroup.LayoutParams.WRAP_CONTENT
        )
    }
    
    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}

