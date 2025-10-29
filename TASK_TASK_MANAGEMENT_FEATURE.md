# TASK: Implement Task Management Feature - Core Todo Functionality

## 🎯 Objective
Implement complete Task Management feature to make the app functional. Users will be able to create, view, edit, complete, and delete tasks. This is the **core MVP feature** that transforms the app from authentication-only to a usable todo application.

---

## 📋 Current Status

### ✅ Backend Ready
- ✅ TaskController with full CRUD API
- ✅ Task model with relationships
- ✅ Endpoints: GET, POST, PUT, DELETE /api/tasks
- ✅ Task actions: complete, start, filter by status/priority
- ✅ Task stats endpoint

### ⚠️ Android Incomplete
- ⚠️ MainActivity exists but only shows Toast messages
- ⚠️ AddTaskActivity is empty skeleton
- ⚠️ EditTaskActivity is empty skeleton
- ❌ No TaskViewModel
- ❌ No RecyclerView adapter
- ❌ No task display logic

**Goal:** Connect Android UI to existing backend APIs to create functional task management.

---

## 🎯 Success Criteria

After completing this task:
1. ✅ User can view list of tasks in MainActivity
2. ✅ User can create new task via AddTaskActivity
3. ✅ User can edit existing task via EditTaskActivity
4. ✅ User can mark task as complete (swipe or button)
5. ✅ User can delete task (swipe or button)
6. ✅ User can filter tasks (All/Pending/Completed)
7. ✅ Pull-to-refresh to reload tasks
8. ✅ Loading states and error handling
9. ✅ Empty state when no tasks
10. ✅ Beautiful Material Design 3 UI

---

## 📁 Files to Create/Modify

### 🆕 Create (7 files)
1. ❌ `TaskViewModel.kt` - Business logic for task operations
2. ❌ `TaskAdapter.kt` - RecyclerView adapter
3. ❌ `AddTaskViewModel.kt` - Logic for creating tasks
4. ❌ `EditTaskViewModel.kt` - Logic for editing tasks
5. ❌ `item_task.xml` - Layout for task list item
6. ❌ `fragment_empty_state.xml` - Empty state view

### 🔧 Modify (4 files)
1. 🔄 `MainActivity.kt` - Display task list
2. 🔄 `activity_main.xml` - Add RecyclerView
3. 🔄 `AddTaskActivity.java` - Implement form
4. 🔄 `EditTaskActivity.kt` - Implement form

---

## 📝 STEP 1: Create TaskViewModel

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/viewmodels/TaskViewModel.kt`

**Purpose:** Manage task data and business logic

**Complete Code:**

```kotlin
package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * TaskViewModel
 * タスクリストの管理とCRUD操作
 */
class TaskViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _tasks = MutableLiveData<List<Task>>()
    val tasks: LiveData<List<Task>> = _tasks

    private val _filteredTasks = MutableLiveData<List<Task>>()
    val filteredTasks: LiveData<List<Task>> = _filteredTasks

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _taskDeleted = MutableLiveData<Boolean>()
    val taskDeleted: LiveData<Boolean> = _taskDeleted

    private val _taskCompleted = MutableLiveData<Task?>()
    val taskCompleted: LiveData<Task?> = _taskCompleted

    private var currentFilter: TaskFilter = TaskFilter.ALL

    enum class TaskFilter {
        ALL, PENDING, COMPLETED
    }

    /**
     * タスク一覧を取得
     */
    fun fetchTasks() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getTasks()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        _tasks.value = apiResponse.data ?: emptyList()
                        applyFilter(currentFilter)
                    } else {
                        _error.value = "タスクの取得に失敗しました"
                    }
                } else {
                    _error.value = when (response.code()) {
                        401 -> "認証エラー。再度ログインしてください"
                        500 -> "サーバーエラーが発生しました"
                        else -> "タスクの取得に失敗しました: ${response.message()}"
                    }
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * タスクを完了にする
     */
    fun completeTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.completeTask(taskId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        _taskCompleted.value = apiResponse.data
                        // リストを更新
                        fetchTasks()
                    } else {
                        _error.value = "タスクの完了に失敗しました"
                    }
                } else {
                    _error.value = "タスクの完了に失敗しました: ${response.message()}"
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    /**
     * タスクを開始する
     */
    fun startTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.startTask(taskId)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        // リストを更新
                        fetchTasks()
                    } else {
                        _error.value = "タスクの開始に失敗しました"
                    }
                } else {
                    _error.value = "タスクの開始に失敗しました: ${response.message()}"
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    /**
     * タスクを削除
     */
    fun deleteTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.deleteTask(taskId)

                if (response.isSuccessful) {
                    _taskDeleted.value = true
                    // リストを更新
                    fetchTasks()
                } else {
                    _error.value = "タスクの削除に失敗しました: ${response.message()}"
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    /**
     * フィルターを適用
     */
    fun applyFilter(filter: TaskFilter) {
        currentFilter = filter
        val allTasks = _tasks.value ?: emptyList()

        _filteredTasks.value = when (filter) {
            TaskFilter.ALL -> allTasks
            TaskFilter.PENDING -> allTasks.filter { it.status == "pending" || it.status == "in_progress" }
            TaskFilter.COMPLETED -> allTasks.filter { it.status == "completed" }
        }
    }

    fun clearError() {
        _error.value = null
    }

    fun clearTaskCompleted() {
        _taskCompleted.value = null
    }

    fun clearTaskDeleted() {
        _taskDeleted.value = false
    }
}
```

---

## 📝 STEP 2: Create TaskAdapter

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/adapters/TaskAdapter.kt`

**Purpose:** RecyclerView adapter to display tasks

**Complete Code:**

```kotlin
package ecccomp.s2240788.mobile_android.ui.adapters

import android.graphics.Paint
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.databinding.ItemTaskBinding
import java.text.SimpleDateFormat
import java.util.*

/**
 * TaskAdapter
 * タスクリスト表示用RecyclerViewアダプター
 */
class TaskAdapter(
    private val onTaskClick: (Task) -> Unit,
    private val onTaskComplete: (Task) -> Unit,
    private val onTaskDelete: (Task) -> Unit
) : ListAdapter<Task, TaskAdapter.TaskViewHolder>(TaskDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TaskViewHolder {
        val binding = ItemTaskBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return TaskViewHolder(binding)
    }

    override fun onBindViewHolder(holder: TaskViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class TaskViewHolder(
        private val binding: ItemTaskBinding
    ) : RecyclerView.ViewHolder(binding.root) {

        fun bind(task: Task) {
            binding.apply {
                // タイトル
                tvTaskTitle.text = task.title

                // 説明（nullチェック）
                if (!task.description.isNullOrEmpty()) {
                    tvTaskDescription.text = task.description
                    tvTaskDescription.visibility = View.VISIBLE
                } else {
                    tvTaskDescription.visibility = View.GONE
                }

                // ステータスに応じた表示
                when (task.status) {
                    "completed" -> {
                        tvTaskTitle.paintFlags = tvTaskTitle.paintFlags or Paint.STRIKE_THRU_TEXT_FLAG
                        tvTaskTitle.alpha = 0.6f
                        tvTaskDescription.alpha = 0.6f
                        btnComplete.visibility = View.GONE
                        tvStatusBadge.text = "完了"
                        tvStatusBadge.setBackgroundResource(R.drawable.badge_completed)
                        tvStatusBadge.visibility = View.VISIBLE
                    }
                    "in_progress" -> {
                        tvTaskTitle.paintFlags = tvTaskTitle.paintFlags and Paint.STRIKE_THRU_TEXT_FLAG.inv()
                        tvTaskTitle.alpha = 1.0f
                        tvTaskDescription.alpha = 1.0f
                        btnComplete.visibility = View.VISIBLE
                        tvStatusBadge.text = "進行中"
                        tvStatusBadge.setBackgroundResource(R.drawable.badge_in_progress)
                        tvStatusBadge.visibility = View.VISIBLE
                    }
                    else -> {
                        tvTaskTitle.paintFlags = tvTaskTitle.paintFlags and Paint.STRIKE_THRU_TEXT_FLAG.inv()
                        tvTaskTitle.alpha = 1.0f
                        tvTaskDescription.alpha = 1.0f
                        btnComplete.visibility = View.VISIBLE
                        tvStatusBadge.visibility = View.GONE
                    }
                }

                // 優先度の表示
                when (task.priority) {
                    "high" -> {
                        ivPriority.setColorFilter(
                            ContextCompat.getColor(root.context, R.color.priority_high)
                        )
                        ivPriority.visibility = View.VISIBLE
                    }
                    "medium" -> {
                        ivPriority.setColorFilter(
                            ContextCompat.getColor(root.context, R.color.priority_medium)
                        )
                        ivPriority.visibility = View.VISIBLE
                    }
                    "low" -> {
                        ivPriority.setColorFilter(
                            ContextCompat.getColor(root.context, R.color.priority_low)
                        )
                        ivPriority.visibility = View.VISIBLE
                    }
                    else -> {
                        ivPriority.visibility = View.GONE
                    }
                }

                // 期限の表示
                if (!task.due_date.isNullOrEmpty()) {
                    try {
                        val inputFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                        val outputFormat = SimpleDateFormat("MM/dd", Locale.getDefault())
                        val date = inputFormat.parse(task.due_date)
                        tvDueDate.text = "〆 ${outputFormat.format(date!!)}"
                        tvDueDate.visibility = View.VISIBLE

                        // 期限切れチェック
                        if (date.before(Date()) && task.status != "completed") {
                            tvDueDate.setTextColor(
                                ContextCompat.getColor(root.context, R.color.error)
                            )
                        } else {
                            tvDueDate.setTextColor(
                                ContextCompat.getColor(root.context, R.color.text_secondary)
                            )
                        }
                    } catch (e: Exception) {
                        tvDueDate.visibility = View.GONE
                    }
                } else {
                    tvDueDate.visibility = View.GONE
                }

                // クリックリスナー
                root.setOnClickListener {
                    onTaskClick(task)
                }

                // 完了ボタン
                btnComplete.setOnClickListener {
                    onTaskComplete(task)
                }

                // 削除ボタン
                btnDelete.setOnClickListener {
                    onTaskDelete(task)
                }
            }
        }
    }

    class TaskDiffCallback : DiffUtil.ItemCallback<Task>() {
        override fun areItemsTheSame(oldItem: Task, newItem: Task): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: Task, newItem: Task): Boolean {
            return oldItem == newItem
        }
    }
}
```

---

## 📝 STEP 3: Create Task Item Layout

**File:** `mobileandroid/app/src/main/res/layout/item_task.xml`

**Purpose:** Layout for each task in the list

**Complete Code:**

```xml
<?xml version="1.0" encoding="utf-8"?>
<com.google.android.material.card.MaterialCardView xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:app="http://schemas.android.com/apk/res-auto"
    xmlns:tools="http://schemas.android.com/tools"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:layout_marginHorizontal="@dimen/spacing_md"
    android:layout_marginVertical="@dimen/spacing_sm"
    app:cardBackgroundColor="@color/white"
    app:cardCornerRadius="16dp"
    app:cardElevation="2dp"
    app:strokeColor="@color/stroke_light"
    app:strokeWidth="1dp">

    <androidx.constraintlayout.widget.ConstraintLayout
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:padding="@dimen/spacing_md">

        <!-- Priority Indicator -->
        <ImageView
            android:id="@+id/iv_priority"
            android:layout_width="24dp"
            android:layout_height="24dp"
            android:src="@drawable/ic_priority"
            android:contentDescription="@string/priority_indicator"
            app:layout_constraintStart_toStartOf="parent"
            app:layout_constraintTop_toTopOf="parent"
            app:tint="@color/priority_high" />

        <!-- Task Title -->
        <TextView
            android:id="@+id/tv_task_title"
            android:layout_width="0dp"
            android:layout_height="wrap_content"
            android:layout_marginStart="@dimen/spacing_sm"
            android:layout_marginEnd="@dimen/spacing_sm"
            android:textColor="@color/text_primary"
            android:textSize="16sp"
            android:textStyle="bold"
            android:maxLines="2"
            android:ellipsize="end"
            app:layout_constraintEnd_toStartOf="@id/btn_complete"
            app:layout_constraintStart_toEndOf="@id/iv_priority"
            app:layout_constraintTop_toTopOf="parent"
            tools:text="Complete project documentation" />

        <!-- Task Description -->
        <TextView
            android:id="@+id/tv_task_description"
            android:layout_width="0dp"
            android:layout_height="wrap_content"
            android:layout_marginTop="@dimen/spacing_xs"
            android:layout_marginEnd="@dimen/spacing_sm"
            android:textColor="@color/text_secondary"
            android:textSize="14sp"
            android:maxLines="2"
            android:ellipsize="end"
            app:layout_constraintEnd_toStartOf="@id/btn_complete"
            app:layout_constraintStart_toStartOf="@id/tv_task_title"
            app:layout_constraintTop_toBottomOf="@id/tv_task_title"
            tools:text="Write comprehensive documentation for all API endpoints" />

        <!-- Status Badge -->
        <TextView
            android:id="@+id/tv_status_badge"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:layout_marginTop="@dimen/spacing_sm"
            android:background="@drawable/badge_in_progress"
            android:paddingHorizontal="@dimen/spacing_sm"
            android:paddingVertical="4dp"
            android:text="進行中"
            android:textColor="@color/white"
            android:textSize="11sp"
            android:textStyle="bold"
            app:layout_constraintStart_toStartOf="@id/tv_task_title"
            app:layout_constraintTop_toBottomOf="@id/tv_task_description" />

        <!-- Due Date -->
        <TextView
            android:id="@+id/tv_due_date"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:layout_marginStart="@dimen/spacing_sm"
            android:drawablePadding="4dp"
            android:textColor="@color/text_secondary"
            android:textSize="12sp"
            app:drawableStartCompat="@drawable/ic_calendar"
            app:drawableTint="@color/text_secondary"
            app:layout_constraintBottom_toBottomOf="@id/tv_status_badge"
            app:layout_constraintStart_toEndOf="@id/tv_status_badge"
            app:layout_constraintTop_toTopOf="@id/tv_status_badge"
            tools:text="〆 12/31" />

        <!-- Complete Button -->
        <com.google.android.material.button.MaterialButton
            android:id="@+id/btn_complete"
            style="@style/Widget.Material3.Button.IconButton"
            android:layout_width="40dp"
            android:layout_height="40dp"
            android:contentDescription="@string/complete_task"
            app:icon="@drawable/ic_check"
            app:iconGravity="textStart"
            app:iconPadding="0dp"
            app:iconSize="20dp"
            app:iconTint="@color/success"
            app:layout_constraintEnd_toStartOf="@id/btn_delete"
            app:layout_constraintTop_toTopOf="parent" />

        <!-- Delete Button -->
        <com.google.android.material.button.MaterialButton
            android:id="@+id/btn_delete"
            style="@style/Widget.Material3.Button.IconButton"
            android:layout_width="40dp"
            android:layout_height="40dp"
            android:contentDescription="@string/delete_task"
            app:icon="@drawable/ic_delete"
            app:iconGravity="textStart"
            app:iconPadding="0dp"
            app:iconSize="20dp"
            app:iconTint="@color/error"
            app:layout_constraintEnd_toEndOf="parent"
            app:layout_constraintTop_toTopOf="parent" />

    </androidx.constraintlayout.widget.ConstraintLayout>

</com.google.android.material.card.MaterialCardView>
```

---

## 📝 STEP 4: Update MainActivity

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/MainActivity.kt`

**Changes Required:** Replace Toast-only implementation with actual task list

**Complete Updated Code:**

```kotlin
package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityMainBinding
import ecccomp.s2240788.mobile_android.ui.adapters.TaskAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.LogoutViewModel
import ecccomp.s2240788.mobile_android.ui.viewmodels.TaskViewModel
import com.google.android.material.tabs.TabLayout

/**
 * MainActivity
 * タスク一覧を表示するメイン画面
 */
class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var taskViewModel: TaskViewModel
    private lateinit var logoutViewModel: LogoutViewModel
    private lateinit var taskAdapter: TaskAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        taskViewModel = ViewModelProvider(this)[TaskViewModel::class.java]
        logoutViewModel = ViewModelProvider(this)[LogoutViewModel::class.java]

        setupRecyclerView()
        setupClickListeners()
        setupObservers()
        setupTabLayout()

        // 初回タスク取得
        taskViewModel.fetchTasks()
    }

    private fun setupRecyclerView() {
        taskAdapter = TaskAdapter(
            onTaskClick = { task ->
                // タスク詳細画面へ遷移（将来実装）
                // val intent = Intent(this, TaskDetailActivity::class.java)
                // intent.putExtra("task_id", task.id)
                // startActivity(intent)

                // 現在は編集画面へ
                val intent = Intent(this, EditTaskActivity::class.java)
                intent.putExtra("task_id", task.id)
                startActivity(intent)
            },
            onTaskComplete = { task ->
                if (task.status != "completed") {
                    taskViewModel.completeTask(task.id)
                }
            },
            onTaskDelete = { task ->
                showDeleteConfirmationDialog(task.id)
            }
        )

        binding.rvTasks.apply {
            layoutManager = LinearLayoutManager(this@MainActivity)
            adapter = taskAdapter
            setHasFixedSize(true)
        }

        // Pull to refresh
        binding.swipeRefresh.setOnRefreshListener {
            taskViewModel.fetchTasks()
        }
    }

    private fun setupClickListeners() {
        // FAB: 新規タスク作成
        binding.fabAddTask.setOnClickListener {
            val intent = Intent(this, AddTaskActivity::class.java)
            startActivity(intent)
        }

        // 仮のログアウトボタン（長押し）
        binding.btnLanguage?.setOnLongClickListener {
            showLogoutDialog()
            true
        }
    }

    private fun setupTabLayout() {
        binding.tabLayout?.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab?) {
                when (tab?.position) {
                    0 -> taskViewModel.applyFilter(TaskViewModel.TaskFilter.ALL)
                    1 -> taskViewModel.applyFilter(TaskViewModel.TaskFilter.PENDING)
                    2 -> taskViewModel.applyFilter(TaskViewModel.TaskFilter.COMPLETED)
                }
            }

            override fun onTabUnselected(tab: TabLayout.Tab?) {}
            override fun onTabReselected(tab: TabLayout.Tab?) {}
        })
    }

    private fun setupObservers() {
        // タスクリスト
        taskViewModel.filteredTasks.observe(this) { tasks ->
            taskAdapter.submitList(tasks)

            // Empty state
            if (tasks.isEmpty()) {
                binding.emptyStateView?.visibility = View.VISIBLE
                binding.rvTasks.visibility = View.GONE
            } else {
                binding.emptyStateView?.visibility = View.GONE
                binding.rvTasks.visibility = View.VISIBLE
            }
        }

        // Loading state
        taskViewModel.isLoading.observe(this) { isLoading ->
            binding.swipeRefresh.isRefreshing = isLoading
        }

        // Error handling
        taskViewModel.error.observe(this) { error ->
            error?.let {
                showError(it)
                taskViewModel.clearError()
            }
        }

        // Task completed
        taskViewModel.taskCompleted.observe(this) { task ->
            task?.let {
                Toast.makeText(this, "タスクを完了しました！", Toast.LENGTH_SHORT).show()
                taskViewModel.clearTaskCompleted()
            }
        }

        // Task deleted
        taskViewModel.taskDeleted.observe(this) { deleted ->
            if (deleted) {
                Toast.makeText(this, "タスクを削除しました", Toast.LENGTH_SHORT).show()
                taskViewModel.clearTaskDeleted()
            }
        }

        // Logout
        logoutViewModel.logoutSuccess.observe(this) { success ->
            if (success) {
                val intent = Intent(this, LoginActivity::class.java)
                intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                startActivity(intent)
                finish()
            }
        }
    }

    private fun showDeleteConfirmationDialog(taskId: Int) {
        AlertDialog.Builder(this)
            .setTitle("タスクの削除")
            .setMessage("このタスクを削除しますか？")
            .setPositiveButton("削除") { _, _ ->
                taskViewModel.deleteTask(taskId)
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }

    private fun showLogoutDialog() {
        AlertDialog.Builder(this)
            .setTitle("ログアウト")
            .setMessage("ログアウトしますか？")
            .setPositiveButton("ログアウト") { _, _ ->
                logoutViewModel.logout()
            }
            .setNegativeButton("キャンセル", null)
            .show()
    }

    private fun showError(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    }

    override fun onResume() {
        super.onResume()
        // 画面に戻った時にタスクを再取得
        taskViewModel.fetchTasks()
    }
}
```

---

## 📝 STEP 5: Update MainActivity Layout

**File:** `mobileandroid/app/src/main/res/layout/activity_main.xml`

**Changes Required:** Add RecyclerView, TabLayout, Empty State, SwipeRefreshLayout

**Find the existing layout and update to:**

```xml
<?xml version="1.0" encoding="utf-8"?>
<androidx.coordinatorlayout.widget.CoordinatorLayout xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:app="http://schemas.android.com/apk/res-auto"
    xmlns:tools="http://schemas.android.com/tools"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    android:background="@color/background"
    tools:context=".ui.activities.MainActivity">

    <!-- App Bar -->
    <com.google.android.material.appbar.AppBarLayout
        android:id="@+id/app_bar"
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:background="@color/primary"
        android:elevation="4dp">

        <com.google.android.material.appbar.MaterialToolbar
            android:id="@+id/toolbar"
            android:layout_width="match_parent"
            android:layout_height="?attr/actionBarSize"
            android:background="@color/primary"
            app:title="タスク"
            app:titleTextColor="@color/white" />

        <!-- Filter Tabs -->
        <com.google.android.material.tabs.TabLayout
            android:id="@+id/tab_layout"
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:background="@color/primary"
            app:tabTextColor="@color/white"
            app:tabSelectedTextColor="@color/white"
            app:tabIndicatorColor="@color/white"
            app:tabMode="fixed">

            <com.google.android.material.tabs.TabItem
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="すべて" />

            <com.google.android.material.tabs.TabItem
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="未完了" />

            <com.google.android.material.tabs.TabItem
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:text="完了" />

        </com.google.android.material.tabs.TabLayout>

    </com.google.android.material.appbar.AppBarLayout>

    <!-- Main Content -->
    <androidx.swiperefreshlayout.widget.SwipeRefreshLayout
        android:id="@+id/swipe_refresh"
        android:layout_width="match_parent"
        android:layout_height="match_parent"
        app:layout_behavior="@string/appbar_scrolling_view_behavior">

        <androidx.constraintlayout.widget.ConstraintLayout
            android:layout_width="match_parent"
            android:layout_height="match_parent">

            <!-- Task List -->
            <androidx.recyclerview.widget.RecyclerView
                android:id="@+id/rv_tasks"
                android:layout_width="match_parent"
                android:layout_height="match_parent"
                android:clipToPadding="false"
                android:paddingTop="@dimen/spacing_sm"
                android:paddingBottom="80dp"
                tools:listitem="@layout/item_task" />

            <!-- Empty State -->
            <LinearLayout
                android:id="@+id/empty_state_view"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:gravity="center"
                android:orientation="vertical"
                android:visibility="gone"
                app:layout_constraintBottom_toBottomOf="parent"
                app:layout_constraintEnd_toEndOf="parent"
                app:layout_constraintStart_toStartOf="parent"
                app:layout_constraintTop_toTopOf="parent">

                <ImageView
                    android:layout_width="120dp"
                    android:layout_height="120dp"
                    android:alpha="0.3"
                    android:contentDescription="@string/no_tasks"
                    android:src="@drawable/ic_task"
                    app:tint="@color/text_secondary" />

                <TextView
                    android:layout_width="wrap_content"
                    android:layout_height="wrap_content"
                    android:layout_marginTop="@dimen/spacing_md"
                    android:text="タスクがありません"
                    android:textColor="@color/text_secondary"
                    android:textSize="18sp"
                    android:textStyle="bold" />

                <TextView
                    android:layout_width="wrap_content"
                    android:layout_height="wrap_content"
                    android:layout_marginTop="@dimen/spacing_xs"
                    android:text="右下のボタンから\n新しいタスクを作成しましょう"
                    android:textAlignment="center"
                    android:textColor="@color/text_secondary"
                    android:textSize="14sp" />

            </LinearLayout>

        </androidx.constraintlayout.widget.ConstraintLayout>

    </androidx.swiperefreshlayout.widget.SwipeRefreshLayout>

    <!-- FAB: Add Task -->
    <com.google.android.material.floatingactionbutton.FloatingActionButton
        android:id="@+id/fab_add_task"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:layout_gravity="bottom|end"
        android:layout_margin="@dimen/spacing_lg"
        android:contentDescription="@string/add_task"
        app:backgroundTint="@color/primary"
        app:srcCompat="@drawable/ic_add"
        app:tint="@color/white" />

