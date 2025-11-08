package ecccomp.s2240788.mobile_android.ui.dialogs

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.DialogFragment
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.DialogComplexitySelectorBinding

/**
 * ComplexitySelectorDialog
 * AIタスク分割の複雑度レベル選択ダイアログ
 */
class ComplexitySelectorDialog : DialogFragment() {

    private var _binding: DialogComplexitySelectorBinding? = null
    private val binding get() = _binding!!

    private var onComplexitySelectedListener: ((String) -> Unit)? = null
    private var selectedComplexity: String = "medium"

    companion object {
        @JvmStatic
        fun newInstance(): ComplexitySelectorDialog {
            return ComplexitySelectorDialog()
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
        _binding = DialogComplexitySelectorBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        // Set default selection
        updateSelection(selectedComplexity)

        // Setup click listeners
        binding.chipSimple.setOnClickListener {
            selectedComplexity = "simple"
            updateSelection(selectedComplexity)
        }

        binding.chipMedium.setOnClickListener {
            selectedComplexity = "medium"
            updateSelection(selectedComplexity)
        }

        binding.chipComplex.setOnClickListener {
            selectedComplexity = "complex"
            updateSelection(selectedComplexity)
        }

        // Confirm button
        binding.btnConfirm.setOnClickListener {
            onComplexitySelectedListener?.invoke(selectedComplexity)
            dismiss()
        }

        // Cancel button
        binding.btnCancel.setOnClickListener {
            dismiss()
        }
    }

    private fun updateSelection(complexity: String) {
        // Reset all chips
        binding.chipSimple.isChecked = false
        binding.chipMedium.isChecked = false
        binding.chipComplex.isChecked = false

        // Set selected chip
        when (complexity) {
            "simple" -> binding.chipSimple.isChecked = true
            "medium" -> binding.chipMedium.isChecked = true
            "complex" -> binding.chipComplex.isChecked = true
        }
    }

    fun setOnComplexitySelectedListener(listener: (String) -> Unit) {
        onComplexitySelectedListener = listener
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

