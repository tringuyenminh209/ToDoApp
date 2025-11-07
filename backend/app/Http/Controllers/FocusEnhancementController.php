<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\FocusEnvironment;
use App\Models\DistractionLog;
use App\Models\ContextSwitch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FocusEnhancementController extends Controller
{
    /**
     * Save focus environment checklist
     * POST /api/focus/environment/check
     */
    public function saveEnvironmentCheck(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|exists:tasks,id',
            'focus_session_id' => 'nullable|integer|exists:focus_sessions,id',
            'quiet_space' => 'boolean',
            'phone_silent' => 'boolean',
            'materials_ready' => 'boolean',
            'water_coffee_ready' => 'boolean',
            'comfortable_position' => 'boolean',
            'notifications_off' => 'boolean',
            'apps_closed' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $environment = FocusEnvironment::create([
                'task_id' => $request->task_id,
                'user_id' => $request->user()->id,
                'focus_session_id' => $request->focus_session_id,
                'quiet_space' => $request->boolean('quiet_space'),
                'phone_silent' => $request->boolean('phone_silent'),
                'materials_ready' => $request->boolean('materials_ready'),
                'water_coffee_ready' => $request->boolean('water_coffee_ready'),
                'comfortable_position' => $request->boolean('comfortable_position'),
                'notifications_off' => $request->boolean('notifications_off'),
                'apps_closed' => $request->apps_closed ?? [],
                'notes' => $request->notes,
            ]);

            $environment->updateAllChecksStatus();

            return response()->json([
                'success' => true,
                'message' => 'Environment checklist saved successfully',
                'data' => $environment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save environment checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get focus environment history for a task
     * GET /api/focus/environment/task/{taskId}
     */
    public function getEnvironmentHistory(int $taskId): JsonResponse
    {
        try {
            $environments = FocusEnvironment::where('task_id', $taskId)
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $environments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get environment history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log a distraction
     * POST /api/focus/distraction/log
     */
    public function logDistraction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|exists:tasks,id',
            'focus_session_id' => 'nullable|integer|exists:focus_sessions,id',
            'distraction_type' => 'required|in:phone,social_media,noise,person,thoughts,hunger_thirst,fatigue,other',
            'duration_seconds' => 'nullable|integer|min:0|max:3600',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $distraction = DistractionLog::create([
                'task_id' => $request->task_id,
                'user_id' => $request->user()->id,
                'focus_session_id' => $request->focus_session_id,
                'distraction_type' => $request->distraction_type,
                'duration_seconds' => $request->duration_seconds,
                'notes' => $request->notes,
                'occurred_at' => now(),
                'time_of_day' => now()->format('H:i:s'),
            ]);

            // Update task distraction count
            $task = Task::find($request->task_id);
            if ($task) {
                $task->increment('distraction_count');
            }

            return response()->json([
                'success' => true,
                'message' => 'Distraction logged successfully',
                'data' => $distraction
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log distraction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get distraction logs for a task
     * GET /api/focus/distraction/task/{taskId}
     */
    public function getDistractionLogs(int $taskId): JsonResponse
    {
        try {
            $distractions = DistractionLog::where('task_id', $taskId)
                ->where('user_id', auth()->id())
                ->orderBy('occurred_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $distractions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get distraction logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get distraction analytics for user
     * GET /api/focus/distraction/analytics
     */
    public function getDistractionAnalytics(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $days = $request->query('days', 7);

            // Top distractions
            $topDistractions = DistractionLog::where('user_id', $userId)
                ->where('occurred_at', '>=', now()->subDays($days))
                ->select('distraction_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(duration_seconds) as total_duration'))
                ->groupBy('distraction_type')
                ->orderByDesc('count')
                ->get();

            // Distractions by time of day
            $byTimeOfDay = DistractionLog::where('user_id', $userId)
                ->where('occurred_at', '>=', now()->subDays($days))
                ->whereNotNull('time_of_day')
                ->select(DB::raw('HOUR(time_of_day) as hour'), DB::raw('COUNT(*) as count'))
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            // Total distractions
            $totalDistractions = DistractionLog::where('user_id', $userId)
                ->where('occurred_at', '>=', now()->subDays($days))
                ->count();

            // Average duration
            $avgDuration = DistractionLog::where('user_id', $userId)
                ->where('occurred_at', '>=', now()->subDays($days))
                ->whereNotNull('duration_seconds')
                ->avg('duration_seconds');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_distractions' => $totalDistractions,
                    'average_duration_seconds' => round($avgDuration ?? 0),
                    'top_distractions' => $topDistractions,
                    'by_time_of_day' => $byTimeOfDay,
                    'period_days' => $days,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get distraction analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for context switch and log it
     * POST /api/focus/context-switch/check
     */
    public function checkContextSwitch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_task_id' => 'nullable|integer|exists:tasks,id',
            'to_task_id' => 'required|integer|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = $request->user()->id;
            $toTask = Task::find($request->to_task_id);

            // If no from_task, get the last task user worked on
            $fromTask = null;
            if ($request->from_task_id) {
                $fromTask = Task::find($request->from_task_id);
            } else {
                // Get last task from recent focus sessions
                $lastSession = DB::table('focus_sessions')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($lastSession) {
                    $fromTask = Task::find($lastSession->task_id);
                }
            }

            // Determine if it's a significant switch
            $isSignificant = false;
            $estimatedCost = 15; // Base cost

            if ($fromTask) {
                // Different category
                if ($fromTask->category !== $toTask->category) {
                    $isSignificant = true;
                    $estimatedCost += 10;
                }

                // Focus difficulty jump
                $difficultyJump = abs($toTask->focus_difficulty - $fromTask->focus_difficulty);
                if ($difficultyJump >= 2) {
                    $isSignificant = true;
                    $estimatedCost += ($difficultyJump * 5);
                }
            }

            // Create context switch record
            $contextSwitch = ContextSwitch::create([
                'user_id' => $userId,
                'from_task_id' => $fromTask?->id,
                'from_category' => $fromTask?->category,
                'from_focus_difficulty' => $fromTask?->focus_difficulty,
                'to_task_id' => $toTask->id,
                'to_category' => $toTask->category,
                'to_focus_difficulty' => $toTask->focus_difficulty,
                'is_significant_switch' => $isSignificant,
                'estimated_cost_minutes' => $estimatedCost,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'context_switch' => $contextSwitch,
                    'should_warn' => $isSignificant,
                    'warning_message' => $isSignificant ? $this->getContextSwitchWarning($fromTask, $toTask, $estimatedCost) : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check context switch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm user proceeded with context switch
     * PUT /api/focus/context-switch/{id}/proceed
     */
    public function confirmContextSwitch(Request $request, int $id): JsonResponse
    {
        try {
            $contextSwitch = ContextSwitch::findOrFail($id);

            if ($contextSwitch->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $contextSwitch->update([
                'user_proceeded' => true,
                'user_note' => $request->note,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Context switch confirmed',
                'data' => $contextSwitch
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm context switch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get context switch analytics
     * GET /api/focus/context-switch/analytics
     */
    public function getContextSwitchAnalytics(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $days = $request->query('days', 7);

            $totalSwitches = ContextSwitch::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays($days))
                ->count();

            $significantSwitches = ContextSwitch::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays($days))
                ->where('is_significant_switch', true)
                ->count();

            $avgCost = ContextSwitch::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays($days))
                ->avg('estimated_cost_minutes');

            $totalCost = ContextSwitch::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays($days))
                ->sum('estimated_cost_minutes');

            $commonPatterns = ContextSwitch::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays($days))
                ->select('from_category', 'to_category', DB::raw('COUNT(*) as count'))
                ->groupBy('from_category', 'to_category')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_switches' => $totalSwitches,
                    'significant_switches' => $significantSwitches,
                    'average_cost_minutes' => round($avgCost ?? 0),
                    'total_cost_minutes' => $totalCost,
                    'total_cost_hours' => round($totalCost / 60, 1),
                    'common_patterns' => $commonPatterns,
                    'period_days' => $days,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get context switch analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get context switch warning message
     */
    private function getContextSwitchWarning(?Task $fromTask, Task $toTask, int $cost): string
    {
        if (!$fromTask) {
            return "Starting focus on: {$toTask->title}";
        }

        $message = "⚠️ Context Switch Detected!\n\n";
        $message .= "From: {$fromTask->title} ({$fromTask->category})\n";
        $message .= "To: {$toTask->title} ({$toTask->category})\n\n";
        $message .= "⏱️ Estimated recovery time: ~{$cost} minutes\n\n";

        if ($fromTask->category !== $toTask->category) {
            $message .= "💡 Tip: Different task categories require mental adjustment. ";
            $message .= "Consider batching similar tasks together to minimize context switching.\n\n";
        }

        if (abs($toTask->focus_difficulty - $fromTask->focus_difficulty) >= 2) {
            $message .= "🧠 Focus difficulty change detected. ";
            $message .= "Take a moment to prepare mentally for the new task.\n\n";
        }

        return $message;
    }
}
