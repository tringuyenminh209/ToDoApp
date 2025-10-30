package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class TaskDetailViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _task = MutableLiveData<Task?>()
    val task: LiveData<Task?> = _task

    private val _toast = MutableLiveData<String?>()
    val toast: LiveData<String?> = _toast

    private val _finishEvent = MutableLiveData<Boolean>()
    val finishEvent: LiveData<Boolean> = _finishEvent

    fun loadTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val res = apiService.getTasks()
                if (res.isSuccessful) {
                    val body = res.body()
                    if (body?.success == true) {
                        val list = extractTasksFromResponse(body.data)
                        _task.value = list.find { it.id == taskId }
                        if (_task.value == null) _toast.value = "タスクが見つかりません"
                    } else _toast.value = body?.message ?: "取得に失敗しました"
                } else _toast.value = "API Error: ${res.message()}"
            } catch (e: Exception) {
                _toast.value = "ネットワークエラー: ${e.message}"
            }
        }
    }

    fun completeTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val res = apiService.completeTask(taskId)
                if (res.isSuccessful && res.body()?.success == true) {
                    _toast.value = "完了しました"
                    _finishEvent.value = true
                } else _toast.value = "完了に失敗しました"
            } catch (e: Exception) { _toast.value = "ネットワークエラー: ${e.message}" }
        }
    }

    fun deleteTask(taskId: Int) {
        viewModelScope.launch {
            try {
                val res = apiService.deleteTask(taskId)
                if (res.isSuccessful && res.body()?.success == true) {
                    _toast.value = "削除しました"
                    _finishEvent.value = true
                } else _toast.value = "削除に失敗しました"
            } catch (e: Exception) { _toast.value = "ネットワークエラー: ${e.message}" }
        }
    }

    private fun extractTasksFromResponse(data: Any?): List<Task> {
        return try {
            if (data is Map<*, *>) {
                val tasksData = data["data"] as? List<*>
                tasksData?.mapNotNull { m -> if (m is Map<*, *>) convertMapToTask(m) else null } ?: emptyList()
            } else if (data is List<*>) {
                data.mapNotNull { m -> if (m is Map<*, *>) convertMapToTask(m) else null }
            } else emptyList()
        } catch (e: Exception) {
            emptyList()
        }
    }

    private fun convertMapToTask(map: Map<*, *>): Task? {
        return try {
            Task(
                id = (map["id"] as? Number)?.toInt() ?: 0,
                title = map["title"] as? String ?: "",
                description = map["description"] as? String,
                status = map["status"] as? String ?: "pending",
                priority = (map["priority"] as? Number)?.toInt() ?: 3,
                energy_level = map["energy_level"] as? String ?: "medium",
                estimated_minutes = (map["estimated_minutes"] as? Number)?.toInt(),
                deadline = map["deadline"] as? String,
                created_at = map["created_at"] as? String ?: "",
                updated_at = map["updated_at"] as? String ?: "",
                user_id = (map["user_id"] as? Number)?.toInt() ?: 0,
                project_id = (map["project_id"] as? Number)?.toInt(),
                learning_milestone_id = (map["learning_milestone_id"] as? Number)?.toInt(),
                ai_breakdown_enabled = map["ai_breakdown_enabled"] as? Boolean ?: false
            )
        } catch (e: Exception) { null }
    }

    fun clearToast() { _toast.value = null }
}


