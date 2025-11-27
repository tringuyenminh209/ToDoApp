<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tasks:send-reminders {--minutes=15 : Minutes before scheduled time to send reminder}';

    /**
     * The console command description.
     */
    protected $description = 'Send reminders for tasks approaching their scheduled time';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutesBefore = (int) $this->option('minutes');
        $this->info("Checking for tasks scheduled in the next {$minutesBefore} minutes...");

        // Find tasks with scheduled_time in the next X minutes
        // scheduled_time is stored as DATETIME in database
        $now = now();
        $futureTime = now()->addMinutes($minutesBefore);

        $tasks = Task::where('status', 'pending')
            ->whereNotNull('scheduled_time')
            ->whereBetween('scheduled_time', [$now, $futureTime])
            ->with('user')
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No tasks found with upcoming scheduled time.');
            return Command::SUCCESS;
        }

        $sentCount = 0;

        foreach ($tasks as $task) {
            try {
                // Check if reminder already sent for this task recently
                $existingReminder = Notification::where('user_id', $task->user_id)
                    ->where('type', 'reminder')
                    ->where('data->task_id', $task->id)
                    ->where('created_at', '>=', now()->subHours(1))
                    ->first();

                if ($existingReminder) {
                    $this->line("Skipping task #{$task->id} - reminder already sent recently");
                    continue;
                }

                // Calculate time until scheduled
                $scheduledTime = Carbon::parse($task->scheduled_time);
                $minutesUntil = $now->diffInMinutes($scheduledTime);

                // Create reminder notification
                $message = "タスク「{$task->title}」の開始時刻が近づいています。";

                if ($minutesUntil <= 0) {
                    $message .= "\n開始時刻: 今すぐ";
                } else {
                    $message .= "\n開始時刻: あと{$minutesUntil}分";
                }

                if ($task->estimated_minutes) {
                    $message .= "\n予想時間: {$task->estimated_minutes}分";
                }

                Notification::create([
                    'user_id' => $task->user_id,
                    'type' => 'reminder',
                    'title' => 'タスクのリマインダー',
                    'message' => $message,
                    'data' => [
                        'action_type' => 'task_reminder',
                        'task_id' => $task->id,
                        'scheduled_time' => $task->scheduled_time,
                        'minutes_until' => $minutesUntil,
                        'button_text' => 'タスクを開始',
                        'url' => "/tasks/{$task->id}",
                    ],
                    'scheduled_at' => now(),
                ]);

                $sentCount++;
                $this->line("Sent reminder for task #{$task->id}: {$task->title} (User: {$task->user_id})");

            } catch (\Exception $e) {
                Log::error('Failed to send task reminder', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
                $this->error("Failed to send reminder for task #{$task->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$sentCount} task reminders.");

        return Command::SUCCESS;
    }
}
