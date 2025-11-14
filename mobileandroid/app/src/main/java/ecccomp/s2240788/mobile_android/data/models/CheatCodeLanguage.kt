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

/**
 * ExerciseListResponse
 * 練習問題リストのレスポンス
 */
data class ExerciseListResponse(
    val success: Boolean,
    val data: ExerciseListData,
    val message: String
)

data class ExerciseListData(
    val language: CheatCodeLanguage,
    val exercises: List<ExerciseSummary>
)

/**
 * ExerciseSummary
 * 練習問題の概要（リスト表示用）
 */
data class ExerciseSummary(
    val id: Int,
    val languageId: Int,
    val title: String,
    val slug: String? = null,
    val description: String,
    val difficulty: String = "medium",
    val points: Int = 10,
    val tags: List<String>? = null,
    val timeLimit: Int = 30,
    val submissionsCount: Int = 0,
    val successCount: Int = 0,
    val successRate: Float = 0f,
    val sortOrder: Int = 0,
    val createdAt: String? = null,
    val updatedAt: String? = null
)

/**
 * ExerciseDetail
 * 練習問題の詳細
 */
data class ExerciseDetail(
    val id: Int,
    val languageId: Int,
    val title: String,
    val slug: String? = null,
    val description: String,
    val question: String,
    val starterCode: String? = null,
    val hints: List<String>? = null,
    val difficulty: String = "medium",
    val points: Int = 10,
    val tags: List<String>? = null,
    val timeLimit: Int = 30,
    val submissionsCount: Int = 0,
    val successCount: Int = 0,
    val successRate: Float = 0f,
    val testCases: List<ExerciseTestCase>? = null,
    val totalTestCases: Int = 0,
    val createdAt: String? = null,
    val updatedAt: String? = null
)

/**
 * ExerciseTestCase
 * 練習問題のテストケース（sample only）
 */
data class ExerciseTestCase(
    val id: Int,
    val input: String,
    val expectedOutput: String,
    val description: String? = null,
    val sortOrder: Int = 0
)

/**
 * SubmitSolutionRequest
 * ソリューション提出リクエスト
 */
data class SubmitSolutionRequest(
    val code: String
)

/**
 * SubmitSolutionResponse
 * ソリューション提出レスポンス
 */
data class SubmitSolutionResponse(
    val allPassed: Boolean,
    val passedCount: Int,
    val totalCount: Int,
    val results: List<TestCaseResult>,
    val points: Int
)

/**
 * TestCaseResult
 * テストケースの結果
 */
data class TestCaseResult(
    val description: String? = null,
    val input: String? = null,
    val expectedOutput: String? = null,
    val actualOutput: String? = null,
    val passed: Boolean,
    val isSample: Boolean,
    val error: String? = null
)

/**
 * SolutionResponse
 * 解答レスポンス
 */
data class SolutionResponse(
    val solution: String
)

/**
 * ExerciseStatistics
 * 練習問題の統計情報
 */
data class ExerciseStatistics(
    val submissionsCount: Int,
    val successCount: Int,
    val successRate: Float,
    val difficulty: String,
    val points: Int
)