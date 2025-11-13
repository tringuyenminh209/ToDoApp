package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.cardview.widget.CardView
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.DayItem

/**
 * Adapter for day selection in schedule setup
 * 曜日選択アダプター
 */
class DaySelectionAdapter(
    private val days: List<DayItem>,
    private val onDayClick: (DayItem) -> Unit
) : RecyclerView.Adapter<DaySelectionAdapter.DayViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): DayViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_day_selection, parent, false)
        return DayViewHolder(view)
    }

    override fun onBindViewHolder(holder: DayViewHolder, position: Int) {
        holder.bind(days[position])
    }

    override fun getItemCount(): Int = days.size

    inner class DayViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val card: CardView = itemView.findViewById(R.id.card_day)
        private val tvDayShort: TextView = itemView.findViewById(R.id.tv_day_short)
        private val tvDayName: TextView = itemView.findViewById(R.id.tv_day_name)

        fun bind(day: DayItem) {
            tvDayShort.text = day.dayShort
            tvDayName.text = day.dayName

            updateSelection(day.isSelected)

            card.setOnClickListener {
                day.isSelected = !day.isSelected
                updateSelection(day.isSelected)
                onDayClick(day)
            }
        }

        private fun updateSelection(isSelected: Boolean) {
            val context = itemView.context
            if (isSelected) {
                card.setCardBackgroundColor(ContextCompat.getColor(context, R.color.primary))
                tvDayShort.setTextColor(ContextCompat.getColor(context, R.color.white))
                tvDayName.setTextColor(ContextCompat.getColor(context, R.color.white))
            } else {
                card.setCardBackgroundColor(ContextCompat.getColor(context, R.color.white))
                tvDayShort.setTextColor(ContextCompat.getColor(context, R.color.text_primary))
                tvDayName.setTextColor(ContextCompat.getColor(context, R.color.text_muted))
            }
        }
    }
}
