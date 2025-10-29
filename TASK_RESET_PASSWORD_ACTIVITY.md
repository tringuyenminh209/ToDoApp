# TASK: Implement Reset Password Activity - Complete Password Reset Flow

## 🎯 Objective
Tạo ResetPasswordActivity để user có thể nhập 6-digit OTP token và password mới sau khi nhận email từ forgot password flow. Đây là bước 2 trong password reset flow.

---

## 📋 Current Flow (BUG)

```
User → ForgotPasswordActivity
  ↓ Nhập email: user@example.com
  ↓ Click "Send Reset Email"
Backend gửi OTP: 123456 qua email ✅
  ↓
Navigate to LoginActivity ❌ BUG!
  ↓
User nhận OTP nhưng KHÔNG CÓ NƠI NHẬP! 😱
```

## ✅ Expected Flow (CORRECT)

```
User → ForgotPasswordActivity
  ↓ Nhập email: user@example.com
  ↓ Click "Send Reset Email"
Backend gửi OTP: 123456 qua email ✅
  ↓
Navigate to ResetPasswordActivity ✅ (CẦN TẠO)
  ↓ User nhập:
    - Email: user@example.com (read-only, pre-filled)
    - OTP Token: 123456
    - New Password: NewPass123A
    - Confirm Password: NewPass123A
  ↓ Click "Reset Password"
Backend verify OTP + update password ✅
  ↓
Navigate to LoginActivity ✅
  ↓
User login với password mới ✅ DONE!
```

---

## 📁 Files to Create/Modify

### ✅ Already Available (No Need to Create)
- ✅ `backend/app/Http/Controllers/PasswordResetController.php` - Backend endpoint ready
- ✅ `backend/routes/api.php` - POST /api/reset-password endpoint exists
- ✅ `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/data/api/ApiService.kt` - resetPassword() method exists
- ✅ `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/data/models/Task.kt` - ResetPasswordRequest model exists
- ✅ Backend email template với OTP 6 digits

### 🆕 Files to CREATE (3 files)
1. ❌ `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/viewmodels/ResetPasswordViewModel.kt`
2. ❌ `mobileandroid/app/src/main/res/layout/activity_reset_password.xml`
3. ❌ `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/ResetPasswordActivity.kt`

### 🔧 Files to MODIFY (2 files)
1. 🔄 `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/ForgotPasswordActivity.kt` - Fix navigation
2. 🔄 `mobileandroid/app/src/main/AndroidManifest.xml` - Register new activity

---

## 📝 TASK 1: Create ResetPasswordViewModel

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/viewmodels/ResetPasswordViewModel.kt`

**Requirements:**
- Method `resetPassword(email, token, password, passwordConfirmation)`
- Validation:
  - Token must be exactly 6 digits (numeric only)
  - Password min 8 chars with uppercase, lowercase, digit (match backend regex)
  - Password confirmation must match password
- Call `apiService.resetPassword(ResetPasswordRequest(...))`
- Error handling for status codes: 422 (invalid/expired token), 404 (email not found), 500 (server error)
- LiveData: `isLoading`, `error`, `resetSuccess`
- Japanese error messages

**Complete Code:**

```kotlin
package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.ResetPasswordRequest
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import kotlinx.coroutines.launch

/**
 * ResetPasswordViewModel
 * パスワードリセット処理（OTP + 新しいパスワード）
 * Backend: POST /api/reset-password
 */
class ResetPasswordViewModel : ViewModel() {

    private val apiService: ApiService = NetworkModule.provideApiService(
        NetworkModule.provideRetrofit(NetworkModule.provideOkHttpClient())
    )

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _resetSuccess = MutableLiveData<Boolean>()
    val resetSuccess: LiveData<Boolean> = _resetSuccess

