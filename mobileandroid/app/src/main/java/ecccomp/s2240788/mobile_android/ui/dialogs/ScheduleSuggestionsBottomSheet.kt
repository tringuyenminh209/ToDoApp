package ecccomp.s2240788.mobile_android.ui.dialogs

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ProgressBar
import android.widget.TextView
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.ScheduleSuggestion
import ecccomp.s2240788.mobile_android.ui.adapters.ScheduleSuggestionAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.TaskDetailViewModel

class ScheduleSuggestionsBottomSheet(
    private val taskId: Int,
    private val onSuggestionSelected: (ScheduleSuggestion) -> Unit
) : BottomSheetDialogFragment() {

    private lateinit var rvSuggestions: RecyclerView
    private lateinit var progressLoading: ProgressBar
    private lateinit var tvNoSuggestions: TextView
    private lateinit var adapter: ScheduleSuggestionAdapter
    private lateinit var viewModel: TaskDetailViewModel

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.bottom_sheet_schedule_suggestions, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        // Initialize views
        rvSuggestions = view.findViewById(R.id.rv_suggestions)
        progressLoading = view.findViewById(R.id.progress_loading)
        tvNoSuggestions = view.findViewById(R.id.tv_no_suggestions)

        // Initialize ViewModel (shared with TaskDetailActivity)
        viewModel = ViewModelProvider(requireActivity())[TaskDetailViewModel::class.java]

        setupRecyclerView()
        observeViewModel()

        // Load suggestions
        viewModel.loadScheduleSuggestions(taskId)
    }

    private fun setupRecyclerView() {
        adapter = ScheduleSuggestionAdapter { suggestion ->
            onSuggestionSelected(suggestion)
            dismiss()
        }
        rvSuggestions.layoutManager = LinearLayoutManager(requireContext())
        rvSuggestions.adapter = adapter
    }

    private fun observeViewModel() {
        viewModel.scheduleSuggestions.observe(viewLifecycleOwner) { suggestions ->
            if (suggestions.isEmpty()) {
                rvSuggestions.visibility = View.GONE
                tvNoSuggestions.visibility = View.VISIBLE
            } else {
                rvSuggestions.visibility = View.VISIBLE
                tvNoSuggestions.visibility = View.GONE
                adapter.submitList(suggestions)
            }
        }

        viewModel.loadingSuggestions.observe(viewLifecycleOwner) { loading ->
            progressLoading.visibility = if (loading) View.VISIBLE else View.GONE
        }
    }

    companion object {
        const val TAG = "ScheduleSuggestionsBottomSheet"
    }
}