</androidx.coordinatorlayout.widget.CoordinatorLayout>
```

---

## 📝 STEP 6: Implement AddTaskActivity

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/AddTaskActivity.java`

**Current Status:** Empty skeleton

**Complete Implementation:**

```java
package ecccomp.s2240788.mobile_android.ui.activities;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.lifecycle.ViewModelProvider;

import ecccomp.s2240788.mobile_android.R;
import ecccomp.s2240788.mobile_android.databinding.ActivityAddTaskBinding;
import ecccomp.s2240788.mobile_android.ui.viewmodels.AddTaskViewModel;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Locale;

/**
 * AddTaskActivity
 * 新規タスク作成画面
 */
public class AddTaskActivity extends AppCompatActivity {

    private ActivityAddTaskBinding binding;
    private AddTaskViewModel viewModel;
    private String selectedDueDate = null;
    private Calendar calendar = Calendar.getInstance();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityAddTaskBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        viewModel = new ViewModelProvider(this).get(AddTaskViewModel.class);

        setupToolbar();
        setupPrioritySpinner();
        setupClickListeners();
        setupObservers();
    }

    private void setupToolbar() {
        setSupportActionBar(binding.toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("新しいタスク");
        }
        binding.toolbar.setNavigationOnClickListener(v -> finish());
    }

    private void setupPrioritySpinner() {
        String[] priorities = {"低", "中", "高"};
        ArrayAdapter<String> adapter = new ArrayAdapter<>(
                this,
                android.R.layout.simple_spinner_dropdown_item,
                priorities
        );
        binding.spinnerPriority.setAdapter(adapter);
        binding.spinnerPriority.setSelection(1); // Default: Medium
    }

    private void setupClickListeners() {
        // Due Date Picker
        binding.btnSelectDueDate.setOnClickListener(v -> showDatePicker());

        // Clear Due Date
        binding.btnClearDueDate.setOnClickListener(v -> {
            selectedDueDate = null;
            binding.tvSelectedDate.setText("期限なし");
            binding.btnClearDueDate.setVisibility(View.GONE);
        });

        // Save Task
        binding.btnSaveTask.setOnClickListener(v -> {
            if (validateInputs()) {
                createTask();
            }
        });
    }

    private void setupObservers() {
        // Loading state
        viewModel.getIsLoading().observe(this, isLoading -> {
            binding.progressBar.setVisibility(isLoading ? View.VISIBLE : View.GONE);
            binding.btnSaveTask.setEnabled(!isLoading);
        });

        // Error handling
        viewModel.getError().observe(this, error -> {
            if (error != null) {
                showError(error);
                viewModel.clearError();
            }
        });

        // Success
        viewModel.getTaskCreated().observe(this, success -> {
            if (success) {
                Toast.makeText(this, "タスクを作成しました！", Toast.LENGTH_SHORT).show();
                finish();
            }
        });
    }

    private void showDatePicker() {
        DatePickerDialog datePickerDialog = new DatePickerDialog(
                this,
                (view, year, month, dayOfMonth) -> {
                    calendar.set(Calendar.YEAR, year);
                    calendar.set(Calendar.MONTH, month);
                    calendar.set(Calendar.DAY_OF_MONTH, dayOfMonth);

                    SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
                    selectedDueDate = sdf.format(calendar.getTime());

                    SimpleDateFormat displayFormat = new SimpleDateFormat("yyyy年MM月dd日", Locale.JAPANESE);
                    binding.tvSelectedDate.setText(displayFormat.format(calendar.getTime()));
                    binding.btnClearDueDate.setVisibility(View.VISIBLE);
                },
                calendar.get(Calendar.YEAR),
                calendar.get(Calendar.MONTH),
                calendar.get(Calendar.DAY_OF_MONTH)
        );
        datePickerDialog.show();
    }

    private boolean validateInputs() {
        String title = binding.etTitle.getText().toString().trim();

        if (title.isEmpty()) {
            binding.tilTitle.setError("タイトルは必須です");
            return false;
        }

        binding.tilTitle.setError(null);
        return true;
    }

    private void createTask() {
        String title = binding.etTitle.getText().toString().trim();
        String description = binding.etDescription.getText().toString().trim();

        // Priority mapping
        String priority;
        switch (binding.spinnerPriority.getSelectedItemPosition()) {
            case 0: priority = "low"; break;
            case 2: priority = "high"; break;
            default: priority = "medium"; break;
        }

        viewModel.createTask(title, description, priority, selectedDueDate);
    }

    private void showError(String message) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show();
    }
}
```

