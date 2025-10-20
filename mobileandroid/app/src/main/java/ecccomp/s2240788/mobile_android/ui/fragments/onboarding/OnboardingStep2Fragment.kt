package ecccomp.s2240788.mobile_android.ui.fragments.onboarding

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import com.google.android.material.card.MaterialCardView
import ecccomp.s2240788.mobile_android.R

/**
 * Step 2: Time Selection Fragment
 * User selects their preferred working time slot
 */
class OnboardingStep2Fragment : Fragment() {

    private var onDataChanged: ((String) -> Unit)? = null
    private var selectedTime: String? = null
    private val timeCards = mutableListOf<MaterialCardView>()

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.onboarding_step2_fragment, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        setupTimeCards(view)
    }

    private fun setupTimeCards(view: View) {
        val cardMorning = view.findViewById<MaterialCardView>(R.id.card_time_morning)
        val cardMorningLate = view.findViewById<MaterialCardView>(R.id.card_time_morning_late)
        val cardAfternoon = view.findViewById<MaterialCardView>(R.id.card_time_afternoon)
        val cardEvening = view.findViewById<MaterialCardView>(R.id.card_time_evening)

        timeCards.apply {
            add(cardMorning)
            add(cardMorningLate)
            add(cardAfternoon)
            add(cardEvening)
        }

        cardMorning.setOnClickListener { selectTime("morning", cardMorning) }
        cardMorningLate.setOnClickListener { selectTime("morning-late", cardMorningLate) }
        cardAfternoon.setOnClickListener { selectTime("afternoon", cardAfternoon) }
        cardEvening.setOnClickListener { selectTime("evening", cardEvening) }
    }

    private fun selectTime(time: String, selectedCard: MaterialCardView) {
        selectedTime = time

        // Update card states
        timeCards.forEach { card ->
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
        onDataChanged?.invoke(time)
    }

    companion object {
        fun newInstance(onDataChanged: (String) -> Unit): OnboardingStep2Fragment {
            return OnboardingStep2Fragment().apply {
                this.onDataChanged = onDataChanged
            }
        }
    }
}

