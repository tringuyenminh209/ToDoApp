package ecccomp.s2240788.mobile_android.utils

import android.content.Context
import android.content.SharedPreferences
import android.content.res.Configuration
import android.os.Build
import java.util.Locale

/**
 * Helper class to manage app locale/language
 */
object LocaleHelper {
    
    private const val PREF_NAME = "locale_prefs"
    private const val KEY_LANGUAGE = "language"
    
    // Supported languages
    const val LANGUAGE_JAPANESE = "ja"
    const val LANGUAGE_VIETNAMESE = "vi"
    const val LANGUAGE_ENGLISH = "en"
    
    private fun getPreferences(context: Context): SharedPreferences {
        return context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE)
    }
    
    /**
     * Get current saved language
     */
    fun getLanguage(context: Context): String {
        return getPreferences(context).getString(KEY_LANGUAGE, LANGUAGE_JAPANESE) ?: LANGUAGE_JAPANESE
    }
    
    /**
     * Save language preference
     */
    fun setLanguage(context: Context, language: String) {
        getPreferences(context).edit().putString(KEY_LANGUAGE, language).apply()
    }
    
    /**
     * Apply locale to context
     */
    fun applyLocale(context: Context): Context {
        val language = getLanguage(context)
        return updateLocale(context, language)
    }
    
    /**
     * Update locale for context
     */
    private fun updateLocale(context: Context, language: String): Context {
        val locale = Locale(language)
        Locale.setDefault(locale)
        
        val config = Configuration(context.resources.configuration)
        config.setLocale(locale)
        
        return if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            context.createConfigurationContext(config)
        } else {
            @Suppress("DEPRECATION")
            context.resources.updateConfiguration(config, context.resources.displayMetrics)
            context
        }
    }
    
    /**
     * Get display name for language code
     */
    fun getLanguageDisplayName(language: String): String {
        return when (language) {
            LANGUAGE_JAPANESE -> "日本語"
            LANGUAGE_VIETNAMESE -> "Tiếng Việt"
            LANGUAGE_ENGLISH -> "English"
            else -> "日本語"
        }
    }
    
    /**
     * Get short display name for button
     */
    fun getLanguageShortName(language: String): String {
        return when (language) {
            LANGUAGE_JAPANESE -> "JP"
            LANGUAGE_VIETNAMESE -> "VI"
            LANGUAGE_ENGLISH -> "EN"
            else -> "JP"
        }
    }
    
    /**
     * Get next language in cycle (JP -> VI -> EN -> JP)
     */
    fun getNextLanguage(currentLanguage: String): String {
        return when (currentLanguage) {
            LANGUAGE_JAPANESE -> LANGUAGE_VIETNAMESE
            LANGUAGE_VIETNAMESE -> LANGUAGE_ENGLISH
            LANGUAGE_ENGLISH -> LANGUAGE_JAPANESE
            else -> LANGUAGE_JAPANESE
        }
    }
}

