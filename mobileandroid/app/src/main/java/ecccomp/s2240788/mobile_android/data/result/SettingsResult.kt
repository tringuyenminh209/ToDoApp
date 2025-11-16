package ecccomp.s2240788.mobile_android.data.result

/**
 * Sealed Class for Settings Results
 */
sealed class SettingsResult<out T> {
    /**
     * Success state with data
     */
    data class Success<T>(val data: T) : SettingsResult<T>()

    /**
     * Error state with message
     */
    data class Error(val message: String) : SettingsResult<Nothing>()

    /**
     * Loading state
     */
    object Loading : SettingsResult<Nothing>()

    /**
     * Check if result is success
     */
    val isSuccess: Boolean
        get() = this is Success

    /**
     * Check if result is error
     */
    val isError: Boolean
        get() = this is Error

    /**
     * Fold result into a single value
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
