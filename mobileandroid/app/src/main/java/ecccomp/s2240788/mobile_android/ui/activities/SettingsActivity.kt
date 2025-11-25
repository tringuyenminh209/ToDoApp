package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.lifecycle.lifecycleScope
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.SettingsRequest
import ecccomp.s2240788.mobile_android.data.models.User
import ecccomp.s2240788.mobile_android.data.models.UserSettings
import ecccomp.s2240788.mobile_android.data.repository.AuthRepository
import ecccomp.s2240788.mobile_android.data.repository.SettingsRepository
import ecccomp.s2240788.mobile_android.data.result.AuthResult
import ecccomp.s2240788.mobile_android.data.result.SettingsResult
import ecccomp.s2240788.mobile_android.databinding.ActivitySettingsBinding
import ecccomp.s2240788.mobile_android.utils.LocaleHelper
import ecccomp.s2240788.mobile_android.utils.NetworkModule
import ecccomp.s2240788.mobile_android.utils.SettingsPreferences
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

class SettingsActivity : BaseActivity() {

    companion object {
        private const val TAG = "SettingsActivity"
    }

    private lateinit var binding: ActivitySettingsBinding
    private lateinit var settingsRepository: SettingsRepository
    private lateinit var authRepository: AuthRepository
    private var currentSettings: UserSettings? = null
    private var currentUser: User? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivitySettingsBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()
        initializeRepositories()
        setupSpinners()
        setupClickListeners()
        setupBottomNavigation()

