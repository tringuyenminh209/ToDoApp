package ecccomp.s2240788.mobile_android.ui.dialogs

import android.app.Dialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.DialogFragment
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.databinding.DialogStartTaskBinding
import ecccomp.s2240788.mobile_android.ui.adapters.StartTaskAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.MainViewModel

/**
 * StartTaskDialogFragment
 * タスク開始ダイアログ - dialog_start_task.xmlを使用
 */
class StartTaskDialogFragment : DialogFragment() {

    private var _binding: DialogStartTaskBinding? = null
    private val binding get() = _binding!!
    
    private lateinit var viewModel: MainViewModel
    private lateinit var adapter: StartTaskAdapter
    
    private var onTaskSelected: ((Task) -> Unit)? = null
    private var allTasks: List<Task> = emptyList()
    private var filteredTasks: List<Task> = emptyList()
    
    private var currentFilter: FilterType = FilterType.ALL
    
    enum class FilterType {
        ALL, HIGH_PRIORITY, QUICK_TASKS, TODAY
    }
    
    companion object {
        fun newInstance(tasks: List<Task>, onTaskSelected: (Task) -> Unit): StartTaskDialogFragment {
            return StartTaskDialogFragment().apply {
                this.allTasks = tasks
                this.onTaskSelected = onTaskSelected
            }
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
        _binding = DialogStartTaskBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        
        viewModel = ViewModelProvider(requireActivity())[MainViewModel::class.java]
        
        setupRecyclerView()
        setupClickListeners()
        setupChipFilters()
        
        // Load tasks from argument or ViewModel
        // Always reload from ViewModel if allTasks is empty to ensure fresh data
        if (allTasks.isEmpty()) {
            val tasksFromViewModel = viewModel.tasks.value?.filter { 
                it.status != "completed" && it.status != "in_progress" 
            } ?: emptyList()
            
            allTasks = tasksFromViewModel
            
            android.util.Log.d("StartTaskDialog", "Loaded ${allTasks.size} tasks from ViewModel")
        } else {
            android.util.Log.d("StartTaskDialog", "Using ${allTasks.size} tasks from arguments")
        }
        
        applyFilter(FilterType.ALL)
    }
    
    private fun setupRecyclerView() {
        adapter = StartTaskAdapter(
            onTaskClick = { task ->
                onTaskSelected?.invoke(task)
                dismiss()
            },
            onStartClick = { task ->
                onTaskSelected?.invoke(task)
                dismiss()
            }
        )
        
        binding.rvTasks.apply {
            adapter = this@StartTaskDialogFragment.adapter
            layoutManager = LinearLayoutManager(requireContext())
        }
    }
    
    private fun setupClickListeners() {
        // Close button
        binding.btnClose.setOnClickListener {
            dismiss()
        }
        
        // Sort button
        binding.btnSort.setOnClickListener {
            showSortOptions()
        }
        
        // AI Suggest button
        binding.btnAiSuggest.setOnClickListener {
            Toast.makeText(requireContext(), getString(R.string.start_task_ai_suggest), Toast.LENGTH_SHORT).show()
            // TODO: Implement AI suggestion
        }
        
        // Create Task button
        binding.btnCreateTask.setOnClickListener {
            Toast.makeText(requireContext(), getString(R.string.start_task_create_new), Toast.LENGTH_SHORT).show()
            dismiss()
            // TODO: Navigate to AddTaskActivity
        }
    }
    
    private fun setupChipFilters() {
        // All filter
        binding.chipAll.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) applyFilter(FilterType.ALL)
        }
        
        // High priority filter
        binding.chipHighPriority.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) applyFilter(FilterType.HIGH_PRIORITY)
        }
        
        // Quick tasks filter
        binding.chipQuickTasks.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) applyFilter(FilterType.QUICK_TASKS)
        }
        
        // Today filter
        binding.chipToday.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) applyFilter(FilterType.TODAY)
        }
    }
    
    private fun applyFilter(filterType: FilterType) {
        currentFilter = filterType
        
        filteredTasks = when (filterType) {
            FilterType.ALL -> allTasks
            
            FilterType.HIGH_PRIORITY -> allTasks.filter { 
                it.priority >= 4 // Priority 4-5
            }
            
            FilterType.QUICK_TASKS -> allTasks.filter { 
                it.estimated_minutes != null && it.estimated_minutes <= 30 // <= 30 minutes
            }
            
            FilterType.TODAY -> {
                val today = java.text.SimpleDateFormat("yyyy-MM-dd", java.util.Locale.getDefault())
                    .format(java.util.Date())
                allTasks.filter { task ->
                    task.deadline?.let { deadline ->
                        // Check if deadline starts with today's date (handles ISO format)
                        deadline.startsWith(today) || deadline.contains(today)
                    } ?: false
                }
            }
        }
        
        updateTaskList()
    }
    
    private fun updateTaskList() {
        // Update task count
        val count = filteredTasks.size
        binding.tvTaskCount.text = getString(R.string.start_task_available_count, count)
        
        android.util.Log.d("StartTaskDialog", "updateTaskList: ${filteredTasks.size} tasks")
        
        // Show/hide empty state
        if (filteredTasks.isEmpty()) {
            binding.rvTasks.visibility = View.GONE
            binding.emptyState.visibility = View.VISIBLE
            android.util.Log.d("StartTaskDialog", "Showing empty state")
        } else {
            binding.rvTasks.visibility = View.VISIBLE
            binding.emptyState.visibility = View.GONE
            adapter.submitList(filteredTasks)
            android.util.Log.d("StartTaskDialog", "Showing ${filteredTasks.size} tasks in RecyclerView")
        }
    }
    
    private fun showSortOptions() {
        val sortOptions = arrayOf(
            getString(R.string.start_task_sort_priority),
            getString(R.string.start_task_sort_time),
            getString(R.string.start_task_sort_deadline)
        )
        
        MaterialAlertDialogBuilder(requireContext())
            .setTitle(getString(R.string.start_task_sort))
            .setItems(sortOptions) { _, which ->
                when (which) {
                    0 -> sortByPriority()
                    1 -> sortByEstimatedTime()
                    2 -> sortByDeadline()
                }
            }
            .show()
    }
    
    private fun sortByPriority() {
        filteredTasks = filteredTasks.sortedByDescending { it.priority }
        updateTaskList()
    }
    
    private fun sortByEstimatedTime() {
        filteredTasks = filteredTasks.sortedBy { it.estimated_minutes ?: Int.MAX_VALUE }
        updateTaskList()
    }
    
    private fun sortByDeadline() {
        filteredTasks = filteredTasks.sortedBy { it.deadline ?: "9999-12-31" }
        updateTaskList()
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

