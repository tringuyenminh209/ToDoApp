package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

/**
 * 復習評価リクエスト
 * quality: "hard" | "good" | "easy"
 */
data class MarkReviewRequest(
    val quality: String // "hard", "good", "easy"
)

