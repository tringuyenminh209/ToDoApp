package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import ecccomp.s2240788.mobile_android.utils.TokenManager
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

class SplashViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _navigateToLogin = MutableLiveData<Boolean>()
    val navigateToLogin: LiveData<Boolean> = _navigateToLogin

    private val _navigateToMain = MutableLiveData<Boolean>()
    val navigateToMain: LiveData<Boolean> = _navigateToMain

    /**
     * 認証状態をチェック
     * - Tokenが存在する場合、GET /api/userで検証
     * - 200: MainActivityへ遷移
     * - 401: Tokenを削除し、LoginActivityへ遷移
     * - Network error: LoginActivityへ遷移
     */
    fun checkAuthStatus() {
        viewModelScope.launch {
            _isLoading.value = true

            // スプラッシュスクリーンを1.5秒表示（UX改善）
            delay(1500)

            // Tokenが有効かチェック
            if (TokenManager.isTokenValid()) {
                try {
                    // BackendでTokenを検証
                    val response = apiService.getUser()

                    if (response.isSuccessful) {
                        // Token有効: MainActivityへ
                        _navigateToMain.value = true
                    } else {
                        // Token無効(401): TokenをクリアしてLoginActivityへ
                        TokenManager.clearToken()
                        _navigateToLogin.value = true
                    }
                } catch (e: Exception) {
                    // Network error: LoginActivityへ（オフライン対応は後で実装）
                    TokenManager.clearToken()
                    _navigateToLogin.value = true
                }
            } else {
                // Tokenなし: LoginActivityへ
                _navigateToLogin.value = true
            }

            _isLoading.value = false
        }
    }

    fun clearNavigationFlags() {
        _navigateToLogin.value = false
        _navigateToMain.value = false
    }
}
