package ecccomp.s2240788.mobile_android.ui.dialogs

import android.app.Dialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.WindowManager
import androidx.fragment.app.DialogFragment
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.SaveEnvironmentCheckRequest
import ecccomp.s2240788.mobile_android.databinding.DialogEnvironmentChecklistBinding

/**
 * EnvironmentChecklistDialog
 * フォーカス環境チェックリストダイアログ
 * フォーカスセッション開始前に環境をチェック
 */
class EnvironmentChecklistDialog : DialogFragment() {

    private var _binding: DialogEnvironmentChecklistBinding? = null
    private val binding get() = _binding!!

    private var onStartSessionListener: ((SaveEnvironmentCheckRequest) -> Unit)? = null
    private var taskId: Int = 0

    companion object {
        private const val ARG_TASK_ID = "task_id"

        @JvmStatic
        fun newInstance(taskId: Int): EnvironmentChecklistDialog {
            val dialog = EnvironmentChecklistDialog()
            val args = Bundle()
            args.putInt(ARG_TASK_ID, taskId)
            dialog.arguments = args
            return dialog
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setStyle(STYLE_NORMAL, android.R.style.Theme_Material_Light_Dialog)
        taskId = arguments?.getInt(ARG_TASK_ID) ?: 0
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = DialogEnvironmentChecklistBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupChecklistListeners()
        setupButtons()
        updateProgress()
    }

    private fun setupChecklistListeners() {
        val checkboxes = listOf(
            binding.checkQuietSpace,
            binding.checkPhoneSilent,
            binding.checkMaterialsReady,
            binding.checkWaterCoffee,
            binding.checkComfortable,
            binding.checkNotificationsOff
        )

        checkboxes.forEach { checkbox ->
            checkbox.setOnCheckedChangeListener { _, _ ->
                updateProgress()
            }
        }
    }

    private fun updateProgress() {
        val totalChecks = 6
        val checkedCount = listOf(
            binding.checkQuietSpace.isChecked,
            binding.checkPhoneSilent.isChecked,
            binding.checkMaterialsReady.isChecked,
            binding.checkWaterCoffee.isChecked,
            binding.checkComfortable.isChecked,
            binding.checkNotificationsOff.isChecked
        ).count { it }

        val remaining = totalChecks - checkedCount

        if (remaining == 0) {
            binding.tvChecklistProgress.text = getString(R.string.all_checks_passed)
            binding.btnStartSession.isEnabled = true
        } else {
            binding.tvChecklistProgress.text = getString(R.string.checks_remaining, remaining)
            binding.btnStartSession.isEnabled = false
        }
    }

    private fun setupButtons() {
        binding.btnSkip.setOnClickListener {
            // Allow skip but still save checklist
            val environmentData = buildEnvironmentData()
            onStartSessionListener?.invoke(environmentData)
            dismiss()
        }

        binding.btnStartSession.setOnClickListener {
            val environmentData = buildEnvironmentData()
            onStartSessionListener?.invoke(environmentData)
            dismiss()
        }
    }

    private fun buildEnvironmentData(): SaveEnvironmentCheckRequest {
        return SaveEnvironmentCheckRequest(
            task_id = taskId,
            focus_session_id = null,
            quiet_space = binding.checkQuietSpace.isChecked,
            phone_silent = binding.checkPhoneSilent.isChecked,
            materials_ready = binding.checkMaterialsReady.isChecked,
            water_coffee_ready = binding.checkWaterCoffee.isChecked,
            comfortable_position = binding.checkComfortable.isChecked,
            notifications_off = binding.checkNotificationsOff.isChecked,
            apps_closed = null, // TODO: Implement app selection
            notes = binding.etChecklistNotes.text?.toString()?.takeIf { it.isNotBlank() }
        )
    }

    fun setOnStartSessionListener(listener: (SaveEnvironmentCheckRequest) -> Unit) {
        onStartSessionListener = listener
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

