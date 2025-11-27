package ecccomp.s2240788.mobile_android.data.result

/**
 * Sealed Class for Notification Results
 * API call results for notification operations
 */
sealed class NotificationResult<out T> {
    /**
     * Success state with data
     */
    data class Success<T>(val data: T) : NotificationResult<T>()

    /**
     * Error state with message
     */
    data class Error(val message: String) : NotificationResult<Nothing>()

    /**
     * Loading state
     */
    object Loading : NotificationResult<Nothing>()

    /**
     * Check if success
     */
    val isSuccess: Boolean
        get() = this is Success

    /**
     * Check if error
     */
    val isError: Boolean
        get() = this is Error

    /**
     * Fold result
     */
    inline fun <R> fold(
        onSuccess: (T) -> R,
        onError: (String) -> R
    ): R = when (this) {
        is Success -> onSuccess(data)
        is Error -> onError(message)
        is Loading -> onError("Processing...")
    }
}
