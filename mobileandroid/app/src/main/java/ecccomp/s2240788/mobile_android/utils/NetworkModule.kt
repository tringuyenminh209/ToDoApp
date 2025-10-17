package ecccomp.s2240788.mobile_android.utils

import ecccomp.s2240788.mobile_android.data.api.ApiService
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.Response
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object NetworkModule {

    private const val BASE_URL = "http://10.0.2.2:8080/api/"

    fun provideOkHttpClient(): OkHttpClient {
        return OkHttpClient.Builder()
            .addInterceptor(AuthInterceptor())               // Thêm token vào header
            .addInterceptor(HttpLoggingInterceptor().apply {
                level = HttpLoggingInterceptor.Level.BODY
            })                                              // Log request và response
            .connectTimeout(30, TimeUnit.SECONDS)           // Thời gian kết nối tối đa
            .readTimeout(30, TimeUnit.SECONDS)              // Thời gian đọc tối đa
            .writeTimeout(30, TimeUnit.SECONDS)             // Thời gian ghi tối đa
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

// Auth Interceptor để thêm token vào header
class AuthInterceptor : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val token = TokenManager.getToken()

        val request = if (token != null) {
            chain.request().newBuilder()
                .addHeader("Authorization", "Bearer $token")
                .addHeader("Accept", "application/json")
                .addHeader("Content-Type", "application/json")
                .build()
        } else {
            chain.request()
        }

        return chain.proceed(request)
    }
}

// Token Manager để lưu trữ token
object TokenManager {
    private const val TOKEN_KEY = "auth_token"

    fun saveToken(token: String) {
        // Sẽ implement sau với SharedPreferences
    }

    fun getToken(): String? {
        // Sẽ implement sau với SharedPreferences
        return null
    }

    fun clearToken() {
        // Sẽ implement sau với SharedPreferences
    }
}