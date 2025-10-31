package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.FrameLayout
import android.widget.LinearLayout
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.card.MaterialCardView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.ClassModel

/**
 * Adapter for Timetable Grid (Periods)
 */
class TimetableAdapter(
    private val timetableData: Map<String, ClassModel>,
    private val onCellClick: (day: Int, period: Int, classModel: ClassModel?) -> Unit
) : RecyclerView.Adapter<TimetableAdapter.PeriodViewHolder>() {

    private val periods = 5 // Number of periods
    private val days = 7 // Number of days (0-6)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PeriodViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_timetable_period, parent, false)
        return PeriodViewHolder(view)
    }

    override fun onBindViewHolder(holder: PeriodViewHolder, position: Int) {
        holder.bind(position + 1) // Period numbers start from 1
    }

    override fun getItemCount(): Int = periods

    inner class PeriodViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val periodNumber: TextView = itemView.findViewById(R.id.tv_period_number)
        private val dayCells = listOf(
            itemView.findViewById<FrameLayout>(R.id.cell_day_0),
            itemView.findViewById<FrameLayout>(R.id.cell_day_1),
            itemView.findViewById<FrameLayout>(R.id.cell_day_2),
            itemView.findViewById<FrameLayout>(R.id.cell_day_3),
            itemView.findViewById<FrameLayout>(R.id.cell_day_4),
            itemView.findViewById<FrameLayout>(R.id.cell_day_5),
            itemView.findViewById<FrameLayout>(R.id.cell_day_6)
        )

        fun bind(period: Int) {
            // Set period number
            periodNumber.text = period.toString()
            
            // Set up each day cell
            for (day in 0 until days) {
                val cell = dayCells[day]
                val classKey = "$day-$period"
                val classModel = timetableData[classKey]
                
                // Clear previous content
                cell.removeAllViews()
                
                if (classModel != null) {
                    // Add class block
                    val classBlock = createClassBlock(classModel)
                    cell.addView(classBlock)
                }
                
                // Set click listener
                cell.setOnClickListener {
                    onCellClick(day, period, classModel)
                }
            }
        }
        
        private fun createClassBlock(classModel: ClassModel): View {
            val inflater = LayoutInflater.from(itemView.context)
            val classBlock = inflater.inflate(R.layout.item_class_block, null, false)
            
            val card = classBlock.findViewById<MaterialCardView>(R.id.class_block)
            val className = classBlock.findViewById<TextView>(R.id.tv_class_name)
            val classRoom = classBlock.findViewById<TextView>(R.id.tv_class_room)
            
            // Set text
            className.text = classModel.name
            classRoom.text = classModel.room
            
            // Set background color
            card.setCardBackgroundColor(itemView.context.getColor(classModel.color.colorResId))
            
            // Set layout params to fill parent
            classBlock.layoutParams = FrameLayout.LayoutParams(
                FrameLayout.LayoutParams.MATCH_PARENT,
                FrameLayout.LayoutParams.MATCH_PARENT
            )
            
            return classBlock
        }
    }
}
