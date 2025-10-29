package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import ecccomp.s2240788.mobile_android.utils.TokenManager
import kotlinx.coroutines.launch

class LogoutViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _logoutSuccess = MutableLiveData<Boolean>()
    val logoutSuccess: LiveData<Boolean> = _logoutSuccess

    /**
     * ログアウト処理
     * Backend: POST /api/logout (Bearer token required)
     * Response: { "message": "ログアウト成功！" }
     */
    fun logout() {
        viewModelScope.launch {
            try {
                _isLoading.value = true
                _error.value = null

                val response = apiService.logout()

                if (response.isSuccessful) {
                    // Tokenをクリア（API成功に関わらず）
                    TokenManager.clearToken()
                    _logoutSuccess.value = true
                } else {
                    // API失敗でもTokenはクリア
                    TokenManager.clearToken()
                    _logoutSuccess.value = true
                }

            } catch (e: Exception) {
                // Network errorでもTokenはクリア
                TokenManager.clearToken()
                _logoutSuccess.value = true
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearError() {
        _error.value = null
    }
}

