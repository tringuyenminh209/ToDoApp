package ecccomp.s2240788.mobile_android.utils

import ecccomp.s2240788.mobile_android.data.models.StudyScheduleInput

/**
 * スケジュールの重複・間隔チェック用ユーティリティ
 * - 同一曜日で時間が重ならないこと
 * - 2つのスケジュールは最低 [GAP_MINUTES] 分空けること
 */
object ScheduleValidationUtils {

    const val GAP_MINUTES = 60

    /**
     * 重複または間隔不足があればエラーメッセージを返す。問題なければ null。
     * @param overlapMessage 重複時のメッセージ
     * @param gapMessage 間隔不足時のメッセージ
     */
    fun validateOverlapAndGap(
        schedules: List<StudyScheduleInput>,
        overlapMessage: String = "スケジュールの時間が重なっています。",
        gapMessage: String = "2つのスケジュールは少なくとも1時間以上空けてください。"
    ): String? {
        if (schedules.size < 2) return null
        val byDay = schedules.groupBy { it.day_of_week }
        for ((_, slots) in byDay) {
            if (slots.size < 2) continue
            val ranges = slots.map { s ->
                val start = timeToMinutes(s.study_time)
                val dur = s.duration_minutes.coerceAtLeast(0)
                start to (start + dur)
            }
            for (i in ranges.indices) {
                for (j in i + 1 until ranges.size) {
                    val a = ranges[i]
                    val b = ranges[j]
                    if (a.first < b.second && b.first < a.second) return overlapMessage
                    val (earlier, later) = if (a.first <= b.first) Pair(a, b) else Pair(b, a)
                    if (later.first - earlier.second < GAP_MINUTES) return gapMessage
                }
            }
        }
        return null
    }

    private fun timeToMinutes(t: String): Int {
        val parts = (t.ifBlank { "00:00" }).split(":")
        return (parts.getOrNull(0)?.toIntOrNull() ?: 0) * 60 + (parts.getOrNull(1)?.toIntOrNull() ?: 0)
    }
}
