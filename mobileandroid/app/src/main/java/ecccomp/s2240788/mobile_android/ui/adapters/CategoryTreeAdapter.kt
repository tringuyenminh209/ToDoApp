package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageButton
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import androidx.core.view.isVisible
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.KnowledgeCategory

/**
 * カテゴリツリーアイテム（表示用）
 */
data class CategoryTreeItem(
    val category: KnowledgeCategory,
    val level: Int = 0, // 階層レベル（0 = root）
    val children: List<CategoryTreeItem> = emptyList()
)

/**
 * 階層構造のカテゴリを表示するアダプター
 * 展開/折りたたみ機能付き
 */
class CategoryTreeAdapter(
    private val onCategoryClick: (KnowledgeCategory) -> Unit,
    private val onCategorySelect: (KnowledgeCategory) -> Unit
) : ListAdapter<CategoryTreeItem, CategoryTreeAdapter.CategoryViewHolder>(CategoryDiffCallback()) {

    private var selectedCategoryId: Int? = null
    private val expandedCategories = mutableSetOf<Int>()
    private var originalCategories: List<KnowledgeCategory> = emptyList()

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CategoryViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_category_tree, parent, false)
        return CategoryViewHolder(view)
    }

    override fun onBindViewHolder(holder: CategoryViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    /**
     * カテゴリツリーを構築して表示
     * 「すべて」オプションも含める
     */
    fun setCategories(categories: List<KnowledgeCategory>, includeAllOption: Boolean = true) {
        // 元のカテゴリリストを保持
        originalCategories = categories
        val treeItems = buildCategoryTree(categories)
        val flattened = flattenTree(treeItems)
        
        // 「すべて」オプションを先頭に追加
        if (includeAllOption) {
            val allOption = CategoryTreeItem(
                category = KnowledgeCategory(
                    id = -1, // 特別なID
                    user_id = 0,
                    parent_id = null,
                    name = "すべて",
                    description = null,
                    sort_order = 0,
                    color = null,
                    icon = null,
                    item_count = categories.sumOf { it.item_count },
                    created_at = "",
                    updated_at = ""
                ),
                level = 0,
                children = emptyList()
            )
            submitList(listOf(allOption) + flattened)
        } else {
            submitList(flattened)
        }
    }

    /**
     * 階層構造を構築
     */
    private fun buildCategoryTree(
        categories: List<KnowledgeCategory>,
        parentId: Int? = null,
        level: Int = 0
    ): List<CategoryTreeItem> {
        return categories
            .filter { it.parent_id == parentId }
            .map { category ->
                val children = buildCategoryTree(categories, category.id, level + 1)
                CategoryTreeItem(category, level, children)
            }
    }

    /**
     * ツリーをフラットリストに変換（展開状態を考慮）
     */
    private fun flattenTree(
        items: List<CategoryTreeItem>,
        result: MutableList<CategoryTreeItem> = mutableListOf()
    ): List<CategoryTreeItem> {
        items.forEach { item ->
            result.add(item)
            // 展開されている場合は子要素も追加
            if (expandedCategories.contains(item.category.id) && item.children.isNotEmpty()) {
                flattenTree(item.children, result)
            }
        }
        return result
    }

    /**
     * カテゴリの展開/折りたたみ
     */
    fun toggleExpand(categoryId: Int) {
        if (expandedCategories.contains(categoryId)) {
            expandedCategories.remove(categoryId)
        } else {
            expandedCategories.add(categoryId)
        }
        // 元のカテゴリリストを使って再構築（展開状態を保持）
        val includeAllOption = currentList.firstOrNull()?.category?.id == -1
        setCategories(originalCategories, includeAllOption)
    }

    /**
     * 選択されたカテゴリを設定
     */
    fun setSelectedCategory(categoryId: Int?) {
        selectedCategoryId = categoryId
        notifyDataSetChanged()
    }

    inner class CategoryViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val llCategoryItem: LinearLayout = itemView.findViewById(R.id.ll_category_item)
        private val viewIndent: View = itemView.findViewById(R.id.view_indent)
        private val btnExpand: ImageButton = itemView.findViewById(R.id.btn_expand)
        private val ivCategoryIcon: ImageView = itemView.findViewById(R.id.iv_category_icon)
        private val tvCategoryName: TextView = itemView.findViewById(R.id.tv_category_name)
        private val tvItemCount: TextView = itemView.findViewById(R.id.tv_item_count)
        private val ivSelected: ImageView = itemView.findViewById(R.id.iv_selected)
        private val llChildren: LinearLayout = itemView.findViewById(R.id.ll_children)

        fun bind(item: CategoryTreeItem) {
            val category = item.category
            val level = item.level
            val hasChildren = item.children.isNotEmpty()
            val isExpanded = expandedCategories.contains(category.id)
            val isSelected = selectedCategoryId == category.id

            // カテゴリ名
            tvCategoryName.text = category.name

            // 階層レベルに応じたインデント
            val indentWidth = (level * 24 * itemView.context.resources.displayMetrics.density).toInt()
            viewIndent.layoutParams.width = indentWidth
            viewIndent.isVisible = level > 0

            // 子カテゴリがある場合の展開ボタン
            btnExpand.isVisible = hasChildren
            btnExpand.rotation = if (isExpanded) 180f else 0f
            btnExpand.setOnClickListener {
                toggleExpand(category.id)
            }

            // アイテム数
            if (category.item_count > 0) {
                tvItemCount.text = "(${category.item_count})"
                tvItemCount.isVisible = true
            } else {
                tvItemCount.isVisible = false
            }

            // 選択状態
            ivSelected.isVisible = isSelected

            // カテゴリクリック
            llCategoryItem.setOnClickListener {
                if (hasChildren) {
                    // 子カテゴリがある場合は展開/折りたたみ
                    toggleExpand(category.id)
                } else {
                    // 子カテゴリがない場合は選択
                    onCategorySelect(category)
                }
            }

            // 長押しで選択（子カテゴリがある場合でも選択可能）
            llCategoryItem.setOnLongClickListener {
                onCategorySelect(category)
                true
            }
        }
    }

    private class CategoryDiffCallback : DiffUtil.ItemCallback<CategoryTreeItem>() {
        override fun areItemsTheSame(oldItem: CategoryTreeItem, newItem: CategoryTreeItem): Boolean {
            return oldItem.category.id == newItem.category.id
        }

        override fun areContentsTheSame(oldItem: CategoryTreeItem, newItem: CategoryTreeItem): Boolean {
            return oldItem == newItem
        }
    }
}

