package ecccomp.s2240788.mobile_android.utils

import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import android.widget.Toast
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.ui.activities.LoginActivity
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.Response
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.lang.ref.WeakReference
import java.util.concurrent.TimeUnit

object NetworkModule {

    // Sử dụng AppConfig để dễ thay đổi URL
    private const val BASE_URL = AppConfig.BASE_URL
    internal var contextRef: WeakReference<Context>? = null

    // Lazy initialization of ApiService
    val apiService: ApiService by lazy {
        provideApiService(provideRetrofit(provideOkHttpClient()))
    }

    // Timeout constants - Đồng bộ với backend
    // Local LLM (qwen2.5:1.5b) は初回ロードが遅いので長めに設定
    private const val CONNECT_TIMEOUT_SECONDS = 30L    // Kết nối: 30s
    private const val READ_TIMEOUT_SECONDS = 120L       // Đọc response: 120s
    private const val WRITE_TIMEOUT_SECONDS = 30L      // Ghi request: 30s

    /**
     * Contextを設定（Applicationから呼び出す）
     */
    fun setContext(context: Context) {
        contextRef = WeakReference(context)
    }

    fun provideOkHttpClient(): OkHttpClient {
        return OkHttpClient.Builder()
            .addInterceptor(AuthInterceptor())               // Tokenをheaderに追加
            .addInterceptor(ResponseInterceptor())           // 401エラーをキャッチして自動ログアウト
            .addInterceptor(HttpLoggingInterceptor().apply {
                level = HttpLoggingInterceptor.Level.BASIC  // Changed from BODY to BASIC for better performance
            })                                              // Log request và response
            .connectTimeout(CONNECT_TIMEOUT_SECONDS, TimeUnit.SECONDS)
            .readTimeout(READ_TIMEOUT_SECONDS, TimeUnit.SECONDS)
            .writeTimeout(WRITE_TIMEOUT_SECONDS, TimeUnit.SECONDS)
            .build()
    }

    fun provideRetrofit(okHttpClient: OkHttpClient): Retrofit {
        return Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }

    fun provideApiService(retrofit: Retrofit): ApiService {
        return retrofit.create(ApiService::class.java)
    }
}

// Auth Interceptor: token + X-Locale（APIで知識・コース等の言語を合わせる）
class AuthInterceptor : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val token = TokenManager.getToken()
        val builder = chain.request().newBuilder()
            .addHeader("Accept", "application/json")
            .addHeader("Content-Type", "application/json")

        NetworkModule.contextRef?.get()?.let { ctx ->
            builder.addHeader("X-Locale", LocaleHelper.getLanguage(ctx))
        }
        if (token != null) {
            builder.addHeader("Authorization", "Bearer $token")
        }

        return chain.proceed(builder.build())
    }
}

// Response Interceptor để catch 401 errors và auto-logout
class ResponseInterceptor : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val response = chain.proceed(chain.request())

        // 401 Unauthorizedをキャッチ
        if (response.code == 401 && !response.request.url.encodedPath.contains("/api/login")) {
            val context = NetworkModule.contextRef?.get()

            context?.let { ctx ->
                // Tokenをクリア
                TokenManager.clearToken()

                // メインスレッドで処理
                android.os.Handler(android.os.Looper.getMainLooper()).post {
                    try {
                        // Toastメッセージ表示
                        Toast.makeText(
                            ctx,
                            "セッションが期限切れです。再度ログインしてください",
                            Toast.LENGTH_LONG
                        ).show()

                        // LoginActivityへ遷移
                        val intent = Intent(ctx, LoginActivity::class.java).apply {
                            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                        }
                        ctx.startActivity(intent)
                    } catch (e: Exception) {
                        // Contextが無効な場合
                    }
                }
            }
        }

        return response
    }
}

// Token Manager để lưu trữ token với EncryptedSharedPreferences
object TokenManager {
    private const val PREFS_NAME = "auth_prefs"
    private const val TOKEN_KEY = "auth_token"

    private var encryptedPrefs: SharedPreferences? = null

    /**
     * 初期化関数 - ContextからEncryptedSharedPreferencesを取得
     * Applicationクラスでinit()を呼び出すこと
     */
    fun init(context: Context) {
        if (encryptedPrefs == null) {
            try {
                val masterKey = MasterKey.Builder(context)
                    .setKeyScheme(MasterKey.KeyScheme.AES256_GCM)
                    .build()

                encryptedPrefs = EncryptedSharedPreferences.create(
                    context,
                    PREFS_NAME,
                    masterKey,
                    EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
                    EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM
                )
            } catch (e: Exception) {
                // 暗号化キーの復号に失敗した場合（署名キーが変わった場合など）
                // フォールバック: 通常のSharedPreferencesを使用
                android.util.Log.e("TokenManager", "Failed to initialize EncryptedSharedPreferences: ${e.message}", e)
                android.util.Log.w("TokenManager", "Falling back to regular SharedPreferences")
                
                // 通常のSharedPreferencesを使用（セキュリティは低いが、アプリは起動可能）
                encryptedPrefs = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
            }
        }
    }

    /**
     * トークンを保存
     */
    fun saveToken(token: String) {
        encryptedPrefs?.edit()?.putString(TOKEN_KEY, token)?.apply()
    }

    /**
     * トークンを取得
     */
    fun getToken(): String? {
        return encryptedPrefs?.getString(TOKEN_KEY, null)
    }

    /**
     * トークンをクリア（ログアウト時）
     */
    fun clearToken() {
        encryptedPrefs?.edit()?.remove(TOKEN_KEY)?.apply()
    }

    /**
     * トークンが有効かチェック
     */
    fun isTokenValid(): Boolean {
        return getToken() != null && getToken()!!.isNotEmpty()
    }
}