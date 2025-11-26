<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TimetableClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Smart Scheduler Service
 *
 * Suggests optimal times to schedule tasks based on:
 * - User's timetable (finds free slots)
 * - Task properties (deadline, priority, duration)
 * - Conflict detection
 */
class SmartSchedulerService
{
    /**
     * Suggest optimal schedule time for a task
     *
     * @param Task $task The task to schedule
     * @param User $user The user
     * @param int $daysAhead How many days ahead to look (default 7)
     * @return array Array of time slot suggestions with scores
     */
    public function suggestScheduleTime(Task $task, User $user, int $daysAhead = 7): array
    {
        Log::info('SmartScheduler: Starting schedule suggestion', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'estimated_minutes' => $task->estimated_minutes,
            'deadline' => $task->deadline,
            'priority' => $task->priority,
        ]);

        // 1. Get all scheduled items (timetable + scheduled tasks)
        $timetableClasses = TimetableClass::where('user_id', $user->id)->get();
        $scheduledTasks = Task::where('user_id', $user->id)
            ->whereNotNull('scheduled_time')
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->get();

        Log::info('SmartScheduler: Loaded scheduled items', [
            'timetable_classes' => $timetableClasses->count(),
            'scheduled_tasks' => $scheduledTasks->count(),
        ]);

        // 2. Find free slots in the next N days
        $freeSlots = $this->findFreeSlots($timetableClasses, $scheduledTasks, $task, $daysAhead);

        Log::info('SmartScheduler: Found free slots', [
            'free_slots_count' => count($freeSlots),
        ]);

        if (empty($freeSlots)) {
            return [];
        }

        // 3. Score each slot based on task properties
        $scoredSlots = $this->scoreSlots($freeSlots, $task, $user);

        // 4. Sort by score (highest first) and return top 3
        usort($scoredSlots, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $topSuggestions = array_slice($scoredSlots, 0, 3);

        Log::info('SmartScheduler: Returning top suggestions', [
            'suggestions_count' => count($topSuggestions),
            'top_scores' => array_column($topSuggestions, 'score'),
        ]);

        return $topSuggestions;
    }

    /**
     * Find free time slots
     *
     * @param Collection $timetableClasses User's timetable classes
     * @param Collection $scheduledTasks User's scheduled tasks
     * @param Task $task The task to schedule (for duration)
     * @param int $daysAhead How many days to look ahead
     * @return array Array of free slots [['date' => '2025-11-26', 'day' => 'tuesday', 'start_time' => '14:00', 'end_time' => '16:00', 'duration_minutes' => 120]]
     */
    private function findFreeSlots(Collection $timetableClasses, Collection $scheduledTasks, Task $task, int $daysAhead): array
    {
        $freeSlots = [];
        $taskDuration = $task->estimated_minutes ?? 60; // Default 60 minutes

        // Map day names to Carbon day numbers (0 = Sunday, 1 = Monday, ...)
        $dayNameToNumber = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
        ];

        // Define work hours (8 AM to 10 PM)
        $workStart = '08:00:00';
        $workEnd = '22:00:00';

        // Iterate through each day in the next N days
        $startDate = Carbon::today();
        for ($i = 0; $i < $daysAhead; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dayOfWeek = $currentDate->dayOfWeek; // 0-6
            $dayName = strtolower($currentDate->format('l')); // 'monday', 'tuesday', etc.

            // Get busy periods for this day
            $busyPeriods = $this->getBusyPeriods($currentDate, $dayName, $timetableClasses, $scheduledTasks);

            // Find gaps between busy periods
            $gaps = $this->findGaps($workStart, $workEnd, $busyPeriods);

            // Filter gaps that are long enough for the task
            foreach ($gaps as $gap) {
                $gapDuration = $this->getMinutesDifference($gap['start'], $gap['end']);
                if ($gapDuration >= $taskDuration) {
                    $freeSlots[] = [
                        'date' => $currentDate->format('Y-m-d'),
                        'day' => $dayName,
                        'start_time' => $gap['start'],
                        'end_time' => $gap['end'],
                        'duration_minutes' => $gapDuration,
                    ];
                }
            }
        }

