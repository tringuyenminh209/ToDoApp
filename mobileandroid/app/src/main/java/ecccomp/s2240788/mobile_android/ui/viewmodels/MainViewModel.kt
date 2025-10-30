package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.Task
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class MainViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> = _error

    private val _tasks = MutableLiveData<List<Task>?>()
    val tasks: LiveData<List<Task>?> = _tasks

    fun getTasks() {
        viewModelScope.launch {
            try {
                _isLoading.value = true

                val response = apiService.getTasks()

                if (response.isSuccessful) {
                    val apiResponse = response.body()
                    if (apiResponse?.success == true) {
                        val tasks = extractTasksFromResponse(apiResponse.data)
                        // Always post value to trigger observer
                        _tasks.postValue(tasks)
                        android.util.Log.d("MainViewModel", "Tasks loaded: ${tasks.size}")
                    } else {
                        _error.value = apiResponse?.message ?: "Failed to get tasks"
                        _tasks.postValue(emptyList()) // Post empty list on error
                    }
                } else {
                    _error.value = "API Error: ${response.message()}"
                    _tasks.postValue(emptyList())
                }

            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
                _tasks.postValue(emptyList())
                android.util.Log.e("MainViewModel", "Error loading tasks", e)
            } finally {
                _isLoading.value = false
            }
        }
    }

    private fun extractTasksFromResponse(data: Any?): List<Task> {
        return try {
            if (data is Map<*, *>) {
                val tasksData = data["data"] as? List<*>
                tasksData?.mapNotNull { taskMap ->
                    if (taskMap is Map<*, *>) {
                        convertMapToTask(taskMap)
                    } else {
                        null
                    }
                } ?: emptyList()
            } else if (data is List<*>) {
                data.mapNotNull { taskMap ->
                    if (taskMap is Map<*, *>) {
                        convertMapToTask(taskMap)
                    } else {
                        null
                    }
                }
            } else {
                emptyList()
            }
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
        } catch (e: Exception) {
            null
        }
    }
}