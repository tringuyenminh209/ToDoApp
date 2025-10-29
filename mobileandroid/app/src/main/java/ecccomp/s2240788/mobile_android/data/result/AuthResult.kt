package ecccomp.s2240788.mobile_android.data.result

/**
 * Sealed Class for Auth Results
 * API呼び出しの結果を表す
 */
sealed class AuthResult<out T> {
    /**
     * 成功時のデータ
     */
    data class Success<T>(val data: T) : AuthResult<T>()

    /**
     * エラー時のメッセージ
     */
    data class Error(val message: String) : AuthResult<Nothing>()

    /**
     * ローディング状態
     */
    object Loading : AuthResult<Nothing>()

    /**
     * 成功かどうか
     */
    val isSuccess: Boolean
        get() = this is Success

    /**
     * エラーかどうか
     */
    val isError: Boolean
        get() = this is Error

    /**
     * データを取得
     */
    inline fun <R> fold(
        onSuccess: (T) -> R,
        onError: (String) -> R
    ): R = when (this) {
        is Success -> onSuccess(data)
        is Error -> onError(message)
        is Loading -> onError("処理中です")
    }
}

