package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.LoginRequest
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import ecccomp.s2240788.mobile_android.utils.TokenManager
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

    /**
     * ログイン処理
     * Backend: POST /api/login
     * Response: { user, token, message }返回す
     */
    fun login(email: String, password: String) {
        viewModelScope.launch {
            try {
                // Validation: email format
                if (!android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
                    _error.value = "メールアドレスの形式が正しくありません"
                    return@launch
                }

                // Validation: password minimum 8 characters (backend requirement)
                if (password.length < 8) {
                    _error.value = "パスワードは8文字以上である必要があります"
                    return@launch
                }

                _isLoading.value = true
                _error.value = null

                val request = LoginRequest(email, password)
                val response = apiService.login(request)

                if (response.isSuccessful) {
                    val authResponse = response.body()
                    if (authResponse != null && authResponse.token.isNotEmpty()) {
                        // Tokenを保存
                        TokenManager.saveToken(authResponse.token)
                        _loginSuccess.value = true
                    } else {
                        _error.value = "ログインに失敗しました"
                    }
                } else {
                    // HTTPエラーの詳細な処理
                    _error.value = when (response.code()) {
                        401 -> "メールアドレスまたはパスワードが正しくありません"
                        422 -> "入力データが無効です"
                        500 -> "サーバーエラーが発生しました。しばらくしてからお試しください"
                        else -> "ログインに失敗しました: ${response.message()}"
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