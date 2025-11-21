<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Task;
use App\Models\FocusSession;
use App\Models\UserStatsCache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateUserStatsCache implements ShouldQueue
{
    use Queueable;

    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("UpdateUserStatsCache: Starting for user {$this->userId}");

            // Get task statistics
            $totalTasks = Task::where('user_id', $this->userId)->count();
            $completedTasks = Task::where('user_id', $this->userId)->where('status', 'completed')->count();
            $pendingTasks = Task::where('user_id', $this->userId)->where('status', 'pending')->count();
            $inProgressTasks = Task::where('user_id', $this->userId)->where('status', 'in_progress')->count();

            // Calculate completion rate
            $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

            // Get focus session statistics
            $focusSessions = FocusSession::where('user_id', $this->userId)
                ->where('status', 'completed')
                ->get();
            $totalFocusTime = $focusSessions->sum('actual_minutes');
            $totalFocusSessions = $focusSessions->count();
            $averageSessionDuration = $totalFocusSessions > 0
                ? round($totalFocusTime / $totalFocusSessions)
                : 0;

            // Calculate streak
            $streaks = $this->calculateStreaks($this->userId);

            // Update or create cache
            UserStatsCache::updateOrCreate(
                ['user_id' => $this->userId],
                [
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'pending_tasks' => $pendingTasks,
                    'in_progress_tasks' => $inProgressTasks,
                    'completion_rate' => round($completionRate, 2),
                    'total_focus_time' => $totalFocusTime,
                    'total_focus_sessions' => $totalFocusSessions,
                    'average_session_duration' => $averageSessionDuration,
                    'current_streak' => $streaks['current'],
                    'longest_streak' => $streaks['longest'],
                    'last_calculated_at' => now(),
                ]
            );

            Log::info("UpdateUserStatsCache: Completed for user {$this->userId}");
        } catch (\Exception $e) {
            Log::error("UpdateUserStatsCache: Failed for user {$this->userId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate user streaks
     */
    private function calculateStreaks(int $userId): array
    {
        // Get all days with completed tasks, ordered by date descending
        $completedDays = Task::where('user_id', $userId)
            ->where('status', 'completed')
            ->select(DB::raw('DATE(updated_at) as completion_date'))
            ->distinct()
            ->orderBy('completion_date', 'desc')
            ->pluck('completion_date')
            ->toArray();

        if (empty($completedDays)) {
            return ['current' => 0, 'longest' => 0];
        }

        $currentStreak = 0;
        $longestStreak = 0;
        $tempStreak = 0;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Check if the most recent completion was today or yesterday
        $lastCompletionDate = Carbon::parse($completedDays[0]);

        if ($lastCompletionDate->isSameDay($today) || $lastCompletionDate->isSameDay($yesterday)) {
            $currentStreak = 1;
            $tempStreak = 1;
            $previousDate = $lastCompletionDate;

            // Count consecutive days
            for ($i = 1; $i < count($completedDays); $i++) {
                $currentDate = Carbon::parse($completedDays[$i]);
                $daysDiff = $previousDate->diffInDays($currentDate);

                if ($daysDiff === 1) {
                    $currentStreak++;
                    $tempStreak++;
                } else {
                    break;
                }

                $previousDate = $currentDate;
            }
        }

        // Calculate longest streak
        $tempStreak = 1;
        for ($i = 0; $i < count($completedDays) - 1; $i++) {
            $currentDate = Carbon::parse($completedDays[$i]);
            $nextDate = Carbon::parse($completedDays[$i + 1]);
            $daysDiff = $currentDate->diffInDays($nextDate);

            if ($daysDiff === 1) {
                $tempStreak++;
                $longestStreak = max($longestStreak, $tempStreak);
            } else {
                $tempStreak = 1;
            }
        }

        $longestStreak = max($longestStreak, $currentStreak, 1);

        return [
            'current' => $currentStreak,
            'longest' => $longestStreak,
        ];
    }
}
