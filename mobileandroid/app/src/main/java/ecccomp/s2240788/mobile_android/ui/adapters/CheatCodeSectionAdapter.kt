package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.CheatCodeSection
import ecccomp.s2240788.mobile_android.data.models.CodeExample

class CheatCodeSectionAdapter(
    private val languageName: String,
    private val onExampleClick: (CodeExample) -> Unit
) : ListAdapter<CheatCodeSection, CheatCodeSectionAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_cheat_code_section, parent, false)
        return ViewHolder(view, languageName)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position), onExampleClick)
    }

    class ViewHolder(itemView: View, languageName: String) : RecyclerView.ViewHolder(itemView) {
        private val tvSectionTitle: TextView = itemView.findViewById(R.id.tv_section_title)
        private val chipExamplesCount: Chip = itemView.findViewById(R.id.chip_examples_count)
        private val rvExamples: RecyclerView = itemView.findViewById(R.id.rv_examples)

        private var currentOnExampleClick: ((CodeExample) -> Unit)? = null
        private val examplesAdapter = CodeExampleAdapter(languageName) { example ->
            // Use the callback from bind()
            currentOnExampleClick?.invoke(example)
        }

        init {
            rvExamples.apply {
                layoutManager = LinearLayoutManager(itemView.context)
                adapter = examplesAdapter
                isNestedScrollingEnabled = false
            }
        }

        fun bind(section: CheatCodeSection, onExampleClick: (CodeExample) -> Unit) {
            // Set section title
            tvSectionTitle.text = section.title

            // Set examples count
            val examplesCount = section.examples?.size ?: 0
            chipExamplesCount.text = itemView.context.getString(R.string.examples_count, examplesCount)

            // Update callback and submit list
            currentOnExampleClick = onExampleClick
            examplesAdapter.submitList(section.examples)

            // Show/hide RecyclerView
            if (!section.examples.isNullOrEmpty()) {
                rvExamples.visibility = View.VISIBLE
            } else {
                rvExamples.visibility = View.GONE
            }
        }
    }

    private class DiffCallback : DiffUtil.ItemCallback<CheatCodeSection>() {
        override fun areItemsTheSame(oldItem: CheatCodeSection, newItem: CheatCodeSection): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: CheatCodeSection, newItem: CheatCodeSection): Boolean {
            return oldItem == newItem
        }
    }
}
