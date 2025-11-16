package ecccomp.s2240788.mobile_android.data.repository

import android.util.Log
import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.SettingsRequest
import ecccomp.s2240788.mobile_android.data.models.SettingsResponse
import ecccomp.s2240788.mobile_android.data.models.UserSettings
import ecccomp.s2240788.mobile_android.data.result.SettingsResult

/**
 * Settings Repository
 * Handles settings API calls and local caching
 */
class SettingsRepository(
    private val apiService: ApiService
) {
    companion object {
        private const val TAG = "SettingsRepository"
    }

    /**
     * Get user settings from server
     */
    suspend fun getSettings(): SettingsResult<UserSettings> {
        return try {
            val response = apiService.getSettings()

            if (response.isSuccessful) {
                val settingsResponse = response.body()
                if (settingsResponse != null && settingsResponse.success && settingsResponse.data != null) {
                    Log.d(TAG, "Settings retrieved successfully")
                    SettingsResult.Success(settingsResponse.data)
                } else {
                    val errorMsg = settingsResponse?.message ?: "Failed to get settings"
                    Log.e(TAG, "Settings retrieval failed: $errorMsg")
                    SettingsResult.Error(errorMsg)
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "Unauthorized. Please login again"
                    404 -> "Settings not found"
                    500 -> "Server error occurred"
                    else -> "Failed to get settings: ${response.message()}"
                }
                Log.e(TAG, "API error: $errorMessage")
                SettingsResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            Log.e(TAG, "Network error: ${e.message}", e)
            SettingsResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Update user settings
     */
    suspend fun updateSettings(request: SettingsRequest): SettingsResult<UserSettings> {
        return try {
            val response = apiService.updateSettings(request)

            if (response.isSuccessful) {
                val settingsResponse = response.body()
                if (settingsResponse != null && settingsResponse.success && settingsResponse.data != null) {
                    Log.d(TAG, "Settings updated successfully")
                    SettingsResult.Success(settingsResponse.data)
                } else {
                    val errorMsg = settingsResponse?.message ?: "Failed to update settings"
                    Log.e(TAG, "Settings update failed: $errorMsg")
                    SettingsResult.Error(errorMsg)
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "Unauthorized. Please login again"
                    422 -> "Invalid data. Please check your input"
                    500 -> "Server error occurred"
                    else -> "Failed to update settings: ${response.message()}"
                }
                Log.e(TAG, "API error: $errorMessage")
                SettingsResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            Log.e(TAG, "Network error: ${e.message}", e)
            SettingsResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Reset settings to default values
     */
    suspend fun resetSettings(): SettingsResult<UserSettings> {
        return try {
            val response = apiService.resetSettings()

            if (response.isSuccessful) {
                val settingsResponse = response.body()
                if (settingsResponse != null && settingsResponse.success && settingsResponse.data != null) {
                    Log.d(TAG, "Settings reset successfully")
                    SettingsResult.Success(settingsResponse.data)
                } else {
                    val errorMsg = settingsResponse?.message ?: "Failed to reset settings"
                    Log.e(TAG, "Settings reset failed: $errorMsg")
                    SettingsResult.Error(errorMsg)
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "Unauthorized. Please login again"
                    500 -> "Server error occurred"
                    else -> "Failed to reset settings: ${response.message()}"
                }
                Log.e(TAG, "API error: $errorMessage")
                SettingsResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            Log.e(TAG, "Network error: ${e.message}", e)
            SettingsResult.Error("Network error: ${e.message}")
        }
    }

    /**
     * Update a specific setting by key
     */
    suspend fun updateSetting(key: String, value: Any): SettingsResult<Pair<String, Any>> {
        return try {
            val valueMap = mapOf("value" to value)
            val response = apiService.updateSetting(key, valueMap)

            if (response.isSuccessful) {
                val settingsResponse = response.body()
                if (settingsResponse != null && settingsResponse.success) {
                    Log.d(TAG, "Setting $key updated successfully")
                    SettingsResult.Success(Pair(key, value))
                } else {
                    val errorMsg = settingsResponse?.message ?: "Failed to update setting"
                    Log.e(TAG, "Setting update failed: $errorMsg")
                    SettingsResult.Error(errorMsg)
                }
            } else {
                val errorMessage = when (response.code()) {
                    400 -> "Invalid setting key"
                    401 -> "Unauthorized. Please login again"
                    422 -> "Invalid value"
                    500 -> "Server error occurred"
                    else -> "Failed to update setting: ${response.message()}"
                }
                Log.e(TAG, "API error: $errorMessage")
                SettingsResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            Log.e(TAG, "Network error: ${e.message}", e)
            SettingsResult.Error("Network error: ${e.message}")
        }
    }
}
