package ecccomp.s2240788.mobile_android.utils

import android.content.Context
import android.text.Html
import java.io.BufferedReader
import java.io.InputStreamReader

object CodeHighlightHelper {

    private var htmlTemplate: String? = null

    /**
     * Generate highlighted HTML from code
     */
    fun generateHighlightedHtml(context: Context, code: String, language: String): String {
        // Load template if not cached
        if (htmlTemplate == null) {
            htmlTemplate = loadTemplate(context)
        }

        // Escape HTML entities in code
        val escapedCode = escapeHtml(code)

        // Map language names to highlight.js language codes
        val languageCode = mapLanguage(language.lowercase())

        // Replace placeholders
        return htmlTemplate!!
            .replace("LANGUAGE_PLACEHOLDER", languageCode)
            .replace("CODE_PLACEHOLDER", escapedCode)
    }

    /**
     * Load HTML template from assets
     */
    private fun loadTemplate(context: Context): String {
        return try {
            val inputStream = context.assets.open("code_highlight_template.html")
            val reader = BufferedReader(InputStreamReader(inputStream))
            val stringBuilder = StringBuilder()
            var line: String?

            while (reader.readLine().also { line = it } != null) {
                stringBuilder.append(line)
                stringBuilder.append('\n')
            }

            reader.close()
            stringBuilder.toString()
        } catch (e: Exception) {
            e.printStackTrace()
            // Fallback template
            """
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body {
                        font-family: 'Courier New', monospace;
                        font-size: 13px;
                        margin: 0;
                        padding: 0;
                        background: transparent;
                    }
                    pre {
                        margin: 0;
                        white-space: pre-wrap;
                        word-wrap: break-word;
                    }
                </style>
            </head>
            <body>
                <pre><code>CODE_PLACEHOLDER</code></pre>
            </body>
            </html>
            """.trimIndent()
        }
    }

    /**
     * Escape HTML entities
     */
    private fun escapeHtml(text: String): String {
        return text
            .replace("&", "&amp;")
            .replace("<", "&lt;")
            .replace(">", "&gt;")
            .replace("\"", "&quot;")
            .replace("'", "&#39;")
    }

    /**
     * Map language names to highlight.js codes
     */
    private fun mapLanguage(language: String): String {
        return when (language) {
            "php" -> "php"
            "java" -> "java"
            "javascript", "js" -> "javascript"
            "python", "py" -> "python"
            "html" -> "html"
            "css" -> "css"
            "kotlin", "kt" -> "kotlin"
            "go" -> "go"
            "c", "cpp", "c++" -> "cpp"
            "swift" -> "swift"
            "ruby", "rb" -> "ruby"
            "sql" -> "sql"
            "json" -> "json"
            "xml" -> "xml"
            "markdown", "md" -> "markdown"
            "bash", "sh", "shell" -> "bash"
            else -> "plaintext"
        }
    }

    /**
     * Get background color for language
     */
    fun getLanguageColor(language: String): String {
        return when (language.lowercase()) {
            "php" -> "#777BB4"
            "java" -> "#007396"
            "javascript", "js" -> "#F7DF1E"
            "python", "py" -> "#3776AB"
            "html" -> "#E34F26"
            "css" -> "#1572B6"
            "kotlin", "kt" -> "#7F52FF"
            "go" -> "#00ADD8"
            "swift" -> "#FA7343"
            "ruby", "rb" -> "#CC342D"
            else -> "#6C757D"
        }
    }

    /**
     * Get light background color for section headers
     */
    fun getLanguageLightColor(language: String): String {
        val color = getLanguageColor(language)
        // Convert to light version (add opacity or lighten)
        return when (language.lowercase()) {
            "php" -> "#EDE7F6"
            "java" -> "#E3F2FD"
            "javascript", "js" -> "#FFFDE7"
            "python", "py" -> "#E1F5FE"
            "html" -> "#FFEBEE"
            "css" -> "#E0F2F1"
            "kotlin", "kt" -> "#EDE7F6"
            "go" -> "#E0F7FA"
            "swift" -> "#FFF3E0"
            "ruby", "rb" -> "#FFEBEE"
            else -> "#F5F5F5"
        }
    }
}
