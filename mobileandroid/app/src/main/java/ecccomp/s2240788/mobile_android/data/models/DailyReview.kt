package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

/**
 * DailyReview data model
 * デイリーレビューモデル
 */
data class DailyReview(
    val id: Int,
    val user_id: Int,
    val date: String, // YYYY-MM-DD format
    val mood: String, // excellent, good, average, poor, terrible
    val tasks_completed: Int?,
    val focus_time_minutes: Int?,
    val productivity_score: Float, // Overall score
    val focus_time_score: Int, // 1-10
    val task_completion_score: Int, // 1-10
    val goal_achievement_score: Int, // 1-10
    val work_life_balance_score: Int, // 1-10
    val achievements: List<String>?, // Array of achievements
    val challenges: List<String>?, // Array of challenges
    val lessons_learned: List<String>?, // Array of lessons learned
    val gratitude: List<String>?, // Array of gratitude items
    val notes: String?,
    val created_at: String,
    val updated_at: String
)

/**
 * Create DailyReview Request
 * デイリーレビュー作成リクエスト
 */
data class CreateDailyReviewRequest(
    val date: String, // YYYY-MM-DD format
    val mood: String, // excellent, good, average, poor, terrible
    val productivity_score: Int, // 1-10
    val focus_time_score: Int, // 1-10
    val task_completion_score: Int, // 1-10
    val goal_achievement_score: Int, // 1-10
    val work_life_balance_score: Int, // 1-10
    val achievements: List<String>, // At least 1 required
    val challenges: List<String>? = null,
    val lessons_learned: List<String>? = null,
    val tomorrow_goals: List<String>? = null,
    val gratitude: List<String>? = null,
    val notes: String? = null
)

/**
 * Update DailyReview Request
 * デイリーレビュー更新リクエスト
 */
data class UpdateDailyReviewRequest(
    val mood: String? = null,
    val productivity_score: Int? = null,
    val focus_time_score: Int? = null,
    val task_completion_score: Int? = null,
    val goal_achievement_score: Int? = null,
    val work_life_balance_score: Int? = null,
    val achievements: List<String>? = null,
    val challenges: List<String>? = null,
    val lessons_learned: List<String>? = null,
    val tomorrow_goals: List<String>? = null,
    val gratitude: List<String>? = null,
    val notes: String? = null
)

/**
 * DailyReview Stats Response
 * デイリーレビュー統計レスポンス
 */
data class DailyReviewStats(
    val total_reviews: Int,
    val average_productivity_score: Float,
    val average_focus_time_score: Float,
    val average_task_completion_score: Float,
    val average_goal_achievement_score: Float,
    val average_work_life_balance_score: Float,
    val mood_distribution: MoodDistribution,
    val score_distribution: ScoreDistribution,
    val consistency_score: Float,
    val improvement_trend: String // improving, declining, stable
)

data class MoodDistribution(
    val excellent: Int,
    val good: Int,
    val average: Int,
    val poor: Int,
    val terrible: Int
)

data class ScoreDistribution(
    val excellent: Int, // 9-10
    val good: Int, // 7-8
    val average: Int, // 5-6
    val poor: Int, // 3-4
    val terrible: Int // 0-2
)

/**
 * DailyReview Trends Response
 * デイリーレビュートレンドレスポンス
 */
data class DailyReviewTrend(
    val date: String,
    val score: Float,
    val mood: String,
    val productivity_score: Float,
    val focus_time_score: Float,
    val task_completion_score: Float,
    val goal_achievement_score: Float,
    val work_life_balance_score: Float
)

/**
 * DailyReview Insights Response
 * デイリーレビューインサイトレスポンス
 */
data class DailyReviewInsights(
    val best_day: DayInsight?,
    val worst_day: DayInsight?,
    val most_common_mood: MoodInsight?,
    val strongest_area: AreaInsight?,
    val weakest_area: AreaInsight?,
    val improvement_suggestions: List<String>,
    val achievement_patterns: List<PatternInsight>,
    val challenge_patterns: List<PatternInsight>
)

data class DayInsight(
    val date: String,
    val score: Float,
    val mood: String
)

data class MoodInsight(
    val mood: String,
    val count: Int,
    val percentage: Float
)

data class AreaInsight(
    val area: String,
    val score: Float
)

data class PatternInsight(
    val achievement: String?,
    val challenge: String?,
    val count: Int,
    val frequency: Float
)
