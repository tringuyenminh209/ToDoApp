package ecccomp.s2240788.mobile_android.data.models

/**
 * CheatCodeLanguage
 * プログラミング言語のモデル
 */
data class CheatCodeLanguage(
    val id: Int,
    val name: String,
    val displayName: String,
    val icon: String? = null,
    val color: String,
    val description: String? = null,
    val popularity: Int = 0,
    val category: String = "programming", // programming, markup, database, etc.
    val sectionsCount: Int = 0,
    val examplesCount: Int = 0,
    val exercisesCount: Int = 0,
    val createdAt: String? = null,
    val updatedAt: String? = null
)

/**
 * CheatCodeSection
 * コードセクション（例：Getting Started, Arrays, Functions）
 */
data class CheatCodeSection(
    val id: Int,
    val languageId: Int,
    val title: String,
    val description: String? = null,
    val sortOrder: Int = 0,
    val examples: List<CodeExample>? = null,
    val createdAt: String? = null,
    val updatedAt: String? = null
)

/**
 * CodeExample
 * コードサンプル
 */
data class CodeExample(
    val id: Int,
    val sectionId: Int,
    val title: String,
    val code: String,
    val description: String? = null,
    val output: String? = null,
    val tags: List<String>? = null,
    val difficulty: String? = null, // easy, medium, hard
    val sortOrder: Int = 0,
    val createdAt: String? = null,
    val updatedAt: String? = null
)

/**
 * Exercise
 * 練習問題
 */
data class Exercise(
    val id: Int,
    val languageId: Int,
    val title: String,
    val description: String,
    val question: String,
    val starterCode: String? = null,
    val solution: String? = null,
    val testCases: List<TestCase>? = null,
    val difficulty: String = "medium",
    val points: Int = 10,
    val tags: List<String>? = null,
    val hints: List<String>? = null,
    val isCompleted: Boolean = false,
    val userSubmissions: Int = 0,
    val successRate: Float = 0f,
    val createdAt: String? = null,
    val updatedAt: String? = null
)

/**
 * TestCase
 * テストケース
 */
data class TestCase(
    val input: String,
    val expectedOutput: String,
    val description: String? = null
)

/**
 * UserExerciseSubmission
 * ユーザーの提出
 */
data class UserExerciseSubmission(
    val id: Int,
    val exerciseId: Int,
    val userId: Int,
    val code: String,
    val isPassed: Boolean,
    val passedTestCases: Int,
    val totalTestCases: Int,
    val score: Int,
    val submittedAt: String
)

/**
 * CheatCodeSectionsResponse
 * セクションのレスポンス（言語情報を含む）
 */
data class CheatCodeSectionsResponse(
    val language: CheatCodeLanguage,
    val sections: List<CheatCodeSection>
)