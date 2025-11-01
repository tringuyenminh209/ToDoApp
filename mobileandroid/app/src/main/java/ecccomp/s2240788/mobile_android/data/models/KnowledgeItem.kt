package ecccomp.s2240788.mobile_android.data.models

data class KnowledgeItem(
    val id: Int,
    val user_id: Int,
    val category_id: Int?,
    val title: String,
    val item_type: String, // note, code_snippet, exercise, resource_link, attachment
    val content: String?,
    val code_language: String?,
    val url: String?,
    val question: String?,
    val answer: String?,
    val difficulty: String?, // easy, medium, hard
    val attachment_path: String?,
    val attachment_mime: String?,
    val attachment_size: Int?,
    val tags: List<String>?,
    val learning_path_id: Int?,
    val source_task_id: Int?,
    val review_count: Int,
    val last_reviewed_at: String?,
    val next_review_date: String?,
    val retention_score: Int?,
    val ai_summary: String?,
    val view_count: Int,
    val is_favorite: Boolean,
    val is_archived: Boolean,
    val created_at: String,
    val updated_at: String,
    val category: KnowledgeCategory?
)

data class KnowledgeCategory(
    val id: Int,
    val user_id: Int,
    val parent_id: Int?,
    val name: String,
    val description: String?,
    val sort_order: Int,
    val color: String?,
    val icon: String?,
    val item_count: Int,
    val created_at: String,
    val updated_at: String
)

/**
 * Create Knowledge Item Request
 */
data class CreateKnowledgeItemRequest(
    val title: String,
    val item_type: String, // note, code_snippet, exercise, resource_link, attachment
    val content: String? = null,
    val code_language: String? = null,
    val url: String? = null,
    val question: String? = null,
    val answer: String? = null,
    val difficulty: String? = null,
    val tags: List<String>? = null,
    val learning_path_id: Int? = null,
    val source_task_id: Int? = null,
    val category_id: Int? = null
)

