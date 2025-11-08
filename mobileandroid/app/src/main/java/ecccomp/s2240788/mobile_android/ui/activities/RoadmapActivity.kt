package ecccomp.s2240788.mobile_android.ui.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.textfield.TextInputLayout
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.databinding.ActivityRoadmapBinding
import ecccomp.s2240788.mobile_android.data.models.GeneratedRoadmap
import ecccomp.s2240788.mobile_android.data.models.PopularRoadmap
import ecccomp.s2240788.mobile_android.ui.adapters.RoadmapAdapter
import ecccomp.s2240788.mobile_android.ui.viewmodels.RoadmapViewModel

/**
 * RoadmapActivity
 * Roadmap一覧画面 - 人気のロードマップとAI生成機能
 */
class RoadmapActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRoadmapBinding
    private val viewModel: RoadmapViewModel by viewModels()
    
    private lateinit var roadmapAdapter: RoadmapAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRoadmapBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupToolbar()
        setupRecyclerView()
        setupObservers()
        setupClickListeners()

        // Load popular roadmaps
        viewModel.loadPopularRoadmaps()
    }

    private fun setupToolbar() {
        binding.btnBack.setOnClickListener {
            finish()
        }
        
        binding.btnGenerate.setOnClickListener {
            showGenerateDialog()
        }
    }

    private fun setupRecyclerView() {
        roadmapAdapter = RoadmapAdapter(
            onRoadmapClick = { roadmap ->
                importRoadmap(roadmap)
            },
            onGenerateClick = {
                showGenerateDialog()
            }
        )
        binding.rvRoadmaps.apply {
            layoutManager = LinearLayoutManager(this@RoadmapActivity)
            adapter = roadmapAdapter
        }
    }

    private fun setupObservers() {
        // Popular roadmaps
        viewModel.popularRoadmaps.observe(this) { roadmaps ->
            roadmapAdapter.submitList(roadmaps)
            updateEmptyState(roadmaps.isEmpty())
        }

        // Loading state
        viewModel.isLoadingPopular.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }

        // Imported learning path ID - navigate to PathsActivity after import
        viewModel.importedLearningPathId.observe(this) { learningPathId ->
            learningPathId?.let {
                // Navigate back to PathsActivity after successful import
                val intent = Intent(this, PathsActivity::class.java).apply {
                    flags = Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_SINGLE_TOP
                }
                startActivity(intent)
                finish() // Close RoadmapActivity
                viewModel.resetImportedRoadmapData()
            }
        }

        // Error
        viewModel.error.observe(this) { error ->
            error?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearError()
            }
        }

        // Success message
        viewModel.successMessage.observe(this) { message ->
            message?.let {
                Toast.makeText(this, it, Toast.LENGTH_SHORT).show()
                viewModel.clearSuccessMessage()
            }
        }
    }

    private fun setupClickListeners() {
        binding.fabGenerate.setOnClickListener {
            showGenerateDialog()
        }
    }

    private fun importRoadmap(roadmap: PopularRoadmap) {
        viewModel.importPopularRoadmap(roadmap.id)
    }

    private fun showGenerateDialog() {
        val dialogView = layoutInflater.inflate(R.layout.dialog_generate_roadmap, null)
        val topicInputLayout = dialogView.findViewById<TextInputLayout>(R.id.til_topic)
        val topicInput = topicInputLayout?.editText
        val levelSpinner = dialogView.findViewById<android.widget.Spinner>(R.id.spinner_level)
        
        levelSpinner?.adapter = android.widget.ArrayAdapter(
            this,
            android.R.layout.simple_spinner_item,
            listOf("Beginner", "Intermediate", "Advanced")
        ).apply {
            setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        }

        var generatedObserver: Observer<GeneratedRoadmap?>? = null
        
        val dialog = android.app.AlertDialog.Builder(this)
            .setTitle("AIロードマップ生成")
            .setView(dialogView)
            .setPositiveButton("生成") { _, _ ->
                val topic = topicInput?.text?.toString() ?: ""
                val level = levelSpinner?.selectedItem?.toString()?.lowercase() ?: "beginner"
                
                if (topic.isNotBlank()) {
                    viewModel.generateRoadmap(topic, level)
                    
                    // Observe và auto-import sau khi generate thành công
                    generatedObserver = Observer<GeneratedRoadmap?> { generated ->
                        generated?.let {
                            viewModel.importAIGeneratedRoadmap(topic, level)
                            viewModel.resetGeneratedRoadmap()
                            generatedObserver?.let { observer ->
                                viewModel.generatedRoadmap.removeObserver(observer)
                            }
                        }
                    }
                    generatedObserver?.let { observer ->
                        viewModel.generatedRoadmap.observe(this, observer)
                    }
                } else {
                    Toast.makeText(this, "トピックを入力してください", Toast.LENGTH_SHORT).show()
                }
            }
            .setNegativeButton("キャンセル", null)
            .create()
        
        dialog.setOnDismissListener {
            generatedObserver?.let { observer ->
                viewModel.generatedRoadmap.removeObserver(observer)
            }
        }
        
        dialog.show()
    }

    private fun updateEmptyState(isEmpty: Boolean) {
        binding.tvEmptyState.visibility = if (isEmpty) View.VISIBLE else View.GONE
        binding.rvRoadmaps.visibility = if (isEmpty) View.GONE else View.VISIBLE
    }
}