    /**
     * パスワードリセット処理
     * Backend: POST /api/reset-password
     * Request: { email, token, password, password_confirmation }
     * Response: { message: "パスワードがリセットされました" }
     */
    fun resetPassword(email: String, token: String, password: String, passwordConfirmation: String) {
        viewModelScope.launch {
            try {
                // Validation: token must be exactly 6 digits
                if (token.length != 6) {
                    _error.value = "トークンは6桁である必要があります"
                    return@launch
                }

                if (!token.all { it.isDigit() }) {
                    _error.value = "トークンは数字のみである必要があります"
                    return@launch
                }

                // Validation: password min 8 chars with uppercase, lowercase, digit
                if (password.length < 8) {
                    _error.value = "パスワードは8文字以上である必要があります"
                    return@launch
                }

                if (!password.matches(Regex("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).+$"))) {
                    _error.value = "パスワードは大文字、小文字、数字を含む必要があります"
                    return@launch
                }

                // Validation: password confirmation match
                if (password != passwordConfirmation) {
                    _error.value = "パスワードが一致しません"
                    return@launch
                }

                _isLoading.value = true
                _error.value = null

                val request = ResetPasswordRequest(email, token, password, passwordConfirmation)
                val response = apiService.resetPassword(request)

                if (response.isSuccessful) {
                    _resetSuccess.value = true
                } else {
                    // HTTP error handling
                    _error.value = when (response.code()) {
                        422 -> "トークンが無効または期限切れです"
                        404 -> "このメールアドレスは登録されていません"
                        500 -> "サーバーエラーが発生しました。しばらくしてからお試しください"
                        else -> "パスワードリセットに失敗しました: ${response.message()}"
                    }
                }

            } catch (e: Exception) {
                _error.value = "ネットワークエラー: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearError() {
        _error.value = null
    }
}
```

---

## 📝 TASK 2: Create Layout activity_reset_password.xml

**File:** `mobileandroid/app/src/main/res/layout/activity_reset_password.xml`

**Design Requirements:**
- Follow same design pattern as `activity_forgot_password.xml` (glassmorphism, gradient background)
- Components:
  - Top bar with back button
  - Icon container (same style as forgot password)
  - Title: "パスワードリセット"
  - Subtitle: "6桁のトークンと新しいパスワードを入力してください"
  - Card with 4 input fields:
    1. Email (TextInputLayout, **disabled/read-only**, pre-filled)
    2. Token (TextInputLayout, 6 digits, number input type)
    3. New Password (TextInputLayout, password input type)
    4. Confirm Password (TextInputLayout, password input type)
  - Reset Password button
  - Loading overlay

**Complete Code:**

```xml
<?xml version="1.0" encoding="utf-8"?>
<androidx.constraintlayout.widget.ConstraintLayout xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:app="http://schemas.android.com/apk/res-auto"
    xmlns:tools="http://schemas.android.com/tools"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    android:background="@drawable/login_background_gradient"
    tools:context=".ui.activities.ResetPasswordActivity">

    <!-- Animated Background Overlay -->
    <View
        android:id="@+id/background_overlay"
        android:layout_width="match_parent"
        android:layout_height="match_parent"
        android:alpha="0.95"
        android:background="@drawable/animated_gradient_background" />

    <!-- Top Bar -->
    <androidx.constraintlayout.widget.ConstraintLayout
        android:id="@+id/top_bar"
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:paddingHorizontal="@dimen/spacing_lg"
        android:paddingVertical="@dimen/spacing_xl"
        app:layout_constraintTop_toTopOf="parent">

        <!-- Back Button -->
        <com.google.android.material.button.MaterialButton
            android:id="@+id/btn_back"
            style="@style/Widget.Material3.Button.TextButton"
            android:layout_width="48dp"
            android:layout_height="48dp"
            android:contentDescription="@string/back"
            android:insetLeft="0dp"
            android:insetTop="0dp"
            android:insetRight="0dp"
            android:insetBottom="0dp"
            app:backgroundTint="@color/glassmorphism_overlay"
            app:cornerRadius="24dp"
            app:icon="@drawable/ic_arrow_back"
            app:iconGravity="textStart"
            app:iconPadding="0dp"
            app:iconSize="24dp"
            app:iconTint="@color/white"
            app:layout_constraintStart_toStartOf="parent"
            app:layout_constraintTop_toTopOf="parent"
            app:strokeColor="@color/glassmorphism_border"
            app:strokeWidth="1dp" />

    </androidx.constraintlayout.widget.ConstraintLayout>

    <!-- Main Content ScrollView -->
    <ScrollView
        android:layout_width="match_parent"
        android:layout_height="0dp"
        android:fillViewport="true"
        android:overScrollMode="never"
        app:layout_constraintBottom_toBottomOf="parent"
        app:layout_constraintTop_toBottomOf="@id/top_bar">

        <androidx.constraintlayout.widget.ConstraintLayout
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:paddingHorizontal="@dimen/spacing_xl"
            android:paddingBottom="@dimen/spacing_xl">

            <!-- Icon Container with Glassmorphism -->
            <com.google.android.material.card.MaterialCardView
                android:id="@+id/icon_container"
                android:layout_width="100dp"
                android:layout_height="100dp"
                android:layout_marginTop="@dimen/spacing_xxl"
                app:cardBackgroundColor="@color/surface_glassmorphism"
                app:cardCornerRadius="28dp"
                app:cardElevation="0dp"
                app:layout_constraintEnd_toEndOf="parent"
                app:layout_constraintStart_toStartOf="parent"
                app:layout_constraintTop_toTopOf="parent"
                app:strokeColor="@color/glassmorphism_border"
                app:strokeWidth="2dp">

                <ImageView
                    android:id="@+id/iv_reset_icon"
                    android:layout_width="match_parent"
                    android:layout_height="match_parent"
                    android:contentDescription="@string/reset_password_icon"
                    android:padding="20dp"
                    android:scaleType="fitCenter"
                    android:src="@drawable/ic_lock" />

            </com.google.android.material.card.MaterialCardView>

            <!-- Title -->
            <TextView
                android:id="@+id/tv_reset_title"
                android:layout_width="wrap_content"
                android:layout_height="wrap_content"
                android:layout_marginTop="@dimen/spacing_lg"
                android:text="パスワードリセット"
                android:textColor="@color/white"
                android:textSize="24sp"
                android:textStyle="bold"
                app:layout_constraintEnd_toEndOf="parent"
                app:layout_constraintStart_toStartOf="parent"
                app:layout_constraintTop_toBottomOf="@id/icon_container" />

            <!-- Subtitle -->
            <TextView
                android:id="@+id/tv_reset_subtitle"
                android:layout_width="0dp"
                android:layout_height="wrap_content"
                android:layout_marginTop="@dimen/spacing_sm"
                android:alpha="0.85"
                android:gravity="center"
                android:text="6桁のトークンと新しいパスワードを入力してください"
                android:textColor="@color/white"
                android:textSize="12sp"
                app:layout_constraintEnd_toEndOf="parent"
                app:layout_constraintStart_toStartOf="parent"
                app:layout_constraintTop_toBottomOf="@id/tv_reset_title" />

            <!-- Reset Form Card with Enhanced Glassmorphism -->
            <com.google.android.material.card.MaterialCardView
                android:id="@+id/reset_form_card"
                android:layout_width="match_parent"
                android:layout_height="wrap_content"
                android:layout_marginTop="@dimen/spacing_xxl"
                app:cardBackgroundColor="@color/surface_glassmorphism"
                app:cardCornerRadius="28dp"
                app:cardElevation="0dp"
                app:layout_constraintTop_toBottomOf="@id/tv_reset_subtitle"
                app:strokeColor="@color/glassmorphism_border"
                app:strokeWidth="2dp">

                <LinearLayout
                    android:layout_width="match_parent"
                    android:layout_height="wrap_content"
                    android:orientation="vertical"
                    android:padding="@dimen/spacing_xxl">

                    <!-- Email Input (Read-only) -->
                    <com.google.android.material.textfield.TextInputLayout
                        android:id="@+id/til_email"
                        style="@style/Widget.Material3.TextInputLayout.FilledBox"
                        android:layout_width="match_parent"
                        android:layout_height="wrap_content"
                        android:layout_marginBottom="@dimen/spacing_md"
                        android:hint="@string/email_hint"
                        app:boxBackgroundColor="@color/white"
                        app:boxCornerRadiusBottomEnd="16dp"
                        app:boxCornerRadiusBottomStart="16dp"
                        app:boxCornerRadiusTopEnd="16dp"
                        app:boxCornerRadiusTopStart="16dp"
                        app:boxStrokeColor="@color/primary"
                        app:boxStrokeWidth="0dp"
                        app:boxStrokeWidthFocused="3dp"
                        app:hintTextColor="@color/text_secondary"
                        app:startIconDrawable="@drawable/ic_email">

                        <com.google.android.material.textfield.TextInputEditText
                            android:id="@+id/et_email"
                            android:layout_width="match_parent"
                            android:layout_height="56dp"
                            android:enabled="false"
                            android:inputType="textEmailAddress"
                            android:maxLines="1"
                            android:paddingStart="56dp"
                            android:paddingEnd="12dp"
                            android:textColor="@color/text_secondary"
                            android:textSize="14sp" />

                    </com.google.android.material.textfield.TextInputLayout>

                    <!-- Token Input (6 digits) -->
                    <com.google.android.material.textfield.TextInputLayout
                        android:id="@+id/til_token"
                        style="@style/Widget.Material3.TextInputLayout.FilledBox"
                        android:layout_width="match_parent"
                        android:layout_height="wrap_content"
                        android:layout_marginBottom="@dimen/spacing_md"
                        android:hint="6桁のトークン"
                        app:boxBackgroundColor="@color/white"
                        app:boxCornerRadiusBottomEnd="16dp"
                        app:boxCornerRadiusBottomStart="16dp"
                        app:boxCornerRadiusTopEnd="16dp"
                        app:boxCornerRadiusTopStart="16dp"
                        app:boxStrokeColor="@color/primary"
                        app:boxStrokeWidth="0dp"
                        app:boxStrokeWidthFocused="3dp"
                        app:hintTextColor="@color/text_secondary"
                        app:startIconDrawable="@drawable/ic_lock">

                        <com.google.android.material.textfield.TextInputEditText
                            android:id="@+id/et_token"
                            android:layout_width="match_parent"
                            android:layout_height="56dp"
                            android:inputType="number"
                            android:maxLength="6"
                            android:maxLines="1"
                            android:paddingStart="56dp"
                            android:paddingEnd="12dp"
                            android:textColor="@color/text_primary"
                            android:textSize="14sp" />

                    </com.google.android.material.textfield.TextInputLayout>

                    <!-- New Password Input -->
                    <com.google.android.material.textfield.TextInputLayout
                        android:id="@+id/til_password"
                        style="@style/Widget.Material3.TextInputLayout.FilledBox"
                        android:layout_width="match_parent"
                        android:layout_height="wrap_content"
                        android:layout_marginBottom="@dimen/spacing_md"
                        android:hint="新しいパスワード"
                        app:boxBackgroundColor="@color/white"
                        app:boxCornerRadiusBottomEnd="16dp"
                        app:boxCornerRadiusBottomStart="16dp"
                        app:boxCornerRadiusTopEnd="16dp"
                        app:boxCornerRadiusTopStart="16dp"
                        app:boxStrokeColor="@color/primary"
                        app:boxStrokeWidth="0dp"
                        app:boxStrokeWidthFocused="3dp"
                        app:endIconMode="password_toggle"
                        app:hintTextColor="@color/text_secondary"
                        app:startIconDrawable="@drawable/ic_password">

                        <com.google.android.material.textfield.TextInputEditText
                            android:id="@+id/et_password"
                            android:layout_width="match_parent"
                            android:layout_height="56dp"
                            android:inputType="textPassword"
                            android:maxLines="1"
                            android:paddingStart="56dp"
                            android:paddingEnd="12dp"
                            android:textColor="@color/text_primary"
                            android:textSize="14sp" />

                    </com.google.android.material.textfield.TextInputLayout>

                    <!-- Confirm Password Input -->
                    <com.google.android.material.textfield.TextInputLayout
                        android:id="@+id/til_confirm_password"
                        style="@style/Widget.Material3.TextInputLayout.FilledBox"
                        android:layout_width="match_parent"
                        android:layout_height="wrap_content"
                        android:layout_marginBottom="@dimen/spacing_lg"
                        android:hint="パスワード確認"
                        app:boxBackgroundColor="@color/white"
                        app:boxCornerRadiusBottomEnd="16dp"
                        app:boxCornerRadiusBottomStart="16dp"
                        app:boxCornerRadiusTopEnd="16dp"
                        app:boxCornerRadiusTopStart="16dp"
                        app:boxStrokeColor="@color/primary"
                        app:boxStrokeWidth="0dp"
                        app:boxStrokeWidthFocused="3dp"
                        app:endIconMode="password_toggle"
                        app:hintTextColor="@color/text_secondary"
                        app:startIconDrawable="@drawable/ic_password">

                        <com.google.android.material.textfield.TextInputEditText
                            android:id="@+id/et_confirm_password"
                            android:layout_width="match_parent"
                            android:layout_height="56dp"
                            android:inputType="textPassword"
                            android:maxLines="1"
                            android:paddingStart="56dp"
                            android:paddingEnd="12dp"
                            android:textColor="@color/text_primary"
                            android:textSize="14sp" />

                    </com.google.android.material.textfield.TextInputLayout>

                    <!-- Reset Password Button -->
                    <com.google.android.material.button.MaterialButton
                        android:id="@+id/btn_reset_password"
                        android:layout_width="match_parent"
                        android:layout_height="56dp"
                        android:text="パスワードをリセット"
                        android:textColor="@color/white"
                        android:textSize="15sp"
                        android:textStyle="bold"
                        app:backgroundTint="@color/primary"
                        app:cornerRadius="16dp"
                        app:elevation="4dp" />

                </LinearLayout>

            </com.google.android.material.card.MaterialCardView>

        </androidx.constraintlayout.widget.ConstraintLayout>

    </ScrollView>

    <!-- Loading Overlay -->
    <androidx.constraintlayout.widget.ConstraintLayout
        android:id="@+id/loading_overlay"
        android:layout_width="match_parent"
        android:layout_height="match_parent"
        android:background="@color/overlay"
        android:clickable="true"
        android:focusable="true"
        android:visibility="gone">

        <com.google.android.material.progressindicator.CircularProgressIndicator
            android:id="@+id/progress_bar"
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:indeterminate="true"
            app:indicatorColor="@color/white"
            app:indicatorSize="56dp"
            app:layout_constraintBottom_toBottomOf="parent"
            app:layout_constraintEnd_toEndOf="parent"
            app:layout_constraintStart_toStartOf="parent"
            app:layout_constraintTop_toTopOf="parent"
            app:trackColor="@color/glassmorphism_border"
            app:trackThickness="4dp" />

    </androidx.constraintlayout.widget.ConstraintLayout>

</androidx.constraintlayout.widget.ConstraintLayout>
```

---

## 📝 TASK 3: Create ResetPasswordActivity

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/ResetPasswordActivity.kt`

**Requirements:**
- Receive email from ForgotPasswordActivity via Intent extras
- ViewBinding setup
- ViewModel initialization
- Real-time input validation with `setOnFocusChangeListener`
- Observe ViewModel LiveData (isLoading, error, resetSuccess)
- On success: navigate to LoginActivity with success message
- Back button handling

**Complete Code:**

```kotlin
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
class ResetPasswordActivity : AppCompatActivity() {

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
```

---

## 📝 TASK 4: Fix ForgotPasswordActivity Navigation

**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/ForgotPasswordActivity.kt`

**Change Required:**
Update the success handler to navigate to ResetPasswordActivity instead of LoginActivity.

**Location:** Lines 72-80

**OLD CODE (❌ BUG):**
```kotlin
viewModel.resetSuccess.observe(this) { success ->
    if (success) {
        Toast.makeText(this, getString(R.string.reset_success), Toast.LENGTH_SHORT).show()
        // Navigate back to LoginActivity ← BUG!
        startActivity(Intent(this, LoginActivity::class.java))
        finish()
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right)
    }
}
```

**NEW CODE (✅ CORRECT):**
```kotlin
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
```

**Action:** Replace lines 72-80 in ForgotPasswordActivity.kt with the new code above.

---

## 📝 TASK 5: Register Activity in AndroidManifest.xml

**File:** `mobileandroid/app/src/main/AndroidManifest.xml`

**Location:** Inside `<application>` tag, after other activities

**Add the following:**
```xml
<activity
    android:name=".ui.activities.ResetPasswordActivity"
    android:exported="false"
    android:screenOrientation="portrait"
    android:windowSoftInputMode="adjustResize" />
```

**Example placement:**
```xml
<application>
    ...
    <activity
        android:name=".ui.activities.ForgotPasswordActivity"
        android:exported="false"
        android:screenOrientation="portrait" />