        return $freeSlots;
    }

    /**
     * Get busy periods for a specific day
     *
     * @param Carbon $date The date
     * @param string $dayName Day name (e.g., 'monday')
     * @param Collection $timetableClasses Timetable classes
     * @param Collection $scheduledTasks Scheduled tasks
     * @return array Array of busy periods [['start' => '09:00:00', 'end' => '10:30:00']]
     */
    private function getBusyPeriods(Carbon $date, string $dayName, Collection $timetableClasses, Collection $scheduledTasks): array
    {
        $busyPeriods = [];

        // Add timetable classes for this day
        foreach ($timetableClasses as $class) {
            if (strtolower($class->day) === $dayName) {
                $busyPeriods[] = [
                    'start' => $class->start_time,
                    'end' => $class->end_time,
                    'type' => 'timetable',
                    'title' => $class->name,
                ];
            }
        }

        // Add scheduled tasks for this date
        foreach ($scheduledTasks as $task) {
            // Check if task is scheduled for this date (need to parse scheduled_time)
            // Note: scheduled_time is TIME type (HH:MM:SS), not DATE
            // We need to check if there's a way to know which date the task is scheduled for
            // For now, we'll skip tasks without a deadline or if they don't match

            // TODO: This logic needs improvement - tasks need a scheduled_date field
            // For now, we'll only consider tasks scheduled for today if they have scheduled_time
            if ($task->scheduled_time && $date->isToday()) {
                $duration = $task->estimated_minutes ?? 60;
                $startTime = $task->scheduled_time;
                $endTime = Carbon::createFromFormat('H:i:s', $startTime)->addMinutes($duration)->format('H:i:s');

                $busyPeriods[] = [
                    'start' => $startTime,
                    'end' => $endTime,
                    'type' => 'task',
                    'title' => $task->title,
                ];
            }
        }

        // Sort by start time
        usort($busyPeriods, function($a, $b) {
            return strcmp($a['start'], $b['start']);
        });

        return $busyPeriods;
    }

    /**
     * Find gaps between busy periods
     *
     * @param string $dayStart Start of work day (e.g., '08:00:00')
     * @param string $dayEnd End of work day (e.g., '22:00:00')
     * @param array $busyPeriods Busy periods sorted by start time
     * @return array Array of gaps [['start' => '10:30:00', 'end' => '14:00:00']]
     */
    private function findGaps(string $dayStart, string $dayEnd, array $busyPeriods): array
    {
        $gaps = [];
        $currentTime = $dayStart;

        foreach ($busyPeriods as $busy) {
            // If there's a gap before this busy period
            if ($currentTime < $busy['start']) {
                $gaps[] = [
                    'start' => $currentTime,
                    'end' => $busy['start'],
                ];
            }

            // Move current time to end of this busy period
            if ($busy['end'] > $currentTime) {
                $currentTime = $busy['end'];
            }
        }

        // Check if there's a gap at the end of the day
        if ($currentTime < $dayEnd) {
            $gaps[] = [
                'start' => $currentTime,
                'end' => $dayEnd,
            ];
        }

        return $gaps;
    }

    /**
     * Score time slots based on task properties
     *
     * @param array $slots Free time slots
     * @param Task $task The task
     * @param User $user The user
     * @return array Slots with scores added
     */
    private function scoreSlots(array $slots, Task $task, User $user): array
    {
        $scoredSlots = [];

        foreach ($slots as $slot) {
            $score = 0;
            $reasons = [];

            // Factor 1: Deadline proximity (30% weight)
            $deadlineScore = $this->calculateDeadlineScore($slot['date'], $task->deadline);
            $score += $deadlineScore * 0.3;
            if ($deadlineScore > 3) {
                $reasons[] = '締め切りが近い';
            }

            // Factor 2: Priority alignment (20% weight)
            $priorityScore = ($task->priority ?? 3) / 5 * 5; // Normalize to 0-5
            $score += $priorityScore * 0.2;
            if ($task->priority >= 4) {
                $reasons[] = '優先度が高い';
            }

            // Factor 3: Time of day preference (20% weight)
            $timeOfDayScore = $this->calculateTimeOfDayScore($slot['start_time']);
            $score += $timeOfDayScore * 0.2;
            if ($timeOfDayScore >= 4) {
                $reasons[] = '最適な時間帯';
            }

            // Factor 4: Sufficient time buffer (15% weight)
            $bufferScore = $this->calculateBufferScore($slot['duration_minutes'], $task->estimated_minutes ?? 60);
            $score += $bufferScore * 0.15;
            if ($bufferScore >= 4) {
                $reasons[] = '十分な時間あり';
            }

            // Factor 5: How soon can start (15% weight)
            $soonScore = $this->calculateSoonScore($slot['date']);
            $score += $soonScore * 0.15;
            if ($soonScore >= 4) {
                $reasons[] = 'すぐに開始可能';
            }

            $scoredSlots[] = array_merge($slot, [
                'score' => round($score, 2),
                'reasons' => $reasons,
                'confidence' => $this->calculateConfidence($score),
            ]);
        }

        return $scoredSlots;
    }

    /**
     * Calculate deadline proximity score (0-5)
     */
    private function calculateDeadlineScore(string $slotDate, $deadline): float
    {
        if (!$deadline) {
            return 3.0; // Neutral score
        }

        $slotCarbon = Carbon::parse($slotDate);
        $deadlineCarbon = Carbon::parse($deadline);
        $daysUntilDeadline = $slotCarbon->diffInDays($deadlineCarbon, false);

        if ($daysUntilDeadline < 0) {
            return 0.0; // Past deadline, not good
        }

        if ($daysUntilDeadline == 0) {
            return 5.0; // Due today, schedule ASAP
        }

        if ($daysUntilDeadline == 1) {
            return 4.5; // Due tomorrow
        }

        if ($daysUntilDeadline <= 3) {
            return 4.0; // Within 3 days
        }

        if ($daysUntilDeadline <= 7) {
            return 3.0; // Within a week
        }

        return 2.0; // More than a week away
    }

    /**
     * Calculate time of day score (0-5)
     * Morning (8-12): 4, Afternoon (12-17): 5, Evening (17-22): 3
     */
    private function calculateTimeOfDayScore(string $time): float
    {
        $hour = (int)substr($time, 0, 2);

        if ($hour >= 8 && $hour < 12) {
            return 4.0; // Morning - good for focused work
        }

        if ($hour >= 12 && $hour < 17) {
            return 5.0; // Afternoon - peak productivity
        }

        if ($hour >= 17 && $hour < 22) {
            return 3.0; // Evening - less optimal
        }

        return 2.0; // Very early or very late
    }

    /**
     * Calculate buffer score (0-5)
     * More buffer time = better score
     */
    private function calculateBufferScore(int $availableMinutes, int $requiredMinutes): float
    {
        $ratio = $availableMinutes / $requiredMinutes;

        if ($ratio >= 2.0) {
            return 5.0; // Double the time needed
        }

        if ($ratio >= 1.5) {
            return 4.0; // 50% buffer
        }

        if ($ratio >= 1.2) {
            return 3.0; // 20% buffer
        }

        if ($ratio >= 1.0) {
            return 2.0; // Just enough
        }

        return 0.0; // Not enough time
    }

    /**
     * Calculate "soon" score (0-5)
     * Sooner = better
     */
    private function calculateSoonScore(string $date): float
    {
        $days = Carbon::today()->diffInDays(Carbon::parse($date), false);

        if ($days == 0) {
            return 5.0; // Today
        }

        if ($days == 1) {
            return 4.0; // Tomorrow
        }

        if ($days <= 3) {
            return 3.0; // Within 3 days
        }

        if ($days <= 7) {
            return 2.0; // Within a week
        }

        return 1.0; // Far away
    }

    /**
     * Calculate confidence level based on score
     */
    private function calculateConfidence(float $score): string
    {
        if ($score >= 4.0) {
            return 'high';
        }

        if ($score >= 3.0) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get minutes difference between two time strings
     */
    private function getMinutesDifference(string $start, string $end): int
    {
        $startCarbon = Carbon::createFromFormat('H:i:s', $start);
        $endCarbon = Carbon::createFromFormat('H:i:s', $end);
        return $startCarbon->diffInMinutes($endCarbon);
    }
}
