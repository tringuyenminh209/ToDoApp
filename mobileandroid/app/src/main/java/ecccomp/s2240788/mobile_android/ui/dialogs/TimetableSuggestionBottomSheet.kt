package ecccomp.s2240788.mobile_android.ui.dialogs

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import ecccomp.s2240788.mobile_android.data.models.TimetableClassSuggestion
import ecccomp.s2240788.mobile_android.databinding.BottomSheetTimetableSuggestionBinding

class TimetableSuggestionBottomSheet : BottomSheetDialogFragment() {

    private var _binding: BottomSheetTimetableSuggestionBinding? = null
    private val binding get() = _binding!!

    private var suggestion: TimetableClassSuggestion? = null
    private var onConfirm: ((TimetableClassSuggestion) -> Unit)? = null
    private var onCancel: (() -> Unit)? = null

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = BottomSheetTimetableSuggestionBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupUI()
        setupListeners()
    }

    private fun setupUI() {
        suggestion?.let { s ->
            // Set class name
            binding.tvClassName.text = s.name

            // Set day and time
            val dayNameMap = mapOf(
                "monday" to "月曜日",
                "tuesday" to "火曜日",
                "wednesday" to "水曜日",
                "thursday" to "木曜日",
                "friday" to "金曜日",
                "saturday" to "土曜日",
                "sunday" to "日曜日"
            )
            val dayJapanese = dayNameMap[s.day] ?: s.day
            binding.tvDayTime.text = "$dayJapanese ${s.start_time} - ${s.end_time}"

            // Set period
            binding.tvPeriod.text = "第${s.period}時限"

            // Set room (optional)
            if (!s.room.isNullOrEmpty()) {
                binding.layoutRoom.visibility = View.VISIBLE
                binding.tvRoom.text = "教室: ${s.room}"
            } else {
                binding.layoutRoom.visibility = View.GONE
            }

            // Set instructor (optional)
            if (!s.instructor.isNullOrEmpty()) {
                binding.layoutInstructor.visibility = View.VISIBLE
                binding.tvInstructor.text = "教員: ${s.instructor}"
            } else {
                binding.layoutInstructor.visibility = View.GONE
            }

            // Set description (optional)
            if (!s.description.isNullOrEmpty()) {
                binding.tvDescription.visibility = View.VISIBLE
                binding.tvDescription.text = s.description
            } else {
                binding.tvDescription.visibility = View.GONE
            }
        }
    }

    private fun setupListeners() {
        binding.btnClose.setOnClickListener {
            onCancel?.invoke()
            dismiss()
        }

        binding.btnCancel.setOnClickListener {
            onCancel?.invoke()
            dismiss()
        }

        binding.btnConfirm.setOnClickListener {
            suggestion?.let { s ->
                onConfirm?.invoke(s)
            }
            dismiss()
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }

    companion object {
        fun newInstance(
            suggestion: TimetableClassSuggestion,
            onConfirm: (TimetableClassSuggestion) -> Unit,
            onCancel: () -> Unit
        ): TimetableSuggestionBottomSheet {
            return TimetableSuggestionBottomSheet().apply {
                this.suggestion = suggestion
                this.onConfirm = onConfirm
                this.onCancel = onCancel
            }
        }
    }
}
