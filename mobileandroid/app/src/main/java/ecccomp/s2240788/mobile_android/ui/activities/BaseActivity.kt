package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Context
import androidx.appcompat.app.AppCompatActivity
import ecccomp.s2240788.mobile_android.utils.LocaleHelper

/**
 * Base Activity that applies locale to all child activities
 * All activities should extend this class to support multi-language
 */
open class BaseActivity : AppCompatActivity() {
    
    override fun attachBaseContext(newBase: Context) {
        super.attachBaseContext(LocaleHelper.applyLocale(newBase))
    }
}