---

## 📝 STEP 7: Create AddTaskViewModel

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/viewmodels/AddTaskViewModel.kt`

**Complete Code:**

```kotlin
package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateTaskRequest
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * AddTaskViewModel
 * 新規タスク作成のビジネスロジック
 */
class AddTaskViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _taskCreated = MutableLiveData<Boolean>()
    val taskCreated: LiveData<Boolean> = _taskCreated

    /**
     * タスクを作成
     */
    fun createTask(
        title: String,
        description: String?,
        priority: String,
        dueDate: String?
    ) {
        viewModelScope.launch {
            try {
                // Validation
                if (title.trim().isEmpty()) {
                    _error.value = "タイトルは必須です"
                    return@launch
                }

                _isLoading.value = true
                _error.value = null

                val request = CreateTaskRequest(
                    title = title.trim(),
                    description = if (description.isNullOrBlank()) null else description.trim(),
                    priority = priority,
                    due_date = dueDate,
                    status = "pending"
                )

                val response = apiService.createTask(request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        _taskCreated.value = true
                    } else {
                        _error.value = "タスクの作成に失敗しました"
                    }
                } else {
                    _error.value = when (response.code()) {
                        422 -> "入力データが無効です"
                        500 -> "サーバーエラーが発生しました"
                        else -> "タスクの作成に失敗しました: ${response.message()}"
                    }
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearError() {
        _error.value = null
    }
}
```

---

## 📝 STEP 8: Update AddTask Layout

**File:** `mobileandroid/app/src/main/res/layout/activity_add_task.xml`

**Note:** Layout already exists. Verify it has these components:
- Toolbar
- EditText for title (et_title, til_title)
- EditText for description (et_description)
- Spinner for priority (spinner_priority)
- Button for due date (btn_select_due_date)
- TextView for selected date (tv_selected_date)
- Button to clear date (btn_clear_due_date)
- Button to save (btn_save_task)
- ProgressBar (progress_bar)

If missing, reference the existing layout and ensure IDs match the Java code above.

---

## 📝 STEP 9: Implement EditTaskActivity

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/EditTaskActivity.kt`

**Current Status:** Skeleton implementation

**Complete Implementation:**

```kotlin
package ecccomp.s2240788.mobile_android.ui.activities

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.databinding.ActivityEditTaskBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.EditTaskViewModel
import java.text.SimpleDateFormat
import java.util.*

/**
 * EditTaskActivity
 * タスク編集画面
 */
class EditTaskActivity : AppCompatActivity() {

    private lateinit var binding: ActivityEditTaskBinding
    private lateinit var viewModel: EditTaskViewModel
    private var taskId: Int = -1
    private var selectedDueDate: String? = null
    private val calendar = Calendar.getInstance()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityEditTaskBinding.inflate(layoutInflater)
        setContentView(binding.root)

        taskId = intent.getIntExtra("task_id", -1)
        if (taskId == -1) {
            showError("タスクIDが無効です")
            finish()
            return
        }

        viewModel = ViewModelProvider(this)[EditTaskViewModel::class.java]

        setupToolbar()
        setupPrioritySpinner()
        setupClickListeners()
        setupObservers()

        // タスクデータを読み込む
        viewModel.loadTask(taskId)
    }

    private fun setupToolbar() {
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            setDisplayHomeAsUpEnabled(true)
            title = "タスクを編集"
        }
        binding.toolbar.setNavigationOnClickListener { finish() }
    }

    private fun setupPrioritySpinner() {
        val priorities = arrayOf("低", "中", "高")
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_dropdown_item, priorities)
        binding.spinnerPriority.adapter = adapter
    }

    private fun setupClickListeners() {
        // Due Date Picker
        binding.btnSelectDueDate.setOnClickListener {
            showDatePicker()
        }

        // Clear Due Date
        binding.btnClearDueDate.setOnClickListener {
            selectedDueDate = null
            binding.tvSelectedDate.text = "期限なし"
            binding.btnClearDueDate.visibility = View.GONE
        }

        // Save Task
        binding.btnSaveTask.setOnClickListener {
            if (validateInputs()) {
                updateTask()
            }
        }
    }

    private fun setupObservers() {
        // Task data loaded
        viewModel.task.observe(this) { task ->
            task?.let {
                // タイトル
                binding.etTitle.setText(it.title)

                // 説明
                binding.etDescription.setText(it.description)

                // 優先度
                val priorityIndex = when (it.priority) {
                    "low" -> 0
                    "high" -> 2
                    else -> 1
                }
                binding.spinnerPriority.setSelection(priorityIndex)

                // 期限
                if (!it.due_date.isNullOrEmpty()) {
                    selectedDueDate = it.due_date
                    try {
                        val inputFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                        val date = inputFormat.parse(it.due_date)
                        val displayFormat = SimpleDateFormat("yyyy年MM月dd日", Locale.JAPANESE)
                        binding.tvSelectedDate.text = displayFormat.format(date!!)
                        binding.btnClearDueDate.visibility = View.VISIBLE
                    } catch (e: Exception) {
                        binding.tvSelectedDate.text = "期限なし"
                    }
                }
            }
        }

        // Loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnSaveTask.isEnabled = !isLoading
        }

        // Error handling
        viewModel.error.observe(this) { error ->
            error?.let {
                showError(it)
                viewModel.clearError()
            }
        }

        // Update success
        viewModel.taskUpdated.observe(this) { success ->
            if (success) {
                Toast.makeText(this, "タスクを更新しました！", Toast.LENGTH_SHORT).show()
                finish()
            }
        }
    }

    private fun showDatePicker() {
        DatePickerDialog(
            this,
            { _, year, month, dayOfMonth ->
                calendar.set(Calendar.YEAR, year)
                calendar.set(Calendar.MONTH, month)
                calendar.set(Calendar.DAY_OF_MONTH, dayOfMonth)

                val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                selectedDueDate = sdf.format(calendar.time)

                val displayFormat = SimpleDateFormat("yyyy年MM月dd日", Locale.JAPANESE)
                binding.tvSelectedDate.text = displayFormat.format(calendar.time)
                binding.btnClearDueDate.visibility = View.VISIBLE
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        ).show()
    }

    private fun validateInputs(): Boolean {
        val title = binding.etTitle.text.toString().trim()

        if (title.isEmpty()) {
            binding.tilTitle.error = "タイトルは必須です"
            return false
        }

        binding.tilTitle.error = null
        return true
    }

    private fun updateTask() {
        val title = binding.etTitle.text.toString().trim()
        val description = binding.etDescription.text.toString().trim()

        val priority = when (binding.spinnerPriority.selectedItemPosition) {
            0 -> "low"
            2 -> "high"
            else -> "medium"
        }

        viewModel.updateTask(taskId, title, description, priority, selectedDueDate)
    }

    private fun showError(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    }
}
```

---

## 📝 STEP 10: Create EditTaskViewModel

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/viewmodels/EditTaskViewModel.kt`

**Complete Code:**

```kotlin
package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.CreateTaskRequest
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * EditTaskViewModel
 * タスク編集のビジネスロジック
 */
class EditTaskViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _task = MutableLiveData<Task?>()
    val task: LiveData<Task?> = _task

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _taskUpdated = MutableLiveData<Boolean>()
    val taskUpdated: LiveData<Boolean> = _taskUpdated

    /**
     * タスクを読み込む
     */
    fun loadTask(taskId: Int) {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.getTasks()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        val taskList = apiResponse.data ?: emptyList()
                        _task.value = taskList.find { it.id == taskId }

                        if (_task.value == null) {
                            _error.value = "タスクが見つかりません"
                        }
                    } else {
                        _error.value = "タスクの読み込みに失敗しました"
                    }
                } else {
                    _error.value = "タスクの読み込みに失敗しました: ${response.message()}"
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * タスクを更新
     */
    fun updateTask(
        taskId: Int,
        title: String,
        description: String?,
        priority: String,
        dueDate: String?
    ) {
        viewModelScope.launch {
            try {
                // Validation
                if (title.trim().isEmpty()) {
                    _error.value = "タイトルは必須です"
                    return@launch
                }

                _isLoading.value = true
                _error.value = null

                val request = CreateTaskRequest(
                    title = title.trim(),
                    description = if (description.isNullOrBlank()) null else description.trim(),
                    priority = priority,
                    due_date = dueDate,
                    status = _task.value?.status ?: "pending"
                )

                val response = apiService.updateTask(taskId, request)

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse != null && apiResponse.success) {
                        _taskUpdated.value = true
                    } else {
                        _error.value = "タスクの更新に失敗しました"
                    }
                } else {
                    _error.value = when (response.code()) {
                        422 -> "入力データが無効です"
                        404 -> "タスクが見つかりません"
                        500 -> "サーバーエラーが発生しました"
                        else -> "タスクの更新に失敗しました: ${response.message()}"
                    }
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearError() {
        _error.value = null
    }
}
```

---

## 📝 STEP 11: Add Required Drawables & Colors

**Files to create/update:**

### Colors (`res/values/colors.xml`)
Add these colors if not present:

```xml
<color name="priority_high">#F44336</color>
<color name="priority_medium">#FF9800</color>
<color name="priority_low">#4CAF50</color>
<color name="success">#4CAF50</color>
<color name="stroke_light">#E0E0E0</color>
<color name="background">#F5F5F5</color>
```

### Drawable for badges (`res/drawable/badge_completed.xml`)
```xml
<?xml version="1.0" encoding="utf-8"?>
<shape xmlns:android="http://schemas.android.com/apk/res/android">
    <solid android:color="@color/success" />
    <corners android:radius="12dp" />
</shape>
```

### Drawable for badges (`res/drawable/badge_in_progress.xml`)
```xml
<?xml version="1.0" encoding="utf-8"?>
<shape xmlns:android="http://schemas.android.com/apk/res/android">
    <solid android:color="@color/primary" />
    <corners android:radius="12dp" />
</shape>
```

---

## 📝 STEP 12: Add Required Icons

**Required Drawable Icons:**
- `ic_priority.xml` - Priority indicator
- `ic_calendar.xml` - Calendar icon
- `ic_check.xml` - Checkmark (complete)
- `ic_delete.xml` - Delete icon
- `ic_add.xml` - Add FAB icon
- `ic_task.xml` - Task icon for empty state

**Note:** If these don't exist, use Material Icons or copy from similar projects.

---

## 📝 STEP 13: Add Strings

**File:** `mobileandroid/app/src/main/res/values/strings.xml`

Add these strings:

```xml
<!-- Task Management -->
<string name="add_task">タスクを追加</string>
<string name="edit_task">タスクを編集</string>
<string name="complete_task">完了</string>
<string name="delete_task">削除</string>
<string name="no_tasks">タスクがありません</string>
<string name="priority_indicator">優先度</string>
<string name="task_title">タスクタイトル</string>
<string name="task_description">説明（任意）</string>
<string name="priority">優先度</string>
<string name="due_date">期限</string>
<string name="select_due_date">期限を選択</string>
<string name="save_task">保存</string>
```

---

## ✅ Testing Checklist

After implementation, test these scenarios:

### Happy Path
- [ ] Launch app → See empty task list with empty state
- [ ] Click FAB → AddTaskActivity opens
- [ ] Fill title "Test Task" → Save → Returns to main screen
- [ ] See "Test Task" in list
- [ ] Click task → EditTaskActivity opens with data
- [ ] Change title → Save → See updated task
- [ ] Click complete button → Task marked as completed (strikethrough)
- [ ] Click delete button → Confirmation dialog → Delete → Task removed
- [ ] Create task with priority "High" → See red indicator
- [ ] Create task with due date → See date displayed
- [ ] Pull to refresh → Tasks reload

### Filters
- [ ] Create multiple tasks with different statuses
- [ ] Click "すべて" tab → See all tasks
- [ ] Click "未完了" tab → See only pending/in_progress tasks
- [ ] Click "完了" tab → See only completed tasks

### Validation
- [ ] Try to save task without title → Error: "タイトルは必須です"
- [ ] Save task with only title → Success
- [ ] Save task with description → Description displayed
- [ ] Select due date → Date displayed
- [ ] Clear due date → "期限なし" displayed

### Error Handling
- [ ] Turn off backend → Error message displayed
- [ ] Invalid task ID → Error message
- [ ] Network timeout → Error handled gracefully

### UI/UX
- [ ] Loading spinner shows during API calls
- [ ] SwipeRefreshLayout works
- [ ] Empty state shows when no tasks
- [ ] Task list updates after create/edit/delete
- [ ] Back navigation works correctly

---

## 📊 Estimated Effort

| Step | Task | Estimated Time |
|------|------|----------------|
| 1 | TaskViewModel | 1-2 hours |
| 2 | TaskAdapter | 2-3 hours |
| 3 | Task Item Layout | 1-2 hours |
| 4-5 | MainActivity Update | 2-3 hours |
| 6-7 | AddTaskActivity | 2-3 hours |
| 8 | AddTaskViewModel | 1 hour |
| 9-10 | EditTaskActivity | 2-3 hours |
| 11-13 | Resources (drawables, colors, strings) | 1-2 hours |
| Testing | Full feature testing | 2-3 hours |

**Total:** 14-22 hours (~2-3 days of focused work)

---

## 🎯 Success Criteria

Feature is complete when:
1. ✅ All files created/modified
2. ✅ No compilation errors
3. ✅ All tests in checklist pass
4. ✅ Backend API integration working
5. ✅ User can perform all CRUD operations
6. ✅ Filters working correctly
7. ✅ Error handling comprehensive
8. ✅ UI polished and responsive
9. ✅ No crashes or ANRs
10. ✅ Code follows project conventions

---

## 📝 Notes

- Backend API is already complete and tested
- Follow Material Design 3 guidelines
- Use Japanese for all user-facing text
- Maintain consistency with authentication screens
- Test on both emulator and real device
- Consider offline support (future enhancement)
- Task detail screen can be implemented later

---

## 🚀 Ready to Implement!

This task file contains everything Cursor needs to implement Task Management feature:
- Complete code for all files
- Detailed instructions
- Testing checklist
- Success criteria

After completing this, the app will be a **functional todo application** with create, view, edit, complete, and delete tasks capabilities.

**Good luck! 🎉**
