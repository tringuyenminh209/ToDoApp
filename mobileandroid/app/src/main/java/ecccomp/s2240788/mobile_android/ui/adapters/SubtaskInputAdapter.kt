package ecccomp.s2240788.mobile_android.ui.adapters

import android.text.Editable
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.databinding.ItemSubtaskInputBinding

/**
 * Data class for subtask input
 */
data class SubtaskInput(
    val id: String = "", // Temporary ID for local management
    var title: String = "",
    var estimatedMinutes: Int? = null
)

/**
 * Adapter for managing subtask inputs in Add/Edit Task screens
 */
class SubtaskInputAdapter(
    private val onRemove: (SubtaskInput) -> Unit
) : ListAdapter<SubtaskInput, SubtaskInputAdapter.SubtaskViewHolder>(SubtaskDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): SubtaskViewHolder {
        val binding = ItemSubtaskInputBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return SubtaskViewHolder(binding, onRemove)
    }

    override fun onBindViewHolder(holder: SubtaskViewHolder, position: Int) {
        holder.bind(getItem(position), position + 1)
    }

    /**
     * Get all subtasks with their current input values
     */
    fun getSubtasks(): List<SubtaskInput> {
        return currentList.toList()
    }

    class SubtaskViewHolder(
        private val binding: ItemSubtaskInputBinding,
        private val onRemove: (SubtaskInput) -> Unit
    ) : RecyclerView.ViewHolder(binding.root) {

        private var currentSubtask: SubtaskInput? = null
        private var titleTextWatcher: TextWatcher? = null
        private var timeTextWatcher: TextWatcher? = null

        init {
            // Remove button
            binding.btnRemove.setOnClickListener {
                currentSubtask?.let { onRemove(it) }
            }

            // Quick time buttons
            binding.btnTime5.setOnClickListener {
                binding.etTime.setText("5")
                currentSubtask?.estimatedMinutes = 5
            }

            binding.btnTime10.setOnClickListener {
                binding.etTime.setText("10")
                currentSubtask?.estimatedMinutes = 10
            }

            binding.btnTime15.setOnClickListener {
                binding.etTime.setText("15")
                currentSubtask?.estimatedMinutes = 15
            }
        }

        fun bind(subtask: SubtaskInput, number: Int) {
            currentSubtask = subtask

            // Set subtask number
            binding.tvSubtaskNumber.text = number.toString()

            // Remove old text watchers
            titleTextWatcher?.let { binding.etSubtask.removeTextChangedListener(it) }
            timeTextWatcher?.let { binding.etTime.removeTextChangedListener(it) }

            // Set title (only if different to avoid cursor jumping)
            val currentText = binding.etSubtask.text?.toString() ?: ""
            if (currentText != subtask.title) {
                binding.etSubtask.setText(subtask.title)
            }

            // Set time
            val currentTime = binding.etTime.text?.toString() ?: ""
            val subtaskTime = subtask.estimatedMinutes?.toString() ?: ""
            if (currentTime != subtaskTime) {
                binding.etTime.setText(subtaskTime)
            }

            // Add new text watchers
            titleTextWatcher = object : TextWatcher {
                override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
                override fun afterTextChanged(s: Editable?) {
                    currentSubtask?.title = s?.toString() ?: ""
                }
            }
            binding.etSubtask.addTextChangedListener(titleTextWatcher)

            timeTextWatcher = object : TextWatcher {
                override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
                override fun afterTextChanged(s: Editable?) {
                    val timeStr = s?.toString() ?: ""
                    currentSubtask?.estimatedMinutes = if (timeStr.isNotEmpty()) {
                        timeStr.toIntOrNull()
                    } else {
                        null
                    }
                }
            }
            binding.etTime.addTextChangedListener(timeTextWatcher)
        }
    }

    class SubtaskDiffCallback : DiffUtil.ItemCallback<SubtaskInput>() {
        override fun areItemsTheSame(oldItem: SubtaskInput, newItem: SubtaskInput): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: SubtaskInput, newItem: SubtaskInput): Boolean {
            return oldItem == newItem
        }
    }
}
