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
        adapter = TaskAdapter(
            onTaskClick = { task -> openTaskDetail(task) },
            onTaskComplete = { task -> handleTaskComplete(task) },
            onTaskDelete = { task -> handleTaskOptions(task) }
        )

        binding.rvTasks.layoutManager = LinearLayoutManager(requireContext())
        binding.rvTasks.adapter = adapter
    }

    /**
     * カレンダーのセットアップ
     */
    private fun setupCalendar() {
        // カレンダーの日付選択イベント
        binding.calendarView.setOnDateChangeListener { _, year, month, dayOfMonth ->
            val calendar = Calendar.getInstance()
            calendar.set(year, month, dayOfMonth)
            viewModel.selectDate(calendar.time)
        }
    }

    /**
     * フィルターチップのセットアップ
     */
    private fun setupFilters() {
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
     * クリックリスナーのセットアップ
     */
    private fun setupClickListeners() {
        binding.btnToday.setOnClickListener {
            viewModel.selectToday()
            // カレンダービューも今日に移動
            binding.calendarView.date = System.currentTimeMillis()
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
        Log.d("CalendarFragment", "Empty state visible")
    }

    /**
     * RecyclerView を表示
     */
    private fun showTasks(tasks: List<Task>) {
        binding.emptyState.visibility = View.GONE
        binding.rvTasks.visibility = View.VISIBLE
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
