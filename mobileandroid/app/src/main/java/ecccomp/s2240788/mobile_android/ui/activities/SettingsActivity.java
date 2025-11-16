package ecccomp.s2240788.mobile_android.ui.activities;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;

import ecccomp.s2240788.mobile_android.R;
import ecccomp.s2240788.mobile_android.data.api.ApiService;
import ecccomp.s2240788.mobile_android.data.models.SettingsRequest;
import ecccomp.s2240788.mobile_android.data.models.User;
import ecccomp.s2240788.mobile_android.data.models.UserSettings;
import ecccomp.s2240788.mobile_android.data.repository.AuthRepository;
import ecccomp.s2240788.mobile_android.data.repository.SettingsRepository;
import ecccomp.s2240788.mobile_android.data.result.AuthResult;
import ecccomp.s2240788.mobile_android.data.result.SettingsResult;
import ecccomp.s2240788.mobile_android.databinding.ActivitySettingsBinding;
import ecccomp.s2240788.mobile_android.utils.NetworkModule;
import ecccomp.s2240788.mobile_android.utils.SettingsPreferences;
import ecccomp.s2240788.mobile_android.utils.TokenManager;
import kotlinx.coroutines.CoroutineScope;
import kotlinx.coroutines.Dispatchers;
import kotlinx.coroutines.Job;
import kotlinx.coroutines.launch;

public class SettingsActivity extends BaseActivity {

    private static final String TAG = "SettingsActivity";

    private ActivitySettingsBinding binding;
    private SettingsRepository settingsRepository;
    private AuthRepository authRepository;
    private UserSettings currentSettings;
    private User currentUser;

    private final CoroutineScope coroutineScope = CoroutineScope(Dispatchers.getMain() + new Job());

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivitySettingsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        setupWindowInsets();
        initializeRepositories();
        setupSpinners();
        setupClickListeners();
        setupBottomNavigation();