    <!-- ADD THIS -->
    <activity
        android:name=".ui.activities.ResetPasswordActivity"
        android:exported="false"
        android:screenOrientation="portrait"
        android:windowSoftInputMode="adjustResize" />

    ...
</application>
```

---

## ✅ Testing Checklist

After implementation, test the following scenarios:

### Happy Path
- [ ] ForgotPasswordActivity → Enter valid email → Click Send
- [ ] Toast message: "リセットメールを送信しました"
- [ ] Navigate to ResetPasswordActivity
- [ ] Email field is pre-filled and disabled
- [ ] Enter 6-digit OTP (check your email or backend logs in dev mode)
- [ ] Enter new password (min 8 chars, uppercase, lowercase, digit)
- [ ] Enter confirm password (must match)
- [ ] Click "パスワードをリセット"
- [ ] Success: Toast "パスワードがリセットされました！"
- [ ] Navigate to LoginActivity
- [ ] Login with new password → Success

### Validation Tests
- [ ] Token < 6 digits → Error: "トークンは6桁である必要があります"
- [ ] Token > 6 digits → Max length prevents (maxLength="6" in XML)
- [ ] Token with letters → Error: "トークンは数字のみです"
- [ ] Empty token → Error: "トークンは必須です"
- [ ] Password < 8 chars → Error: "パスワードは8文字以上である必要があります"
- [ ] Password without uppercase → Error: "パスワードは大文字、小文字、数字を含む必要があります"
- [ ] Password without lowercase → Same error
- [ ] Password without digit → Same error
- [ ] Confirm password doesn't match → Error: "パスワードが一致しません"
- [ ] Real-time validation works on focus change

### Error Handling
- [ ] Invalid/expired token → Backend returns 422 → Error: "トークンが無効または期限切れです"
- [ ] Email not found → Backend returns 404 → Error: "このメールアドレスは登録されていません"
- [ ] Server error → Backend returns 500 → Error: "サーバーエラーが発生しました"
- [ ] Network error → Error: "ネットワークエラー: ..."

### UI/UX
- [ ] Loading overlay shows during API call
- [ ] Button disabled during loading
- [ ] Back button works correctly
- [ ] Keyboard behavior correct (adjustResize)
- [ ] Error messages clear when user starts typing
- [ ] Transitions smooth (slide animations)

---

## 📊 Expected Backend Behavior

### Development Mode (APP_DEBUG=true)
Backend returns token directly in response:
```json
POST /api/forgot-password
Request: { "email": "user@example.com" }
Response: {
  "message": "パスワードリセットトークンが発行されました",
  "token": "123456",
  "expires_in": 60
}
```

### Production Mode (APP_DEBUG=false)
Backend sends email, no token in response:
```json
POST /api/forgot-password
Request: { "email": "user@example.com" }
Response: {
  "message": "パスワードリセットリンクを送信しました",
  "expires_in": 60
}
```

### Reset Password (Both modes)
```json
POST /api/reset-password
Request: {
  "email": "user@example.com",
  "token": "123456",
  "password": "NewPass123",
  "password_confirmation": "NewPass123"
}
Response: {
  "message": "パスワードがリセットされました"
}
```

---

## 🎯 Success Criteria

Implementation is complete when:

1. ✅ All 3 new files created (ViewModel, Layout, Activity)
2. ✅ ForgotPasswordActivity navigation fixed
3. ✅ ResetPasswordActivity registered in manifest
4. ✅ Complete password reset flow works end-to-end
5. ✅ All validation working correctly
6. ✅ Error handling covers all status codes
7. ✅ UI matches design of other auth screens
8. ✅ All tests in checklist pass
9. ✅ No compilation errors
10. ✅ User can successfully reset password and login

---

## 📝 Notes

- Backend endpoint is already implemented and tested
- Email template is already created with professional styling
- Token expiration is 60 minutes (backend setting)
- In development mode, check backend logs for OTP token if email not sent
- Follow same code style as LoginActivity and RegisterActivity
- Use Japanese for all user-facing messages
- Maintain consistency with glassmorphism design pattern

---

## 🚀 Estimated Time

- TASK 1 (ViewModel): 20 minutes
- TASK 2 (Layout): 30 minutes
- TASK 3 (Activity): 40 minutes
- TASK 4 (Fix navigation): 5 minutes
- TASK 5 (Manifest): 2 minutes
- Testing: 30 minutes

**Total: ~2 hours**

---

## ✅ Completion Report

After completing all tasks, create a summary report with:
- Files created (3)
- Files modified (2)
- Testing results
- Screenshots of working flow
- Any issues encountered and solutions
