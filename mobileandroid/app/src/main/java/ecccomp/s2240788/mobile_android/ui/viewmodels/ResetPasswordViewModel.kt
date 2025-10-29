package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.ResetPasswordRequest
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

class ResetPasswordViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _resetSuccess = MutableLiveData<Boolean>()
    val resetSuccess: LiveData<Boolean> = _resetSuccess

    fun resetPassword(email: String, token: String, password: String, passwordConfirmation: String) {
        viewModelScope.launch {
            try {
                // Validation: token must be 6 digits
                if (token.length != 6 || !token.all { it.isDigit() }) {
                    _error.value = "トークンは6桁の数字である必要があります"
                    return@launch
                }

                // Validation: password min 8 chars with uppercase, lowercase, digit
                if (password.length < 8) {
                    _error.value = "パスワードは8文字以上である必要があります"
                    return@launch
                }

                if (!password.matches(Regex("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).+$"))) {
                    _error.value = "パスワードは大文字、小文字、数字を含む必要があります"
                    return@launch
                }

                // Validation: password confirmation match
                if (password != passwordConfirmation) {
                    _error.value = "パスワードが一致しません"
                    return@launch
                }

                _isLoading.value = true
                _error.value = null

                val request = ResetPasswordRequest(email, token, password, passwordConfirmation)
                val response = apiService.resetPassword(request)

                if (response.isSuccessful) {
                    _resetSuccess.value = true
                } else {
                    _error.value = when (response.code()) {
                        422 -> "トークンが無効または期限切れです"
                        404 -> "このメールアドレスは登録されていません"
                        500 -> "サーバーエラーが発生しました。しばらくしてからお試しください"
                        else -> "パスワードリセットに失敗しました: ${response.message()}"
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

