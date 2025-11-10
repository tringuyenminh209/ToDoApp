package ecccomp.s2240788.mobile_android.ui.activities;

import android.app.DatePickerDialog;
import android.app.TimePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;
import androidx.lifecycle.ViewModelProvider;
import androidx.recyclerview.widget.LinearLayoutManager;
import ecccomp.s2240788.mobile_android.R;
import ecccomp.s2240788.mobile_android.data.models.ContextSwitch;
import ecccomp.s2240788.mobile_android.data.models.ContextSwitchResponse;
import ecccomp.s2240788.mobile_android.data.models.SaveEnvironmentCheckRequest;
import ecccomp.s2240788.mobile_android.databinding.ActivityAddTaskBinding;
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskInput;
import ecccomp.s2240788.mobile_android.ui.adapters.SubtaskInputAdapter;
import ecccomp.s2240788.mobile_android.ui.dialogs.ContextSwitchWarningDialog;
import ecccomp.s2240788.mobile_android.ui.dialogs.EnvironmentChecklistDialog;
import ecccomp.s2240788.mobile_android.ui.dialogs.ComplexitySelectorDialog;
import ecccomp.s2240788.mobile_android.ui.dialogs.SubtaskPreviewDialog;
import ecccomp.s2240788.mobile_android.ui.viewmodels.AddTaskViewModel;
import ecccomp.s2240788.mobile_android.ui.activities.FocusSessionActivity;
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
    private String selectedCategory = "study"; // Default: study
    private String selectedDeadline = null;
    private String selectedScheduledTime = null;
    private Calendar calendar = Calendar.getInstance();
    private Calendar scheduledCalendar = Calendar.getInstance();
    private SubtaskInputAdapter subtaskAdapter;
    private final List<SubtaskInput> subtasks = new ArrayList<>();

    // Deep Work Mode fields
    private boolean requiresDeepFocus = false;
    private boolean allowInterruptions = true;
    private int focusDifficulty = 3; // Default: medium (1-5)
    private Integer warmupMinutes = null;
    private Integer cooldownMinutes = null;

    // Focus Enhancement
    private boolean shouldStartImmediately = false;
    private Integer createdTaskId = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityAddTaskBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        viewModel = new ViewModelProvider(this).get(AddTaskViewModel.class);

        setupSubtaskRecyclerView();
        setupClickListeners();
        setupObservers();
        setupDeepWorkMode();
        
        // Check for context switch when opening AddTask
        // Note: We'll check after task is created, not on open

        // Set initial priority selection (medium is default)
        updatePrioritySelection();

        // Energy default (medium)
        selectedEnergy = "medium";
        if (binding.chipEnergyMedium != null) {
            binding.chipEnergyMedium.setChecked(true);
        }

        // Category default (study)
        selectedCategory = "study";
        if (binding.chipTypeStudy != null) {
            binding.chipTypeStudy.setChecked(true);
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

        // Category/Type selection
        binding.chipTypeStudy.setOnClickListener(v -> selectedCategory = "study");
        binding.chipTypeWork.setOnClickListener(v -> selectedCategory = "work");

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

        // Scheduled Time quick buttons
        binding.btnMorning.setOnClickListener(v -> {
            setScheduledTime(9, 0);
        });

        binding.btnAfternoon.setOnClickListener(v -> {
            setScheduledTime(13, 0);
        });

        binding.btnEvening.setOnClickListener(v -> {
            setScheduledTime(18, 0);
        });

        // Scheduled Time input - show time picker
        binding.etScheduledTime.setOnClickListener(v -> {
            showTimePicker();
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

        // AI Breakdown button
        if (binding.btnAiBreakdown != null) {
            binding.btnAiBreakdown.setOnClickListener(v -> {
                handleAiBreakdown();
            });
        }

        // Save button
        binding.btnSave.setOnClickListener(v -> {
            if (validateInputs()) {
                createTask(false);
            }
        });

        // Quick Start button (create and start immediately)
        if (binding.btnCreateAndStart != null) {
            binding.btnCreateAndStart.setOnClickListener(v -> {
                if (validateInputs()) {
                    createTask(true);
                }
            });
        }
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

        // Success - handle task creation
        viewModel.getTaskCreated().observe(this, success -> {
            if (success) {
                Integer taskId = viewModel.getCreatedTaskId().getValue();
                if (taskId != null) {
                    createdTaskId = taskId;
                    
                    if (shouldStartImmediately) {
                        // Show environment checklist before starting
                        showEnvironmentChecklist(taskId);
                    } else {
                        Toast.makeText(this, "タスクを作成しました！", Toast.LENGTH_SHORT).show();
                        finish();
                    }
                } else {
                    Toast.makeText(this, "タスクを作成しました！", Toast.LENGTH_SHORT).show();
                    finish();
                }
            }
        });

        // AI Breakdown success - show preview dialog
        viewModel.getBreakdownSuccess().observe(this, task -> {
            if (task != null && task.getSubtasks() != null && !task.getSubtasks().isEmpty()) {
                // Show preview dialog instead of auto-applying
                showSubtaskPreviewDialog(task.getSubtasks());
            }
        });

        // AI Breakdown loading state
        viewModel.isBreakingDown().observe(this, isBreakingDown -> {
            if (binding.btnAiBreakdown != null) {
                binding.btnAiBreakdown.setEnabled(!isBreakingDown);
                binding.btnAiBreakdown.setText(isBreakingDown ? "AI分割中..." : getString(R.string.ai_breakdown_task));
            }
        });

        // Environment check saved
        viewModel.getEnvironmentCheckSaved().observe(this, saved -> {
            if (saved != null && saved && createdTaskId != null) {
                // Check for context switch after environment check is saved
                checkContextSwitchAndStart(createdTaskId);
            }
        });

        // Context switch response
        viewModel.getContextSwitchResponse().observe(this, switchResponse -> {
            if (switchResponse != null && createdTaskId != null) {
                if (switchResponse.getShould_warn() && switchResponse.getWarning_message() != null) {
                    showContextSwitchWarning(switchResponse, createdTaskId);
                } else {
                    startFocusSession(createdTaskId);
                }
            } else if (createdTaskId != null) {
                // No significant switch, start directly
                startFocusSession(createdTaskId);
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

    private void createTask(boolean startImmediately) {
        shouldStartImmediately = startImmediately;
        
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

        // Get warmup/cooldown times
        try {
            if (binding.etWarmup != null && binding.etWarmup.getText() != null) {
                String warmupStr = binding.etWarmup.getText().toString().trim();
                warmupMinutes = warmupStr.isEmpty() ? null : Integer.parseInt(warmupStr);
            }
            if (binding.etCooldown != null && binding.etCooldown.getText() != null) {
                String cooldownStr = binding.etCooldown.getText().toString().trim();
                cooldownMinutes = cooldownStr.isEmpty() ? null : Integer.parseInt(cooldownStr);
            }
        } catch (Exception ignored) {}

        // Get subtasks from adapter
        List<SubtaskInput> currentSubtasks = getSubtasks();

        viewModel.createTask(
            title,
            description,
            selectedPriority,
            selectedDeadline,
            selectedScheduledTime,
            selectedEnergy,
            estimated,
            selectedCategory,
            currentSubtasks,
            requiresDeepFocus,
            allowInterruptions,
            focusDifficulty,
            warmupMinutes,
            cooldownMinutes,
            startImmediately
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

    private void showTimePicker() {
        int hour = scheduledCalendar.get(Calendar.HOUR_OF_DAY);
        int minute = scheduledCalendar.get(Calendar.MINUTE);

        TimePickerDialog timePickerDialog = new TimePickerDialog(
            this,
            (view, hourOfDay, minuteOfHour) -> {
                scheduledCalendar.set(Calendar.HOUR_OF_DAY, hourOfDay);
                scheduledCalendar.set(Calendar.MINUTE, minuteOfHour);
                updateScheduledTimeDisplay();
            },
            hour,
            minute,
            true // 24-hour format
        );
        timePickerDialog.show();
    }

    private void setScheduledTime(int hour, int minute) {
        scheduledCalendar = Calendar.getInstance();
        scheduledCalendar.set(Calendar.HOUR_OF_DAY, hour);
        scheduledCalendar.set(Calendar.MINUTE, minute);
        updateScheduledTimeDisplay();
    }

    private void updateScheduledTimeDisplay() {
        SimpleDateFormat sdf = new SimpleDateFormat("HH:mm", Locale.getDefault());
        String timeString = sdf.format(scheduledCalendar.getTime());
        binding.etScheduledTime.setText(timeString);

        // Format for API (yyyy-MM-dd HH:mm:ss)
        SimpleDateFormat apiFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
        selectedScheduledTime = apiFormat.format(scheduledCalendar.getTime());
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

    private void setupDeepWorkMode() {
        // Deep Work Mode toggle
        if (binding.switchDeepWork != null) {
            binding.switchDeepWork.setOnCheckedChangeListener((buttonView, isChecked) -> {
                requiresDeepFocus = isChecked;
                allowInterruptions = !isChecked; // Inverse logic
            });
        }

        // Focus Difficulty slider (1-5)
        if (binding.sliderFocusDifficulty != null) {
            binding.sliderFocusDifficulty.addOnChangeListener((slider, value, fromUser) -> {
                focusDifficulty = (int) value;
            });

            // Set default value
            binding.sliderFocusDifficulty.setValue(3f);
        }

        // Auto-adjust settings when deep work is enabled
        if (binding.switchDeepWork != null) {
            binding.switchDeepWork.setOnCheckedChangeListener((buttonView, isChecked) -> {
                if (isChecked) {
                    // Auto-set focus difficulty to 4 when deep work enabled
                    if (binding.sliderFocusDifficulty != null) {
                        binding.sliderFocusDifficulty.setValue(4f);
                        focusDifficulty = 4;
                    }
                    // Suggest warmup/cooldown times
                    if (binding.etWarmup != null && (binding.etWarmup.getText() == null || binding.etWarmup.getText().toString().trim().isEmpty())) {
                        binding.etWarmup.setText("5");
                        warmupMinutes = 5;
                    }
                    if (binding.etCooldown != null && (binding.etCooldown.getText() == null || binding.etCooldown.getText().toString().trim().isEmpty())) {
                        binding.etCooldown.setText("10");
                        cooldownMinutes = 10;
                    }
                }
            });
        }
    }

    /**
     * Show environment checklist dialog
     */
    private void showEnvironmentChecklist(int taskId) {
        EnvironmentChecklistDialog dialog = EnvironmentChecklistDialog.newInstance(taskId);
        dialog.setOnStartSessionListener(environmentData -> {
            // Save environment check via API
            saveEnvironmentCheck(environmentData);
            return null;
        });
        dialog.show(getSupportFragmentManager(), "environment_checklist");
    }

    /**
     * Save environment check and start focus session
     */
    private void saveEnvironmentCheck(SaveEnvironmentCheckRequest environmentData) {
        viewModel.saveEnvironmentCheck(environmentData);
    }

    /**
     * Check for context switch and start focus session
     */
    private void checkContextSwitchAndStart(int taskId) {
        viewModel.checkContextSwitch(taskId, null);
    }

    /**
     * Show context switch warning dialog
     */
    private void showContextSwitchWarning(ContextSwitchResponse switchResponse, int taskId) {
        ContextSwitch contextSwitch = switchResponse.getContext_switch();
        String fromTask = contextSwitch.getFrom_category() != null 
            ? contextSwitch.getFrom_category() 
            : "前のタスク";
        String toTask = binding.etTaskTitle.getText().toString().trim();
        int cost = contextSwitch.getEstimated_cost_minutes();
        String tips = switchResponse.getWarning_message() != null 
            ? switchResponse.getWarning_message() 
            : "異なるタスクカテゴリへの切り替えは集中力を低下させる可能性があります。";

        ContextSwitchWarningDialog dialog = ContextSwitchWarningDialog.newInstance(
            fromTask, toTask, cost, tips, contextSwitch
        );
        
        dialog.setOnProceedListener(() -> {
            // Confirm context switch
            if (contextSwitch != null) {
                viewModel.confirmContextSwitch(contextSwitch.getId(), "User proceeded with context switch");
            }
            startFocusSession(taskId);
            return null;
        });

        dialog.setOnBatchTasksListener(() -> {
            // TODO: Navigate to task batching screen
            Toast.makeText(this, "タスクのバッチ処理機能は今後実装予定です", Toast.LENGTH_SHORT).show();
            finish();
            return null;
        });

        dialog.setOnCancelListener(() -> {
            // User cancelled, just finish
            finish();
            return null;
        });
        
        dialog.show(getSupportFragmentManager(), "context_switch_warning");
    }


    /**
     * Start focus session
     */
    private void startFocusSession(int taskId) {
        Intent intent = new Intent(this, FocusSessionActivity.class);
        intent.putExtra("task_id", taskId);
        intent.putExtra("task_title", binding.etTaskTitle.getText().toString().trim());
        startActivity(intent);
        finish();
    }

    /**
     * Handle AI Breakdown button click
     */
    private void handleAiBreakdown() {
        String title = binding.etTaskTitle.getText().toString().trim();
        
        if (title.isEmpty()) {
            Toast.makeText(this, "まずタスクのタイトルを入力してください", Toast.LENGTH_SHORT).show();
            return;
        }

        // Check if task already exists (was created)
        Integer taskId = viewModel.getCreatedTaskId().getValue();
        
        if (taskId == null) {
            // Task chưa được tạo, cần tạo task trước
            // Show dialog để confirm
            new androidx.appcompat.app.AlertDialog.Builder(this)
                .setTitle("AIでタスクを分割")
                .setMessage("タスクを先に作成してからAIで分割しますか？")
                .setPositiveButton("作成して分割", (dialog, which) -> {
                    // Create task first, then show complexity selector
                    if (validateInputs()) {
                        shouldStartImmediately = false;
                        createTask(false);
                        
                        // Observe task creation và show complexity selector
                        viewModel.getCreatedTaskId().observe(this, createdId -> {
                            if (createdId != null) {
                                // Show complexity selector
                                showComplexitySelectorDialog(createdId);
                            }
                        });
                    }
                })
                .setNegativeButton("キャンセル", null)
                .show();
        } else {
            // Task đã tồn tại, show complexity selector
            showComplexitySelectorDialog(taskId);
        }
    }

    /**
     * Show complexity selector dialog
     */
    private void showComplexitySelectorDialog(int taskId) {
        ComplexitySelectorDialog dialog = ComplexitySelectorDialog.newInstance();
        dialog.setOnComplexitySelectedListener(complexityLevel -> {
            // Call breakdown with selected complexity
            viewModel.breakdownTask(taskId, complexityLevel);
            return null;
        });
        dialog.show(getSupportFragmentManager(), "complexity_selector");
    }

    /**
     * Show subtask preview dialog
     */
    private void showSubtaskPreviewDialog(List<ecccomp.s2240788.mobile_android.data.models.Subtask> subtasks) {
        SubtaskPreviewDialog dialog = SubtaskPreviewDialog.newInstance(subtasks);
        dialog.setOnApplyListener(() -> {
            // Apply subtasks to UI
            applyBreakdownSubtasks();
            return null;
        });
        dialog.setOnCancelListener(() -> {
            // User cancelled, do nothing
            return null;
        });
        dialog.show(getSupportFragmentManager(), "subtask_preview");
    }

    /**
     * Apply breakdown subtasks to UI
     */
    private void applyBreakdownSubtasks() {
        ecccomp.s2240788.mobile_android.data.models.Task task = viewModel.getPendingBreakdownTask();
        if (task != null && task.getSubtasks() != null) {
            // Clear existing subtasks
            subtasks.clear();

            // Add AI-generated subtasks
            for (ecccomp.s2240788.mobile_android.data.models.Subtask subtask : task.getSubtasks()) {
                SubtaskInput subtaskInput = new SubtaskInput(
                    String.valueOf(subtask.getId()),
                    subtask.getTitle(),
                    subtask.getEstimated_minutes()
                );
                subtasks.add(subtaskInput);
            }

            subtaskAdapter.submitList(new ArrayList<>(subtasks));
            updateEmptyState();

            Toast.makeText(this, "AIで" + task.getSubtasks().size() + "個のサブタスクを適用しました！", Toast.LENGTH_SHORT).show();
        }
    }
}
