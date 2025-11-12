package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Context
import android.os.Build
import android.view.View
import android.view.WindowInsetsController
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowCompat
import androidx.core.view.WindowInsetsCompat
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.utils.LocaleHelper

/**
 * Base Activity that applies locale to all child activities
 * All activities should extend this class to support multi-language
 */
open class BaseActivity : AppCompatActivity() {
    
    override fun attachBaseContext(newBase: Context) {
        super.attachBaseContext(LocaleHelper.applyLocale(newBase))
    }

    /**
     * Setup window insets handling for the main view
     * This method should be called in onCreate() after setContentView()
     * @param mainViewId The ID of the main view (default: R.id.main)
     * @param lightStatusBar If true, status bar icons will be dark (for light backgrounds). Default: true
     */
    @JvmOverloads
    protected fun setupWindowInsets(mainViewId: Int = R.id.main, lightStatusBar: Boolean = true) {
        // Enable edge-to-edge display
        WindowCompat.setDecorFitsSystemWindows(window, false)
        
        // Setup status bar appearance (dark icons for light backgrounds)
        setupStatusBarAppearance(lightStatusBar)
        
        // Check if bottom navigation bar or input container exists
        val bottomNav = findViewById<View>(R.id.bottom_navigation)
        val inputContainer = findViewById<View>(R.id.input_container)
        
        // Setup window insets listener for main view
        val mainView = findViewById<View>(mainViewId)
        mainView?.let {
            ViewCompat.setOnApplyWindowInsetsListener(it) { v, insets ->
                val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
                // Apply top padding to avoid status bar overlap
                // Apply bottom padding only if there's no bottom navigation bar or input container
                val bottomPadding = if (bottomNav == null && inputContainer == null) {
                    systemBars.bottom
                } else {
                    0
                }
                v.setPadding(systemBars.left, systemBars.top, systemBars.right, bottomPadding)
                insets
            }
        }
        
        // Setup window insets for bottom navigation bar (if exists)
        bottomNav?.let {
            ViewCompat.setOnApplyWindowInsetsListener(it) { v, insets ->
                val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
                // Apply bottom padding to bottom navigation to avoid system navigation bar overlap
                v.setPadding(v.paddingLeft, v.paddingTop, v.paddingRight, systemBars.bottom)
                insets
            }
        }
        
        // Setup window insets for input container (if exists and no bottom navigation)
        // This handles cases like AICoachActivity where input is at the bottom
        if (bottomNav == null && inputContainer != null) {
            // Store original padding values before any insets are applied
            val originalPaddingLeft = inputContainer.paddingLeft
            val originalPaddingTop = inputContainer.paddingTop
            val originalPaddingRight = inputContainer.paddingRight
            val originalPaddingBottom = inputContainer.paddingBottom
            
            ViewCompat.setOnApplyWindowInsetsListener(inputContainer) { v, insets ->
                val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
                // Apply bottom padding to input container to avoid system navigation bar overlap
                // Use original padding + system bars bottom
                v.setPadding(
                    originalPaddingLeft, 
                    originalPaddingTop, 
                    originalPaddingRight, 
                    originalPaddingBottom + systemBars.bottom
                )
                insets
            }
        }
    }
    
    /**
     * Setup status bar appearance (icon colors)
     * @param lightStatusBar If true, status bar icons will be dark (for light backgrounds)
     */
    private fun setupStatusBarAppearance(lightStatusBar: Boolean) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            // Android 11+ (API 30+)
            window.insetsController?.let { controller ->
                if (lightStatusBar) {
                    // Dark icons (for light backgrounds)
                    controller.setSystemBarsAppearance(
                        WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS,
                        WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS
                    )
                } else {
                    // Light icons (for dark backgrounds)
                    controller.setSystemBarsAppearance(
                        0,
                        WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS
                    )
                }
            }
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            // Android 6.0+ (API 23-29)
            @Suppress("DEPRECATION")
            var flags = window.decorView.systemUiVisibility
            if (lightStatusBar) {
                // Dark icons (for light backgrounds)
                flags = flags or View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR
            } else {
                // Light icons (for dark backgrounds)
                flags = flags and View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR.inv()
            }
            @Suppress("DEPRECATION")
            window.decorView.systemUiVisibility = flags
        }
    }
}

