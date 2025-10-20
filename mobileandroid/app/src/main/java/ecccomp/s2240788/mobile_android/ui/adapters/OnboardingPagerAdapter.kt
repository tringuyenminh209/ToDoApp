package ecccomp.s2240788.mobile_android.ui.adapters

import androidx.fragment.app.Fragment
import androidx.fragment.app.FragmentActivity
import androidx.viewpager2.adapter.FragmentStateAdapter
import ecccomp.s2240788.mobile_android.ui.fragments.onboarding.OnboardingStep1Fragment
import ecccomp.s2240788.mobile_android.ui.fragments.onboarding.OnboardingStep2Fragment
import ecccomp.s2240788.mobile_android.ui.fragments.onboarding.OnboardingStep3Fragment

/**
 * ViewPager2 Adapter for Onboarding screens
 * Manages 3 steps: Goal Selection, Time Selection, Notifications
 */
class OnboardingPagerAdapter(
    fragmentActivity: FragmentActivity,
    private val onDataChanged: (step: Int, data: Any) -> Unit
) : FragmentStateAdapter(fragmentActivity) {

    override fun getItemCount(): Int = 3

    override fun createFragment(position: Int): Fragment {
        return when (position) {
            0 -> OnboardingStep1Fragment.newInstance { data ->
                onDataChanged(0, data)
            }
            1 -> OnboardingStep2Fragment.newInstance { data ->
                onDataChanged(1, data)
            }
            2 -> OnboardingStep3Fragment.newInstance { data ->
                onDataChanged(2, data)
            }
            else -> throw IllegalStateException("Invalid onboarding step: $position")
        }
    }
}

