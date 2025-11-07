package ecccomp.s2240788.mobile_android.ui.dialogs

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.DialogFragment
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.ContextSwitch
import ecccomp.s2240788.mobile_android.databinding.DialogContextSwitchWarningBinding

/**
 * ContextSwitchWarningDialog
 * コンテキストスイッチ警告ダイアログ
 * タスク間の切り替えによる集中力の低下を警告
 */
class ContextSwitchWarningDialog : DialogFragment() {

    private var _binding: DialogContextSwitchWarningBinding? = null
    private val binding get() = _binding!!

    private var onProceedListener: (() -> Unit)? = null
    private var onBatchTasksListener: (() -> Unit)? = null
    private var onCancelListener: (() -> Unit)? = null
    private var contextSwitch: ContextSwitch? = null
    private var warningMessage: String? = null

    companion object {
        private const val ARG_FROM_TASK = "from_task"
        private const val ARG_TO_TASK = "to_task"
        private const val ARG_COST = "cost"
        private const val ARG_TIPS = "tips"
        private const val ARG_CONTEXT_SWITCH = "context_switch"

        @JvmStatic
        fun newInstance(
            fromTask: String,
            toTask: String,
            cost: Int,
            tips: String,
            contextSwitch: ContextSwitch? = null
        ): ContextSwitchWarningDialog {
            val dialog = ContextSwitchWarningDialog()
            val args = Bundle()
            args.putString(ARG_FROM_TASK, fromTask)
            args.putString(ARG_TO_TASK, toTask)
            args.putInt(ARG_COST, cost)
            args.putString(ARG_TIPS, tips)
            if (contextSwitch != null) {
                // Store context switch ID for later confirmation
                args.putInt(ARG_CONTEXT_SWITCH, contextSwitch.id)
            }
            dialog.arguments = args
            dialog.contextSwitch = contextSwitch
            return dialog
        }
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = DialogContextSwitchWarningBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val fromTask = arguments?.getString(ARG_FROM_TASK) ?: ""
        val toTask = arguments?.getString(ARG_TO_TASK) ?: ""
        val cost = arguments?.getInt(ARG_COST) ?: 0
        val tips = arguments?.getString(ARG_TIPS) ?: ""

        binding.tvFromTask.text = fromTask
        binding.tvToTask.text = toTask
        binding.tvEstimatedCost.text = getString(R.string.estimated_recovery_cost, cost)
        binding.tvTips.text = tips

        setupButtons()
    }

    private fun setupButtons() {
        binding.btnProceed.setOnClickListener {
            onProceedListener?.invoke()
            dismiss()
        }

        binding.btnBatchTasks.setOnClickListener {
            onBatchTasksListener?.invoke()
            dismiss()
        }

        binding.btnCancel.setOnClickListener {
            onCancelListener?.invoke()
            dismiss()
        }
    }

    fun setOnProceedListener(listener: () -> Unit) {
        onProceedListener = listener
    }

    fun setOnBatchTasksListener(listener: () -> Unit) {
        onBatchTasksListener = listener
    }

    fun setOnCancelListener(listener: () -> Unit) {
        onCancelListener = listener
    }

    fun getContextSwitchId(): Int? {
        return contextSwitch?.id
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

