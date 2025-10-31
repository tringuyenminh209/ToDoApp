package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.WindowCompat
import androidx.viewpager2.widget.ViewPager2
import com.google.android.material.button.MaterialButton
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.ui.adapters.OnboardingPagerAdapter

class onboardingActivity : BaseActivity() {
    
    // UI Components
    private lateinit var viewPager: ViewPager2
    private lateinit var tvProgress: TextView
    private lateinit var btnBack: MaterialButton
    private lateinit var btnNext: MaterialButton
    private lateinit var btnLanguage: MaterialButton
    
    // Adapter
    private lateinit var pagerAdapter: OnboardingPagerAdapter
    
    // State
    private var currentStep = 0
    private val totalSteps = 3
    private var selectedGoal: String? = null
    private var selectedTime: String? = null
    private var notificationsEnabled: Boolean = false
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Enable edge-to-edge display (full screen gradient)
        WindowCompat.setDecorFitsSystemWindows(window, false)
        
        setContentView(R.layout.activity_onboarding)
        
        initViews()
        setupViewPager()
        setupButtons()
        updateProgress()
    }
    
    private fun initViews() {
        viewPager = findViewById(R.id.view_pager)
        tvProgress = findViewById(R.id.tv_progress)
        btnBack = findViewById(R.id.btn_back)
        btnNext = findViewById(R.id.btn_next)
        btnLanguage = findViewById(R.id.btn_language)
    }
    
    private fun setupViewPager() {
        pagerAdapter = OnboardingPagerAdapter(this) { step, data ->
            handleStepData(step, data)
        }
        
        viewPager.adapter = pagerAdapter
        viewPager.isUserInputEnabled = false // Disable swipe
        
        viewPager.registerOnPageChangeCallback(object : ViewPager2.OnPageChangeCallback() {
            override fun onPageSelected(position: Int) {
                currentStep = position
                updateProgress()
                updateButtons()
            }
        })
    }
    
    private fun setupButtons() {
        // Back button
        btnBack.setOnClickListener {
            if (currentStep > 0) {
                viewPager.currentItem = currentStep - 1
            }
        }
        
        // Next button
        btnNext.setOnClickListener {
            if (currentStep < totalSteps - 1) {
                if (canProceed()) {
                    viewPager.currentItem = currentStep + 1
                }
            } else {
                completeOnboarding()
            }
        }
        
        // Language button
        btnLanguage.setOnClickListener {
            showLanguageDialog()
        }
    }
    
    private fun handleStepData(step: Int, data: Any) {
        when (step) {
            0 -> selectedGoal = data as? String
            1 -> selectedTime = data as? String
            2 -> notificationsEnabled = data as? Boolean ?: false
        }
        updateButtons()
    }
    
    private fun canProceed(): Boolean {
        return when (currentStep) {
            0 -> selectedGoal != null
            1 -> selectedTime != null
            2 -> true // Notifications are optional
            else -> false
        }
    }
    
    private fun updateProgress() {
        // Update progress text based on current step
        tvProgress.text = when (currentStep) {
            0 -> "ステップ 1/3"
            1 -> "ステップ 2/3"
            2 -> "ステップ 3/3"
            else -> "ステップ 1/3"
        }
    }
    
    private fun updateButtons() {
        // Button visibility and layout
        if (currentStep == 0) {
            // Step 1: Only Next button, full width
            btnBack.visibility = View.GONE
            val params = btnNext.layoutParams as android.widget.LinearLayout.LayoutParams
            params.weight = 1f
            params.marginStart = 0 // Remove left margin when Back button is hidden
            btnNext.layoutParams = params
        } else {
            // Other steps: Show both buttons
            btnBack.visibility = View.VISIBLE
            val paramsBack = btnBack.layoutParams as android.widget.LinearLayout.LayoutParams
            paramsBack.weight = 1f
            btnBack.layoutParams = paramsBack
            
            val paramsNext = btnNext.layoutParams as android.widget.LinearLayout.LayoutParams
            paramsNext.weight = 1f
            paramsNext.marginStart = resources.getDimensionPixelSize(ecccomp.s2240788.mobile_android.R.dimen.spacing_md) // Restore margin
            btnNext.layoutParams = paramsNext
        }
        
        // Next button state
        btnNext.isEnabled = canProceed()
        btnNext.text = if (currentStep == totalSteps - 1) {
            getString(R.string.btn_complete)
        } else {
            getString(R.string.btn_next)
        }
    }
    
    private fun showLanguageDialog() {
        val languages = arrayOf("Tiếng Việt", "English", "日本語")
        val languageCodes = arrayOf("vi", "en", "ja")
        
        MaterialAlertDialogBuilder(this)
            .setTitle(getString(R.string.app_name))
            .setItems(languages) { _, which ->
                updateLanguage(languageCodes[which])
            }
            .show()
    }
    
    private fun updateLanguage(languageCode: String) {
        // Update button text
        btnLanguage.text = languageCode.uppercase()
        
        // TODO: Implement locale change
        // This would require recreating the activity with new locale
        // For now, just update the button text
    }
    
    private fun completeOnboarding() {
        // Disable button to prevent double-click
        btnNext.isEnabled = false
        
        // Save onboarding data to SharedPreferences
        val prefs = getSharedPreferences("onboarding", MODE_PRIVATE)
        prefs.edit().apply {
            putBoolean("completed", true)
            putString("goal", selectedGoal)
            putString("time", selectedTime)
            putBoolean("notifications", notificationsEnabled)
            putLong("completed_at", System.currentTimeMillis())
            apply()
        }
        
        // Navigate to Login
        val intent = Intent(this, LoginActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        finish()
    }
}