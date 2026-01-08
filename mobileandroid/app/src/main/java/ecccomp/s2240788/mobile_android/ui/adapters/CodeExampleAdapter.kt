package ecccomp.s2240788.mobile_android.ui.adapters

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.webkit.WebView
import android.webkit.WebViewClient
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
            // ViewHolderが再利用される場合に備えて、WebViewをリセット
            webView.clearHistory()
            webView.clearCache(true)
            
            // Configure WebView
            webView.settings.apply {
                javaScriptEnabled = true
                domStorageEnabled = true
                loadWithOverviewMode = false
                useWideViewPort = true  // 幅を広げてコンテンツを表示
                builtInZoomControls = false
                displayZoomControls = false
                setSupportZoom(false)
                layoutAlgorithm = android.webkit.WebSettings.LayoutAlgorithm.TEXT_AUTOSIZING
            }
            
            // WebViewのスクロールを有効化
            webView.isHorizontalScrollBarEnabled = true
            webView.isVerticalScrollBarEnabled = false  // 高さは自動調整するため、縦スクロールは不要
            webView.scrollBarStyle = View.SCROLLBARS_INSIDE_OVERLAY

            // Generate highlighted HTML
            val html = CodeHighlightHelper.generateHighlightedHtml(
                webView.context,
                code,
                language
            )

            // Set WebViewClient to handle page load completion
            webView.webViewClient = object : WebViewClient() {
                override fun onPageFinished(view: WebView?, url: String?) {
                    super.onPageFinished(view, url)
                    // ページ読み込み完了後、JavaScriptのハイライト処理が完了するまで待つ
                    // DOMが完全にレンダリングされるまで待つため、より長い遅延を設定
                    webView.postDelayed({
                        adjustWebViewHeight(webView)
                    }, 300)  // 300msに増やして、highlight処理が確実に完了するのを待つ
                }
            }

            // Load HTML
            webView.loadDataWithBaseURL(
                null,
                html,
                "text/html",
                "UTF-8",
                null
            )
        }

        /**
         * WebViewの高さを動的に調整
         * ページ読み込み完了後、またはViewがレイアウトされた後に呼び出す
         */
        private fun adjustWebViewHeight(webView: WebView) {
            // Viewがまだレイアウトされていない場合は、レイアウト後に再試行
            if (webView.width == 0) {
                webView.post {
                    // レイアウト後に再試行
                    webView.postDelayed({
                        adjustWebViewHeight(webView)
                    }, 50)
                }
                return
            }

            // JavaScriptを使用してコンテンツの高さを取得
            // より正確な測定のために、複数の方法で高さを取得
            // まず、DOMが完全にレンダリングされるまで待つ
            webView.postDelayed({
                // 複数回試行して確実に正確な高さを取得
                var attemptCount = 0
                val maxAttempts = 3
                
                // 再帰的に呼び出せるように関数を定義（lateinit varで参照を保持）
                lateinit var measureHeight: () -> Unit
                measureHeight = {
                    webView.evaluateJavascript(
                        """
                        (function() {
                            // Force reflow đểDOMが更新されるのを確認
                            void document.body.offsetHeight;
                            
                            // 少し待ってから測定（DOM更新を確実に待つ）
                            var body = document.body;
                            var html = document.documentElement;
                            
                            // pre要素とcode要素の高さも確認
                            var pre = document.querySelector('pre');
                            var code = document.querySelector('code');
                            
                            // 各要素の高さを取得（すべての可能な値を確認）
                            var heights = [];
                            
                            // bodyとhtmlの高さ（scrollHeightが最も正確）
                            heights.push(body.scrollHeight, body.offsetHeight);
                            heights.push(html.scrollHeight, html.offsetHeight);
                            
                            // pre要素の高さ
                            if (pre) {
                                heights.push(pre.scrollHeight, pre.offsetHeight);
                                // pre内の実際のコンテンツ高さも確認
                                var preRect = pre.getBoundingClientRect();
                                if (preRect.height > 0) {
                                    heights.push(Math.ceil(preRect.height));
                                }
                                // pre内のすべての子要素も確認
                                var preChildren = pre.children;
                                for (var i = 0; i < preChildren.length; i++) {
                                    var childRect = preChildren[i].getBoundingClientRect();
                                    if (childRect.height > 0) {
                                        heights.push(Math.ceil(childRect.height));
                                    }
                                }
                            }
                            
                            // code要素の高さ
                            if (code) {
                                heights.push(code.scrollHeight, code.offsetHeight);
                                // code内の実際のコンテンツ高さも確認
                                var codeRect = code.getBoundingClientRect();
                                if (codeRect.height > 0) {
                                    heights.push(Math.ceil(codeRect.height));
                                }
                                // code内のすべてのspan要素（token）も確認
                                var codeSpans = code.querySelectorAll('span');
                                for (var i = 0; i < codeSpans.length; i++) {
                                    var spanRect = codeSpans[i].getBoundingClientRect();
                                    if (spanRect.height > 0) {
                                        heights.push(Math.ceil(spanRect.height));
                                    }
                                }
                            }
                            
                            // 最大値を返す（最小値は0より大きい値のみ）
                            var validHeights = heights.filter(function(h) { return h > 0 && !isNaN(h) && isFinite(h); });
                            var maxHeight = validHeights.length > 0 ? Math.max.apply(null, validHeights) : 0;
                            
                            // 最小高さを確保（少なくとも1行分、約30px）
                            var minHeight = 30;
                            return Math.max(maxHeight, minHeight);
                        })();
                        """.trimIndent()
                    ) { result ->
                        try {
                            // JavaScriptの結果を処理（JSON形式の文字列から数値を抽出）
                            val cleanResult = result?.removeSurrounding("\"")?.removeSurrounding("'")?.trim() ?: "0"
                            var contentHeight = cleanResult.toIntOrNull() ?: 0
                            
                            // 結果が0または無効な場合、もう一度試行
                            if (contentHeight <= 0) {
                                attemptCount++
                                if (attemptCount < maxAttempts) {
                                    webView.postDelayed({
                                        measureHeight()
                                    }, 150)
                                } else {
                                    // 最大試行回数に達した場合、フォールバック方法を使用
                                    fallbackMeasureHeight(webView)
                                }
                                return@evaluateJavascript
                            }
                            
                            // より正確な高さを取得するため、もう一度測定
                            webView.post {
                                // 追加のマージンを考慮（行間、パディングなど）
                                val padding = webView.paddingTop + webView.paddingBottom
                                // 安全マージンを追加（約30%でより余裕を持たせる）
                                val safetyMargin = (contentHeight * 0.3).toInt().coerceAtLeast(20)
                                val finalHeight = contentHeight + padding + safetyMargin
                                
                                val params = webView.layoutParams
                                // 最小高さを確保
                                params.height = finalHeight.coerceAtLeast(60)
                                webView.layoutParams = params
                                
                                // レイアウト後に再度確認して確実に
                                webView.postDelayed({
                                    webView.evaluateJavascript(
                                        """
                                        (function() {
                                            return Math.max(
                                                document.body.scrollHeight,
                                                document.documentElement.scrollHeight
                                            );
                                        })();
                                        """.trimIndent()
                                    ) { verifyResult ->
                                        try {
                                            val verifyHeight = verifyResult?.removeSurrounding("\"")?.toIntOrNull() ?: 0
                                            if (verifyHeight > contentHeight) {
                                                // 実際の高さが予想より大きい場合、更新
                                                val params = webView.layoutParams
                                                val padding = webView.paddingTop + webView.paddingBottom
                                                val safetyMargin = (verifyHeight * 0.3).toInt().coerceAtLeast(20)
                                                params.height = verifyHeight + padding + safetyMargin
                                                webView.layoutParams = params
                                            }
                                        } catch (e: Exception) {
                                            // 検証エラーは無視
                                        }
                                    }
                                }, 200)
                            }
                        } catch (e: Exception) {
                            attemptCount++
                            if (attemptCount < maxAttempts) {
                                webView.postDelayed({
                                    measureHeight()
                                }, 150)
                            } else {
                                // エラーが発生した場合、フォールバック方法を使用
                                android.util.Log.e("CodeExampleAdapter", "Error parsing JavaScript result: $result", e)
                                fallbackMeasureHeight(webView)
                            }
                        }
                    }
                }
                
                // 最初の測定を開始
                measureHeight()
            }, 100)  // 100ms遅延してから測定を開始
        }

        /**
         * フォールバック方法: measureを使用して高さを測定
         */
        private fun fallbackMeasureHeight(webView: WebView) {
            webView.post {
                if (webView.width > 0) {
                    val heightMeasureSpec = View.MeasureSpec.makeMeasureSpec(
                        0,
                        View.MeasureSpec.UNSPECIFIED
                    )
                    val widthMeasureSpec = View.MeasureSpec.makeMeasureSpec(
                        webView.width,
                        View.MeasureSpec.EXACTLY
                    )
                    webView.measure(widthMeasureSpec, heightMeasureSpec)

                    val params = webView.layoutParams
                    params.height = webView.measuredHeight
                    webView.layoutParams = params
                } else {
                    // まだwidthがない場合、少し遅延して再試行
                    webView.postDelayed({
                        fallbackMeasureHeight(webView)
                    }, 100)
                }
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
