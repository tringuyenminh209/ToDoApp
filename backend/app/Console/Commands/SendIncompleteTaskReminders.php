<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendIncompleteTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tasks:send-incomplete-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send reminders for overdue and long-pending tasks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for incomplete tasks...');

        $sentCount = 0;

        // 1. Find overdue tasks (deadline passed, not completed)
        $overdueTasks = Task::whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->with('user')
            ->get();

        $this->info("Found {$overdueTasks->count()} overdue tasks");

        foreach ($overdueTasks as $task) {
            try {
                // Check if overdue reminder already sent recently (last 12 hours)
                $existingReminder = Notification::where('user_id', $task->user_id)
                    ->where('type', 'reminder')
                    ->where('data->action_type', 'overdue_task')
                    ->where('data->task_id', $task->id)
                    ->where('created_at', '>=', now()->subHours(12))
                    ->first();

                if ($existingReminder) {
                    $this->line("Skipping overdue task #{$task->id} - reminder already sent recently");
                    continue;
                }

                $deadline = Carbon::parse($task->deadline);
                $daysOverdue = $deadline->diffInDays(now());
                $hoursOverdue = $deadline->diffInHours(now());

                $title = '🔴 期限超過のタスク';
                $message = "タスク「{$task->title}」は期限を過ぎています。\n";

                if ($daysOverdue > 0) {
                    $message .= "期限超過: {$daysOverdue}日";
                } else {
                    $message .= "期限超過: {$hoursOverdue}時間";
                }

                if ($task->status === 'pending') {
                    $message .= "\n\n⚠️ このタスクはまだ開始されていません。できるだけ早く取り組んでください。";
                } else {
                    $message .= "\n\n⏰ このタスクは進行中ですが、期限を過ぎています。優先的に完了させてください。";
                }

                Notification::create([
                    'user_id' => $task->user_id,
                    'type' => 'reminder',
                    'title' => $title,
                    'message' => $message,
                    'data' => [
                        'action_type' => 'overdue_task',
                        'task_id' => $task->id,
                        'deadline' => $task->deadline,
                        'days_overdue' => $daysOverdue,
                        'urgency' => 'critical',
                        'button_text' => $task->status === 'pending' ? 'すぐに開始' : 'タスクを完了',
                        'url' => "/tasks/{$task->id}",
                    ],
                    'scheduled_at' => now(),
                ]);

                $sentCount++;
                $this->line("Sent overdue reminder for task #{$task->id}: {$task->title} ({$daysOverdue} days overdue)");

            } catch (\Exception $e) {
                Log::error('Failed to send overdue task reminder', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // 2. Find long-pending tasks (created more than 7 days ago, still pending)
        $longPendingTasks = Task::where('status', 'pending')
            ->whereNull('deadline') // Tasks without deadline
            ->where('created_at', '<', now()->subDays(7))
            ->with('user')
            ->get();

        $this->info("Found {$longPendingTasks->count()} long-pending tasks");

        foreach ($longPendingTasks as $task) {
            try {
                // Check if pending reminder already sent recently (last 3 days)
                $existingReminder = Notification::where('user_id', $task->user_id)
                    ->where('type', 'reminder')
                    ->where('data->action_type', 'long_pending_task')
                    ->where('data->task_id', $task->id)
                    ->where('created_at', '>=', now()->subDays(3))
                    ->first();

                if ($existingReminder) {
                    $this->line("Skipping pending task #{$task->id} - reminder already sent recently");
                    continue;
                }

                $createdAt = Carbon::parse($task->created_at);
                $daysPending = $createdAt->diffInDays(now());

                $title = '📋 長期未着手のタスク';
                $message = "タスク「{$task->title}」は{$daysPending}日間未着手です。\n";
                $message .= "\n💡 このタスクは本当に必要ですか？不要であればキャンセルすることを検討してください。";

                Notification::create([
                    'user_id' => $task->user_id,
                    'type' => 'reminder',
                    'title' => $title,
                    'message' => $message,
                    'data' => [
                        'action_type' => 'long_pending_task',
                        'task_id' => $task->id,
                        'days_pending' => $daysPending,
                        'button_text' => 'タスクを確認',
                        'url' => "/tasks/{$task->id}",
                    ],
                    'scheduled_at' => now(),
                ]);

                $sentCount++;
                $this->line("Sent pending reminder for task #{$task->id}: {$task->title} ({$daysPending} days pending)");

            } catch (\Exception $e) {
                Log::error('Failed to send pending task reminder', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // 3. Find in-progress tasks that haven't been active for a while (3+ days)
        $staleTasks = Task::where('status', 'in_progress')
            ->whereNotNull('last_active_at')
            ->where('last_active_at', '<', now()->subDays(3))
            ->with('user')
            ->get();

        $this->info("Found {$staleTasks->count()} stale in-progress tasks");

        foreach ($staleTasks as $task) {
            try {
                // Check if stale reminder already sent recently (last 2 days)
                $existingReminder = Notification::where('user_id', $task->user_id)
                    ->where('type', 'reminder')
                    ->where('data->action_type', 'stale_task')
                    ->where('data->task_id', $task->id)
                    ->where('created_at', '>=', now()->subDays(2))
                    ->first();

                if ($existingReminder) {
                    $this->line("Skipping stale task #{$task->id} - reminder already sent recently");
                    continue;
                }

                $lastActive = Carbon::parse($task->last_active_at);
                $daysInactive = $lastActive->diffInDays(now());

                $title = '⚠️ 進行中のタスクが停止しています';
                $message = "タスク「{$task->title}」は{$daysInactive}日間更新されていません。\n";
                $message .= "\n続行しますか？それとも一時停止しますか？";

                Notification::create([
                    'user_id' => $task->user_id,
                    'type' => 'reminder',
                    'title' => $title,
                    'message' => $message,
                    'data' => [
                        'action_type' => 'stale_task',
                        'task_id' => $task->id,
                        'days_inactive' => $daysInactive,
                        'last_active_at' => $task->last_active_at,
                        'button_text' => 'タスクを再開',
                        'url' => "/tasks/{$task->id}",
                    ],
                    'scheduled_at' => now(),
                ]);

                $sentCount++;
                $this->line("Sent stale task reminder for task #{$task->id}: {$task->title} ({$daysInactive} days inactive)");

            } catch (\Exception $e) {
                Log::error('Failed to send stale task reminder', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Successfully sent {$sentCount} incomplete task reminders.");

        return Command::SUCCESS;
    }
}