        // Load user profile and settings
        loadUserProfile()
        loadSettings()
    }

    private fun initializeRepositories() {
        val apiService = NetworkModule.apiService
        settingsRepository = SettingsRepository(apiService)
        authRepository = AuthRepository(apiService)
    }

    private fun setupSpinners() {
        // Theme spinner
        ArrayAdapter.createFromResource(
            this,
            R.array.theme_options,
            android.R.layout.simple_spinner_item
        ).also { adapter ->
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            binding.spinnerTheme.adapter = adapter
        }

        // Language spinner
        ArrayAdapter.createFromResource(
            this,
            R.array.language_options,
            android.R.layout.simple_spinner_item
        ).also { adapter ->
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            binding.spinnerLanguage.adapter = adapter
        }

        // Pomodoro time spinner
        ArrayAdapter.createFromResource(
            this,
            R.array.pomodoro_time_options,
            android.R.layout.simple_spinner_item
        ).also { adapter ->
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            binding.spinnerPomodoroTime.adapter = adapter
        }
    }

    private fun setupClickListeners() {
        // Back button
        binding.btnBack?.setOnClickListener { finish() }

        // Save settings button
        binding.btnSaveSettings.setOnClickListener { saveSettings() }

        // Reset settings button
        binding.btnResetSettings.setOnClickListener { showResetConfirmationDialog() }

        // Export data button
        binding.btnExportData.setOnClickListener { exportUserData() }

        // Delete data button
        binding.btnDeleteData.setOnClickListener { showDeleteDataDialog() }

        // Edit profile button
        binding.btnEditProfile.setOnClickListener { editProfile() }

        // Change password button
        binding.btnChangePassword.setOnClickListener { changePassword() }

        // Logout button
        binding.btnLogout.setOnClickListener { logout() }
    }

    private fun loadUserProfile() {
        lifecycleScope.launch {
            Log.d(TAG, "Loading user profile...")
            val result = withContext(Dispatchers.IO) {
                authRepository.getCurrentUser()
            }

            when (result) {
                is AuthResult.Success -> {
                    currentUser = result.data
                    Log.d(TAG, "User profile loaded: name=${result.data.name}, email=${result.data.email}")
                    updateUserProfileUI(result.data)
                }
                is AuthResult.Error -> {
                    Log.e(TAG, "Failed to load user profile: ${result.message}")
                    Toast.makeText(
                        this@SettingsActivity,
                        getString(R.string.failed_to_load_profile, result.message),
                        Toast.LENGTH_SHORT
                    ).show()
                }
                else -> {
                    Log.e(TAG, "Unknown result type when loading user profile")
                }
            }
        }
    }

    private fun updateUserProfileUI(user: User) {
        Log.d(TAG, "Updating UI with user: name=${user.name}, email=${user.email}")
        binding.tvUserName.text = user.name ?: getString(R.string.unknown_user)
        binding.tvUserEmail.text = user.email ?: getString(R.string.no_email)
        // Load user stats
        loadUserStats()
    }

    private fun loadUserStats() {
        lifecycleScope.launch {
            try {
                val response = withContext(Dispatchers.IO) {
                    NetworkModule.apiService.getUserStats()
                }

                if (response.isSuccessful && response.body()?.success == true) {
                    val stats = response.body()?.data
                    stats?.let {
                        // Update UI with real stats
                        binding.tvStreakValue.text = it.current_streak.toString()
                        binding.tvFocusValue.text = it.total_focus_time.toString()
                        binding.tvTasksValue.text = it.completed_tasks.toString()
                        Log.d(TAG, "Stats loaded: streak=${it.current_streak}, focus=${it.total_focus_time}, tasks=${it.completed_tasks}")
                    }
                } else {
                    Log.e(TAG, "Failed to load stats: ${response.code()}")
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error loading stats", e)
            }
        }
    }

    private fun loadSettings() {
        // First load from cache
        val cachedSettings = SettingsPreferences.getSettings(this)
        if (cachedSettings != null) {
            currentSettings = cachedSettings
            updateUIWithSettings(cachedSettings)
        }

        // Then fetch from server
        lifecycleScope.launch {
            val result = withContext(Dispatchers.IO) {
                settingsRepository.getSettings()
            }

            when (result) {
                is SettingsResult.Success -> {
                    currentSettings = result.data
                    updateUIWithSettings(result.data)
                    // Save to cache
                    SettingsPreferences.saveSettings(this@SettingsActivity, result.data)
                }
                is SettingsResult.Error -> {
                    Log.e(TAG, "Failed to load settings: ${result.message}")
                    Toast.makeText(
                        this@SettingsActivity,
                        getString(R.string.failed_to_load_settings, result.message),
                        Toast.LENGTH_SHORT
                    ).show()
                }
                else -> {}
            }
        }
    }

    private fun updateUIWithSettings(settings: UserSettings) {
        // Theme
        binding.spinnerTheme.setSelection(getThemePosition(settings.theme))

        // Language
        binding.spinnerLanguage.setSelection(getLanguagePosition(settings.language))

        // Pomodoro time
        binding.spinnerPomodoroTime.setSelection(getPomodoroPosition(settings.pomodoroDuration))

        // Switches
        binding.switchPushNotifications.isChecked = settings.pushNotifications
        binding.switchDailyReminders.isChecked = settings.dailyReminders
        binding.switchGoalReminders.isChecked = settings.goalReminders
        binding.switchBlockNotifications.isChecked = settings.blockNotifications
        binding.switchBackgroundSound.isChecked = settings.backgroundSound
    }

    private fun saveSettings() {
        if (currentSettings == null) {
            Toast.makeText(this, getString(R.string.settings_not_loaded), Toast.LENGTH_SHORT).show()
            return
        }

        // Get values from UI
        val theme = getThemeFromPosition(binding.spinnerTheme.selectedItemPosition)
        val language = getLanguageFromPosition(binding.spinnerLanguage.selectedItemPosition)
        val pomodoroDuration = getPomodoroFromPosition(binding.spinnerPomodoroTime.selectedItemPosition)

        val pushNotifications = binding.switchPushNotifications.isChecked
        val dailyReminders = binding.switchDailyReminders.isChecked
        val goalReminders = binding.switchGoalReminders.isChecked
        val blockNotifications = binding.switchBlockNotifications.isChecked
        val backgroundSound = binding.switchBackgroundSound.isChecked

        // Check if language has changed
        val languageChanged = currentSettings?.language != language

        // Create request
        val request = SettingsRequest(
            theme = theme,
            pomodoroDuration = pomodoroDuration,
            breakMinutes = currentSettings?.breakMinutes,
            longBreakMinutes = currentSettings?.longBreakMinutes,
            autoStartBreak = currentSettings?.autoStartBreak,
            blockNotifications = blockNotifications,
            backgroundSound = backgroundSound,
            dailyTargetTasks = currentSettings?.dailyTargetTasks,
            notificationEnabled = currentSettings?.notificationEnabled,
            pushNotifications = pushNotifications,
            dailyReminders = dailyReminders,
            goalReminders = goalReminders,
            reminderTimes = currentSettings?.reminderTimes,
            language = language,
            timezone = currentSettings?.timezone
        )

        // Show loading
        binding.btnSaveSettings.isEnabled = false
        binding.btnSaveSettings.text = getString(R.string.saving)

        lifecycleScope.launch {
            val result = withContext(Dispatchers.IO) {
                settingsRepository.updateSettings(request)
            }

            binding.btnSaveSettings.isEnabled = true
            binding.btnSaveSettings.setText(R.string.save_settings)

            when (result) {
                is SettingsResult.Success -> {
                    currentSettings = result.data
                    // Save to cache
                    SettingsPreferences.saveSettings(this@SettingsActivity, result.data)

                    // If language changed, update LocaleHelper and recreate activity
                    if (languageChanged) {
                        LocaleHelper.setLanguage(this@SettingsActivity, language)
                        Toast.makeText(
                            this@SettingsActivity,
                            getString(R.string.settings_saved),
                            Toast.LENGTH_SHORT
                        ).show()
                        // Recreate activity to apply language change
                        recreate()
                    } else {
                        Toast.makeText(
                            this@SettingsActivity,
                            getString(R.string.settings_saved),
                            Toast.LENGTH_SHORT
                        ).show()
                    }
                }
                is SettingsResult.Error -> {
                    Toast.makeText(
                        this@SettingsActivity,
                        getString(R.string.failed_to_save, result.message),
                        Toast.LENGTH_SHORT
                    ).show()
                }
                else -> {}
            }
        }
    }

    private fun showResetConfirmationDialog() {
        AlertDialog.Builder(this)
            .setTitle(getString(R.string.reset_settings_title))
            .setMessage(getString(R.string.reset_settings_message))
            .setPositiveButton(getString(R.string.reset)) { _, _ -> resetSettings() }
            .setNegativeButton(getString(R.string.cancel), null)
            .show()
    }

    private fun resetSettings() {
        lifecycleScope.launch {
            val result = withContext(Dispatchers.IO) {
                settingsRepository.resetSettings()
            }

            when (result) {
                is SettingsResult.Success -> {
                    currentSettings = result.data
                    updateUIWithSettings(result.data)
                    // Save to cache
                    SettingsPreferences.saveSettings(this@SettingsActivity, result.data)
                    Toast.makeText(
                        this@SettingsActivity,
                        getString(R.string.settings_reset_success),
                        Toast.LENGTH_SHORT
                    ).show()
                }
                is SettingsResult.Error -> {
                    Toast.makeText(
                        this@SettingsActivity,
                        getString(R.string.failed_to_reset, result.message),
                        Toast.LENGTH_SHORT
                    ).show()
                }
                else -> {}
            }
        }
    }

    private fun exportUserData() {
        Toast.makeText(this, getString(R.string.export_data_coming_soon), Toast.LENGTH_SHORT).show()
        // TODO: Implement data export
    }

    private fun showDeleteDataDialog() {
        AlertDialog.Builder(this)
            .setTitle(getString(R.string.delete_data_title))
            .setMessage(getString(R.string.delete_data_message))
            .setPositiveButton(getString(R.string.delete)) { _, _ -> deleteUserData() }
            .setNegativeButton(getString(R.string.cancel), null)
            .show()
    }

    private fun deleteUserData() {
        Toast.makeText(this, getString(R.string.delete_data_coming_soon), Toast.LENGTH_SHORT).show()
        // TODO: Implement data deletion
    }

    private fun editProfile() {
        Toast.makeText(this, getString(R.string.edit_profile_coming_soon), Toast.LENGTH_SHORT).show()
        // TODO: Navigate to edit profile screen
    }

    private fun changePassword() {
        Toast.makeText(this, getString(R.string.change_password_coming_soon), Toast.LENGTH_SHORT).show()
        // TODO: Navigate to change password screen
    }

    private fun logout() {
        AlertDialog.Builder(this)
            .setTitle(getString(R.string.logout_title))
            .setMessage(getString(R.string.logout_message))
            .setPositiveButton(getString(R.string.logout)) { _, _ -> performLogout() }
            .setNegativeButton(getString(R.string.cancel), null)
            .show()
    }

    private fun performLogout() {
        lifecycleScope.launch {
            withContext(Dispatchers.IO) {
                authRepository.logout()
            }

            // Clear settings cache
            SettingsPreferences.clearSettings(this@SettingsActivity)

            // Navigate to login
            val intent = Intent(this@SettingsActivity, LoginActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
            finish()
        }
    }

    // Helper methods for spinner positions
    private fun getThemePosition(theme: String): Int {
        return when (theme) {
            "light" -> 0
            "dark" -> 1
            "auto" -> 2
            else -> 2
        }
    }

    private fun getThemeFromPosition(position: Int): String {
        return when (position) {
            0 -> "light"
            1 -> "dark"
            2 -> "auto"
            else -> "auto"
        }
    }

    private fun getLanguagePosition(language: String): Int {
        return when (language) {
            "vi" -> 0
            "en" -> 1
            "ja" -> 2
            else -> 0
        }
    }

    private fun getLanguageFromPosition(position: Int): String {
        return when (position) {
            0 -> "vi"
            1 -> "en"
            2 -> "ja"
            else -> "vi"
        }
    }

    private fun getPomodoroPosition(minutes: Int): Int {
        return when (minutes) {
            15 -> 0
            25 -> 1
            30 -> 2
            45 -> 3
            60 -> 4
            else -> 1 // 25 minutes default
        }
    }

    private fun getPomodoroFromPosition(position: Int): Int {
        return when (position) {
            0 -> 15
            1 -> 25
            2 -> 30
            3 -> 45
            4 -> 60
            else -> 25
        }
    }

    private fun setupBottomNavigation() {
        binding.bottomNavigation.selectedItemId = R.id.nav_settings

        binding.bottomNavigation.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_home -> {
                    startActivity(Intent(this, MainActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_calendar -> {
                    startActivity(Intent(this, CalendarActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_paths -> {
                    startActivity(Intent(this, PathsActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_knowledge -> {
                    startActivity(Intent(this, KnowledgeActivity::class.java))
                    finish()
                    true
                }
                R.id.nav_settings -> {
                    // Current screen
                    true
                }
                else -> false
            }
        }
    }
}
