package ecccomp.s2240788.mobile_android.ui.activities;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.lifecycle.ViewModelProvider;
import androidx.recyclerview.widget.LinearLayoutManager;
import ecccomp.s2240788.mobile_android.databinding.ActivityAddTaskBinding;
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskInput;
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskInputAdapter;
import ecccomp.s2240788.mobile_android.ui.viewmodels.AddTaskViewModel;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;
import java.util.UUID;

/**
 * AddTaskActivity
 * 新規タスク作成画面
 */
public class AddTaskActivity extends BaseActivity {

    private ActivityAddTaskBinding binding;
    private AddTaskViewModel viewModel;
    private int selectedPriority = 3; // Default: medium (1-5)
    private String selectedEnergy = "medium";
    private String selectedDeadline = null;
    private Calendar calendar = Calendar.getInstance();
    private SubtaskInputAdapter subtaskAdapter;
    private List<SubtaskInput> subtasks = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityAddTaskBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        viewModel = new ViewModelProvider(this).get(AddTaskViewModel.class);

        setupSubtaskRecyclerView();
        setupClickListeners();
        setupObservers();

        // Set initial priority selection (medium is default)
        updatePrioritySelection();

        // Energy default (medium)
        selectedEnergy = "medium";
        if (binding.chipEnergyMedium != null) {
            binding.chipEnergyMedium.setChecked(true);
        }

