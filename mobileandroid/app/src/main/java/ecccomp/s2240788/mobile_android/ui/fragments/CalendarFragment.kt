package ecccomp.s2240788.mobile_android.ui.fragments

import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R

/**
 * Calendar Fragment
 * カレンダー機能を表示するフラグメント
 */
class CalendarFragment : Fragment() {

    private lateinit var recyclerView: RecyclerView
    private lateinit var emptyState: LinearLayout
    private lateinit var taskCountBadge: TextView

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_calendar, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        
        initViews(view)
        setupRecyclerView()
        loadTasks()
    }

    /**
     * ビューの初期化
     */
    private fun initViews(view: View) {
        recyclerView = view.findViewById(R.id.rv_tasks)
        emptyState = view.findViewById(R.id.empty_state)
        taskCountBadge = view.findViewById(R.id.tv_task_count)
    }

    /**
     * RecyclerView のセットアップ
     */
    private fun setupRecyclerView() {
        recyclerView.layoutManager = LinearLayoutManager(requireContext())
        // TODO: Set adapter
        // recyclerView.adapter = TaskAdapter()
    }

    /**
     * タスクをロードして表示状態を切り替え
     */
    private fun loadTasks() {
        // TODO: Load tasks from repository/API
        // Sample data for testing
        val tasks = listOf(
            Task(1, "Học Java cơ bản", "Học OOP cơ bản", false),
            Task(2, "Làm bài tập", "Làm bài tập thực hành", false)
        )
        
        updateUI(tasks)
    }

    /**
     * UI 表示状態の更新
     * - タスクがあれば RecyclerView を表示
     * - タスクがなければ Empty State を表示
     */
    private fun updateUI(tasks: List<Task>) {
        if (tasks.isEmpty()) {
            showEmptyState()
        } else {
            showTasks(tasks)
        }
        
        // Update task count badge
        taskCountBadge.text = "${tasks.size} tasks"
    }

    /**
     * Empty State を表示
     */
    private fun showEmptyState() {
        emptyState.visibility = View.VISIBLE
        recyclerView.visibility = View.GONE
        Log.d("CalendarFragment", "Empty state visible: ${emptyState.visibility}, Recycler: ${recyclerView.visibility}")
    }

    /**
     * RecyclerView を表示
     */
    private fun showTasks(tasks: List<Task>) {
        emptyState.visibility = View.GONE
        recyclerView.visibility = View.VISIBLE
        Log.d("CalendarFragment", "Showing ${tasks.size} tasks. Empty: ${emptyState.visibility}, Recycler: ${recyclerView.visibility}")
        // TODO: Update adapter with tasks
        // adapter.submitList(tasks)
    }
}

// Placeholder Task class
data class Task(
    val id: Long,
    val title: String,
    val description: String,
    val isCompleted: Boolean
)

