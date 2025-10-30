package ecccomp.s2240788.mobile_android.ui.activities;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.lifecycle.ViewModelProvider;
import ecccomp.s2240788.mobile_android.databinding.ActivityAddTaskBinding;
import ecccomp.s2240788.mobile_android.ui.viewmodels.AddTaskViewModel;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Locale;

/**
 * AddTaskActivity
 * 新規タスク作成画面
 */
public class AddTaskActivity extends AppCompatActivity {

    private ActivityAddTaskBinding binding;
    private AddTaskViewModel viewModel;
    private String selectedPriority = "medium";
    private String selectedEnergy = "medium";
    private String selectedDeadline = null;
    private Calendar calendar = Calendar.getInstance();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityAddTaskBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        viewModel = new ViewModelProvider(this).get(AddTaskViewModel.class);

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
            android.widget.ArrayAdapter<String> unitAdapter = new android.widget.ArrayAdapter<>(
                this,
                android.R.layout.simple_spinner_item,
                new String[]{"分", "時間"}
            );
            unitAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            binding.spinnerTimeUnit.setAdapter(unitAdapter);
            binding.spinnerTimeUnit.setSelection(0);
        } catch (Exception ignored) {}
    }

    private void setupClickListeners() {
        // Back button
        binding.btnBack.setOnClickListener(v -> finish());

        // Priority selection
        binding.chipPriorityHigh.setOnClickListener(v -> {
            selectedPriority = "high";
            updatePrioritySelection();
        });

        binding.chipPriorityMedium.setOnClickListener(v -> {
            selectedPriority = "medium";
            updatePrioritySelection();
        });

        binding.chipPriorityLow.setOnClickListener(v -> {
            selectedPriority = "low";
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

        // Add subtask button
        binding.btnAddSubtask.setOnClickListener(v -> {
            Toast.makeText(this, "サブタスク機能は開発中です", Toast.LENGTH_SHORT).show();
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
        binding.chipPriorityHigh.setChecked(false);
        binding.chipPriorityMedium.setChecked(false);
        binding.chipPriorityLow.setChecked(false);

        // Set checked state based on current selection
        switch (selectedPriority) {
            case "high":
                binding.chipPriorityHigh.setChecked(true);
                break;
            case "medium":
                binding.chipPriorityMedium.setChecked(true);
                break;
            case "low":
                binding.chipPriorityLow.setChecked(true);
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
            String val = binding.etEstimatedTime.getText() != null ? binding.etEstimatedTime.getText().toString().trim() : "";
            if (!val.isEmpty()) {
                int base = Integer.parseInt(val);
                // Convert hours to minutes if spinner is set to 時間
                int unitIndex = binding.spinnerTimeUnit.getSelectedItemPosition();
                estimated = (unitIndex == 1) ? base * 60 : base;
            }
        } catch (Exception ignored) {}

        viewModel.createTask(title, description, selectedPriority, selectedDeadline, selectedEnergy, estimated);
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
}