        // Time unit spinner entries (分 / 時間)
        try {
            // Quick time buttons are now in the layout
            // Hours and minutes input fields (et_hours, et_minutes) replace spinner
        } catch (Exception ignored) {}
    }

    private void setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener(v -> finish());

        // Priority selection (1-5)
        binding.chipPriority1.setOnClickListener(v -> {
            selectedPriority = 1;
            updatePrioritySelection();
        });

        binding.chipPriority2.setOnClickListener(v -> {
            selectedPriority = 2;
            updatePrioritySelection();
        });

        binding.chipPriority3.setOnClickListener(v -> {
            selectedPriority = 3;
            updatePrioritySelection();
        });

        binding.chipPriority4.setOnClickListener(v -> {
            selectedPriority = 4;
            updatePrioritySelection();
        });

        binding.chipPriority5.setOnClickListener(v -> {
            selectedPriority = 5;
            updatePrioritySelection();
        });

        // Energy selection
        binding.chipEnergyHigh.setOnClickListener(v -> selectedEnergy = "high");
        binding.chipEnergyMedium.setOnClickListener(v -> selectedEnergy = "medium");
        binding.chipEnergyLow.setOnClickListener(v -> selectedEnergy = "low");

        // Deadline quick buttons
        binding.btnToday.setOnClickListener(v -> {
            setDeadlineToToday();
        });

        binding.btnTomorrow.setOnClickListener(v -> {
            setDeadlineToTomorrow();
        });

        binding.btnNextWeek.setOnClickListener(v -> {
            setDeadlineToNextWeek();
        });

        // Deadline input - show date picker
        binding.etDeadline.setOnClickListener(v -> {
            showDatePicker();
        });

        // Time quick select buttons
        binding.btnTime15.setOnClickListener(v -> {
            binding.etHours.setText("0");
            binding.etMinutes.setText("15");
        });

        binding.btnTime30.setOnClickListener(v -> {
            binding.etHours.setText("0");
            binding.etMinutes.setText("30");
        });

        binding.btnTime60.setOnClickListener(v -> {
            binding.etHours.setText("1");
            binding.etMinutes.setText("0");
        });

        binding.btnTime120.setOnClickListener(v -> {
            binding.etHours.setText("2");
            binding.etMinutes.setText("0");
        });

        // Add subtask button
        binding.btnAddSubtask.setOnClickListener(v -> {
            addNewSubtask();
        });

        // Save button
        binding.btnSave.setOnClickListener(v -> {
            if (validateInputs()) {
                createTask();
            }
        });
    }

    private void setupObservers() {
        // Loading state
        viewModel.isLoading().observe(this, isLoading -> {
            binding.btnSave.setEnabled(!isLoading);
        });

        // Error handling
        viewModel.getError().observe(this, error -> {
            if (error != null) {
                showError(error);
                viewModel.clearError();
            }
        });

        // Success
        viewModel.getTaskCreated().observe(this, success -> {
            if (success) {
                Toast.makeText(this, "タスクを作成しました！", Toast.LENGTH_SHORT).show();
                finish();
            }
        });
    }

    private void updatePrioritySelection() {
        // Reset all priority chips
        binding.chipPriority1.setChecked(false);
        binding.chipPriority2.setChecked(false);
        binding.chipPriority3.setChecked(false);
        binding.chipPriority4.setChecked(false);
        binding.chipPriority5.setChecked(false);

        // Set checked state based on current selection
        switch (selectedPriority) {
            case 1:
                binding.chipPriority1.setChecked(true);
                break;
            case 2:
                binding.chipPriority2.setChecked(true);
                break;
            case 3:
                binding.chipPriority3.setChecked(true);
                break;
            case 4:
                binding.chipPriority4.setChecked(true);
                break;
            case 5:
                binding.chipPriority5.setChecked(true);
                break;
        }
    }

    private boolean validateInputs() {
        String title = binding.etTaskTitle.getText().toString().trim();

        if (title.isEmpty()) {
            binding.tilTaskTitle.setError("タイトルは必須です");
            return false;
        }

        binding.tilTaskTitle.setError(null);
        return true;
    }

    private void createTask() {
        String title = binding.etTaskTitle.getText().toString().trim();
        String description = binding.etTaskDescription.getText().toString().trim();
        Integer estimated = null;
        try {
            String hoursStr = binding.etHours.getText() != null ? binding.etHours.getText().toString().trim() : "";
            String minsStr = binding.etMinutes.getText() != null ? binding.etMinutes.getText().toString().trim() : "";

            int hours = hoursStr.isEmpty() ? 0 : Integer.parseInt(hoursStr);
            int mins = minsStr.isEmpty() ? 0 : Integer.parseInt(minsStr);

            if (hours > 0 || mins > 0) {
                estimated = hours * 60 + mins;
            }
        } catch (Exception ignored) {}

        // Get subtasks from adapter
        List<SubtaskInput> currentSubtasks = getSubtasks();

        viewModel.createTask(
            title,
            description,
            selectedPriority,
            selectedDeadline,
            selectedEnergy,
            estimated,
            currentSubtasks
        );
    }

    private void showError(String message) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show();
    }

    private void setDeadlineToToday() {
        calendar = Calendar.getInstance();
        updateDeadlineDisplay();
    }

    private void setDeadlineToTomorrow() {
        calendar = Calendar.getInstance();
        calendar.add(Calendar.DAY_OF_MONTH, 1);
        updateDeadlineDisplay();
    }

    private void setDeadlineToNextWeek() {
        calendar = Calendar.getInstance();
        calendar.add(Calendar.DAY_OF_MONTH, 7);
        updateDeadlineDisplay();
    }

    private void showDatePicker() {
        DatePickerDialog datePickerDialog = new DatePickerDialog(
            this,
            (view, year, month, dayOfMonth) -> {
                calendar.set(Calendar.YEAR, year);
                calendar.set(Calendar.MONTH, month);
                calendar.set(Calendar.DAY_OF_MONTH, dayOfMonth);
                updateDeadlineDisplay();
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        );
        datePickerDialog.show();
    }

    private void updateDeadlineDisplay() {
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy/MM/dd", Locale.getDefault());
        String dateString = sdf.format(calendar.getTime());
        binding.etDeadline.setText(dateString);

        // Format for API (yyyy-MM-dd)
        SimpleDateFormat apiFormat = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
        selectedDeadline = apiFormat.format(calendar.getTime());
    }

    private void setupSubtaskRecyclerView() {
        subtaskAdapter = new SubtaskInputAdapter(subtask -> {
            removeSubtask(subtask);
            return null;
        });
        binding.rvSubtasks.setLayoutManager(new LinearLayoutManager(this));
        binding.rvSubtasks.setAdapter(subtaskAdapter);

        // Show/hide empty state
        updateEmptyState();
    }

    private void addNewSubtask() {
        SubtaskInput newSubtask = new SubtaskInput(
            UUID.randomUUID().toString(),
            "",
            null
        );
        subtasks.add(newSubtask);
        subtaskAdapter.submitList(new ArrayList<>(subtasks));
        updateEmptyState();

        // Scroll to new subtask
        binding.rvSubtasks.post(() -> {
            binding.rvSubtasks.smoothScrollToPosition(subtasks.size() - 1);
        });
    }

    private void removeSubtask(SubtaskInput subtask) {
        subtasks.remove(subtask);
        subtaskAdapter.submitList(new ArrayList<>(subtasks));
        updateEmptyState();
        Toast.makeText(this, "サブタスクを削除しました", Toast.LENGTH_SHORT).show();
    }

    private void updateEmptyState() {
        if (subtasks.isEmpty()) {
            binding.emptySubtasks.setVisibility(View.VISIBLE);
            binding.rvSubtasks.setVisibility(View.GONE);
        } else {
            binding.emptySubtasks.setVisibility(View.GONE);
            binding.rvSubtasks.setVisibility(View.VISIBLE);
        }
    }

    public List<SubtaskInput> getSubtasks() {
        return subtaskAdapter.getSubtasks();
    }
}
