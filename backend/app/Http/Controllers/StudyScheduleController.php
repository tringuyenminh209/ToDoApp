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
}
