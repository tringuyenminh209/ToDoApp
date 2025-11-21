<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->updateMilestoneProgress($task);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Only update if status changed or milestone changed
        if ($task->isDirty('status') || $task->isDirty('learning_milestone_id')) {
            $this->updateMilestoneProgress($task);

            // Also update old milestone if milestone_id changed
            if ($task->isDirty('learning_milestone_id')) {
                $oldMilestoneId = $task->getOriginal('learning_milestone_id');
                if ($oldMilestoneId) {
                    $oldMilestone = \App\Models\LearningMilestone::find($oldMilestoneId);
                    if ($oldMilestone) {
                        $oldMilestone->calculateProgress();
                        $oldMilestone->learningPath?->calculateProgress();
                    }
                }
            }
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->updateMilestoneProgress($task);
    }

    /**
     * Update milestone and learning path progress
     */
    private function updateMilestoneProgress(Task $task): void
    {
        if ($task->learning_milestone_id) {
            $milestone = $task->learningMilestone;
            if ($milestone) {
                Log::info("Updating milestone {$milestone->id} progress due to task {$task->id} change");

                // Update milestone progress
                $milestone->calculateProgress();

                // Update learning path progress
                if ($milestone->learningPath) {
                    $milestone->learningPath->calculateProgress();
                }
            }
        }
    }
}
