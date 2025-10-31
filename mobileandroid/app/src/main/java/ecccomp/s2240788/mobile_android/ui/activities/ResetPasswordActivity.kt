package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityResetPasswordBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.ResetPasswordViewModel

/**
 * ResetPasswordActivity
 * OTPトークンと新しいパスワードを入力してパスワードをリセットする
 * ForgotPasswordActivityから遷移
 */
class ResetPasswordActivity : BaseActivity() {

    private lateinit var binding: ActivityResetPasswordBinding
    private lateinit var viewModel: ResetPasswordViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityResetPasswordBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Get email from ForgotPasswordActivity
        val email = intent.getStringExtra("email") ?: ""
        binding.etEmail.setText(email)

        setupViewModel()
        setupClickListeners()
        setupInputValidation()
        observeViewModel()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[ResetPasswordViewModel::class.java]
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener {
            onBackPressed()
        }

        // Reset Password button
        binding.btnResetPassword.setOnClickListener {
            if (validateInputs()) {
                val email = binding.etEmail.text.toString().trim()
                val token = binding.etToken.text.toString().trim()
                val password = binding.etPassword.text.toString()
                val confirmPassword = binding.etConfirmPassword.text.toString()

                viewModel.resetPassword(email, token, password, confirmPassword)
            }
        }
    }

    private fun setupInputValidation() {
        // Token validation
        binding.etToken.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) validateToken()
        }

        // Password validation
        binding.etPassword.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) validatePassword()
        }

        // Confirm password validation
        binding.etConfirmPassword.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) validateConfirmPassword()
        }
    }

    private fun validateInputs(): Boolean {
        val isTokenValid = validateToken()
        val isPasswordValid = validatePassword()
        val isConfirmPasswordValid = validateConfirmPassword()

        return isTokenValid && isPasswordValid && isConfirmPasswordValid
    }

    private fun validateToken(): Boolean {
        val token = binding.etToken.text.toString().trim()
        val tokenLayout = binding.tilToken

        return when {
            token.isEmpty() -> {
                tokenLayout.error = "トークンは必須です"
                false
            }
            token.length != 6 -> {
                tokenLayout.error = "トークンは6桁である必要があります"
                false
            }
            !token.all { it.isDigit() } -> {
                tokenLayout.error = "トークンは数字のみです"
                false
            }
            else -> {
                tokenLayout.error = null
                true
            }
        }
    }

    private fun validatePassword(): Boolean {
        val password = binding.etPassword.text.toString()
        val passwordLayout = binding.tilPassword

        return when {
            password.isEmpty() -> {
                passwordLayout.error = "パスワードは必須です"
                false
            }
            password.length < 8 -> {
                passwordLayout.error = "パスワードは8文字以上である必要があります"
                false
            }
            !password.matches(Regex("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).+$")) -> {
                passwordLayout.error = "パスワードは大文字、小文字、数字を含む必要があります"
                false
            }
            else -> {
                passwordLayout.error = null
                true
            }
        }
    }

    private fun validateConfirmPassword(): Boolean {
        val password = binding.etPassword.text.toString()
        val confirmPassword = binding.etConfirmPassword.text.toString()
        val confirmPasswordLayout = binding.tilConfirmPassword

        return when {
            confirmPassword.isEmpty() -> {
                confirmPasswordLayout.error = "パスワード確認は必須です"
                false
            }
            password != confirmPassword -> {
                confirmPasswordLayout.error = "パスワードが一致しません"
                false
            }
            else -> {
                confirmPasswordLayout.error = null
                true
            }
        }
    }

    private fun observeViewModel() {
        // Loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.loadingOverlay.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnResetPassword.isEnabled = !isLoading
        }

        // Error handling
        viewModel.error.observe(this) { error ->
            error?.let {
                showError(it)
                viewModel.clearError()
            }
        }

        // Success handling
        viewModel.resetSuccess.observe(this) { success ->
            if (success) {
                Toast.makeText(this, "パスワードがリセットされました！", Toast.LENGTH_LONG).show()

                // Navigate to LoginActivity
                val intent = Intent(this, LoginActivity::class.java)
                intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                startActivity(intent)
                finish()
                overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
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

