package ecccomp.s2240788.mobile_android.ui.activities

import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModelProvider
import ecccomp.s2240788.mobile_android.databinding.ActivityMainBinding
import ecccomp.s2240788.mobile_android.ui.viewmodels.MainViewModel

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var viewModel: MainViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupViewModel()
        setupClickListeners()
        setupObservers()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[MainViewModel::class.java]
    }

    private fun setupClickListeners() {
        binding.btnGetTasks.setOnClickListener {
            viewModel.getTasks()
        }
    }

    private fun setupObservers() {
        viewModel.isLoading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) android.view.View.VISIBLE else android.view.View.GONE
        }

        viewModel.error.observe(this) { error ->
            if (error != null) {
                Toast.makeText(this, error, Toast.LENGTH_LONG).show()
            }
        }

        viewModel.tasks.observe(this) { tasks ->
            if (tasks != null) {
                Toast.makeText(this, "Got ${tasks.size} tasks!", Toast.LENGTH_SHORT).show()
                // Sẽ implement RecyclerView sau
            }
        }
    }
}