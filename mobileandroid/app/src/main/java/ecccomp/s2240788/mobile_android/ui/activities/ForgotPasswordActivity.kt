package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityForgotPasswordBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.ForgotPasswordViewModel

class ForgotPasswordActivity : BaseActivity() {

    private lateinit var binding: ActivityForgotPasswordBinding
    private lateinit var viewModel: ForgotPasswordViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityForgotPasswordBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupViewModel()
        setupClickListeners()
        setupObservers()
        setupInputValidation()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[ForgotPasswordViewModel::class.java]
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener {
            onBackPressed()
        }

        // Reset button
        binding.btnReset.setOnClickListener {
            if (validateEmail()) {
                val email = binding.etEmail.text.toString().trim()
                viewModel.resetPassword(email)
            }
        }

        // Login link
        binding.tvLoginLink.setOnClickListener {
            startActivity(Intent(this, LoginActivity::class.java))
            finish()
            overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
        }
    }

    private fun setupObservers() {
        // Loading state
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressReset.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnReset.isEnabled = !isLoading
            binding.btnReset.text = if (isLoading) getString(R.string.resetting_password) else getString(R.string.reset_password_button)
        }

        // Error handling
        viewModel.error.observe(this) { error ->
            if (error != null) {
                showError(error)
                viewModel.clearError()
            }
        }

        // Success handling
        viewModel.resetSuccess.observe(this) { success ->
            if (success) {
                Toast.makeText(this, "リセットメールを送信しました。トークンを入力してください", Toast.LENGTH_LONG).show()
                
                // Navigate to ResetPasswordActivity with email
                val intent = Intent(this, ResetPasswordActivity::class.java)
                intent.putExtra("email", binding.etEmail.text.toString().trim())
                startActivity(intent)
                finish()
                overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left)
            }
        }
    }

    private fun setupInputValidation() {
        binding.etEmail.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) {
                validateEmail()
            }
        }
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

    private fun showError(message: String) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    }

    override fun onBackPressed() {
        super.onBackPressed()
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
    }
}