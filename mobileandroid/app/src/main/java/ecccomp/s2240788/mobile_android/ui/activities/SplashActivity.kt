package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivitySplashBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.SplashViewModel

class SplashActivity : BaseActivity() {

    private lateinit var binding: ActivitySplashBinding
    private lateinit var viewModel: SplashViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivitySplashBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        setupViewModel()
        setupObservers()
        startSplash()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[SplashViewModel::class.java]
    }

    private fun setupObservers() {
        viewModel.navigateToLogin.observe(this) { shouldNavigate ->
            if (shouldNavigate) {
                navigateToLogin()
                viewModel.clearNavigationFlags()
            }
        }

        viewModel.navigateToMain.observe(this) { shouldNavigate ->
            if (shouldNavigate) {
                navigateToMain()
                viewModel.clearNavigationFlags()
            }
        }
    }

    private fun startSplash() {
        viewModel.checkAuthStatus()
    }

    private fun navigateToLogin() {
        startActivity(Intent(this, LoginActivity::class.java))
        finish()
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
    }

    private fun navigateToMain() {
        startActivity(Intent(this, MainActivity::class.java))
        finish()
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
    }
}