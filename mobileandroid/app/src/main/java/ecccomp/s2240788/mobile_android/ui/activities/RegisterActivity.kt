package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityRegisterBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.RegisterViewModel

class RegisterActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterBinding
    private lateinit var viewModel: RegisterViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupViewModel()
        setupClickListeners()
        setupObservers()
        setupInputValidation()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[RegisterViewModel::class.java]
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener {
            onBackPressed()
        }

        // Register button
        binding.btnRegister.setOnClickListener {
            if (validateInputs()) {
                val name = binding.etName.text.toString().trim()
                val email = binding.etEmail.text.toString().trim()
                val password = binding.etPassword.text.toString()
                val termsAccepted = binding.cbTerms.isChecked

                if (!termsAccepted) {
                    Toast.makeText(this, getString(R.string.terms_required), Toast.LENGTH_SHORT).show()
                    return@setOnClickListener
                }

                viewModel.register(name, email, password)
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
            binding.progressRegister.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnRegister.isEnabled = !isLoading
            binding.btnRegister.text = if (isLoading) getString(R.string.registering) else getString(R.string.register_button)
        }

        // Error handling
        viewModel.error.observe(this) { error ->
            if (error != null) {
                showError(error)
                viewModel.clearError()
            }
        }

        // Success handling
        viewModel.registerSuccess.observe(this) { success ->
            if (success) {
                Toast.makeText(this, getString(R.string.register_success), Toast.LENGTH_SHORT).show()
                // Navigate to LoginActivity
                startActivity(Intent(this, LoginActivity::class.java))
                finish()
                overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
            }
        }
    }

    private fun setupInputValidation() {
        // Real-time validation
        binding.etName.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) validateName()
        }

        binding.etEmail.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) validateEmail()
        }

        binding.etPassword.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) validatePassword()
        }

        binding.etConfirmPassword.setOnFocusChangeListener { _, hasFocus ->
            if (!hasFocus) validateConfirmPassword()
        }
    }

    private fun validateInputs(): Boolean {
        val isNameValid = validateName()
        val isEmailValid = validateEmail()
        val isPasswordValid = validatePassword()
        val isConfirmPasswordValid = validateConfirmPassword()

        return isNameValid && isEmailValid && isPasswordValid && isConfirmPasswordValid
    }

    private fun validateName(): Boolean {
        val name = binding.etName.text.toString().trim()
        val nameLayout = binding.tilName

        return when {
            name.isEmpty() -> {
                nameLayout.error = getString(R.string.name_required)
                false
            }
            name.length < 2 -> {
                nameLayout.error = getString(R.string.name_too_short)
                false
            }
            else -> {
                nameLayout.error = null
                true
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

    private fun validatePassword(): Boolean {
        val password = binding.etPassword.text.toString()
        val passwordLayout = binding.tilPassword

        return when {
            password.isEmpty() -> {
                passwordLayout.error = getString(R.string.password_required)
                false
            }
            password.length < 6 -> {
                passwordLayout.error = getString(R.string.password_too_short)
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
                confirmPasswordLayout.error = getString(R.string.confirm_password_required)
                false
            }
            password != confirmPassword -> {
                confirmPasswordLayout.error = getString(R.string.passwords_not_match)
                false
            }
            else -> {
                confirmPasswordLayout.error = null
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