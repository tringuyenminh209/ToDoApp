package ecccomp.s2240788.mobile_android.ui.dialogs

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import ecccomp.s2240788.mobile_android.data.models.TaskSuggestion
import ecccomp.s2240788.mobile_android.databinding.BottomSheetTaskSuggestionBinding
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class TaskSuggestionBottomSheet : BottomSheetDialogFragment() {

    private var _binding: BottomSheetTaskSuggestionBinding? = null
    private val binding get() = _binding!!

    private var suggestion: TaskSuggestion? = null
    private var onConfirm: ((TaskSuggestion) -> Unit)? = null
    private var onDismiss: (() -> Unit)? = null

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = BottomSheetTaskSuggestionBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupUI()
        setupListeners()
    }

    private fun setupUI() {
        suggestion?.let { s ->
            // Set title
            binding.tvTaskTitle.text = s.title

            // Set description
            if (!s.description.isNullOrEmpty()) {
                binding.tvTaskDescription.text = s.description
                binding.tvTaskDescription.visibility = View.VISIBLE
            } else {
                binding.tvTaskDescription.visibility = View.GONE
            }

            // Set estimated time
            if (s.estimated_minutes != null && s.estimated_minutes > 0) {
                binding.chipEstimatedTime.text = "${s.estimated_minutes}分"
                binding.chipEstimatedTime.visibility = View.VISIBLE
            } else {
                binding.chipEstimatedTime.visibility = View.GONE
            }

            // Set priority
            val (priorityText, priorityColor) = when (s.priority.lowercase()) {
                "high" -> Pair("高", "#F44336")
                "medium" -> Pair("中", "#FF9800")
                "low" -> Pair("低", "#4CAF50")
                else -> Pair("中", "#FF9800")
            }
            binding.chipPriority.text = priorityText
            binding.chipPriority.setChipBackgroundColorResource(
                when (s.priority.lowercase()) {
                    "high" -> android.R.color.holo_red_light
                    "medium" -> android.R.color.holo_orange_light
                    "low" -> android.R.color.holo_green_light
                    else -> android.R.color.holo_orange_light
                }
            )

            // Set scheduled time
            if (!s.scheduled_time.isNullOrEmpty()) {
                binding.chipDeadline.text = formatScheduledTime(s.scheduled_time)
                binding.chipDeadline.visibility = View.VISIBLE
            } else {
                binding.chipDeadline.visibility = View.GONE
            }

            // Hide subtasks section (not available in TaskSuggestion model)
            binding.layoutSubtasks.visibility = View.GONE

            // Hide tags section (not available in TaskSuggestion model)
            binding.layoutTags.visibility = View.GONE

            // Set reason
            if (!s.reason.isNullOrEmpty()) {
                binding.tvReason.text = s.reason
            } else {
                binding.tvReason.text = "AIがこのタスクを提案しました。"
            }
        }
    }

    private fun setupListeners() {
        binding.btnClose.setOnClickListener {
            onDismiss?.invoke()
            dismiss()
        }

        binding.btnDismiss.setOnClickListener {
            onDismiss?.invoke()
            dismiss()
        }

        binding.btnConfirm.setOnClickListener {
            suggestion?.let { s ->
                onConfirm?.invoke(s)
            }
            dismiss()
        }
    }

    private fun formatScheduledTime(scheduledTime: String): String {
        return try {
            // Try to parse as datetime "yyyy-MM-dd HH:mm:ss"
            val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
            val outputFormat = SimpleDateFormat("M/d HH:mm", Locale.JAPANESE)
            val date = inputFormat.parse(scheduledTime)
            outputFormat.format(date ?: Date())
        } catch (e: Exception) {
            // If failed, return as is
            scheduledTime
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }

    companion object {
        fun newInstance(
            suggestion: TaskSuggestion,
            onConfirm: (TaskSuggestion) -> Unit,
            onDismiss: () -> Unit
        ): TaskSuggestionBottomSheet {
            return TaskSuggestionBottomSheet().apply {
                this.suggestion = suggestion
                this.onConfirm = onConfirm
                this.onDismiss = onDismiss
            }
        }
    }
}
