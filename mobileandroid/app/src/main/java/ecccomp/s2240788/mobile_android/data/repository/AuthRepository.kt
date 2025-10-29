package ecccomp.s2240788.mobile_android.data.repository

import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.utils.TokenManager
import ecccomp.s2240788.mobile_android.data.result.AuthResult

/**
 * Authentication Repository
 * API呼び出しとToken管理を担当
 * ViewModelとApiServiceの間のレイヤー
 */
class AuthRepository(
    private val apiService: ApiService
) {
    /**
     * ログイン処理
     */
    suspend fun login(email: String, password: String): AuthResult<AuthResponse> {
        return try {
            val request = LoginRequest(email, password)
            val response = apiService.login(request)

            if (response.isSuccessful) {
                val authResponse = response.body()
                if (authResponse != null && authResponse.token.isNotEmpty()) {
                    // Tokenを保存
                    TokenManager.saveToken(authResponse.token)
                    AuthResult.Success(authResponse)
                } else {
                    AuthResult.Error("ログインに失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "メールアドレスまたはパスワードが正しくありません"
                    422 -> "入力データが無効です"
                    500 -> "サーバーエラーが発生しました"
                    else -> "ログインに失敗しました: ${response.message()}"
                }
                AuthResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            AuthResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * 登録処理
     */
    suspend fun register(name: String, email: String, password: String): AuthResult<AuthResponse> {
        return try {
            val request = RegisterRequest(name, email, password)
            val response = apiService.register(request)

            if (response.isSuccessful) {
                val authResponse = response.body()
                if (authResponse != null && authResponse.token.isNotEmpty()) {
                    // Tokenを保存
                    TokenManager.saveToken(authResponse.token)
                    AuthResult.Success(authResponse)
                } else {
                    AuthResult.Error("登録に失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    409 -> "このメールアドレスは既に登録されています"
                    422 -> "入力データが無効です"
                    500 -> "サーバーエラーが発生しました"
                    else -> "登録に失敗しました: ${response.message()}"
                }
                AuthResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            AuthResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * パスワードリセット依頼
     */
    suspend fun forgotPassword(email: String): AuthResult<Map<String, String>> {
        return try {
            val request = ForgotPasswordRequest(email)
            val response = apiService.forgotPassword(request)

            if (response.isSuccessful) {
                AuthResult.Success(response.body() ?: emptyMap())
            } else {
                val errorMessage = when (response.code()) {
                    404 -> "このメールアドレスは登録されていません"
                    422 -> "入力データが無効です"
                    500 -> "サーバーエラーが発生しました"
                    else -> "リセットに失敗しました"
                }
                AuthResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            AuthResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * ログアウト処理
     */
    suspend fun logout(): AuthResult<Map<String, Any>> {
        return try {
            val response = apiService.logout()

            if (response.isSuccessful || !TokenManager.isTokenValid()) {
                // Tokenをクリア（API成功に関わらず）
                TokenManager.clearToken()
                AuthResult.Success(emptyMap())
            } else {
                // API失敗でもTokenはクリア
                TokenManager.clearToken()
                AuthResult.Success(emptyMap())
            }
        } catch (e: Exception) {
            // Network errorでもTokenはクリア
            TokenManager.clearToken()
            AuthResult.Success(emptyMap())
        }
    }

    /**
     * 現在のユーザー情報を取得
     */
    suspend fun getCurrentUser(): AuthResult<User> {
        return try {
            val response = apiService.getUser()

            if (response.isSuccessful) {
                val user = response.body()?.data
                if (user != null) {
                    AuthResult.Success(user)
                } else {
                    AuthResult.Error("ユーザー情報の取得に失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "認証に失敗しました"
                    404 -> "ユーザーが見つかりません"
                    else -> "ユーザー情報の取得に失敗しました"
                }
                AuthResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            AuthResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * Tokenが有効かチェック
     */
    fun isTokenValid(): Boolean = TokenManager.isTokenValid()

    /**
     * Tokenを削除
     */
    fun clearToken() = TokenManager.clearToken()
}

