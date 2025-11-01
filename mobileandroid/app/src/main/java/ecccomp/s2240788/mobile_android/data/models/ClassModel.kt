package ecccomp.s2240788.mobile_android.data.models

import android.graphics.Color
import ecccomp.s2240788.mobile_android.R

/**
 * ClassModel - Temporary compatibility model for TimetableAdapter
 * 時間割表示用の互換モデル
 */
data class ClassModel(
    val id: String,
    val name: String,
    val room: String?,
    val instructor: String? = null,
    val day: Int, // 0-6 (Sunday to Saturday)
    val period: Int, // 1-5
    val color: ClassColor = ClassColor.BLUE
)

/**
 * Class Color enum
 */
enum class ClassColor(val colorResId: Int) {
    BLUE(R.color.primary),
    GREEN(R.color.success),
    RED(R.color.error),
    ORANGE(R.color.warning),
    PURPLE(R.color.info)
}

/**
 * Convert TimetableClass to ClassModel for adapter compatibility
 * TimetableClass を ClassModel に変換
 */
fun TimetableClass.toClassModel(): ClassModel {
    // Map day string to int
    val dayInt = when (day.lowercase()) {
        "sunday" -> 0
        "monday" -> 1
        "tuesday" -> 2
        "wednesday" -> 3
        "thursday" -> 4
        "friday" -> 5
        "saturday" -> 6
        else -> 0
    }
    
    // Parse color string to ClassColor
    val classColor = try {
        val colorInt = Color.parseColor(color)
        // Simple mapping based on color
        when {
            color.contains("4F46E5", ignoreCase = true) || color.contains("indigo", ignoreCase = true) -> ClassColor.BLUE
            color.contains("10B981", ignoreCase = true) || color.contains("green", ignoreCase = true) -> ClassColor.GREEN
            color.contains("EF4444", ignoreCase = true) || color.contains("red", ignoreCase = true) -> ClassColor.RED
            color.contains("F59E0B", ignoreCase = true) || color.contains("orange", ignoreCase = true) -> ClassColor.ORANGE
            color.contains("8B5CF6", ignoreCase = true) || color.contains("purple", ignoreCase = true) -> ClassColor.PURPLE
            else -> ClassColor.BLUE
        }
    } catch (e: Exception) {
        ClassColor.BLUE
    }
    
    return ClassModel(
        id = id.toString(),
        name = name,
        room = room,
        instructor = instructor,
        day = dayInt,
        period = period,
        color = classColor
    )
}

