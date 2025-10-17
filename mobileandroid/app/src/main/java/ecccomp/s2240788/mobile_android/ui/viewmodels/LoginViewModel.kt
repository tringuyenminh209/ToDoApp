package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.LoginRequest
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class LoginViewModel : ViewModel() {
    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String> = _error as LiveData<String>

    private val _loginSuccess = MutableLiveData<Boolean>()
    val loginSuccess: LiveData<Boolean> = _loginSuccess

    fun login(email: String, password: String) {
        viewModelScope.launch {
            try {
                _isLoading.value = true

                val request = LoginRequest(email, password)
                val response = apiService.login(request)

                if (response.isSuccessful) {
                    val authResponse = response.body()
                    if (authResponse != null) {
                        // Lưu token (sẽ implement sau)
                        // TokenManager.saveToken(authResponse.token)
                        _loginSuccess.value = true
                    } else {
                        _error.value = "Login failed"
                    }
                } else {
                    _error.value = "Login failed: ${response.message()}"
                }

            } catch (e: Exception) {
                _error.value = "Network error: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearError() {
        _error.value = null
    }
}