        // Load user profile and settings
        loadUserProfile();
        loadSettings();
    }

    private void initializeRepositories() {
        ApiService apiService = NetworkModule.INSTANCE.getApiService();
        settingsRepository = new SettingsRepository(apiService);
        authRepository = new AuthRepository(apiService);
    }

    private void setupSpinners() {
        // Theme spinner
        ArrayAdapter<CharSequence> themeAdapter = ArrayAdapter.createFromResource(
            this,
            R.array.theme_options,
            android.R.layout.simple_spinner_item
        );
        themeAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        binding.spinnerTheme.setAdapter(themeAdapter);

        // Language spinner
        ArrayAdapter<CharSequence> languageAdapter = ArrayAdapter.createFromResource(
            this,
            R.array.language_options,
            android.R.layout.simple_spinner_item
        );
        languageAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        binding.spinnerLanguage.setAdapter(languageAdapter);

        // Pomodoro time spinner
        ArrayAdapter<CharSequence> pomodoroAdapter = ArrayAdapter.createFromResource(
            this,
            R.array.pomodoro_time_options,
            android.R.layout.simple_spinner_item
        );
        pomodoroAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        binding.spinnerPomodoroTime.setAdapter(pomodoroAdapter);
    }

    private void setupClickListeners() {
        // Back button
        if (binding.btnBack != null) {
            binding.btnBack.setOnClickListener(v -> finish());
        }

        // Save settings button
        binding.btnSaveSettings.setOnClickListener(v -> saveSettings());

        // Reset settings button
        binding.btnResetSettings.setOnClickListener(v -> showResetConfirmationDialog());

        // Export data button
        binding.btnExportData.setOnClickListener(v -> exportUserData());

        // Delete data button
        binding.btnDeleteData.setOnClickListener(v -> showDeleteDataDialog());

        // Edit profile button
        binding.btnEditProfile.setOnClickListener(v -> editProfile());

        // Change password button
        binding.btnChangePassword.setOnClickListener(v -> changePassword());

        // Logout button
        binding.btnLogout.setOnClickListener(v -> logout());
    }

    private void loadUserProfile() {
        coroutineScope.launch(Dispatchers.getIO(), (scope, continuation) -> {
            AuthResult<User> result = authRepository.getCurrentUser();

            runOnUiThread(() -> {
                if (result instanceof AuthResult.Success) {
                    currentUser = ((AuthResult.Success<User>) result).getData();
                    updateUserProfileUI(currentUser);
                } else if (result instanceof AuthResult.Error) {
                    String errorMsg = ((AuthResult.Error) result).getMessage();
                    Log.e(TAG, "Failed to load user profile: " + errorMsg);
                    // Use cached user data or default values
                }
            });

            return null;
        });
    }

    private void updateUserProfileUI(User user) {
        if (user != null) {
            binding.tvUserName.setText(user.getName());
            binding.tvUserEmail.setText(user.getEmail());

            // TODO: Load actual stats from API
            // For now using placeholder values
        }
    }

    private void loadSettings() {
        // First load from cache
        UserSettings cachedSettings = SettingsPreferences.INSTANCE.getSettings(this);
        if (cachedSettings != null) {
            currentSettings = cachedSettings;
            updateUIWithSettings(cachedSettings);
        }

        // Then fetch from server
        coroutineScope.launch(Dispatchers.getIO(), (scope, continuation) -> {
            SettingsResult<UserSettings> result = settingsRepository.getSettings();

            runOnUiThread(() -> {
                if (result instanceof SettingsResult.Success) {
                    currentSettings = ((SettingsResult.Success<UserSettings>) result).getData();
                    updateUIWithSettings(currentSettings);

                    // Save to cache
                    SettingsPreferences.INSTANCE.saveSettings(this, currentSettings);
                } else if (result instanceof SettingsResult.Error) {
                    String errorMsg = ((SettingsResult.Error) result).getMessage();
                    Log.e(TAG, "Failed to load settings: " + errorMsg);
                    Toast.makeText(this, "Failed to load settings: " + errorMsg, Toast.LENGTH_SHORT).show();
                }
            });

            return null;
        });
    }

    private void updateUIWithSettings(UserSettings settings) {
        if (settings == null) return;

        // Theme
        String theme = settings.getTheme();
        int themePosition = getThemePosition(theme);
        binding.spinnerTheme.setSelection(themePosition);

        // Language
        String language = settings.getLanguage();
        int languagePosition = getLanguagePosition(language);
        binding.spinnerLanguage.setSelection(languagePosition);

        // Pomodoro time
        int pomodoroDuration = settings.getPomodoroDuration();
        int pomodoroPosition = getPomodoroPosition(pomodoroDuration);
        binding.spinnerPomodoroTime.setSelection(pomodoroPosition);

        // Switches
        binding.switchPushNotifications.setChecked(settings.getPushNotifications());
        binding.switchDailyReminders.setChecked(settings.getDailyReminders());
        binding.switchGoalReminders.setChecked(settings.getGoalReminders());
        binding.switchBlockNotifications.setChecked(settings.getBlockNotifications());
        binding.switchBackgroundSound.setChecked(settings.getBackgroundSound());
    }

    private void saveSettings() {
        if (currentSettings == null) {
            Toast.makeText(this, "Settings not loaded yet", Toast.LENGTH_SHORT).show();
            return;
        }

        // Get values from UI
        String theme = getThemeFromPosition(binding.spinnerTheme.getSelectedItemPosition());
        String language = getLanguageFromPosition(binding.spinnerLanguage.getSelectedItemPosition());
        int pomodoroDuration = getPomodoroFromPosition(binding.spinnerPomodoroTime.getSelectedItemPosition());

        boolean pushNotifications = binding.switchPushNotifications.isChecked();
        boolean dailyReminders = binding.switchDailyReminders.isChecked();
        boolean goalReminders = binding.switchGoalReminders.isChecked();
        boolean blockNotifications = binding.switchBlockNotifications.isChecked();
        boolean backgroundSound = binding.switchBackgroundSound.isChecked();

        // Create request
        SettingsRequest request = new SettingsRequest(
            theme,
            null, // defaultFocusMinutes
            pomodoroDuration,
            currentSettings.getBreakMinutes(),
            currentSettings.getLongBreakMinutes(),
            currentSettings.getAutoStartBreak(),
            blockNotifications,
            backgroundSound,
            currentSettings.getDailyTargetTasks(),
            currentSettings.getNotificationEnabled(),
            pushNotifications,
            dailyReminders,
            goalReminders,
            currentSettings.getReminderTimes(),
            language,
            currentSettings.getTimezone()
        );

        // Show loading
        binding.btnSaveSettings.setEnabled(false);
        binding.btnSaveSettings.setText("Saving...");

        coroutineScope.launch(Dispatchers.getIO(), (scope, continuation) -> {
            SettingsResult<UserSettings> result = settingsRepository.updateSettings(request);

            runOnUiThread(() -> {
                binding.btnSaveSettings.setEnabled(true);
                binding.btnSaveSettings.setText(R.string.save_settings);

                if (result instanceof SettingsResult.Success) {
                    currentSettings = ((SettingsResult.Success<UserSettings>) result).getData();

                    // Save to cache
                    SettingsPreferences.INSTANCE.saveSettings(this, currentSettings);

                    Toast.makeText(this, "Settings saved successfully", Toast.LENGTH_SHORT).show();
                } else if (result instanceof SettingsResult.Error) {
                    String errorMsg = ((SettingsResult.Error) result).getMessage();
                    Toast.makeText(this, "Failed to save: " + errorMsg, Toast.LENGTH_SHORT).show();
                }
            });

            return null;
        });
    }

    private void showResetConfirmationDialog() {
        new AlertDialog.Builder(this)
            .setTitle("Reset Settings")
            .setMessage("Are you sure you want to reset all settings to default values?")
            .setPositiveButton("Reset", (dialog, which) -> resetSettings())
            .setNegativeButton("Cancel", null)
            .show();
    }

    private void resetSettings() {
        coroutineScope.launch(Dispatchers.getIO(), (scope, continuation) -> {
            SettingsResult<UserSettings> result = settingsRepository.resetSettings();

            runOnUiThread(() -> {
                if (result instanceof SettingsResult.Success) {
                    currentSettings = ((SettingsResult.Success<UserSettings>) result).getData();
                    updateUIWithSettings(currentSettings);

                    // Save to cache
                    SettingsPreferences.INSTANCE.saveSettings(this, currentSettings);

                    Toast.makeText(this, "Settings reset to default", Toast.LENGTH_SHORT).show();
                } else if (result instanceof SettingsResult.Error) {
                    String errorMsg = ((SettingsResult.Error) result).getMessage();
                    Toast.makeText(this, "Failed to reset: " + errorMsg, Toast.LENGTH_SHORT).show();
                }
            });

            return null;
        });
    }

    private void exportUserData() {
        Toast.makeText(this, "Export data feature coming soon", Toast.LENGTH_SHORT).show();
        // TODO: Implement data export
    }

    private void showDeleteDataDialog() {
        new AlertDialog.Builder(this)
            .setTitle("Delete Data")
            .setMessage("Are you sure you want to delete all your data? This action cannot be undone.")
            .setPositiveButton("Delete", (dialog, which) -> deleteUserData())
            .setNegativeButton("Cancel", null)
            .show();
    }

    private void deleteUserData() {
        Toast.makeText(this, "Delete data feature coming soon", Toast.LENGTH_SHORT).show();
        // TODO: Implement data deletion
    }

    private void editProfile() {
        Toast.makeText(this, "Edit profile feature coming soon", Toast.LENGTH_SHORT).show();
        // TODO: Navigate to edit profile screen
    }

    private void changePassword() {
        Toast.makeText(this, "Change password feature coming soon", Toast.LENGTH_SHORT).show();
        // TODO: Navigate to change password screen
    }

    private void logout() {
        new AlertDialog.Builder(this)
            .setTitle("Logout")
            .setMessage("Are you sure you want to logout?")
            .setPositiveButton("Logout", (dialog, which) -> performLogout())
            .setNegativeButton("Cancel", null)
            .show();
    }

    private void performLogout() {
        coroutineScope.launch(Dispatchers.getIO(), (scope, continuation) -> {
            authRepository.logout();

            runOnUiThread(() -> {
                // Clear settings cache
                SettingsPreferences.INSTANCE.clearSettings(this);

                // Navigate to login
                Intent intent = new Intent(this, LoginActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                startActivity(intent);
                finish();
            });

            return null;
        });
    }

    // Helper methods for spinner positions
    private int getThemePosition(String theme) {
        switch (theme) {
            case "light": return 0;
            case "dark": return 1;
            case "auto": return 2;
            default: return 2;
        }
    }

    private String getThemeFromPosition(int position) {
        switch (position) {
            case 0: return "light";
            case 1: return "dark";
            case 2: return "auto";
            default: return "auto";
        }
    }

    private int getLanguagePosition(String language) {
        switch (language) {
            case "vi": return 0;
            case "en": return 1;
            case "ja": return 2;
            default: return 0;
        }
    }

    private String getLanguageFromPosition(int position) {
        switch (position) {
            case 0: return "vi";
            case 1: return "en";
            case 2: return "ja";
            default: return "vi";
        }
    }

    private int getPomodoroPosition(int minutes) {
        switch (minutes) {
            case 15: return 0;
            case 25: return 1;
            case 30: return 2;
            case 45: return 3;
            case 60: return 4;
            default: return 1; // 25 minutes default
        }
    }

    private int getPomodoroFromPosition(int position) {
        switch (position) {
            case 0: return 15;
            case 1: return 25;
            case 2: return 30;
            case 3: return 45;
            case 4: return 60;
            default: return 25;
        }
    }

    private void setupBottomNavigation() {
        binding.bottomNavigation.setSelectedItemId(R.id.nav_settings);

        binding.bottomNavigation.setOnItemSelectedListener(item -> {
            int itemId = item.getItemId();

            if (itemId == R.id.nav_home) {
                startActivity(new Intent(this, MainActivity.class));
                finish();
                return true;
            } else if (itemId == R.id.nav_calendar) {
                startActivity(new Intent(this, CalendarActivity.class));
                finish();
                return true;
            } else if (itemId == R.id.nav_paths) {
                startActivity(new Intent(this, PathsActivity.class));
                finish();
                return true;
            } else if (itemId == R.id.nav_knowledge) {
                startActivity(new Intent(this, KnowledgeActivity.class));
                finish();
                return true;
            } else if (itemId == R.id.nav_settings) {
                // Current screen
                return true;
            }

            return false;
        });
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        coroutineScope.cancel();
    }
}
