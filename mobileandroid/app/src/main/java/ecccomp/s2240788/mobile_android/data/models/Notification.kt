package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

// ==================== Notification Models ====================

data class Notification(
    val id: Int,
    val user_id: Int,
    val type: String, // reminder, achievement, motivational, system
    val title: String,
    val message: String,
    @SerializedName("is_read") val is_read: Boolean = false,  // Backend dùng is_read
    @SerializedName("read_at") val read_at: String? = null,
    val scheduled_at: String?,
    @SerializedName("task_id") val task_id: Int? = null,  // Có thể trong data field
    val data: Map<String, Any>? = null,  // Backend có data field
    val created_at: String,
    val updated_at: String
) {
    // Helper property để tương thích với code cũ
    val read: Boolean get() = is_read
}

// Notification list response (paginated)
// Backend trả về: { success: true, data: [...], pagination: {...} }
// Vì data và pagination ở cùng cấp, không thể dùng ApiResponse<NotificationListResponse>
// Phải tạo custom response model
data class NotificationListApiResponse(
    val success: Boolean,
    val data: List<Notification>,  // Array trực tiếp
    val pagination: PaginationInfo
)

data class PaginationInfo(
    val total: Int,
    val per_page: Int,
    val current_page: Int,
    val last_page: Int
)

// Wrapper để tương thích với code hiện tại
data class NotificationListResponse(
    val data: List<Notification>,
    val pagination: PaginationInfo
) {
    companion object {
        fun fromApiResponse(apiResponse: NotificationListApiResponse): NotificationListResponse {
            return NotificationListResponse(
                data = apiResponse.data,
                pagination = apiResponse.pagination
            )
        }
    }
}

// Unread count response
data class UnreadCountResponse(
    val unread_count: Int
)

// Notification stats response
data class NotificationStatsResponse(
    val total: Int,
    val unread: Int,
    val by_type: Map<String, Int>,
    val recent_count: Int
)

// Create notification request (for testing/admin)
data class CreateNotificationRequest(
    val type: String, // reminder, achievement, motivational, system
    val title: String,
    val message: String,
    val task_id: Int? = null,
    val scheduled_at: String? = null,
    val metadata: Map<String, Any>? = null
)
