package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.google.gson.Gson
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.RegisterRequest
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import ecccomp.s2240788.mobile_android.utils.TokenManager
import kotlinx.coroutines.launch
import okhttp3.ResponseBody
import java.io.IOException

class RegisterViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _registerSuccess = MutableLiveData<Boolean>()
    val registerSuccess: LiveData<Boolean> = _registerSuccess

    /**
     * 登録処理
     * Backend: POST /api/register
     * Request: { name, email, password }
     * Response: { user, token, message } (status 201)
     */
    fun register(name: String, email: String, password: String) {
        viewModelScope.launch {
            try {
                // Validation: name required
                if (name.trim().isEmpty()) {
                    _error.value = "名前は必須です"
                    return@launch
                }

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

                val request = RegisterRequest(name, email, password)
                val response = apiService.register(request)

                if (response.isSuccessful) {
                    val authResponse = response.body()
                    if (authResponse != null && authResponse.token.isNotEmpty()) {
                        // Tokenを保存
                        TokenManager.saveToken(authResponse.token)
                        _registerSuccess.value = true
                    } else {
                        _error.value = "登録に失敗しました"
                    }
                } else {
                    // HTTP errorの処理
                    _error.value = when (response.code()) {
                        409 -> "このメールアドレスは既に登録されています"
                        422 -> {
                            // Laravel validation errorsをパース
                            parseValidationErrors(response.errorBody())
                        }
                        500 -> "サーバーエラーが発生しました。しばらくしてからお試しください"
                        else -> "登録に失敗しました: ${response.message()}"
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
     * Laravelのvalidation errorをパース
     */
    private fun parseValidationErrors(errorBody: ResponseBody?): String {
        return try {
            if (errorBody != null) {
                val errorString = errorBody.string()
                // Laravelの422エラーは通常 { "message": "...", "errors": {...} } の形式
                // ここでは簡単にmessageを表示
                if (errorString.contains("\"message\"")) {
                    val gson = Gson()
                    val jsonObject = gson.fromJson(errorString, Map::class.java)
                    jsonObject["message"] as? String ?: "入力データが無効です"
                } else {
                    "入力データが無効です"
                }
            } else {
                "入力データが無効です"
            }
        } catch (e: IOException) {
            "入力データが無効です"
        }
    }

    fun clearError() {
        _error.value = null
    }
}
