package ecccomp.s2240788.mobile_android.ui.fragments

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.CalendarView
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton
import com.google.android.material.chip.Chip
import com.google.android.material.floatingactionbutton.FloatingActionButton
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.ui.activities.AddTaskActivity
import ecccomp.s2240788.mobile_android.ui.activities.TaskDetailActivity
import ecccomp.s2240788.mobile_android.ui.adapters.TaskAdapter
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

    private lateinit var viewModel: CalendarViewModel
    private lateinit var adapter: TaskAdapter

    // Views
    private lateinit var calendarView: CalendarView
    private lateinit var tvMonthYear: TextView
    private lateinit var tvSelectedDate: TextView
    private lateinit var tvTaskCount: TextView
    private lateinit var btnToday: MaterialButton
    private lateinit var chipAllTasks: Chip
    private lateinit var chipActiveTasks: Chip
    private lateinit var chipCompletedTasks: Chip
    private lateinit var recyclerView: RecyclerView
    private lateinit var emptyState: LinearLayout
    private lateinit var btnAddTaskEmpty: MaterialButton
    private lateinit var fabAddTask: FloatingActionButton

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_calendar, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        
        viewModel = ViewModelProvider(this)[CalendarViewModel::class.java]
        
        initViews(view)
        setupRecyclerView()
        setupCalendar()
        setupFilters()
        setupClickListeners()
        observeViewModel()
    }

    /**
     * ビューの初期化
     */
    private fun initViews(view: View) {
        calendarView = view.findViewById(R.id.calendar_view)
        tvMonthYear = view.findViewById(R.id.tv_month_year)
        tvSelectedDate = view.findViewById(R.id.tv_selected_date)
        tvTaskCount = view.findViewById(R.id.tv_task_count)
        btnToday = view.findViewById(R.id.btn_today)
        chipAllTasks = view.findViewById(R.id.chip_all_tasks)
        chipActiveTasks = view.findViewById(R.id.chip_active_tasks)
        chipCompletedTasks = view.findViewById(R.id.chip_completed_tasks)
        recyclerView = view.findViewById(R.id.rv_tasks)
        emptyState = view.findViewById(R.id.empty_state)
        btnAddTaskEmpty = view.findViewById(R.id.btn_add_task_empty)
        fabAddTask = view.findViewById(R.id.fab_add_task)
    }

    /**
     * RecyclerView のセットアップ
     */
    private fun setupRecyclerView() {
        adapter = TaskAdapter(
            onTaskClick = { task -> openTaskDetail(task) },
            onTaskComplete = { task -> handleTaskComplete(task) },
            onTaskDelete = { task -> handleTaskOptions(task) }
        )
        
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        recyclerView.adapter = adapter
    }

    /**
     * カレンダーのセットアップ
     */
    private fun setupCalendar() {
        // カレンダーの日付選択イベント
        calendarView.setOnDateChangeListener { _, year, month, dayOfMonth ->
            val calendar = Calendar.getInstance()
            calendar.set(year, month, dayOfMonth)
            viewModel.selectDate(calendar.time)
        }
    }

    /**
     * フィルターチップのセットアップ
     */
    private fun setupFilters() {
        chipAllTasks.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(CalendarViewModel.FilterType.ALL)
            }
        }

        chipActiveTasks.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(CalendarViewModel.FilterType.ACTIVE)
            }
        }

        chipCompletedTasks.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {
                viewModel.setFilter(CalendarViewModel.FilterType.COMPLETED)
            }
        }
    }

    /**
     * クリックリスナーのセットアップ
     */
    private fun setupClickListeners() {
        btnToday.setOnClickListener {
            viewModel.selectToday()
            // カレンダービューも今日に移動
            calendarView.date = System.currentTimeMillis()
        }

        btnAddTaskEmpty.setOnClickListener {
            openAddTask()
        }

        fabAddTask.setOnClickListener {
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
        val taskText = if (tasks.size == 1) {
            getString(R.string.calendar_task_count_single, tasks.size)
        } else {
            getString(R.string.calendar_task_count_plural, tasks.size)
        }
        tvTaskCount.text = taskText
    }

    /**
     * 日付表示の更新
     */
    private fun updateDateDisplay(date: Date) {
        // 月・年表示
        val monthYearFormat = SimpleDateFormat("MMMM, yyyy", Locale.getDefault())
        tvMonthYear.text = monthYearFormat.format(date)

        // 選択された日付表示
        val selectedDateFormat = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        tvSelectedDate.text = getString(R.string.calendar_selected_date, selectedDateFormat.format(date))
    }

    /**
     * Empty State を表示
     */
    private fun showEmptyState() {
        emptyState.visibility = View.VISIBLE
        recyclerView.visibility = View.GONE
        Log.d("CalendarFragment", "Empty state visible")
    }

    /**
     * RecyclerView を表示
     */
    private fun showTasks(tasks: List<Task>) {
        emptyState.visibility = View.GONE
        recyclerView.visibility = View.VISIBLE
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
