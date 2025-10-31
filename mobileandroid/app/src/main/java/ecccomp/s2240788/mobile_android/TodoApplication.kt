package ecccomp.s2240788.mobile_android

import android.app.Application
import ecccomp.s2240788.mobile_android.utils.LocaleHelper
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import ecccomp.s2240788.mobile_android.utils.TokenManager

/**
 * Application class for TodoApp
 * TokenManagerを初期化する
 */
class TodoApplication : Application() {

    override fun onCreate() {
        super.onCreate()
        
        // Apply saved locale
        LocaleHelper.applyLocale(this)
        
        // TokenManagerを初期化（EncryptedSharedPreferencesを使用するため）
        TokenManager.init(this)
        
        // NetworkModuleにContextを設定（ResponseInterceptorで使用）
        NetworkModule.setContext(this)
    }
}

