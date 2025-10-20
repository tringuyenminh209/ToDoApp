package ecccomp.s2240788.mobile_android.ui.fragments.onboarding

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import com.google.android.material.card.MaterialCardView
import com.google.android.material.switchmaterial.SwitchMaterial
import ecccomp.s2240788.mobile_android.R

/**
 * Step 3: Notification Settings Fragment
 * User decides whether to enable reminder notifications
 */
class OnboardingStep3Fragment : Fragment() {

    private var onDataChanged: ((Boolean) -> Unit)? = null
    private var notificationsEnabled: Boolean = false
    
    private lateinit var cardNotificationToggle: MaterialCardView
    private lateinit var switchNotification: SwitchMaterial

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.onboarding_step3_fragment, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        setupNotificationToggle(view)
    }

    private fun setupNotificationToggle(view: View) {
        cardNotificationToggle = view.findViewById(R.id.card_notification_toggle)
        switchNotification = view.findViewById(R.id.switch_notification)

        // Card click toggles switch
        cardNotificationToggle.setOnClickListener {
            switchNotification.isChecked = !switchNotification.isChecked
        }

        // Switch change listener
        switchNotification.setOnCheckedChangeListener { _, isChecked ->
            notificationsEnabled = isChecked
            onDataChanged?.invoke(isChecked)
        }
    }

    companion object {
        fun newInstance(onDataChanged: (Boolean) -> Unit): OnboardingStep3Fragment {
            return OnboardingStep3Fragment().apply {
                this.onDataChanged = onDataChanged
            }
        }
    }
}

