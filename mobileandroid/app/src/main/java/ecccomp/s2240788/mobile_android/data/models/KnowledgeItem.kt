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

/**
 * Category Request Models
 */
data class CreateKnowledgeCategoryRequest(
    val name: String,
    val parent_id: Int? = null,
    val description: String? = null,
    val color: String? = null,
    val icon: String? = null,
    val sort_order: Int? = null
)

data class ReorderCategoriesRequest(
    val categories: List<CategoryOrder>
)

data class CategoryOrder(
    val id: Int,
    val sort_order: Int
)

data class MoveCategoryRequest(
    val parent_id: Int?
)

/**
 * Quick Capture Request & Response
 */
data class QuickCaptureRequest(
    val content: String,
    val item_type: String, // note, code_snippet, exercise, resource_link
    val category_id: Int? = null
)

data class QuickCaptureResponse(
    val item: KnowledgeItem,
    val suggested_categories: List<CategorySuggestion>,
    val auto_detected_language: String?,
    val auto_generated_tags: List<String>
)

data class CategorySuggestion(
    val category: KnowledgeCategory,
    val confidence: Double,
    val reason: String
)

/**
 * Smart AI Feature Request/Response Models
 */
data class SuggestCategoryRequest(
    val title: String? = null,
    val content: String,
    val item_type: String // note, code_snippet, exercise, resource_link
)

data class SuggestCategoryResponse(
    val suggested_categories: List<SimpleCategorySuggestion>,
    val detected_language: String?,
    val confidence: Double
)

data class SimpleCategorySuggestion(
    val id: Int,
    val name: String,
    val confidence: Double
)

data class SuggestTagsRequest(
    val content: String,
    val item_type: String? = "note"
)

data class SuggestTagsResponse(
    val suggested_tags: List<String>,
    val detected_language: String?
)

/**
 * Bulk Operation Request Models
 */
data class BulkTagRequest(
    val item_ids: List<Int>,
    val tags: List<String>
)

data class BulkMoveRequest(
    val item_ids: List<Int>,
    val category_id: Int?
)

data class BulkDeleteRequest(
    val item_ids: List<Int>
)

data class BulkOperationResponse(
    val success: Boolean,
    val affected_count: Int,
    val message: String
)

/**
 * Clone Knowledge Item Request
 */
data class CloneKnowledgeRequest(
    val title: String? = null,
    val category_id: Int? = null
)

/**
 * Statistics Models
 */
data class KnowledgeStats(
    val total_items: Int,
    val items_by_type: Map<String, Int>,
    val total_reviews: Int,
    val items_due_review: Int,
    val average_retention_score: Double,
    val favorite_items_count: Int,
    val archived_items_count: Int,
    val total_categories: Int,
    val items_by_difficulty: Map<String, Int>
)

data class KnowledgeCategoryStats(
    val total_categories: Int,
    val root_categories: Int,
    val max_depth: Int,
    val total_items: Int,
    val categories_with_items: Int,
    val empty_categories: Int,
    val avg_items_per_category: Double
)

/**
 * AI Knowledge Creation Result
 * Response from AI-powered knowledge creation via chat
 */
data class KnowledgeCreationResult(
    val success: Boolean,
    val categories: List<KnowledgeCategory>,
    val items: List<KnowledgeItem>,
    val errors: List<String>,
    val summary: KnowledgeCreationSummary
)

data class KnowledgeCreationSummary(
    val categories_created: Int,
    val items_created: Int
)

