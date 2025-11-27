<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tasks:send-deadline-reminders {--hours=24 : Hours before deadline to send reminder}';

    /**
     * The console command description.
     */
    protected $description = 'Send reminders for tasks approaching their deadline';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hoursBefore = (int) $this->option('hours');
        $this->info("Checking for tasks with deadline in the next {$hoursBefore} hours...");

        // Find tasks with deadline in the next X hours
        $now = now();
        $futureTime = now()->addHours($hoursBefore);

        $tasks = Task::whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [$now, $futureTime])
            ->with('user')
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No tasks found with upcoming deadline.');
            return Command::SUCCESS;
        }

        $sentCount = 0;

        foreach ($tasks as $task) {
            try {
                // Check if deadline reminder already sent for this task recently
                $existingReminder = Notification::where('user_id', $task->user_id)
                    ->where('type', 'reminder')
                    ->where('data->action_type', 'deadline_reminder')
                    ->where('data->task_id', $task->id)
                    ->where('created_at', '>=', now()->subHours(12))
                    ->first();

                if ($existingReminder) {
                    $this->line("Skipping task #{$task->id} - deadline reminder already sent recently");
                    continue;
                }

                // Calculate time until deadline
                $deadline = Carbon::parse($task->deadline);
                $hoursUntil = $now->diffInHours($deadline);
                $minutesUntil = $now->diffInMinutes($deadline);

                // Determine urgency
                $urgency = 'warning';
                if ($hoursUntil <= 2) {
                    $urgency = 'critical';
                } elseif ($hoursUntil <= 6) {
                    $urgency = 'high';
                }

                // Create deadline reminder notification
                $title = match($urgency) {
                    'critical' => '🔴 緊急：締め切り間近！',
                    'high' => '⚠️ 締め切りが近づいています',
                    default => '📅 締め切りのリマインダー',
                };

                $message = "タスク「{$task->title}」の締め切りが近づいています。\n";

                if ($hoursUntil <= 0) {
                    $message .= "締め切り: まもなく期限切れ！";
                } elseif ($hoursUntil < 1) {
                    $message .= "締め切り: あと{$minutesUntil}分";
                } elseif ($hoursUntil < 24) {
                    $message .= "締め切り: あと{$hoursUntil}時間";
                } else {
                    $days = round($hoursUntil / 24, 1);
                    $message .= "締め切り: あと{$days}日";
                }

                if ($task->status === 'pending') {
                    $message .= "\n\n⚡ このタスクはまだ開始されていません。";
                }

                Notification::create([
                    'user_id' => $task->user_id,
                    'type' => 'reminder',
                    'title' => $title,
                    'message' => $message,
                    'data' => [
                        'action_type' => 'deadline_reminder',
                        'task_id' => $task->id,
                        'deadline' => $task->deadline,
                        'hours_until' => $hoursUntil,
                        'urgency' => $urgency,
                        'button_text' => $task->status === 'pending' ? 'タスクを開始' : 'タスクを見る',
                        'url' => "/tasks/{$task->id}",
                    ],
                    'scheduled_at' => now(),
                ]);

                $sentCount++;
                $this->line("Sent deadline reminder for task #{$task->id}: {$task->title} (Urgency: {$urgency})");

            } catch (\Exception $e) {
                Log::error('Failed to send deadline reminder', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
                $this->error("Failed to send deadline reminder for task #{$task->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$sentCount} deadline reminders.");

        return Command::SUCCESS;
    }
}
