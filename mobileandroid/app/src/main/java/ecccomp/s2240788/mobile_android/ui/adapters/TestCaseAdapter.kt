package ecccomp.s2240788.mobile_android.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.card.MaterialCardView
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.data.models.ExerciseTestCase

class TestCaseAdapter : ListAdapter<ExerciseTestCase, TestCaseAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_test_case, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position), position + 1)
    }

    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val cardTestCase: MaterialCardView = itemView.findViewById(R.id.card_test_case)
        private val tvTestNumber: TextView = itemView.findViewById(R.id.tv_test_number)
        private val tvDescription: TextView = itemView.findViewById(R.id.tv_description)
        private val tvInput: TextView = itemView.findViewById(R.id.tv_input)
        private val tvExpectedOutput: TextView = itemView.findViewById(R.id.tv_expected_output)

        fun bind(testCase: ExerciseTestCase, testNumber: Int) {
            tvTestNumber.text = "Test Case $testNumber"

            // Set description
            if (!testCase.description.isNullOrEmpty()) {
                tvDescription.text = testCase.description
                tvDescription.visibility = View.VISIBLE
            } else {
                tvDescription.visibility = View.GONE
            }

            // Set input
            tvInput.text = if (testCase.input.isEmpty()) {
                "(no input)"
            } else {
                testCase.input
            }

            // Set expected output
            tvExpectedOutput.text = testCase.expectedOutput
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<ExerciseTestCase>() {
        override fun areItemsTheSame(oldItem: ExerciseTestCase, newItem: ExerciseTestCase): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: ExerciseTestCase, newItem: ExerciseTestCase): Boolean {
            return oldItem == newItem
        }
    }
}
