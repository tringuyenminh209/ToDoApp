package ecccomp.s2240788.mobile_android.data.api

import ecccomp.s2240788.mobile_android.data.models.*
import retrofit2.Response
import retrofit2.http.*
interface ApiService {

    //Authentication
    @POST("register")
    suspend fun register(@Body request: RegisterRequest): Response<AuthResponse>

    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<AuthResponse>

    @GET("user")
    suspend fun getUser(): Response<ApiResponse<User>>

    //Tasks
    @GET("tasks")
    suspend fun getTasks(): Response<ApiResponse<List<Task>>>

    @POST("tasks")
    suspend fun createTask(@Body task: CreateTaskRequest): Response<ApiResponse<Task>>

    @PUT("tasks/{id}")
    suspend fun updateTask(@Path("id") id: Int, @Body task: CreateTaskRequest): Response<ApiResponse<Task>>

    @DELETE("tasks/{id}")
    suspend fun deleteTask(@Path("id") id: Int): Response<ApiResponse<Unit>>

    @PUT("tasks/{id}/complete")
    suspend fun completeTask(@Path("id") id: Int): Response<ApiResponse<Task>>

    @PUT("tasks/{id}/start")
    suspend fun startTask(@Path("id") id: Int): Response<ApiResponse<Task>>
}