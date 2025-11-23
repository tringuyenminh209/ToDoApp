package ecccomp.s2240788.mobile_android.ui.activities

import android.graphics.Rect
import android.os.Bundle
import android.view.View
import android.view.ViewTreeObserver
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.lifecycle.ViewModelProvider
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.CreateKnowledgeItemRequest
import ecccomp.s2240788.mobile_android.data.models.KnowledgeCategory
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.databinding.ActivityKnowledgeEditorBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeViewModel

/**
 * KnowledgeEditorActivity
 * Create or edit knowledge items
 * - Dynamic form based on item type
 * - Category selector
 * - Tags input
 * - Validation
 */
class KnowledgeEditorActivity : BaseActivity() {

    private lateinit var binding: ActivityKnowledgeEditorBinding
    private lateinit var viewModel: KnowledgeViewModel
    private var editingItemId: Int? = null
    private var editingItem: KnowledgeItem? = null
    private var categories: List<KnowledgeCategory> = emptyList()
    private var selectedCategoryId: Int? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityKnowledgeEditorBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        viewModel = ViewModelProvider(this)[KnowledgeViewModel::class.java]

        // Check if editing existing item
        editingItemId = intent.getIntExtra("KNOWLEDGE_ITEM_ID", -1).takeIf { it != -1 }

        setupUI()
        setupClickListeners()
        setupObservers()
        setupTypeChangeListeners()
        setupKeyboardHandling()

        // Load categories
        viewModel.loadCategories()

