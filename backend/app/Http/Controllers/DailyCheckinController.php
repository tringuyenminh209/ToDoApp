<?php

namespace App\Http\Controllers;

use App\Models\DailyCheckin;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyCheckinController extends Controller
{
    /**
     * Tạo daily check-in
     * POST /api/daily-checkin
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'mood' => 'required|in:excellent,good,average,poor,terrible',
            'energy_level' => 'required|in:high,medium,low',
            'sleep_hours' => 'required|numeric|min:0|max:24',
            'stress_level' => 'required|in:low,medium,high',
            'priorities' => 'required|array|min:1|max:5',
            'priorities.*' => 'string|max:255',
            'goals' => 'array|max:3',
            'goals.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        $date = Carbon::parse($request->date)->startOfDay();

        // Kiểm tra đã có check-in cho ngày này chưa
        $existingCheckin = DailyCheckin::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($existingCheckin) {
            return response()->json([
                'success' => false,
                'message' => 'この日のチェックインは既に完了しています'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Map mood enum to score for backwards compatibility
            $moodScoreMap = [
                'excellent' => 5,
                'good' => 4,
                'average' => 3,
                'poor' => 2,
                'terrible' => 1,
            ];

            $checkin = DailyCheckin::create([
                'user_id' => $user->id,
                'date' => $date,
                'mood' => $request->mood,
                'mood_score' => $moodScoreMap[$request->mood] ?? 3,
                'energy_level' => $request->energy_level,
                'sleep_hours' => $request->sleep_hours,
                'stress_level' => $request->stress_level,
                'priorities' => $request->priorities,  // Model auto-casts to JSON
                'goals' => $request->goals ?? [],      // Model auto-casts to JSON
                'notes' => $request->notes,
                'schedule_note' => $request->notes,    // Keep for backwards compatibility
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $checkin,
                'message' => 'デイリーチェックインを完了しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'チェックインの作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy check-in của ngày hiện tại
     * GET /api/daily-checkin/today
     */
    public function today(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = today();

        $checkin = DailyCheckin::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$checkin) {
            return response()->json([
                'success' => false,
                'message' => '今日のチェックインが見つかりません'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $checkin,
            'message' => '今日のチェックインを取得しました'
        ]);
    }

    /**
     * Lấy check-in theo ngày
     * GET /api/daily-checkin/{date}
     */
    public function show(Request $request, string $date): JsonResponse
    {
        $user = $request->user();
        $checkinDate = Carbon::parse($date)->startOfDay();

        $checkin = DailyCheckin::where('user_id', $user->id)
            ->whereDate('date', $checkinDate)
            ->first();

        if (!$checkin) {
            return response()->json([
                'success' => false,
                'message' => '指定日のチェックインが見つかりません'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $checkin,
            'message' => 'チェックインを取得しました'
        ]);
    }

    /**
     * Cập nhật check-in
     * PUT /api/daily-checkin/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'mood' => 'in:excellent,good,average,poor,terrible',
            'energy_level' => 'in:high,medium,low',
            'sleep_hours' => 'numeric|min:0|max:24',
            'stress_level' => 'in:low,medium,high',
            'priorities' => 'array|min:1|max:5',
            'priorities.*' => 'string|max:255',
            'goals' => 'array|max:3',
            'goals.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $checkin = DailyCheckin::where('user_id', $request->user()->id)
            ->findOrFail($id);

        try {
            $updateData = $request->only([
                'mood', 'energy_level', 'sleep_hours', 'stress_level', 'notes'
            ]);

            // Update mood_score if mood is changed
            if ($request->has('mood')) {
                $moodScoreMap = [
                    'excellent' => 5,
                    'good' => 4,
                    'average' => 3,
                    'poor' => 2,
                    'terrible' => 1,
                ];
                $updateData['mood_score'] = $moodScoreMap[$request->mood] ?? 3;
            }

            // Priorities and goals - model will auto-cast to JSON
            if ($request->has('priorities')) {
                $updateData['priorities'] = $request->priorities;
            }

            if ($request->has('goals')) {
                $updateData['goals'] = $request->goals;
            }

            // Keep schedule_note in sync with notes for backwards compatibility
            if ($request->has('notes')) {
                $updateData['schedule_note'] = $request->notes;
            }

            $checkin->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $checkin,
                'message' => 'チェックインを更新しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'チェックインの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách check-ins
     * GET /api/daily-checkin
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'mood' => 'in:excellent,good,average,poor,terrible',
            'energy_level' => 'in:high,medium,low',
        ]);

        $query = DailyCheckin::where('user_id', $user->id);

        // Filtering
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->has('mood')) {
            $query->where('mood', $request->mood);
        }

        if ($request->has('energy_level')) {
            $query->where('energy_level', $request->energy_level);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['date', 'mood', 'energy_level', 'sleep_hours', 'stress_level'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $checkins = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $checkins,
            'message' => 'チェックイン履歴を取得しました'
        ]);
    }

    /**
     * Lấy check-in statistics
     * GET /api/daily-checkin/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'in:week,month,year,all',
        ]);

        $period = $request->get('period', 'month');

        try {
            $query = DailyCheckin::where('user_id', $user->id);

            // Apply period filter
            switch ($period) {
                case 'week':
                    $query->where('date', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('date', '>=', now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('date', '>=', now()->startOfYear());
                    break;
                case 'all':
                default:
                    // No filter
                    break;
            }

            $checkins = $query->get();

            $stats = [
                'total_checkins' => $checkins->count(),
                'average_mood' => $this->calculateAverageMood($checkins),
                'average_energy' => $this->calculateAverageEnergy($checkins),
                'average_sleep' => $checkins->count() > 0 ? round($checkins->avg('sleep_hours'), 2) : 0,
                'average_stress' => $this->calculateAverageStress($checkins),
                'mood_distribution' => $this->getMoodDistribution($checkins),
                'energy_distribution' => $this->getEnergyDistribution($checkins),
                'stress_distribution' => $this->getStressDistribution($checkins),
                'consistency_score' => $this->calculateConsistencyScore($checkins, $period),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'チェックイン統計を取得しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '統計の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy check-in trends
     * GET /api/daily-checkin/trends
     */
    public function trends(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'required|in:week,month,year',
            'metric' => 'in:mood,energy,sleep,stress',
        ]);

        $period = $request->get('period', 'week');
        $metric = $request->get('metric', 'mood');

        try {
            $startDate = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subWeek(),
            };

            $checkins = DailyCheckin::where('user_id', $user->id)
                ->where('date', '>=', $startDate)
                ->orderBy('date')
                ->get();

            $trends = $checkins->map(function ($checkin) use ($metric) {
                return [
                    'date' => $checkin->date->format('Y-m-d'),
                    'value' => $this->getMetricValue($checkin, $metric),
                    'mood' => $checkin->mood,
                    'energy_level' => $checkin->energy_level,
                    'sleep_hours' => $checkin->sleep_hours,
                    'stress_level' => $checkin->stress_level,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $trends,
                'message' => 'チェックイントレンドを取得しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'トレンドの取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa check-in
     * DELETE /api/daily-checkin/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $checkin = DailyCheckin::where('user_id', $request->user()->id)
            ->findOrFail($id);

        try {
            $checkin->delete();

            return response()->json([
                'success' => true,
                'message' => 'チェックインを削除しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'チェックインの削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate average mood score
     */
    private function calculateAverageMood($checkins)
    {
        if ($checkins->count() === 0) {
            return 0;
        }

        $moodScores = [
            'excellent' => 5,
            'good' => 4,
            'average' => 3,
            'poor' => 2,
            'terrible' => 1,
        ];

        $totalScore = $checkins->sum(function ($checkin) use ($moodScores) {
            return $moodScores[$checkin->mood] ?? 0;
        });

        return round($totalScore / $checkins->count(), 2);
    }

    /**
     * Calculate average energy score
     */
    private function calculateAverageEnergy($checkins)
    {
        if ($checkins->count() === 0) {
            return 0;
        }

        $energyScores = [
            'high' => 3,
            'medium' => 2,
            'low' => 1,
        ];

        $totalScore = $checkins->sum(function ($checkin) use ($energyScores) {
            return $energyScores[$checkin->energy_level] ?? 0;
        });

        return round($totalScore / $checkins->count(), 2);
    }

    /**
     * Calculate average stress score
     */
    private function calculateAverageStress($checkins)
    {
        if ($checkins->count() === 0) {
            return 0;
        }

        $stressScores = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
        ];

        $totalScore = $checkins->sum(function ($checkin) use ($stressScores) {
            return $stressScores[$checkin->stress_level] ?? 0;
        });

        return round($totalScore / $checkins->count(), 2);
    }

    /**
     * Get mood distribution
     */
    private function getMoodDistribution($checkins)
    {
        return [
            'excellent' => $checkins->where('mood', 'excellent')->count(),
            'good' => $checkins->where('mood', 'good')->count(),
            'average' => $checkins->where('mood', 'average')->count(),
            'poor' => $checkins->where('mood', 'poor')->count(),
            'terrible' => $checkins->where('mood', 'terrible')->count(),
        ];
    }

    /**
     * Get energy distribution
     */
    private function getEnergyDistribution($checkins)
    {
        return [
            'high' => $checkins->where('energy_level', 'high')->count(),
            'medium' => $checkins->where('energy_level', 'medium')->count(),
            'low' => $checkins->where('energy_level', 'low')->count(),
        ];
    }

    /**
     * Get stress distribution
     */
    private function getStressDistribution($checkins)
    {
        return [
            'low' => $checkins->where('stress_level', 'low')->count(),
            'medium' => $checkins->where('stress_level', 'medium')->count(),
            'high' => $checkins->where('stress_level', 'high')->count(),
        ];
    }

    /**
     * Calculate consistency score
     */
    private function calculateConsistencyScore($checkins, $period)
    {
        $expectedDays = match ($period) {
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 30,
        };

        $actualDays = $checkins->count();

        return $expectedDays > 0 ? round(($actualDays / $expectedDays) * 100, 2) : 0;
    }

    /**
     * Get metric value for trends
     */
    private function getMetricValue($checkin, $metric)
    {
        return match ($metric) {
            'mood' => $this->getMoodScore($checkin->mood),
            'energy' => $this->getEnergyScore($checkin->energy_level),
            'sleep' => $checkin->sleep_hours,
            'stress' => $this->getStressScore($checkin->stress_level),
            default => 0,
        };
    }

    /**
     * Get mood score
     */
    private function getMoodScore($mood)
    {
        return match ($mood) {
            'excellent' => 5,
            'good' => 4,
            'average' => 3,
            'poor' => 2,
            'terrible' => 1,
            default => 0,
        };
    }

    /**
     * Get energy score
     */
    private function getEnergyScore($energy)
    {
        return match ($energy) {
            'high' => 3,
            'medium' => 2,
            'low' => 1,
            default => 0,
        };
    }

    /**
     * Get stress score
     */
    private function getStressScore($stress)
    {
        return match ($stress) {
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            default => 0,
        };
    }
}
