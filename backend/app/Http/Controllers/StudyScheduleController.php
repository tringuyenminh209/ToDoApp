<?php

namespace App\Http\Controllers;

use App\Models\StudySchedule;
use App\Models\LearningPath;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * StudyScheduleController
 * Quản lý lịch học cho Learning Path
 * スケジュール学習コントローラー
 *
 * Purpose:
 * - Manage study schedules for learning paths
 * - Enforce discipline with required study times
 * - Track session completion and consistency
 */
class StudyScheduleController extends Controller
{
    /**
     * Get all schedules for a learning path
     * GET /api/learning-paths/{id}/study-schedules
     */
    public function index(Request $request, int $learningPathId): JsonResponse
    {
        try {
            $user = $request->user();
            $learningPath = LearningPath::where('user_id', $user->id)
                ->findOrFail($learningPathId);

            $schedules = $learningPath->studySchedules;

            return response()->json([
                'success' => true,
                'data' => [
                    'schedules' => $schedules,
                    'weekly_summary' => $learningPath->getWeeklyScheduleSummary(),
                    'weekly_hours' => $learningPath->getWeeklyStudyHours(),
                ],
                'message' => 'スケジュールを取得しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching study schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'スケジュールの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Create a new study schedule
     * POST /api/learning-paths/{id}/study-schedules
     */
    public function store(Request $request, int $learningPathId): JsonResponse
    {
        $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'study_time' => 'required|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'reminder_enabled' => 'nullable|boolean',
            'reminder_before_minutes' => 'nullable|integer|min:5|max:120',
        ]);

        try {
            $user = $request->user();
            $learningPath = LearningPath::where('user_id', $user->id)
                ->findOrFail($learningPathId);

            DB::beginTransaction();

            $schedule = StudySchedule::create([
                'learning_path_id' => $learningPath->id,
                'day_of_week' => $request->day_of_week,
                'study_time' => $request->study_time . ':00',
                'duration_minutes' => $request->duration_minutes ?? 60,
                'is_active' => true,
                'reminder_enabled' => $request->reminder_enabled ?? true,
                'reminder_before_minutes' => $request->reminder_before_minutes ?? 30,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $schedule,
                'message' => 'スケジュールを追加しました'
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Check for unique constraint violation
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'この曜日と時間のスケジュールは既に存在します'
                ], 400);
            }

            Log::error('Error creating study schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'スケジュールの作成に失敗しました'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating study schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'スケジュールの作成に失敗しました'
            ], 500);
        }
    }