        // Load existing item if editing
        editingItemId?.let {
            // Load item from passed data or from API
            viewModel.loadKnowledgeItems()
        }
    }

    private fun setupUI() {
        // Set header title
        binding.tvHeaderTitle.text = if (editingItemId != null) {
            getString(R.string.edit_knowledge_item)
        } else {
            getString(R.string.create_knowledge_item)
        }

        // Default to note type
        binding.chipTypeNote.isChecked = true
        updateFormFields("note")
    }

    private fun setupClickListeners() {
        binding.btnBack.setOnClickListener {
            finish()
        }

        binding.btnSave.setOnClickListener {
            saveKnowledgeItem()
        }
    }

    private fun setupTypeChangeListeners() {
        binding.chipGroupType.setOnCheckedStateChangeListener { _, checkedIds ->
            if (checkedIds.isNotEmpty()) {
                val selectedType = when (checkedIds[0]) {
                    R.id.chip_type_note -> "note"
                    R.id.chip_type_code -> "code_snippet"
                    R.id.chip_type_exercise -> "exercise"
                    R.id.chip_type_link -> "resource_link"
                    else -> "note"
                }
                updateFormFields(selectedType)
            }
        }
    }

    private fun updateFormFields(itemType: String) {
        // Hide all optional fields first
        binding.cardCodeLanguage.visibility = View.GONE
        binding.cardUrl.visibility = View.GONE
        binding.cardQuestion.visibility = View.GONE
        binding.cardAnswer.visibility = View.GONE
        binding.cardDifficulty.visibility = View.GONE

        // Show fields based on type
        when (itemType) {
            "note" -> {
                binding.cardContent.visibility = View.VISIBLE
                binding.tvContentLabel.text = getString(R.string.content)
                binding.etContent.hint = getString(R.string.enter_note_content)
            }
            "code_snippet" -> {
                binding.cardContent.visibility = View.VISIBLE
                binding.cardCodeLanguage.visibility = View.VISIBLE
                binding.tvContentLabel.text = getString(R.string.code)
                binding.etContent.hint = getString(R.string.enter_code)
            }
            "exercise" -> {
                binding.cardContent.visibility = View.GONE
                binding.cardQuestion.visibility = View.VISIBLE
                binding.cardAnswer.visibility = View.VISIBLE
                binding.cardDifficulty.visibility = View.VISIBLE
            }
            "resource_link" -> {
                binding.cardContent.visibility = View.VISIBLE
                binding.cardUrl.visibility = View.VISIBLE
                binding.tvContentLabel.text = getString(R.string.description)
                binding.etContent.hint = getString(R.string.enter_description)
            }
        }
    }

    private fun setupObservers() {
        viewModel.categories.observe(this) { categoryList ->
            categories = categoryList
            setupCategorySpinner()
        }

        viewModel.knowledgeItems.observe(this) { items ->
            // If editing, find the item
            editingItemId?.let { id ->
                val item = items.find { it.id == id }
                item?.let {
                    editingItem = it
                    populateFormWithItem(it)
                }
            }
        }

        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnSave.isEnabled = !isLoading
        }

        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearError()
            }
        }

        viewModel.successMessage.observe(this) { message ->
            message?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearSuccessMessage()
                // Close activity on success
                setResult(RESULT_OK)
                finish()
            }
        }
    }

    private fun setupCategorySpinner() {
        val categoryNames = categories.map { "${it.name}" }
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, categoryNames)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        binding.spinnerCategory.adapter = adapter

        // Set selected category if editing
        editingItem?.category_id?.let { categoryId ->
            val index = categories.indexOfFirst { it.id == categoryId }
            if (index >= 0) {
                binding.spinnerCategory.setSelection(index)
            }
        }
    }

    private fun populateFormWithItem(item: KnowledgeItem) {
        // Set title
        binding.etTitle.setText(item.title)

        // Set type
        when (item.item_type) {
            "note" -> binding.chipTypeNote.isChecked = true
            "code_snippet" -> binding.chipTypeCode.isChecked = true
            "exercise" -> binding.chipTypeExercise.isChecked = true
            "resource_link" -> binding.chipTypeLink.isChecked = true
        }

        // Set content
        binding.etContent.setText(item.content ?: "")

        // Set code language
        binding.etCodeLanguage.setText(item.code_language ?: "")

        // Set URL
        binding.etUrl.setText(item.url ?: "")

        // Set question/answer
        binding.etQuestion.setText(item.question ?: "")
        binding.etAnswer.setText(item.answer ?: "")

        // Set difficulty
        when (item.difficulty) {
            "easy" -> binding.chipDiffEasy.isChecked = true
            "medium" -> binding.chipDiffMedium.isChecked = true
            "hard" -> binding.chipDiffHard.isChecked = true
        }

        // Set tags
        val tagsString = item.tags?.joinToString(", ") ?: ""
        binding.etTags.setText(tagsString)

        // Set category
        item.category_id?.let { categoryId ->
            val index = categories.indexOfFirst { it.id == categoryId }
            if (index >= 0) {
                binding.spinnerCategory.setSelection(index)
            }
        }
    }

    private fun saveKnowledgeItem() {
        // Validate required fields
        val title = binding.etTitle.text.toString().trim()
        if (title.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_title_required), Toast.LENGTH_SHORT).show()
            return
        }

        // Get item type
        val itemType = when (binding.chipGroupType.checkedChipId) {
            R.id.chip_type_note -> "note"
            R.id.chip_type_code -> "code_snippet"
            R.id.chip_type_exercise -> "exercise"
            R.id.chip_type_link -> "resource_link"
            else -> "note"
        }

        // Get category
        val categoryId = if (binding.spinnerCategory.selectedItemPosition >= 0) {
            categories[binding.spinnerCategory.selectedItemPosition].id
        } else {
            null
        }

        // Get tags
        val tagsString = binding.etTags.text.toString().trim()
        val tags = if (tagsString.isNotEmpty()) {
            tagsString.split(",").map { it.trim() }.filter { it.isNotEmpty() }
        } else {
            null
        }

        // Build request based on type
        val request = when (itemType) {
            "note" -> {
                val content = binding.etContent.text.toString().trim()
                if (content.isEmpty()) {
                    Toast.makeText(this, getString(R.string.error_content_required), Toast.LENGTH_SHORT).show()
                    return
                }
                CreateKnowledgeItemRequest(
                    title = title,
                    item_type = itemType,
                    content = content,
                    category_id = categoryId,
                    tags = tags
                )
            }
            "code_snippet" -> {
                val content = binding.etContent.text.toString().trim()
                val codeLanguage = binding.etCodeLanguage.text.toString().trim()
                if (content.isEmpty()) {
                    Toast.makeText(this, getString(R.string.error_code_required), Toast.LENGTH_SHORT).show()
                    return
                }
                CreateKnowledgeItemRequest(
                    title = title,
                    item_type = itemType,
                    content = content,
                    code_language = codeLanguage.takeIf { it.isNotEmpty() },
                    category_id = categoryId,
                    tags = tags
                )
            }
            "exercise" -> {
                val question = binding.etQuestion.text.toString().trim()
                val answer = binding.etAnswer.text.toString().trim()
                if (question.isEmpty()) {
                    Toast.makeText(this, getString(R.string.error_question_required), Toast.LENGTH_SHORT).show()
                    return
                }
                val difficulty = when (binding.chipGroupDifficulty.checkedChipId) {
                    R.id.chip_diff_easy -> "easy"
                    R.id.chip_diff_medium -> "medium"
                    R.id.chip_diff_hard -> "hard"
                    else -> null
                }
                CreateKnowledgeItemRequest(
                    title = title,
                    item_type = itemType,
                    question = question,
                    answer = answer.takeIf { it.isNotEmpty() },
                    difficulty = difficulty,
                    category_id = categoryId,
                    tags = tags
                )
            }
            "resource_link" -> {
                val url = binding.etUrl.text.toString().trim()
                val content = binding.etContent.text.toString().trim()
                if (url.isEmpty()) {
                    Toast.makeText(this, getString(R.string.error_url_required), Toast.LENGTH_SHORT).show()
                    return
                }
                CreateKnowledgeItemRequest(
                    title = title,
                    item_type = itemType,
                    url = url,
                    content = content.takeIf { it.isNotEmpty() },
                    category_id = categoryId,
                    tags = tags
                )
            }
            else -> return
        }

        // Save or update
        if (editingItemId != null) {
            viewModel.updateKnowledgeItem(editingItemId!!, request)
        } else {
            viewModel.createKnowledgeItem(request)
        }
    }

    /**
     * キーボード表示時の処理を設定
     * 入力フィールドにフォーカスが当たったときに自動的にスクロール
     */
    private fun setupKeyboardHandling() {
        // すべての入力フィールドにフォーカスリスナーを設定
        val inputFields = listOf(
            binding.etTitle,
            binding.etContent,
            binding.etCodeLanguage,
            binding.etUrl,
            binding.etQuestion,
            binding.etAnswer,
            binding.etTags
        )

        inputFields.forEach { editText ->
            editText?.setOnFocusChangeListener { view, hasFocus ->
                if (hasFocus) {
                    // フォーカスが当たったときに、そのフィールドが見えるようにスクロール
                    view.postDelayed({
                        scrollToViewWithKeyboard(view)
                    }, 300)
                }
            }
        }

        // WindowInsetsを使用してキーボードの高さを正確に取得
        ViewCompat.setOnApplyWindowInsetsListener(binding.nestedScrollView) { _, insets ->
            val imeInsets = insets.getInsets(WindowInsetsCompat.Type.ime())
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            
            // キーボードが表示されている場合
            if (imeInsets.bottom > 0) {
                val focusedView = currentFocus
                focusedView?.let {
                    it.postDelayed({
                        scrollToViewWithKeyboard(it)
                    }, 300)
                }
            }
            
            insets
        }

        // キーボードの表示/非表示を検知してスクロール（フォールバック）
        var wasKeyboardVisible = false
        binding.root.viewTreeObserver.addOnGlobalLayoutListener(object : ViewTreeObserver.OnGlobalLayoutListener {
            override fun onGlobalLayout() {
                val rect = Rect()
                binding.root.getWindowVisibleDisplayFrame(rect)
                val screenHeight = binding.root.height
                val keypadHeight = screenHeight - rect.bottom

                // キーボードが表示されている場合（画面の15%以上）
                val isKeyboardVisible = keypadHeight > screenHeight * 0.15
                
                if (isKeyboardVisible && !wasKeyboardVisible) {
                    // キーボードが表示されたとき
                    val focusedView = currentFocus
                    focusedView?.let {
                        it.postDelayed({
                            scrollToViewWithKeyboard(it)
                        }, 300)
                    }
                }
                
                wasKeyboardVisible = isKeyboardVisible
            }
        })
    }

    /**
     * キーボードを考慮してビューが見えるようにスクロール
     */
    private fun scrollToViewWithKeyboard(view: View) {
        val scrollView = binding.nestedScrollView
        
        // まず、requestRectangleOnScreenを使用して、ビューが見えるようにする
        val rect = Rect()
        view.getHitRect(rect)
        scrollView.requestRectangleOnScreen(rect, true)
        
        // 追加のスクロール処理（より確実にするため）
        view.postDelayed({
            val location = IntArray(2)
            view.getLocationOnScreen(location)
            val scrollViewLocation = IntArray(2)
            scrollView.getLocationOnScreen(scrollViewLocation)
            
            // キーボードの高さを取得
            val rootView = binding.root
            val rootRect = Rect()
            rootView.getWindowVisibleDisplayFrame(rootRect)
            val screenHeight = rootView.height
            val keyboardHeight = screenHeight - rootRect.bottom
            
            // ビューの位置から、キーボードの上に表示されるように計算
            val viewTop = location[1]
            val scrollViewTop = scrollViewLocation[1]
            val visibleHeight = rootRect.height()
            val targetY = viewTop - scrollViewTop
            
            // キーボードの上に表示されるように、十分な余白を確保
            // ビューの高さ + 大きなマージン（150dp）
            val viewHeight = view.height
            val margin = (150 * resources.displayMetrics.density).toInt()
            
            // ビューがキーボードの上に完全に見えるように計算
            val availableHeight = visibleHeight - keyboardHeight
            // ビューの上端が表示される位置を計算
            val desiredTop = availableHeight - viewHeight - margin
            val currentTop = targetY
            val offset = currentTop - desiredTop
            
            if (offset > 0) {
                // 現在のスクロール位置を取得
                val currentScrollY = scrollView.scrollY
                scrollView.smoothScrollTo(0, currentScrollY + offset)
            }
            
            // さらに確実にするため、もう一度requestRectangleOnScreenを呼び出す
            view.postDelayed({
                val updatedRect = Rect()
                view.getHitRect(updatedRect)
                scrollView.requestRectangleOnScreen(updatedRect, true)
            }, 200)
        }, 100)
    }
}
