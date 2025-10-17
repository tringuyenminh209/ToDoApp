package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

class ForgotPasswordViewModel : ViewModel() {

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _resetSuccess = MutableLiveData<Boolean>()
    val resetSuccess: LiveData<Boolean> = _resetSuccess

    fun resetPassword(email: String) {
        viewModelScope.launch {
            try {
                _isLoading.value = true

                // Simulate API call
                delay(2000)

                // Mock validation
                if (android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
                    _resetSuccess.value = true
                } else {
                    _error.value = "Email không hợp lệ"
                }

            } catch (e: Exception) {
                _error.value = "Đã xảy ra lỗi: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearError() {
        _error.value = null
    }
}