package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

class SplashViewModel : ViewModel() {

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _navigateToLogin = MutableLiveData<Boolean>()
    val navigateToLogin: LiveData<Boolean> = _navigateToLogin

    private val _navigateToMain = MutableLiveData<Boolean>()
    val navigateToMain: LiveData<Boolean> = _navigateToMain

    fun checkAuthStatus() {
        viewModelScope.launch {
            _isLoading.value = true

            // Simulate checking auth status
            delay(3000)

            // Mock: Check if user is logged in
            val isLoggedIn = false // Replace with actual auth check

            if (isLoggedIn) {
                _navigateToMain.value = true
            } else {
                _navigateToLogin.value = true
            }

            _isLoading.value = false
        }
    }

    fun clearNavigationFlags() {
        _navigateToLogin.value = false
        _navigateToMain.value = false
    }
}