    /**
     * Update a study schedule
     * PUT /api/study-schedules/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'day_of_week' => 'nullable|integer|between:0,6',
            'study_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'is_active' => 'nullable|boolean',
            'reminder_enabled' => 'nullable|boolean',
            'reminder_before_minutes' => 'nullable|integer|min:5|max:120',
        ]);

        try {
            $user = $request->user();

            // Verify ownership through learning path
            $schedule = StudySchedule::whereHas('learningPath', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($id);

            DB::beginTransaction();

            $updateData = [];

            if ($request->has('day_of_week')) {
                $updateData['day_of_week'] = $request->day_of_week;
            }

            if ($request->has('study_time')) {
                $updateData['study_time'] = $request->study_time . ':00';
            }

            if ($request->has('duration_minutes')) {
                $updateData['duration_minutes'] = $request->duration_minutes;
            }

            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            }

            if ($request->has('reminder_enabled')) {
                $updateData['reminder_enabled'] = $request->reminder_enabled;
            }

            if ($request->has('reminder_before_minutes')) {
                $updateData['reminder_before_minutes'] = $request->reminder_before_minutes;
            }

            $schedule->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $schedule->fresh(),
                'message' => 'スケジュールを更新しました'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating study schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'スケジュールの更新に失敗しました'
            ], 500);
        }
    }

    /**
     * Delete a study schedule
     * DELETE /api/study-schedules/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            $schedule = StudySchedule::whereHas('learningPath', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($id);

            DB::beginTransaction();

            $schedule->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'スケジュールを削除しました'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting study schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'スケジュールの削除に失敗しました'
            ], 500);
        }
    }

    /**
     * Mark a study session as completed
     * POST /api/study-schedules/{id}/complete
     */
    public function markCompleted(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            $schedule = StudySchedule::whereHas('learningPath', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($id);

            DB::beginTransaction();

            $schedule->markCompleted();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $schedule->fresh(),
                'message' => '学習セッションを完了しました！'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking schedule completed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'セッションの完了に失敗しました'
            ], 500);
        }
    }

    /**
     * Mark a study session as missed
     * POST /api/study-schedules/{id}/missed
     */
    public function markMissed(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            $schedule = StudySchedule::whereHas('learningPath', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($id);

            DB::beginTransaction();

            $schedule->markMissed();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $schedule->fresh(),
                'message' => 'セッションを欠席としてマークしました'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking schedule missed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'セッションのマークに失敗しました'
            ], 500);
        }
    }

    /**
     * Get all study schedules for the user (for calendar view)
     * GET /api/study-schedules
     */
    public function allSchedules(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $schedules = StudySchedule::whereHas('learningPath', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('is_active', true)
                ->with('learningPath')
                ->orderBy('day_of_week')
                ->orderBy('study_time')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $schedules,
                'message' => 'スケジュールを取得しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching all schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'スケジュールの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get today's scheduled sessions for the user
     * GET /api/study-schedules/today
     */
    public function todaySessions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $today = now()->dayOfWeek;

            $schedules = StudySchedule::whereHas('learningPath', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'active');
            })
                ->where('day_of_week', $today)
                ->where('is_active', true)
                ->with('learningPath')
                ->orderBy('study_time')
                ->get();

            $upcoming = $schedules->filter(function ($schedule) {
                return $schedule->isUpcoming();
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'all_sessions' => $schedules,
                    'upcoming_sessions' => $upcoming->values(),
                    'total_study_minutes' => $schedules->sum('duration_minutes'),
                ],
                'message' => '今日のスケジュールを取得しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching today sessions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'スケジュールの取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get schedule statistics
     * GET /api/study-schedules/stats
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $schedules = StudySchedule::whereHas('learningPath', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

            $stats = [
                'total_schedules' => $schedules->count(),
                'active_schedules' => $schedules->where('is_active', true)->count(),
                'total_completed' => $schedules->sum('completed_sessions'),
                'total_missed' => $schedules->sum('missed_sessions'),
                'average_completion_rate' => $schedules->avg('completion_rate'),
                'average_consistency_score' => $schedules->avg('consistency_score'),
                'weekly_study_hours' => $schedules->sum('duration_minutes') / 60,
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => '統計を取得しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching schedule stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '統計の取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get combined timeline items (Study Schedules + Timetable Classes)
     * GET /api/study-schedules/timeline
     *
     * Returns both study schedules and timetable classes in unified format for timeline view
     */
    public function getTimelineItems(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Fetch study schedules
            $studySchedules = StudySchedule::whereHas('learningPath', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('is_active', true)
                ->with('learningPath')
                ->get();

            // Fetch timetable classes
            $timetableClasses = \App\Models\TimetableClass::where('user_id', $user->id)
                ->with('learningPath')
                ->get();

            $timelineItems = [];

            // Convert study schedules to timeline format
            foreach ($studySchedules as $schedule) {
                $timelineItems[] = [
                    'id' => 'study_' . $schedule->id,
                    'type' => 'study_schedule',
                    'title' => '📚 ' . ($schedule->learningPath->title ?? '学習'),
                    'day_of_week' => $schedule->day_of_week,
                    'scheduled_time' => $schedule->study_time, // HH:mm:ss
                    'duration_minutes' => $schedule->duration_minutes,
                    'category' => 'study',
                    'learning_path_id' => $schedule->learning_path_id,
                    'learning_path' => $schedule->learningPath ? [
                        'id' => $schedule->learningPath->id,
                        'title' => $schedule->learningPath->title,
                    ] : null,
                ];
            }

            // Convert timetable classes to timeline format
            foreach ($timetableClasses as $class) {
                // Map day string to day_of_week integer
                $dayMap = [
                    'sunday' => 0,
                    'monday' => 1,
                    'tuesday' => 2,
                    'wednesday' => 3,
                    'thursday' => 4,
                    'friday' => 5,
                    'saturday' => 6,
                ];

                $dayOfWeek = $dayMap[strtolower($class->day)] ?? 1;

                // Calculate duration in minutes
                try {
                    $start = \Carbon\Carbon::createFromFormat('H:i', $class->start_time);
                    $end = \Carbon\Carbon::createFromFormat('H:i', $class->end_time);
                    $durationMinutes = $end->diffInMinutes($start);
                } catch (\Exception $e) {
                    $durationMinutes = 60; // Default 1 hour
                }

                // Add seconds to start_time if needed (HH:mm -> HH:mm:ss)
                $scheduledTime = strlen($class->start_time) == 5
                    ? $class->start_time . ':00'
                    : $class->start_time;

                $timelineItems[] = [
                    'id' => 'class_' . $class->id,
                    'type' => 'timetable_class',
                    'title' => '🎓 ' . $class->name,
                    'day_of_week' => $dayOfWeek,
                    'scheduled_time' => $scheduledTime, // HH:mm:ss
                    'duration_minutes' => $durationMinutes,
                    'category' => 'class',
                    'room' => $class->room,
                    'instructor' => $class->instructor,
                    'period' => $class->period,
                    'color' => $class->color,
                    'icon' => $class->icon,
                    'learning_path_id' => $class->learning_path_id,
                    'learning_path' => $class->learningPath ? [
                        'id' => $class->learningPath->id,
                        'title' => $class->learningPath->title,
                    ] : null,
                ];
            }

            // Sort by day_of_week then scheduled_time
            usort($timelineItems, function ($a, $b) {
                if ($a['day_of_week'] === $b['day_of_week']) {
                    return strcmp($a['scheduled_time'], $b['scheduled_time']);
                }
                return $a['day_of_week'] <=> $b['day_of_week'];
            });

            return response()->json([
                'success' => true,
                'data' => $timelineItems,
                'message' => 'タイムラインデータを取得しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching timeline items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'タイムラインデータの取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
