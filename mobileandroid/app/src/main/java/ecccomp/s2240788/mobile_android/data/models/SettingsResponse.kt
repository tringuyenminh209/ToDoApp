package ecccomp.s2240788.mobile_android.data.models

data class SettingsResponse(
    val success: Boolean,
    val message: String,
    val data: UserSettings? = null,
    val error: String? = null,
    val errors: Map<String, List<String>>? = null
)
