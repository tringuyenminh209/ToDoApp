package ecccomp.s2240788.mobile_android.data.models

import com.google.gson.annotations.SerializedName

/**
 * テンプレートモデル
 * Template Library用のデータクラス
 */

// Learning Path Template
data class LearningPathTemplate(
    @SerializedName("id") val id: Long,
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String?,
    @SerializedName("category") val category: TemplateCategory,
    @SerializedName("difficulty") val difficulty: TemplateDifficulty,
    @SerializedName("estimated_hours_total") val estimatedHoursTotal: Int?,
    @SerializedName("tags") val tags: List<String>?,
    @SerializedName("icon") val icon: String?,
    @SerializedName("color") val color: String,
    @SerializedName("is_featured") val isFeatured: Boolean,
    @SerializedName("usage_count") val usageCount: Int,
    @SerializedName("created_at") val createdAt: String?,
    @SerializedName("updated_at") val updatedAt: String?,
    @SerializedName("milestones") val milestones: List<LearningMilestoneTemplate>? = null
)

// Learning Milestone Template
data class LearningMilestoneTemplate(
    @SerializedName("id") val id: Long,
    @SerializedName("template_id") val templateId: Long,
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String?,
    @SerializedName("sort_order") val sortOrder: Int,
    @SerializedName("estimated_hours") val estimatedHours: Int?,
    @SerializedName("deliverables") val deliverables: List<String>?,
    @SerializedName("created_at") val createdAt: String?,
    @SerializedName("updated_at") val updatedAt: String?,
    @SerializedName("tasks") val tasks: List<TaskTemplate>? = null
)

// Task Template
data class TaskTemplate(
    @SerializedName("id") val id: Long,
    @SerializedName("milestone_template_id") val milestoneTemplateId: Long,
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String?,
    @SerializedName("sort_order") val sortOrder: Int,
    @SerializedName("estimated_minutes") val estimatedMinutes: Int?,
    @SerializedName("priority") val priority: Int,
    @SerializedName("resources") val resources: List<String>?,
    @SerializedName("created_at") val createdAt: String?,
    @SerializedName("updated_at") val updatedAt: String?
)

// Enums
enum class TemplateCategory(val value: String, val displayName: String) {
    @SerializedName("programming")
    PROGRAMMING("programming", "プログラミング"),
    
    @SerializedName("design")
    DESIGN("design", "デザイン"),
    
    @SerializedName("business")
    BUSINESS("business", "ビジネス"),
    
    @SerializedName("language")
    LANGUAGE("language", "語学"),
    
    @SerializedName("data_science")
    DATA_SCIENCE("data_science", "データサイエンス"),
    
    @SerializedName("other")
    OTHER("other", "その他");

    companion object {
        fun fromValue(value: String): TemplateCategory {
            return values().find { it.value == value } ?: OTHER
        }
    }
}

enum class TemplateDifficulty(val value: String, val displayName: String, val color: String) {
    @SerializedName("beginner")
    BEGINNER("beginner", "初級", "#4CAF50"),
    
    @SerializedName("intermediate")
    INTERMEDIATE("intermediate", "中級", "#FF9800"),
    
    @SerializedName("advanced")
    ADVANCED("advanced", "上級", "#F44336");

    companion object {
        fun fromValue(value: String): TemplateDifficulty {
            return values().find { it.value == value } ?: BEGINNER
        }
    }
}

// API Response Models
data class TemplateListResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("data") val data: List<LearningPathTemplate>
)

data class TemplateDetailResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("data") val data: LearningPathTemplate
)

data class CloneTemplateResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("message") val message: String,
    @SerializedName("data") val data: CloneTemplateData
)

data class CloneTemplateData(
    @SerializedName("learning_path_id") val learningPathId: Long,
    @SerializedName("learning_path") val learningPath: LearningPath
)

data class TemplateCategoryCount(
    @SerializedName("category") val category: String,
    @SerializedName("count") val count: Int
)

data class TemplateCategoriesResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("data") val data: List<TemplateCategoryCount>
)

// UI Helper Models
data class TemplateFilter(
    var category: TemplateCategory? = null,
    var difficulty: TemplateDifficulty? = null,
    var isFeatured: Boolean? = null,
    var sortBy: String = "usage_count",
    var sortOrder: String = "desc"
) {
    fun toQueryMap(): Map<String, String> {
        val map = mutableMapOf<String, String>()
        category?.let { map["category"] = it.value }
        difficulty?.let { map["difficulty"] = it.value }
        isFeatured?.let { map["featured"] = it.toString() }
        map["sort_by"] = sortBy
        map["sort_order"] = sortOrder
        return map
    }
}

// Extension functions
fun LearningPathTemplate.getTotalTasks(): Int {
    return milestones?.sumOf { it.tasks?.size ?: 0 } ?: 0
}

fun LearningPathTemplate.getTotalMilestones(): Int {
    return milestones?.size ?: 0
}

fun LearningPathTemplate.getFormattedDuration(): String {
    val hours = estimatedHoursTotal ?: 0
    return when {
        hours < 10 -> "${hours}時間"
        hours < 100 -> "${hours}時間"
        else -> "${hours / 10 * 10}時間以上"
    }
}

fun LearningPathTemplate.getCategoryIcon(): String {
    return when (category) {
        TemplateCategory.PROGRAMMING -> "💻"
        TemplateCategory.DESIGN -> "🎨"
        TemplateCategory.BUSINESS -> "💼"
        TemplateCategory.LANGUAGE -> "🗣️"
        TemplateCategory.DATA_SCIENCE -> "📊"
        TemplateCategory.OTHER -> "📚"
    }
}

