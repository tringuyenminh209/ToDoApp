package ecccomp.s2240788.mobile_android.ui.activities

import android.annotation.SuppressLint
import android.graphics.Typeface
import android.os.Bundle
import android.view.View
import android.webkit.JavascriptInterface
import android.webkit.WebView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityExerciseDetailBinding
import ecccomp.s2240788.mobile_android.ui.adapters.TestCaseAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.ExerciseDetailViewModel
import java.io.BufferedReader
import java.io.InputStreamReader

class ExerciseDetailActivity : BaseActivity() {

    private lateinit var binding: ActivityExerciseDetailBinding
    private lateinit var viewModel: ExerciseDetailViewModel
    private lateinit var testCaseAdapter: TestCaseAdapter

    private var languageId: Int = -1
    private var languageName: String = ""
    private var exerciseId: Int = -1
    private var exerciseTitle: String = ""
    private var hasPassedExercise = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityExerciseDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        getIntentData()
        setupViewModel()
        setupCodeEditor()
        setupRecyclerView()
        setupUI()
        observeViewModel()
        loadData()
    }

    private fun getIntentData() {
        languageId = intent.getIntExtra("LANGUAGE_ID", -1)
        languageName = intent.getStringExtra("LANGUAGE_NAME") ?: ""
        exerciseId = intent.getIntExtra("EXERCISE_ID", -1)
        exerciseTitle = intent.getStringExtra("EXERCISE_TITLE") ?: ""

        // Validate intent data
        if (languageId == -1 || exerciseId == -1 || languageName.isEmpty()) {
            Toast.makeText(this, "Invalid exercise data", Toast.LENGTH_SHORT).show()
            finish()
        }
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[ExerciseDetailViewModel::class.java]
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun setupCodeEditor() {
        // Configure WebView for code editing
        binding.webviewCodeEditor.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            loadWithOverviewMode = false
            useWideViewPort = false
            builtInZoomControls = false
            displayZoomControls = false
            setSupportZoom(false)
        }

        // Disable scrollbars for WebView
        binding.webviewCodeEditor.isVerticalScrollBarEnabled = false
        binding.webviewCodeEditor.isHorizontalScrollBarEnabled = false

        // Load editor template
        loadCodeEditor("")
    }

    private fun loadCodeEditor(initialCode: String) {
        try {
            // Load template from assets
            val inputStream = assets.open("code_editor_template.html")
            val reader = BufferedReader(InputStreamReader(inputStream))
            val stringBuilder = StringBuilder()
            var line: String?

            while (reader.readLine().also { line = it } != null) {
                stringBuilder.append(line)
                stringBuilder.append('\n')
            }

            reader.close()

            // Replace placeholders
            val language = mapExerciseLanguage(languageName)
            val escapedCode = escapeHtml(initialCode)
            val html = stringBuilder.toString()
                .replace("LANGUAGE_PLACEHOLDER", language)
                .replace("CODE_PLACEHOLDER", escapedCode)

            // Load HTML into WebView
            binding.webviewCodeEditor.loadDataWithBaseURL(
                null,
                html,
                "text/html",
                "UTF-8",
                null
            )

        } catch (e: Exception) {
            e.printStackTrace()
            Toast.makeText(this, "Failed to load code editor", Toast.LENGTH_SHORT).show()
        }
    }

    private fun getCodeFromEditor(callback: (String) -> Unit) {
        binding.webviewCodeEditor.evaluateJavascript("getCode()") { result ->
            // Remove quotes from JavaScript string result
            val code = result?.removeSurrounding("\"")?.replace("\\n", "\n")?.replace("\\t", "\t") ?: ""
            callback(code)
        }
    }

    private fun setCodeInEditor(code: String) {
        val escapedCode = code.replace("\\", "\\\\")
            .replace("\"", "\\\"")
            .replace("\n", "\\n")
            .replace("\r", "")
        binding.webviewCodeEditor.evaluateJavascript("setCode(\"$escapedCode\")") { }
    }

    private fun escapeHtml(text: String): String {
        return text
            .replace("&", "&amp;")
            .replace("<", "&lt;")
            .replace(">", "&gt;")
            .replace("\"", "&quot;")
            .replace("'", "&#39;")
    }

    private fun mapExerciseLanguage(language: String): String {
        return when (language.lowercase()) {
            "php" -> "php"
            "java" -> "java"
            "javascript", "js" -> "javascript"
            "python", "py" -> "python"
            "bash", "shell" -> "bash"
            "go" -> "go"
            else -> "plaintext"
        }
    }

    private fun setupRecyclerView() {
        testCaseAdapter = TestCaseAdapter()

        binding.rvTestCases.apply {
            layoutManager = LinearLayoutManager(this@ExerciseDetailActivity)
            adapter = testCaseAdapter
        }
    }

    private fun setupUI() {
        // Set toolbar title
        binding.toolbarTitle.text = exerciseTitle

        // Back button
        binding.btnBack.setOnClickListener {
            finish()
        }

        // Submit button
        binding.btnSubmit.setOnClickListener {
            submitSolution()
        }

        // Reset button
        binding.btnReset.setOnClickListener {
            viewModel.exercise.value?.starterCode?.let { starterCode ->
                setCodeInEditor(starterCode)
            }
        }

        // Hints button
        binding.btnHints.setOnClickListener {
            showHintsDialog()
        }

        // Solution button (only visible after passing)
        binding.btnSolution.setOnClickListener {
            loadSolution()
        }
        binding.btnSolution.visibility = View.GONE
    }

    private fun observeViewModel() {
        // Observe exercise details
        viewModel.exercise.observe(this) { exercise ->
            exercise?.let {
                // Set question
                binding.tvQuestion.text = it.question

                // Set description
                binding.tvDescription.text = it.description

                // Set difficulty
                binding.tvDifficulty.text = when (it.difficulty) {
                    "easy" -> "簡単"
                    "medium" -> "中級"
                    "hard" -> "難しい"
                    else -> it.difficulty
                }

                // Set points
                binding.tvPoints.text = "${it.points}pt"

                // Set success rate
                binding.tvSuccessRate.text = if (it.submissionsCount > 0) {
                    "${it.successRate.toInt()}%"
                } else {
                    "No submissions yet"
                }

                // Set starter code in editor
                setCodeInEditor(it.starterCode ?: "")

                // Show test cases
                it.testCases?.let { testCases ->
                    testCaseAdapter.submitList(testCases)
                }

                // Show/hide hints button
                binding.btnHints.visibility = if (it.hints.isNullOrEmpty()) View.GONE else View.VISIBLE
            }
        }

        // Observe submit result
        viewModel.submitResult.observe(this) { result ->
            result?.let {
                showResultDialog(it.allPassed, it.passedCount, it.totalCount, it.points, it.results)

                if (it.allPassed) {
                    hasPassedExercise = true
                    binding.btnSolution.visibility = View.VISIBLE
                }
            }
        }

        // Observe solution
        viewModel.solution.observe(this) { solution ->
            solution?.let {
                showSolutionDialog(it)
            }
        }

        // Observe loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }

        // Observe submitting state
        viewModel.isSubmitting.observe(this) { isSubmitting ->
            binding.btnSubmit.isEnabled = !isSubmitting

            if (isSubmitting) {
                binding.btnSubmit.text = "提出中..."
            } else {
                binding.btnSubmit.text = "提出"
            }
        }

        // Observe errors
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
            }
        }

        // Observe submit errors
        viewModel.submitError.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun loadData() {
        viewModel.loadExercise(languageId, exerciseId)
        viewModel.loadStatistics(languageId, exerciseId)
    }

    private fun submitSolution() {
        getCodeFromEditor { code ->
            if (code.trim().isEmpty()) {
                Toast.makeText(this, "Please write some code first", Toast.LENGTH_SHORT).show()
                return@getCodeFromEditor
            }

            viewModel.submitSolution(languageId, exerciseId, code)
        }
    }

    private fun showResultDialog(
        allPassed: Boolean,
        passedCount: Int,
        totalCount: Int,
        points: Int,
        results: List<ecccomp.s2240788.mobile_android.data.models.TestCaseResult>
    ) {
        val title = if (allPassed) {
            "🎉 All Tests Passed!"
        } else {
            "❌ Some Tests Failed"
        }

        val message = buildString {
            append("Passed: $passedCount / $totalCount\n")
            if (allPassed) {
                append("Points earned: $points\n\n")
            } else {
                append("\n")
            }

            // Show sample test results
            results.filter { it.isSample }.forEachIndexed { index, result ->
                append("Test ${index + 1}: ${if (result.passed) "✓" else "✗"}\n")
                append("Input: ${result.input ?: "none"}\n")
                append("Expected: ${result.expectedOutput}\n")
                append("Got: ${result.actualOutput ?: "error"}\n")
                if (result.error != null) {
                    append("Error: ${result.error}\n")
                }
                append("\n")
            }

            // Show hidden test results (pass/fail only)
            val hiddenResults = results.filter { !it.isSample }
            if (hiddenResults.isNotEmpty()) {
                append("Hidden Tests:\n")
                hiddenResults.forEachIndexed { index, result ->
                    append("Test ${index + 1}: ${if (result.passed) "✓" else "✗"} ${result.description ?: ""}\n")
                }
            }
        }

        MaterialAlertDialogBuilder(this)
            .setTitle(title)
            .setMessage(message)
            .setPositiveButton("OK") { dialog, _ ->
                dialog.dismiss()
            }
            .show()
    }

    private fun showHintsDialog() {
        val hints = viewModel.exercise.value?.hints ?: return

        val message = hints.mapIndexed { index, hint ->
            "💡 Hint ${index + 1}:\n$hint"
        }.joinToString("\n\n")

        MaterialAlertDialogBuilder(this)
            .setTitle("Hints")
            .setMessage(message)
            .setPositiveButton("Got it") { dialog, _ ->
                dialog.dismiss()
            }
            .show()
    }

    private fun showSolutionDialog(solution: String) {
        MaterialAlertDialogBuilder(this)
            .setTitle("Solution")
            .setMessage(solution)
            .setPositiveButton("Copy to Editor") { dialog, _ ->
                setCodeInEditor(solution)
                dialog.dismiss()
            }
            .setNegativeButton("Close") { dialog, _ ->
                dialog.dismiss()
            }
            .show()
    }

    private fun loadSolution() {
        if (!hasPassedExercise) {
            Toast.makeText(this, "Complete the exercise first to view the solution", Toast.LENGTH_SHORT).show()
            return
        }

        viewModel.loadSolution(languageId, exerciseId)
    }
}
