package ecccomp.s2240788.mobile_android.ui.fragments.onboarding

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import com.google.android.material.card.MaterialCardView
import ecccomp.s2240788.mobile_android.R

/**
 * Step 1: Goal Selection Fragment
 * User selects their primary goal: Learning, Work, or Health
 */
class OnboardingStep1Fragment : Fragment() {

    private var onDataChanged: ((String) -> Unit)? = null
    private var selectedGoal: String? = null
    private val goalCards = mutableListOf<MaterialCardView>()

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.onboarding_step1_fragment, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        setupGoalCards(view)
    }

    private fun setupGoalCards(view: View) {
        val cardLearning = view.findViewById<MaterialCardView>(R.id.card_goal_learning)
        val cardWork = view.findViewById<MaterialCardView>(R.id.card_goal_work)
        val cardHealth = view.findViewById<MaterialCardView>(R.id.card_goal_health)

        goalCards.apply {
            add(cardLearning)
            add(cardWork)
            add(cardHealth)
        }

        cardLearning.setOnClickListener { selectGoal("learning", cardLearning) }
        cardWork.setOnClickListener { selectGoal("work", cardWork) }
        cardHealth.setOnClickListener { selectGoal("health", cardHealth) }
    }

    private fun selectGoal(goal: String, selectedCard: MaterialCardView) {
        selectedGoal = goal

        // Update card states
        goalCards.forEach { card ->
            if (card == selectedCard) {
                // Selected state
                card.strokeColor = requireContext().getColor(R.color.primary)
                card.setCardBackgroundColor(requireContext().getColor(R.color.primary_light))
            } else {
                // Normal state
                card.strokeColor = requireContext().getColor(R.color.line)
                card.setCardBackgroundColor(requireContext().getColor(R.color.white))
            }
        }

        // Notify parent activity
        onDataChanged?.invoke(goal)
    }

    companion object {
        fun newInstance(onDataChanged: (String) -> Unit): OnboardingStep1Fragment {
            return OnboardingStep1Fragment().apply {
                this.onDataChanged = onDataChanged
            }
        }
    }
}

