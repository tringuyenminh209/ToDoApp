package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import com.google.android.material.textfield.TextInputLayout
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityLoginBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.LoginViewModel
import ecccomp.s2240788.mobile_android.utils.LocaleHelper

class LoginActivity : BaseActivity() {

    private lateinit var binding: ActivityLoginBinding
    private lateinit var viewModel: LoginViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLoginBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()

        setupViewModel()
        setupLanguageSwitcher()
        setupClickListeners()
        setupObservers()
        setupInputValidation()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[LoginViewModel::class.java]
    }

    private fun setupLanguageSwitcher() {
        // Update button text based on current language
        updateLanguageButton()
        
        // Set click listener for language switcher
        binding.btnLanguage.setOnClickListener {
            val currentLanguage = LocaleHelper.getLanguage(this)
            val nextLanguage = LocaleHelper.getNextLanguage(currentLanguage)
            
            // Save new language preference
            LocaleHelper.setLanguage(this, nextLanguage)
            
            // Restart activity to apply new locale
            recreate()
        }
    }

    private fun updateLanguageButton() {
        val currentLanguage = LocaleHelper.getLanguage(this)
        binding.btnLanguage.text = LocaleHelper.getLanguageShortName(currentLanguage)
    }

    private fun setupClickListeners() {
        // Login button
        binding.btnLogin.setOnClickListener {
            if (validateInputs()) {
                val email = binding.etEmail.text.toString().trim()
                val password = binding.etPassword.text.toString()
                viewModel.login(email, password)
            }
        }

        // Register link
        binding.tvRegisterLink.setOnClickListener {
            startActivity(Intent(this, RegisterActivity::class.java))
            overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left)
        }

        // Forgot password
        binding.tvForgotPassword.setOnClickListener {
            startActivity(Intent(this, ForgotPasswordActivity::class.java))
            overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left)
        }
    }

    private fun setupObservers() {
        // Loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressLogin.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnLogin.isEnabled = !isLoading
            binding.btnLogin.text = if (isLoading) getString(R.string.logging_in) else getString(R.string.login_button)
        }

        // Error handling
        viewModel.error.observe(this) { error ->
            if (error != null) {
                showError(error)
                viewModel.clearError()
            }
        }

        // Success handling
        viewModel.loginSuccess.observe(this) { success ->
            if (success) {
                Toast.makeText(this, getString(R.string.login_success), Toast.LENGTH_SHORT).show()
                // Navigate to MainActivity (clear back stack)
                val intent = Intent(this, MainActivity::class.java).apply {
                    flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                }
                startActivity(intent)
                finish()
                overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
            }
        }
    }

    private fun setupInputValidation() {
        // Real-time validation
        binding.etEmail.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) {
                validateEmail()
            }
        }

        binding.etPassword.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) {
                validatePassword()
            }
        }
    }

    private fun validateInputs(): Boolean {
        val isEmailValid = validateEmail()
        val isPasswordValid = validatePassword()
        return isEmailValid && isPasswordValid
    }

    private fun validateEmail(): Boolean {
        val email = binding.etEmail.text.toString().trim()
        val emailLayout = binding.tilEmail

        return when {
            email.isEmpty() -> {
                emailLayout.error = getString(R.string.email_required)
                false
            }
            !android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches() -> {
                emailLayout.error = getString(R.string.email_invalid)
                false
            }
            else -> {
                emailLayout.error = null
                true
            }
        }
    }

    private fun validatePassword(): Boolean {
        val password = binding.etPassword.text.toString()
        val passwordLayout = binding.tilPassword

        return when {
            password.isEmpty() -> {
                passwordLayout.error = getString(R.string.password_required)
                false
            }
            password.length < 8 -> {
                passwordLayout.error = getString(R.string.password_too_short)
                false
            }
            !password.matches(Regex("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).+$")) -> {
                passwordLayout.error = getString(R.string.password_invalid_format)
                false
            }
            else -> {
                passwordLayout.error = null
                true
            }
        }
    }

    private fun showError(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    }

    override fun onBackPressed() {
        super.onBackPressed()
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
    }
}
