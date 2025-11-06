package ecccomp.s2240788.mobile_android.ui.adapters

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageButton
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.chip.Chip
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.CodeExample

class CodeExampleAdapter(
    private val onExampleClick: (CodeExample) -> Unit
) : ListAdapter<CodeExample, CodeExampleAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_code_example, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position), onExampleClick)
    }

    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val chipTitle: Chip = itemView.findViewById(R.id.chip_title)
        private val btnCopy: ImageButton = itemView.findViewById(R.id.btn_copy)
        private val tvDescription: TextView = itemView.findViewById(R.id.tv_description)
        private val tvCode: TextView = itemView.findViewById(R.id.tv_code)
        private val outputContainer: LinearLayout = itemView.findViewById(R.id.output_container)
        private val tvOutput: TextView = itemView.findViewById(R.id.tv_output)

        fun bind(example: CodeExample, onExampleClick: (CodeExample) -> Unit) {
            // Set title
            chipTitle.text = example.title

            // Set description
            if (example.description.isNullOrBlank()) {
                tvDescription.visibility = View.GONE
            } else {
                tvDescription.visibility = View.VISIBLE
                tvDescription.text = example.description
            }

            // Set code
            tvCode.text = example.code

            // Set output
            if (example.output.isNullOrBlank()) {
                outputContainer.visibility = View.GONE
            } else {
                outputContainer.visibility = View.VISIBLE
                tvOutput.text = example.output
            }

            // Copy button
            btnCopy.setOnClickListener {
                val clipboard = itemView.context.getSystemService(Context.CLIPBOARD_SERVICE) as ClipboardManager
                val clip = ClipData.newPlainText("code", example.code)
                clipboard.setPrimaryClip(clip)
                Toast.makeText(itemView.context, itemView.context.getString(R.string.code_copied), Toast.LENGTH_SHORT).show()
            }

            // Click listener
            itemView.setOnClickListener {
                onExampleClick(example)
            }
        }
    }

    private class DiffCallback : DiffUtil.ItemCallback<CodeExample>() {
        override fun areItemsTheSame(oldItem: CodeExample, newItem: CodeExample): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: CodeExample, newItem: CodeExample): Boolean {
            return oldItem == newItem
        }
    }
}
