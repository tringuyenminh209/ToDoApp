package ecccomp.s2240788.mobile_android.ui.dialogs

import android.content.Intent
import android.graphics.Color
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.KnowledgeCategory
import ecccomp.s2240788.mobile_android.data.models.KnowledgeCreationResult
import ecccomp.s2240788.mobile_android.data.models.KnowledgeItem
import ecccomp.s2240788.mobile_android.databinding.BottomSheetKnowledgeCreationBinding
import ecccomp.s2240788.mobile_android.databinding.ItemCreatedCategoryBinding
import ecccomp.s2240788.mobile_android.databinding.ItemCreatedKnowledgeBinding
import ecccomp.s2240788.mobile_android.ui.activities.KnowledgeActivity

class KnowledgeCreationBottomSheet : BottomSheetDialogFragment() {

    private var _binding: BottomSheetKnowledgeCreationBinding? = null
    private val binding get() = _binding!!

    private var creationResult: KnowledgeCreationResult? = null
    private var onViewKnowledgeClick: (() -> Unit)? = null

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = BottomSheetKnowledgeCreationBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        setupUI()
        setupListeners()
    }

    private fun setupUI() {
        creationResult?.let { result ->
            // Set summary
            val summary = buildString {
                if (result.summary.categories_created > 0) {
                    append("${result.summary.categories_created}フォルダ")
                }
                if (result.summary.items_created > 0) {
                    if (result.summary.categories_created > 0) append(" • ")
                    append("${result.summary.items_created}アイテム")
                }
            }
            binding.tvCreationSummary.text = summary

            // Setup Categories RecyclerView
            if (result.categories.isNotEmpty()) {
                binding.tvCategoriesHeader.visibility = View.VISIBLE
                binding.rvCategories.visibility = View.VISIBLE
                binding.rvCategories.layoutManager = LinearLayoutManager(context)
                binding.rvCategories.adapter = CategoryAdapter(result.categories)
            }

            // Setup Items RecyclerView
            if (result.items.isNotEmpty()) {
                binding.tvItemsHeader.visibility = View.VISIBLE
                binding.rvItems.visibility = View.VISIBLE
                binding.rvItems.layoutManager = LinearLayoutManager(context)
                binding.rvItems.adapter = KnowledgeItemAdapter(result.items)
            }

            // Show empty state if no categories and no items
            if (result.categories.isEmpty() && result.items.isEmpty()) {
                binding.layoutEmpty.visibility = View.VISIBLE
            }
        }
    }

    private fun setupListeners() {
        binding.btnClose.setOnClickListener {
            dismiss()
        }

        binding.btnDismiss.setOnClickListener {
            dismiss()
        }

        binding.btnViewKnowledge.setOnClickListener {
            onViewKnowledgeClick?.invoke() ?: run {
                // Default: Navigate to KnowledgeActivity
                startActivity(Intent(requireContext(), KnowledgeActivity::class.java))
                dismiss()
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }

    companion object {
        fun newInstance(
            result: KnowledgeCreationResult,
            onViewKnowledgeClick: (() -> Unit)? = null
        ): KnowledgeCreationBottomSheet {
            return KnowledgeCreationBottomSheet().apply {
                this.creationResult = result
                this.onViewKnowledgeClick = onViewKnowledgeClick
            }
        }
    }

    // Category Adapter
    private class CategoryAdapter(
        private val categories: List<KnowledgeCategory>
    ) : RecyclerView.Adapter<CategoryAdapter.ViewHolder>() {

        inner class ViewHolder(val binding: ItemCreatedCategoryBinding) :
            RecyclerView.ViewHolder(binding.root)

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            val binding = ItemCreatedCategoryBinding.inflate(
                LayoutInflater.from(parent.context), parent, false
            )
            return ViewHolder(binding)
        }

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val category = categories[position]
            holder.binding.apply {
                // Set color indicator
                category.color?.let {
                    try {
                        viewColorIndicator.setBackgroundColor(Color.parseColor(it))
                    } catch (e: Exception) {
                        viewColorIndicator.setBackgroundColor(Color.parseColor("#4CAF50"))
                    }
                }

                // Set icon
                tvCategoryIcon.setImageResource(getIconDrawable(category.icon))

                // Set name and description
                tvCategoryName.text = category.name
                tvCategoryDescription.text = category.description ?: ""
                tvCategoryDescription.visibility = if (category.description.isNullOrEmpty()) {
                    View.GONE
                } else {
                    View.VISIBLE
                }

                // Set item count
                chipItemCount.text = "${category.item_count}個"
            }
        }

        override fun getItemCount() = categories.size

        private fun getIconDrawable(icon: String?): Int {
            return when (icon?.lowercase()) {
                "javascript", "python", "java", "php", "react", "code" -> ecccomp.s2240788.mobile_android.R.drawable.ic_code
                "algorithm" -> ecccomp.s2240788.mobile_android.R.drawable.ic_code
                "database" -> ecccomp.s2240788.mobile_android.R.drawable.ic_folder
                else -> ecccomp.s2240788.mobile_android.R.drawable.ic_folder
            }
        }
    }

    // Knowledge Item Adapter
    private class KnowledgeItemAdapter(
        private val items: List<KnowledgeItem>
    ) : RecyclerView.Adapter<KnowledgeItemAdapter.ViewHolder>() {

        inner class ViewHolder(val binding: ItemCreatedKnowledgeBinding) :
            RecyclerView.ViewHolder(binding.root)

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            val binding = ItemCreatedKnowledgeBinding.inflate(
                LayoutInflater.from(parent.context), parent, false
            )
            return ViewHolder(binding)
        }

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = items[position]
            holder.binding.apply {
                // Set type icon
                tvTypeIcon.setImageResource(getTypeIconDrawable(item.item_type))

                // Set title
                tvItemTitle.text = item.title

                // Set difficulty
                item.difficulty?.let { diff ->
                    chipDifficulty.visibility = View.VISIBLE
                    chipDifficulty.text = diff.uppercase()
                    chipDifficulty.setChipBackgroundColorResource(
                        when (diff.lowercase()) {
                            "easy" -> android.R.color.holo_green_light
                            "medium" -> android.R.color.holo_orange_light
                            "hard" -> android.R.color.holo_red_light
                            else -> android.R.color.darker_gray
                        }
                    )
                } ?: run {
                    chipDifficulty.visibility = View.GONE
                }

                // Set content preview
                val contentPreview = when (item.item_type) {
                    "code_snippet" -> item.content
                    "note" -> item.content
                    "exercise" -> item.question
                    "resource_link" -> item.url
                    else -> null
                }

                if (!contentPreview.isNullOrEmpty()) {
                    tvContentPreview.visibility = View.VISIBLE
                    tvContentPreview.text = contentPreview
                } else {
                    tvContentPreview.visibility = View.GONE
                }

                // Set tags
                chipGroupTags.removeAllViews()
                item.tags?.take(3)?.forEach { tag ->
                    val chip = Chip(chipGroupTags.context).apply {
                        text = "#$tag"
                        isClickable = false
                        isCheckable = false
                        setChipBackgroundColorResource(android.R.color.transparent)
                        setChipStrokeColorResource(android.R.color.darker_gray)
                        chipStrokeWidth = 1f
                        textSize = 11f
                    }
                    chipGroupTags.addView(chip)
                }

                // Set item type and category
                val typeLabel = getTypeLabel(item.item_type)
                val languageLabel = item.code_language?.let { " • $it" } ?: ""
                tvItemType.text = "$typeLabel$languageLabel"

                tvCategory.text = item.category?.name ?: "未分類"
            }
        }

        override fun getItemCount() = items.size

        private fun getTypeIconDrawable(type: String): Int {
            return when (type) {
                "code_snippet" -> ecccomp.s2240788.mobile_android.R.drawable.ic_code
                "note" -> ecccomp.s2240788.mobile_android.R.drawable.ic_description
                "exercise" -> ecccomp.s2240788.mobile_android.R.drawable.ic_edit
                "resource_link" -> ecccomp.s2240788.mobile_android.R.drawable.ic_link
                "attachment" -> ecccomp.s2240788.mobile_android.R.drawable.ic_link
                else -> ecccomp.s2240788.mobile_android.R.drawable.ic_description
            }
        }

        private fun getTypeLabel(type: String): String {
            return when (type) {
                "code_snippet" -> "Code Snippet"
                "note" -> "Note"
                "exercise" -> "Exercise"
                "resource_link" -> "Resource Link"
                "attachment" -> "Attachment"
                else -> "Item"
            }
        }
    }
}
