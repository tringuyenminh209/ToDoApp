package ecccomp.s2240788.mobile_android.ui.dialogs

import android.app.Dialog
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.FrameLayout
import androidx.fragment.app.activityViewModels
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.bottomsheet.BottomSheetBehavior
import com.google.android.material.bottomsheet.BottomSheetDialog
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.KnowledgeCategory
import ecccomp.s2240788.mobile_android.databinding.BottomSheetCategorySelectorBinding
import ecccomp.s2240788.mobile_android.ui.adapters.CategoryTreeAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.KnowledgeViewModel

/**
 * カテゴリ選択ボトムシート
 * 階層構造のカテゴリを表示し、選択できる
 */
class CategorySelectorBottomSheet : BottomSheetDialogFragment() {

    private var _binding: BottomSheetCategorySelectorBinding? = null
    private val binding get() = _binding!!

    private lateinit var viewModel: KnowledgeViewModel
    private lateinit var adapter: CategoryTreeAdapter

    private var selectedCategory: KnowledgeCategory? = null
    private var onCategorySelected: ((KnowledgeCategory?) -> Unit)? = null

    companion object {
        const val TAG = "CategorySelectorBottomSheet"
        private const val ARG_CATEGORY_ID = "category_id"

        fun newInstance(
            currentCategoryId: Int? = null,
            onCategorySelected: (KnowledgeCategory?) -> Unit
        ): CategorySelectorBottomSheet {
            return CategorySelectorBottomSheet().apply {
                arguments = Bundle().apply {
                    currentCategoryId?.let { putInt(ARG_CATEGORY_ID, it) }
                }
                this.onCategorySelected = onCategorySelected
            }
        }
    }

    override fun onCreateDialog(savedInstanceState: Bundle?): Dialog {
        val dialog = super.onCreateDialog(savedInstanceState) as BottomSheetDialog

        dialog.setOnShowListener {
            val bottomSheet = dialog.findViewById<FrameLayout>(
                com.google.android.material.R.id.design_bottom_sheet
            )

            bottomSheet?.let {
                val behavior = BottomSheetBehavior.from(it)
                behavior.state = BottomSheetBehavior.STATE_EXPANDED
                behavior.isDraggable = true
                behavior.skipCollapsed = true
            }
        }

        return dialog
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = BottomSheetCategorySelectorBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        // ViewModelを取得
        viewModel = ViewModelProvider(requireActivity())[KnowledgeViewModel::class.java]

        // 引数から初期選択カテゴリIDを取得
        val initialCategoryId = arguments?.getInt(ARG_CATEGORY_ID)?.takeIf { it > 0 }

        setupRecyclerView()
        setupSearch()
        setupListeners()
        observeViewModel(initialCategoryId)
        loadCategories()
    }

    private fun setupRecyclerView() {
        adapter = CategoryTreeAdapter(
            onCategoryClick = { category ->
                // カテゴリクリック時の処理（展開/折りたたみはアダプター内で処理）
            },
            onCategorySelect = { category ->
                // 「すべて」オプション（ID = -1）の場合はnullを設定
                if (category.id == -1) {
                    selectedCategory = null
                } else {
                    selectedCategory = category
                }
                updateSelectedCategoryDisplay()
                adapter.setSelectedCategory(selectedCategory?.id)
            }
        )

        binding.rvCategories.layoutManager = LinearLayoutManager(requireContext())
        binding.rvCategories.adapter = adapter
    }

    private fun setupSearch() {
        binding.etSearch.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: Editable?) {
                filterCategories(s?.toString() ?: "")
            }
        })
    }

    private fun setupListeners() {
        binding.btnClose.setOnClickListener {
            dismiss()
        }

        binding.btnClear.setOnClickListener {
            selectedCategory = null
            updateSelectedCategoryDisplay()
            adapter.setSelectedCategory(null)
        }

        binding.btnConfirm.setOnClickListener {
            onCategorySelected?.invoke(selectedCategory)
            dismiss()
        }
    }

    private fun observeViewModel(initialCategoryId: Int? = null) {
        viewModel.categories.observe(viewLifecycleOwner) { categories ->
            if (categories.isNotEmpty()) {
                adapter.setCategories(categories)
                
                // 初期選択カテゴリを設定
                val categoryIdToSelect = initialCategoryId 
                    ?: selectedCategory?.id 
                    ?: viewModel.getCurrentCategoryId()
                
                categoryIdToSelect?.let { categoryId ->
                    val category = categories.find { it.id == categoryId }
                    category?.let {
                        selectedCategory = it
                        adapter.setSelectedCategory(it.id)
                        updateSelectedCategoryDisplay()
                    }
                }
            }
        }
    }

    private fun loadCategories() {
        viewModel.loadCategories()
    }

    private fun filterCategories(query: String) {
        viewModel.categories.value?.let { categories ->
            if (query.isBlank()) {
                adapter.setCategories(categories)
            } else {
                // 階層構造を考慮した検索
                // 親カテゴリがマッチする場合、子カテゴリも含める
                val queryLower = query.lowercase()
                val matchedCategoryIds = mutableSetOf<Int>()
                
                // 直接マッチするカテゴリを検索
                categories.forEach { category ->
                    if (category.name.lowercase().contains(queryLower)) {
                        matchedCategoryIds.add(category.id)
                        // 親カテゴリがマッチした場合、すべての子カテゴリを追加
                        addAllChildren(categories, category.id, matchedCategoryIds)
                    }
                }
                
                // 子カテゴリがマッチした場合、親カテゴリも追加
                categories.forEach { category ->
                    if (category.name.lowercase().contains(queryLower)) {
                        addAllParents(categories, category.parent_id, matchedCategoryIds)
                    }
                }
                
                val filtered = categories.filter { 
                    matchedCategoryIds.contains(it.id) 
                }
                adapter.setCategories(filtered)
            }
        }
    }
    
    /**
     * すべての子カテゴリを追加（再帰的）
     */
    private fun addAllChildren(
        categories: List<KnowledgeCategory>,
        parentId: Int,
        result: MutableSet<Int>
    ) {
        categories.filter { it.parent_id == parentId }.forEach { child ->
            result.add(child.id)
            addAllChildren(categories, child.id, result)
        }
    }
    
    /**
     * すべての親カテゴリを追加（再帰的）
     */
    private fun addAllParents(
        categories: List<KnowledgeCategory>,
        parentId: Int?,
        result: MutableSet<Int>
    ) {
        parentId?.let { pid ->
            categories.find { it.id == pid }?.let { parent ->
                result.add(parent.id)
                addAllParents(categories, parent.parent_id, result)
            }
        }
    }

    private fun updateSelectedCategoryDisplay() {
        if (selectedCategory != null) {
            binding.llSelectedCategory.visibility = View.VISIBLE
            binding.tvSelectedCategoryName.text = selectedCategory!!.name
        } else {
            binding.llSelectedCategory.visibility = View.GONE
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}

