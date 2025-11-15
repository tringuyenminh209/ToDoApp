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
            selectToday()
        }

        // Quick date shortcuts
        binding.chipDateToday.setOnClickListener {
            selectToday()
        }

        binding.chipTomorrow.setOnClickListener {
            selectTomorrow()
        }

        binding.chipNextMonday.setOnClickListener {
            selectNextMonday()
        }

        binding.chipNextWeek.setOnClickListener {
            selectNextWeek()
        }

        binding.btnAddTaskEmpty.setOnClickListener {
            openAddTask()
        }

        binding.fabAddTask.setOnClickListener {
            openAddTask()
        }
    }

    /**
     * Select today's date
     */
    private fun selectToday() {
        val calendar = Calendar.getInstance()
        binding.calendarView.date = calendar.timeInMillis
        viewModel.selectDate(calendar.time)
        Log.d("CalendarFragment", "Selected today: ${SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(calendar.time)}")
    }

    /**
     * Select tomorrow's date
     */
    private fun selectTomorrow() {
        val calendar = Calendar.getInstance()
        calendar.add(Calendar.DAY_OF_MONTH, 1)
        binding.calendarView.date = calendar.timeInMillis
        viewModel.selectDate(calendar.time)
        Log.d("CalendarFragment", "Selected tomorrow: ${SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(calendar.time)}")
    }

    /**
     * Select next Monday
     */
    private fun selectNextMonday() {
        val calendar = Calendar.getInstance()
        val currentDayOfWeek = calendar.get(Calendar.DAY_OF_WEEK)

        // Calculate days until next Monday (Monday = 2 in Calendar)
        val daysUntilMonday = if (currentDayOfWeek == Calendar.MONDAY) {
            7 // If today is Monday, select next Monday
        } else if (currentDayOfWeek < Calendar.MONDAY) {
            Calendar.MONDAY - currentDayOfWeek
        } else {
            7 - (currentDayOfWeek - Calendar.MONDAY)
        }

        calendar.add(Calendar.DAY_OF_MONTH, daysUntilMonday)
        binding.calendarView.date = calendar.timeInMillis
        viewModel.selectDate(calendar.time)
        Log.d("CalendarFragment", "Selected next Monday: ${SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(calendar.time)}")
    }

    /**
     * Select 7 days from now
     */
    private fun selectNextWeek() {
        val calendar = Calendar.getInstance()
        calendar.add(Calendar.DAY_OF_MONTH, 7)
        binding.calendarView.date = calendar.timeInMillis
        viewModel.selectDate(calendar.time)
        Log.d("CalendarFragment", "Selected next week: ${SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(calendar.time)}")
    }

    /**
     * ViewModelの監視
     */
    private fun observeViewModel() {
        // List view: regular tasks filtered by deadline
        viewModel.listTasks.observe(viewLifecycleOwner) { tasks ->
            updateListView(tasks)
        }

        // Timeline view: study schedules only
        viewModel.timelineTasks.observe(viewLifecycleOwner) { tasks ->
            updateTimelineView(tasks)
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
     * Update list view with regular tasks
     */
    private fun updateListView(tasks: List<Task>) {
        adapter.submitList(tasks)

        // Update UI visibility if currently in list view
        if (!isTimelineView) {
            if (tasks.isEmpty()) {
                showEmptyState()
            } else {
                binding.emptyState.visibility = View.GONE
                binding.rvTasks.visibility = View.VISIBLE
                binding.rvTimeline.visibility = View.GONE
            }

            // タスク数バッジの更新
            binding.tvTaskCount.text = tasks.size.toString()
        }

        Log.d("CalendarFragment", "List view updated with ${tasks.size} tasks")
    }

    /**
     * Update timeline view with study schedules
     */
    private fun updateTimelineView(tasks: List<Task>) {
        timelineAdapter.submitList(tasks)

        // Update UI visibility if currently in timeline view
        if (isTimelineView) {
            if (tasks.isEmpty()) {
                showEmptyState()
            } else {
                binding.emptyState.visibility = View.GONE
                binding.rvTasks.visibility = View.GONE
                binding.rvTimeline.visibility = View.VISIBLE
            }

            // タスク数バッジの更新 (study schedules count)
            binding.tvTaskCount.text = tasks.size.toString()
        }

        Log.d("CalendarFragment", "Timeline view updated with ${tasks.size} study schedules")
    }

    /**
     * 日付表示の更新
     */
    private fun updateDateDisplay(date: Date) {
        // 月・年表示
        val monthYearFormat = SimpleDateFormat("MMMM, yyyy", Locale.getDefault())
        binding.tvMonthYear.text = monthYearFormat.format(date)

        // 選択された日付表示 (Header)
        val selectedDateFormat = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        binding.tvSelectedDate.text = selectedDateFormat.format(date)

        // 選択された日付表示 (Card) - 日本語形式
        val calendar = Calendar.getInstance()
        calendar.time = date

        val year = calendar.get(Calendar.YEAR)
        val month = calendar.get(Calendar.MONTH) + 1
        val day = calendar.get(Calendar.DAY_OF_MONTH)

        // 曜日を取得
        val dayOfWeek = when (calendar.get(Calendar.DAY_OF_WEEK)) {
            Calendar.SUNDAY -> "日"
            Calendar.MONDAY -> "月"
            Calendar.TUESDAY -> "火"
            Calendar.WEDNESDAY -> "水"
            Calendar.THURSDAY -> "木"
            Calendar.FRIDAY -> "金"
            Calendar.SATURDAY -> "土"
            else -> ""
        }

        binding.tvSelectedDateDisplay.text = "${year}年${month}月${day}日（${dayOfWeek}）"
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
     * タスク詳細画面を開く
     * If task is a study schedule (negative ID), open learning path detail instead
     */
    private fun openTaskDetail(task: Task) {
        if (task.id < 0) {
            // This is a study schedule (converted from StudySchedule)
            // learning_milestone_id contains the learning_path_id
            val learningPathId = task.learning_milestone_id
            if (learningPathId != null) {
                val intent = Intent(requireContext(), ecccomp.s2240788.mobile_android.ui.activities.LearningPathDetailActivity::class.java)
                intent.putExtra("LEARNING_PATH_ID", learningPathId)
                startActivity(intent)
            }
        } else {
            // Regular task
            val intent = Intent(requireContext(), TaskDetailActivity::class.java)
            intent.putExtra("task_id", task.id)
            startActivity(intent)
        }
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
