package ecccomp.s2240788.mobile_android.ui.adapters

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.webkit.WebView
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
import ecccomp.s2240788.mobile_android.utils.CodeHighlightHelper

class CodeExampleAdapter(
    private val languageName: String,
    private val onExampleClick: (CodeExample) -> Unit
) : ListAdapter<CodeExample, CodeExampleAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_code_example, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position), languageName, onExampleClick)
    }

    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val chipTitle: Chip = itemView.findViewById(R.id.chip_title)
        private val btnCopy: ImageButton = itemView.findViewById(R.id.btn_copy)
        private val tvDescription: TextView = itemView.findViewById(R.id.tv_description)
        private val webviewCode: WebView = itemView.findViewById(R.id.webview_code)
        private val cardCodeContainer: com.google.android.material.card.MaterialCardView = itemView.findViewById(R.id.card_code_container)
        private val outputContainer: LinearLayout = itemView.findViewById(R.id.output_container)
        private val tvOutput: TextView = itemView.findViewById(R.id.tv_output)

        fun bind(example: CodeExample, languageName: String, onExampleClick: (CodeExample) -> Unit) {
            // Set title
            chipTitle.text = example.title

            // Set description
            if (example.description.isNullOrBlank()) {
                tvDescription.visibility = View.GONE
            } else {
                tvDescription.visibility = View.VISIBLE
                tvDescription.text = example.description
            }

            // Set code with syntax highlighting
            setupWebView(webviewCode, example.code, languageName)

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

            // Click on code card to show fullscreen dialog
            cardCodeContainer.setOnClickListener {
                showFullscreenCodeDialog(example, languageName)
            }
        }

        private fun showFullscreenCodeDialog(example: CodeExample, languageName: String) {
            val context = itemView.context
            val dialog = android.app.Dialog(context, android.R.style.Theme_Black_NoTitleBar_Fullscreen)
            dialog.setContentView(ecccomp.s2240788.mobile_android.R.layout.dialog_code_fullscreen)

            // Get views from dialog
            val tvDialogTitle: TextView = dialog.findViewById(ecccomp.s2240788.mobile_android.R.id.tv_dialog_title)
            val chipLanguage: Chip = dialog.findViewById(ecccomp.s2240788.mobile_android.R.id.chip_language)
            val btnClose: ImageButton = dialog.findViewById(ecccomp.s2240788.mobile_android.R.id.btn_close)
            val btnCopyFullscreen: com.google.android.material.button.MaterialButton = dialog.findViewById(ecccomp.s2240788.mobile_android.R.id.btn_copy_fullscreen)
            val webviewCodeFullscreen: WebView = dialog.findViewById(ecccomp.s2240788.mobile_android.R.id.webview_code_fullscreen)
            val outputContainerFullscreen: LinearLayout = dialog.findViewById(ecccomp.s2240788.mobile_android.R.id.output_container_fullscreen)
            val tvOutputFullscreen: TextView = dialog.findViewById(ecccomp.s2240788.mobile_android.R.id.tv_output_fullscreen)

            // Set title and language
            tvDialogTitle.text = example.title
            chipLanguage.text = languageName.uppercase()

            // Setup WebView with syntax highlighting
            setupWebView(webviewCodeFullscreen, example.code, languageName)

            // Set output if available
            if (example.output.isNullOrBlank()) {
                outputContainerFullscreen.visibility = View.GONE
            } else {
                outputContainerFullscreen.visibility = View.VISIBLE
                tvOutputFullscreen.text = example.output
            }

            // Close button
            btnClose.setOnClickListener {
                dialog.dismiss()
            }

            // Copy button
            btnCopyFullscreen.setOnClickListener {
                val clipboard = context.getSystemService(Context.CLIPBOARD_SERVICE) as ClipboardManager
                val clip = ClipData.newPlainText("code", example.code)
                clipboard.setPrimaryClip(clip)
                Toast.makeText(context, context.getString(ecccomp.s2240788.mobile_android.R.string.code_copied), Toast.LENGTH_SHORT).show()
            }

            dialog.show()
        }

        private fun setupWebView(webView: WebView, code: String, language: String) {
            // Configure WebView
            webView.settings.apply {
                javaScriptEnabled = true
                domStorageEnabled = true
                loadWithOverviewMode = false
                useWideViewPort = false
                builtInZoomControls = false
                displayZoomControls = false
                setSupportZoom(false)
            }

            // Generate highlighted HTML
            val html = CodeHighlightHelper.generateHighlightedHtml(
                webView.context,
                code,
                language
            )

            // Load HTML
            webView.loadDataWithBaseURL(
                null,
                html,
                "text/html",
                "UTF-8",
                null
            )

            // Adjust height dynamically
            webView.post {
                val heightMeasureSpec = View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED)
                val widthMeasureSpec = View.MeasureSpec.makeMeasureSpec(webView.width, View.MeasureSpec.EXACTLY)
                webView.measure(widthMeasureSpec, heightMeasureSpec)

                val params = webView.layoutParams
                params.height = webView.measuredHeight
                webView.layoutParams = params
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
