<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAbandonment;
use App\Models\FocusSession;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskAbandonmentService
{
    /**
     * Inactivity threshold in minutes before considering a task abandoned
     */
    const INACTIVITY_THRESHOLD_MINUTES = 15;

    /**
     * Update task's last_active_at timestamp (heartbeat)
     */
    public function updateTaskHeartbeat(int $taskId, int $userId): bool
    {
        try {
            $task = Task::where('id', $taskId)
                ->where('user_id', $userId)
                ->first();

            if (!$task) {
                return false;
            }

            // Update last_active_at
            $task->update([
                'last_active_at' => now(),
                'last_focus_at' => now(),
            ]);

            // If task was previously marked as abandoned, clear the flag
            if ($task->is_abandoned) {
                $task->update(['is_abandoned' => false]);

                // Mark latest abandonment as resumed
                $latestAbandonment = $task->abandonments()
                    ->where('resumed', false)
                    ->latest()
                    ->first();

                if ($latestAbandonment) {
                    $latestAbandonment->markAsResumed();
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update task heartbeat', [
                'task_id' => $taskId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check for abandoned tasks and mark them
     * This should be run periodically (e.g., every 5 minutes via scheduled job)
     */
    public function checkAbandonedTasks(): array
    {
        $abandonedTasks = [];

        try {
            // Find tasks that are:
            // 1. Status = in_progress
            // 2. last_active_at is older than threshold
            // 3. Not already marked as abandoned
            $threshold = now()->subMinutes(self::INACTIVITY_THRESHOLD_MINUTES);

            $tasks = Task::where('status', 'in_progress')
                ->where('is_abandoned', false)
                ->whereNotNull('last_active_at')
                ->where('last_active_at', '<', $threshold)
                ->with(['user', 'focusSessions' => function($query) {
                    $query->where('status', 'active')->latest();
                }])
                ->get();

            foreach ($tasks as $task) {
                $abandoned = $this->markTaskAsAbandoned($task);
                if ($abandoned) {
                    $abandonedTasks[] = [
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'user_id' => $task->user_id,
                        'last_active_at' => $task->last_active_at,
                    ];
                }
            }

            Log::info('Checked for abandoned tasks', [
                'total_checked' => $tasks->count(),
                'abandoned_count' => count($abandonedTasks),
            ]);

            return $abandonedTasks;
        } catch (\Exception $e) {
            Log::error('Failed to check abandoned tasks', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Mark a specific task as abandoned
     */
    public function markTaskAsAbandoned(Task $task, ?string $type = 'long_inactivity', ?string $reason = null, bool $autoDetected = true): ?TaskAbandonment
    {
        try {
            DB::beginTransaction();

            // Get the active focus session if any
            $focusSession = $task->focusSessions()
                ->where('status', 'active')
                ->latest()
                ->first();

            // Determine start time
            $startedAt = $focusSession?->started_at ?? $task->last_focus_at ?? now()->subMinutes(15);

            // Calculate duration
            $durationMinutes = $startedAt->diffInMinutes($task->last_active_at ?? now());

            // Calculate inactivity
            $inactivityMinutes = ($task->last_active_at ?? now())->diffInMinutes(now());

            // Create abandonment record
            $abandonment = TaskAbandonment::create([
                'user_id' => $task->user_id,
                'task_id' => $task->id,
                'focus_session_id' => $focusSession?->id,
                'started_at' => $startedAt,
                'last_active_at' => $task->last_active_at ?? now(),
                'abandoned_at' => now(),
                'duration_minutes' => $durationMinutes,
                'abandonment_type' => $type,
                'inactivity_minutes' => $inactivityMinutes,
                'auto_detected' => $autoDetected,
                'reason' => $reason,
            ]);

            // Update task
            $task->update([
                'status' => 'pending', // Reset to pending
                'is_abandoned' => true,
            ]);
            $task->increment('abandonment_count');

            // Mark focus session as cancelled if exists
            if ($focusSession) {
                $focusSession->update([
                    'status' => 'cancelled',
                    'ended_at' => now(),
                    'actual_minutes' => $durationMinutes,
                ]);
            }

            // Create notification
            $this->createAbandonmentNotification($task, $abandonment);

            DB::commit();

            Log::info('Task marked as abandoned', [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'user_id' => $task->user_id,
                'duration_minutes' => $durationMinutes,
                'inactivity_minutes' => $inactivityMinutes,
                'type' => $type,
            ]);

            return $abandonment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark task as abandoned', [
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create a notification for task abandonment
     */
    protected function createAbandonmentNotification(Task $task, TaskAbandonment $abandonment): void
    {
        try {
            $message = "タスク「{$task->title}」が{$abandonment->inactivity_minutes}分間非アクティブのため、中断されました。";

            if ($abandonment->duration_minutes > 0) {
                $message .= "\n作業時間: {$abandonment->formatted_duration}";
            }

            Notification::create([
                'user_id' => $task->user_id,
                'type' => 'system',
                'title' => 'タスクが中断されました',
                'message' => $message,
                'data' => [
                    'action_type' => 'task_abandoned',
                    'task_id' => $task->id,
                    'abandonment_id' => $abandonment->id,
                    'duration_minutes' => $abandonment->duration_minutes,
                    'inactivity_minutes' => $abandonment->inactivity_minutes,
                    'button_text' => 'タスクに戻る',
                    'url' => "/tasks/{$task->id}",
                ],
                'scheduled_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create abandonment notification', [
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get abandonment statistics for a user
     */
    public function getAbandonmentStats(int $userId, int $days = 7): array
    {
        try {
            return [
                'total_abandonments' => TaskAbandonment::getTotalAbandonmentsForUser($userId, $days),
                'abandonment_rate' => TaskAbandonment::getAbandonmentRateForUser($userId, $days),
                'average_work_time' => round(TaskAbandonment::getAverageWorkTimeBeforeAbandonment($userId, $days) ?? 0, 1),
                'most_common_type' => TaskAbandonment::getMostCommonAbandonmentType($userId),
                'resume_rate' => TaskAbandonment::getResumeRate($userId, $days),
                'period_days' => $days,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get abandonment stats', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Resume an abandoned task
     */
    public function resumeAbandonedTask(int $taskId, int $userId): bool
    {
        try {
            $task = Task::where('id', $taskId)
                ->where('user_id', $userId)
                ->first();

            if (!$task) {
                return false;
            }

            // Mark as in progress and update timestamps
            $task->update([
                'status' => 'in_progress',
                'is_abandoned' => false,
                'last_active_at' => now(),
                'last_focus_at' => now(),
            ]);

            // Mark latest abandonment as resumed
            $latestAbandonment = $task->abandonments()
                ->where('resumed', false)
                ->latest()
                ->first();

            if ($latestAbandonment) {
                $latestAbandonment->markAsResumed();
            }

            Log::info('Task resumed after abandonment', [
                'task_id' => $taskId,
                'user_id' => $userId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to resume abandoned task', [
                'task_id' => $taskId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clean up old abandonment records (older than 90 days)
     */
    public function cleanupOldAbandonments(): int
    {
        try {
            $deleted = TaskAbandonment::where('abandoned_at', '<', now()->subDays(90))
                ->delete();

            Log::info('Cleaned up old abandonment records', [
                'deleted_count' => $deleted,
            ]);

            return $deleted;
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old abandonments', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }
}
