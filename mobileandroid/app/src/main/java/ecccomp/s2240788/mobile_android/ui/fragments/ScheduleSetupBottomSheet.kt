package ecccomp.s2240788.mobile_android.ui.fragments

import android.app.TimePickerDialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.SeekBar
import androidx.fragment.app.viewModels
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.DayItemHelper
import ecccomp.s2240788.mobile_android.data.models.StudyScheduleInput
import ecccomp.s2240788.mobile_android.databinding.BottomSheetScheduleSetupBinding
import ecccomp.s2240788.mobile_android.ui.adapters.DaySelectionAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.StudyScheduleViewModel

/**
 * BottomSheet for setting up study schedule
 * スケジュール設定ボトムシート
 *
 * Usage:
 * val dialog = ScheduleSetupBottomSheet()
 * dialog.setOnConfirmListener { schedules ->
 *     // Use the schedules for roadmap import
 * }
 * dialog.show(supportFragmentManager, "schedule_setup")
 */
class ScheduleSetupBottomSheet : BottomSheetDialogFragment() {

    private var _binding: BottomSheetScheduleSetupBinding? = null
    private val binding get() = _binding!!

    private val viewModel: StudyScheduleViewModel by viewModels()
    private lateinit var dayAdapter: DaySelectionAdapter

    private var onConfirmListener: ((List<StudyScheduleInput>) -> Unit)? = null

    // Duration mapping: seekbar position to minutes
    private val durationMap = listOf(15, 30, 45, 60, 90, 120, 180, 240, 360, 480)

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = BottomSheetScheduleSetupBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupDaySelection()
        setupTimeSelection()
        setupDurationSelection()
        setupListeners()
        observeViewModel()
        updateSummary()
    }

    private fun setupDaySelection() {
        val days = DayItemHelper.getAllDays()
        dayAdapter = DaySelectionAdapter(days) { dayItem ->
            viewModel.toggleDay(dayItem.dayOfWeek)
        }

        // Set GridLayoutManager with 4 columns
        binding.rvDays.layoutManager = androidx.recyclerview.widget.GridLayoutManager(
            requireContext(),
            4 // 4 columns for grid layout
        )
        binding.rvDays.adapter = dayAdapter
    }

    private fun setupTimeSelection() {
        binding.tvSelectedTime.text = viewModel.selectedTime.value ?: "19:30"

        binding.btnSelectTime.setOnClickListener {
            showTimePicker()
        }
    }

    private fun showTimePicker() {
        val currentTime = viewModel.selectedTime.value ?: "19:30"
        val parts = currentTime.split(":")
        val hour = parts[0].toIntOrNull() ?: 19
        val minute = parts[1].toIntOrNull() ?: 30

        val timePicker = TimePickerDialog(
            requireContext(),
            { _, selectedHour, selectedMinute ->
                val time = String.format("%02d:%02d", selectedHour, selectedMinute)
                viewModel.setStudyTime(time)
            },
            hour,
            minute,
            true // 24-hour format
        )

        timePicker.show()
    }

    private fun setupDurationSelection() {
        // Set initial value (60 minutes = index 3)
        binding.seekbarDuration.progress = 3
        binding.tvDuration.text = "60 phút"

        binding.seekbarDuration.setOnSeekBarChangeListener(object : SeekBar.OnSeekBarChangeListener {
            override fun onProgressChanged(seekBar: SeekBar?, progress: Int, fromUser: Boolean) {
                val minutes = durationMap.getOrElse(progress) { 60 }
                binding.tvDuration.text = "$minutes phút"
                viewModel.setDuration(minutes)
            }

            override fun onStartTrackingTouch(seekBar: SeekBar?) {}
            override fun onStopTrackingTouch(seekBar: SeekBar?) {}
        })
    }

    private fun setupListeners() {
        binding.btnClose.setOnClickListener {
            dismiss()
        }

        binding.btnConfirm.setOnClickListener {
            handleConfirm()
        }
    }

    private fun handleConfirm() {
        // Validate
        val errorMessage = viewModel.validateSchedules()
        if (errorMessage != null) {
            // Show error
            com.google.android.material.snackbar.Snackbar.make(
                binding.root,
                errorMessage,
                com.google.android.material.snackbar.Snackbar.LENGTH_SHORT
            ).show()
            return
        }

        // Get schedule inputs
        val schedules = viewModel.getScheduleInputs()

        // Call listener
        onConfirmListener?.invoke(schedules)

        // Dismiss
        dismiss()
    }

    private fun observeViewModel() {
        viewModel.selectedDays.observe(viewLifecycleOwner) { _ ->
            updateSummary()
        }

        viewModel.selectedTime.observe(viewLifecycleOwner) { time ->
            binding.tvSelectedTime.text = time
            updateSummary()
        }

        viewModel.durationMinutes.observe(viewLifecycleOwner) { _ ->
            updateSummary()
        }
    }

    private fun updateSummary() {
        binding.tvSummary.text = viewModel.getSummaryText()
    }

    /**
     * Set listener for confirm button
     */
    fun setOnConfirmListener(listener: (List<StudyScheduleInput>) -> Unit) {
        this.onConfirmListener = listener
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }

    companion object {
        fun newInstance(): ScheduleSetupBottomSheet {
            return ScheduleSetupBottomSheet()
        }
    }
}
