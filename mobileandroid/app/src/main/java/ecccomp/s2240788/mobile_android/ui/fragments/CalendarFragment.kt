package ecccomp.s2240788.mobile_android.ui.fragments

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.fragment.app.activityViewModels
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.databinding.FragmentCalendarBinding
import ecccomp.s2240788.mobile_android.ui.activities.AddTaskActivity
import ecccomp.s2240788.mobile_android.ui.activities.TaskDetailActivity
import ecccomp.s2240788.mobile_android.ui.adapters.TaskAdapter
import ecccomp.s2240788.mobile_android.ui.adapters.TimelineAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.CalendarViewModel
import java.text.SimpleDateFormat
import java.util.*

/**
 * CalendarFragment
 * カレンダー機能を表示するフラグメント
 * - カレンダービューで日付選択
 * - 選択した日付のタスクを表示
 * - フィルター機能（全て/進行中/完了）
 */
class CalendarFragment : Fragment() {

    private var _binding: FragmentCalendarBinding? = null
    private val binding get() = _binding!!

    // Share ViewModel with Activity
    private val viewModel: CalendarViewModel by activityViewModels()
    private lateinit var adapter: TaskAdapter
    private lateinit var timelineAdapter: TimelineAdapter
    private var isTimelineView = false

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentCalendarBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupRecyclerView()
        setupCalendar()
        setupFilters()
        setupClickListeners()
        observeViewModel()
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }

    /**
     * RecyclerView のセットアップ
     */
    private fun setupRecyclerView() {
        // List view adapter
        adapter = TaskAdapter(
            onTaskClick = { task -> openTaskDetail(task) },
            onTaskComplete = { task -> handleTaskComplete(task) },
            onTaskDelete = { task -> handleTaskOptions(task) }
        )
        binding.rvTasks.layoutManager = LinearLayoutManager(requireContext())
        binding.rvTasks.adapter = adapter

        // Timeline view adapter
        timelineAdapter = TimelineAdapter(
            onTaskClick = { task -> openTaskDetail(task) }
        )
        binding.rvTimeline.layoutManager = LinearLayoutManager(requireContext())
        binding.rvTimeline.adapter = timelineAdapter
    }

    /**
     * カレンダーのセットアップ
     */
    private fun setupCalendar() {
        // Set initial date to today
        val today = Calendar.getInstance()
        binding.calendarView.date = today.timeInMillis
        
        // カレンダーの日付選択イベント
        binding.calendarView.setOnDateChangeListener { _, year, month, dayOfMonth ->
            val calendar = Calendar.getInstance()
            calendar.set(year, month, dayOfMonth)
            val selectedDate = calendar.time
            viewModel.selectDate(selectedDate)
            
            Log.d("CalendarFragment", "Date selected: ${SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(selectedDate)}")
        }
    }

    /**
     * フィルターチップのセットアップ
     */
    private fun setupFilters() {
        // View mode toggle
        binding.chipListView.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                switchToListView()
            }
        }

        binding.chipTimelineView.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                switchToTimelineView()
            }
        }

        // Filter chips
        binding.chipAllTasks.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(CalendarViewModel.FilterType.ALL)
            }
        }

        binding.chipActiveTasks.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(CalendarViewModel.FilterType.ACTIVE)
            }
        }

        binding.chipCompletedTasks.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(CalendarViewModel.FilterType.COMPLETED)
            }
        }
    }

    /**
     * リストビューに切り替え
     */
    private fun switchToListView() {
        isTimelineView = false
        binding.rvTasks.visibility = View.VISIBLE
        binding.rvTimeline.visibility = View.GONE
        binding.filterChipGroup.visibility = View.VISIBLE
    }

    /**
     * タイムラインビューに切り替え
     */
    private fun switchToTimelineView() {
        isTimelineView = true
        binding.rvTasks.visibility = View.GONE
        binding.rvTimeline.visibility = View.VISIBLE
        binding.filterChipGroup.visibility = View.GONE
    }

    /**
     * クリックリスナーのセットアップ
     */
    private fun setupClickListeners() {
        binding.btnToday.setOnClickListener {
            // カレンダービューも今日に移動
            val todayMillis = System.currentTimeMillis()
            binding.calendarView.date = todayMillis
            
            // ViewModelの日付も更新
            val calendar = Calendar.getInstance()
            calendar.timeInMillis = todayMillis
            viewModel.selectDate(calendar.time)
            
            Log.d("CalendarFragment", "Selected today: ${SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(calendar.time)}")
        }

        binding.btnAddTaskEmpty.setOnClickListener {
            openAddTask()
        }

        binding.fabAddTask.setOnClickListener {
            openAddTask()
        }
    }

    /**
     * ViewModelの監視
     */
    private fun observeViewModel() {
        // フィルタリングされたタスク
        viewModel.filteredTasks.observe(viewLifecycleOwner) { tasks ->
            updateUI(tasks)
        }

        // 選択された日付
        viewModel.selectedDate.observe(viewLifecycleOwner) { date ->
            updateDateDisplay(date)
        }

        // ローディング状態
        viewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            // TODO: ローディングインジケーターを表示
        }

        // エラー
        viewModel.error.observe(viewLifecycleOwner) { error ->
            error?.let {
                Toast.makeText(requireContext(), it, Toast.LENGTH_SHORT).show()
            }
        }
    }

    /**
     * UI 表示状態の更新
     */
    private fun updateUI(tasks: List<Task>) {
        if (tasks.isEmpty()) {
            showEmptyState()
        } else {
            showTasks(tasks)
        }

        // タスク数バッジの更新
        binding.tvTaskCount.text = tasks.size.toString()

        // Update timeline adapter with all tasks (not filtered)
        timelineAdapter.submitList(tasks)
    }

    /**
     * 日付表示の更新
     */
    private fun updateDateDisplay(date: Date) {
        // 月・年表示
        val monthYearFormat = SimpleDateFormat("MMMM, yyyy", Locale.getDefault())
        binding.tvMonthYear.text = monthYearFormat.format(date)

        // 選択された日付表示
        val selectedDateFormat = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        binding.tvSelectedDate.text = selectedDateFormat.format(date)
    }

    /**
     * Empty State を表示
     */
    private fun showEmptyState() {
        binding.emptyState.visibility = View.VISIBLE
        binding.rvTasks.visibility = View.GONE
        binding.rvTimeline.visibility = View.GONE
        Log.d("CalendarFragment", "Empty state visible")
    }

    /**
     * RecyclerView を表示
     */
    private fun showTasks(tasks: List<Task>) {
        binding.emptyState.visibility = View.GONE

        // Show appropriate view based on mode
        if (isTimelineView) {
            binding.rvTasks.visibility = View.GONE
            binding.rvTimeline.visibility = View.VISIBLE
        } else {
            binding.rvTasks.visibility = View.VISIBLE
            binding.rvTimeline.visibility = View.GONE
        }

        adapter.submitList(tasks)
        Log.d("CalendarFragment", "Showing ${tasks.size} tasks")
    }

    /**
     * タスク詳細画面を開く
     */
    private fun openTaskDetail(task: Task) {
        val intent = Intent(requireContext(), TaskDetailActivity::class.java)
        intent.putExtra("task_id", task.id)
        startActivity(intent)
    }

    /**
     * タスク追加画面を開く
     */
    private fun openAddTask() {
        val intent = Intent(requireContext(), AddTaskActivity::class.java)
        startActivity(intent)
    }

    /**
     * タスク完了処理 - Start Focus Session
     */
    private fun handleTaskComplete(task: Task) {
        val intent = Intent(requireContext(), ecccomp.s2240788.mobile_android.ui.activities.FocusSessionActivity::class.java)
        intent.putExtra("task_id", task.id)
        startActivity(intent)
    }

    /**
     * タスクオプション処理
     */
    private fun handleTaskOptions(task: Task) {
        // TODO: タスクオプションメニューを表示
        Toast.makeText(requireContext(), "オプションメニュー", Toast.LENGTH_SHORT).show()
    }

    override fun onResume() {
        super.onResume()
        // 画面に戻った時にタスクを再読み込み
        viewModel.refreshTasks()
    }
}
