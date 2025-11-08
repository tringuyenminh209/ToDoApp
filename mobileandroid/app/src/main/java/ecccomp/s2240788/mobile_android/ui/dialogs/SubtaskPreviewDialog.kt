package ecccomp.s2240788.mobile_android.ui.dialogs

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.DialogFragment
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Subtask
import ecccomp.s2240788.mobile_android.databinding.DialogSubtaskPreviewBinding
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskPreviewAdapter

/**
 * SubtaskPreviewDialog
 * AI生成されたサブタスクのプレビューダイアログ
 */
class SubtaskPreviewDialog : DialogFragment() {

    private var _binding: DialogSubtaskPreviewBinding? = null
    private val binding get() = _binding!!

    private var onApplyListener: (() -> Unit)? = null
    private var onCancelListener: (() -> Unit)? = null
    private var subtasks: List<Subtask> = emptyList()

    private lateinit var previewAdapter: SubtaskPreviewAdapter

    companion object {
        @JvmStatic
        fun newInstance(subtasks: List<Subtask>): SubtaskPreviewDialog {
            val dialog = SubtaskPreviewDialog()
            dialog.subtasks = subtasks
            return dialog
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setStyle(STYLE_NORMAL, android.R.style.Theme_Material_Light_Dialog)
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = DialogSubtaskPreviewBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        // Setup RecyclerView
        previewAdapter = SubtaskPreviewAdapter(subtasks)
        binding.rvSubtasks.layoutManager = LinearLayoutManager(requireContext())
        binding.rvSubtasks.adapter = previewAdapter

        // Update subtitle with count
        binding.tvSubtitle.text = getString(R.string.ai_preview_subtitle, subtasks.size)

        // Apply button
        binding.btnApply.setOnClickListener {
            onApplyListener?.invoke()
            dismiss()
        }

        // Cancel button
        binding.btnCancel.setOnClickListener {
            onCancelListener?.invoke()
            dismiss()
        }

        // Update empty state
        if (subtasks.isEmpty()) {
            binding.rvSubtasks.visibility = View.GONE
            binding.emptyState.visibility = View.VISIBLE
        } else {
            binding.rvSubtasks.visibility = View.VISIBLE
            binding.emptyState.visibility = View.GONE
        }
    }

    fun setOnApplyListener(listener: () -> Unit) {
        onApplyListener = listener
    }

    fun setOnCancelListener(listener: () -> Unit) {
        onCancelListener = listener
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

