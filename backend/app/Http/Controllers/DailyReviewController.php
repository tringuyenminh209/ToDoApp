<?php

namespace App\Http\Controllers;

use App\Models\DailyReview;
use App\Models\Task;
use App\Models\FocusSession;
use App\Models\PerformanceMetric;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyReviewController extends Controller
{
    /**
     * Tạo daily review
     * POST /api/daily-review
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'mood' => 'required|in:excellent,good,average,poor,terrible',
            'productivity_score' => 'required|integer|min:1|max:10',
            'focus_time_score' => 'required|integer|min:1|max:10',
            'task_completion_score' => 'required|integer|min:1|max:10',
            'goal_achievement_score' => 'required|integer|min:1|max:10',
            'work_life_balance_score' => 'required|integer|min:1|max:10',
            'achievements' => 'required|array|min:1|max:10',
            'achievements.*' => 'string|max:255',
            'challenges' => 'array|max:10',
            'challenges.*' => 'string|max:255',
            'lessons_learned' => 'array|max:5',
            'lessons_learned.*' => 'string|max:255',
            'tomorrow_goals' => 'array|max:5',
            'tomorrow_goals.*' => 'string|max:255',
            'gratitude' => 'array|max:3',
            'gratitude.*' => 'string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $date = Carbon::parse($request->date)->startOfDay();

        // Kiểm tra đã có review cho ngày này chưa
        $existingReview = DailyReview::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'この日のレビューは既に完了しています'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Tính toán overall productivity score
            $overallScore = round((
                $request->productivity_score +
                $request->focus_time_score +
                $request->task_completion_score +
                $request->goal_achievement_score +
                $request->work_life_balance_score
            ) / 5, 2);

            $review = DailyReview::create([
                'user_id' => $user->id,
                'date' => $date,
                'mood' => $request->mood,
                'productivity_score' => $overallScore,
                'focus_time_score' => $request->focus_time_score,
                'task_completion_score' => $request->task_completion_score,
                'goal_achievement_score' => $request->goal_achievement_score,
                'work_life_balance_score' => $request->work_life_balance_score,
                'achievements' => $request->achievements,  // Auto-casts to JSON
                'challenges' => $request->challenges ?? [],
                'lessons_learned' => $request->lessons_learned ?? [],
                'gratitude' => $request->gratitude ?? [],
                'notes' => $request->notes,
                // Backwards compatibility
                'gratitude_note' => $request->notes,
                'challenges_faced' => is_array($request->challenges) && count($request->challenges) > 0
                    ? implode(', ', $request->challenges)
                    : null,
                'tomorrow_goals' => is_array($request->tomorrow_goals) && count($request->tomorrow_goals) > 0
                    ? implode(', ', $request->tomorrow_goals)
                    : null,
            ]);

            // Tạo performance metric records (row-based storage)
            $metrics = [
                ['metric_type' => 'productivity', 'metric_value' => $overallScore * 10], // Convert 1-10 to percentage
                ['metric_type' => 'focus_time', 'metric_value' => $request->focus_time_score * 10],
                ['metric_type' => 'task_completion', 'metric_value' => $request->task_completion_score * 10],
                ['metric_type' => 'goal_achievement', 'metric_value' => $request->goal_achievement_score * 10],
                ['metric_type' => 'work_life_balance', 'metric_value' => $request->work_life_balance_score * 10],
            ];

            foreach ($metrics as $metric) {
                PerformanceMetric::create([
                    'user_id' => $user->id,
                    'metric_date' => $date,  // Note: metric_date, not 'date'
                    'metric_type' => $metric['metric_type'],
                    'metric_value' => $metric['metric_value'],
                    'trend_direction' => 'stable',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $review,
                'message' => 'デイリーレビューを完了しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'レビューの作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy review của ngày hiện tại
     * GET /api/daily-review/today
     */
    public function today(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = today();

        $review = DailyReview::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => '今日のレビューが見つかりません'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => '今日のレビューを取得しました'
        ]);
    }

    /**
     * Lấy review theo ngày
     * GET /api/daily-review/{date}
     */
    public function show(Request $request, string $date): JsonResponse
    {
        $user = $request->user();
        $reviewDate = Carbon::parse($date)->startOfDay();

        $review = DailyReview::where('user_id', $user->id)
            ->whereDate('date', $reviewDate)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => '指定日のレビューが見つかりません'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'レビューを取得しました'
        ]);
    }

    /**
     * Cập nhật review
     * PUT /api/daily-review/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'mood' => 'in:excellent,good,average,poor,terrible',
            'productivity_score' => 'integer|min:1|max:10',
            'focus_time_score' => 'integer|min:1|max:10',
            'task_completion_score' => 'integer|min:1|max:10',
            'goal_achievement_score' => 'integer|max:10',
            'work_life_balance_score' => 'integer|min:1|max:10',
            'achievements' => 'array|min:1|max:10',
            'achievements.*' => 'string|max:255',
            'challenges' => 'array|max:10',
            'challenges.*' => 'string|max:255',
            'lessons_learned' => 'array|max:5',
            'lessons_learned.*' => 'string|max:255',
            'tomorrow_goals' => 'array|max:5',
            'tomorrow_goals.*' => 'string|max:255',
            'gratitude' => 'array|max:3',
            'gratitude.*' => 'string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $review = DailyReview::where('user_id', $request->user()->id)
            ->findOrFail($id);

        try {
            $updateData = $request->only([
                'mood', 'notes'
            ]);

            // Update scores if provided
            if ($request->has('productivity_score') ||
                $request->has('focus_time_score') ||
                $request->has('task_completion_score') ||
                $request->has('goal_achievement_score') ||
                $request->has('work_life_balance_score')) {

                $focusTimeScore = $request->get('focus_time_score', $review->focus_time_score);
                $taskCompletionScore = $request->get('task_completion_score', $review->task_completion_score);
                $goalAchievementScore = $request->get('goal_achievement_score', $review->goal_achievement_score);
                $workLifeBalanceScore = $request->get('work_life_balance_score', $review->work_life_balance_score);

                $overallScore = round((
                    $focusTimeScore +
                    $taskCompletionScore +
                    $goalAchievementScore +
                    $workLifeBalanceScore
                ) / 4, 2);

                $updateData['productivity_score'] = $overallScore;
                $updateData['focus_time_score'] = $focusTimeScore;
                $updateData['task_completion_score'] = $taskCompletionScore;
                $updateData['goal_achievement_score'] = $goalAchievementScore;
                $updateData['work_life_balance_score'] = $workLifeBalanceScore;
            }

            // Update JSON fields (auto-cast by model)
            if ($request->has('achievements')) {
                $updateData['achievements'] = $request->achievements;
            }

            if ($request->has('challenges')) {
                $updateData['challenges'] = $request->challenges;
            }

            if ($request->has('lessons_learned')) {
                $updateData['lessons_learned'] = $request->lessons_learned;
            }

            if ($request->has('tomorrow_goals')) {
                $updateData['tomorrow_goals'] = $request->tomorrow_goals;
            }

            if ($request->has('gratitude')) {
                $updateData['gratitude'] = $request->gratitude;
            }

            $review->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $review,
                'message' => 'レビューを更新しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'レビューの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách reviews
     * GET /api/daily-review
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'mood' => 'in:excellent,good,average,poor,terrible',
            'min_score' => 'integer|min:1|max:10',
            'max_score' => 'integer|min:1|max:10',
        ]);

        $query = DailyReview::where('user_id', $user->id);

        // Filtering
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->has('mood')) {
            $query->where('mood', $request->mood);
        }

        if ($request->has('min_score')) {
            $query->where('productivity_score', '>=', $request->min_score);
        }

        if ($request->has('max_score')) {
            $query->where('productivity_score', '<=', $request->max_score);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['date', 'mood', 'productivity_score', 'focus_time_score', 'task_completion_score'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $reviews = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'レビュー履歴を取得しました'
        ]);
    }

    /**
     * Lấy review statistics
     * GET /api/daily-review/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'in:week,month,year,all',
        ]);

        $period = $request->get('period', 'month');

        try {
            $query = DailyReview::where('user_id', $user->id);

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

            $reviews = $query->get();

            $stats = [
                'total_reviews' => $reviews->count(),
                'average_productivity_score' => $reviews->count() > 0 ? round($reviews->avg('productivity_score'), 2) : 0,
                'average_focus_time_score' => $reviews->count() > 0 ? round($reviews->avg('focus_time_score'), 2) : 0,
                'average_task_completion_score' => $reviews->count() > 0 ? round($reviews->avg('task_completion_score'), 2) : 0,
                'average_goal_achievement_score' => $reviews->count() > 0 ? round($reviews->avg('goal_achievement_score'), 2) : 0,
                'average_work_life_balance_score' => $reviews->count() > 0 ? round($reviews->avg('work_life_balance_score'), 2) : 0,
                'mood_distribution' => $this->getMoodDistribution($reviews),
                'score_distribution' => $this->getScoreDistribution($reviews),
                'consistency_score' => $this->calculateConsistencyScore($reviews, $period),
                'improvement_trend' => $this->calculateImprovementTrend($reviews),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'レビュー統計を取得しました'
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
     * Lấy review trends
     * GET /api/daily-review/trends
     */
    public function trends(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'required|in:week,month,year',
            'metric' => 'in:productivity,focus_time,task_completion,goal_achievement,work_life_balance',
        ]);

        $period = $request->get('period', 'week');
        $metric = $request->get('metric', 'productivity');

        try {
            $startDate = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subWeek(),
            };

            $reviews = DailyReview::where('user_id', $user->id)
                ->where('date', '>=', $startDate)
                ->orderBy('date')
                ->get();

            $trends = $reviews->map(function ($review) use ($metric) {
                return [
                    'date' => $review->date->format('Y-m-d'),
                    'score' => $this->getMetricScore($review, $metric),
                    'mood' => $review->mood,
                    'productivity_score' => $review->productivity_score,
                    'focus_time_score' => $review->focus_time_score,
                    'task_completion_score' => $review->task_completion_score,
                    'goal_achievement_score' => $review->goal_achievement_score,
                    'work_life_balance_score' => $review->work_life_balance_score,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $trends,
                'message' => 'レビュートレンドを取得しました'
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
     * Lấy review insights
     * GET /api/daily-review/insights
     */
    public function insights(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'in:week,month,year,all',
        ]);

        $period = $request->get('period', 'month');

        try {
            $query = DailyReview::where('user_id', $user->id);

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

            $reviews = $query->orderBy('date', 'desc')->get();

            $insights = [
                'best_day' => $this->getBestDay($reviews),
                'worst_day' => $this->getWorstDay($reviews),
                'most_common_mood' => $this->getMostCommonMood($reviews),
                'strongest_area' => $this->getStrongestArea($reviews),
                'weakest_area' => $this->getWeakestArea($reviews),
                'improvement_suggestions' => $this->getImprovementSuggestions($reviews),
                'achievement_patterns' => $this->getAchievementPatterns($reviews),
                'challenge_patterns' => $this->getChallengePatterns($reviews),
            ];

            return response()->json([
                'success' => true,
                'data' => $insights,
                'message' => 'レビューインサイトを取得しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'インサイトの取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa review
     * DELETE /api/daily-review/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $review = DailyReview::where('user_id', $request->user()->id)
            ->findOrFail($id);

        try {
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'レビューを削除しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'レビューの削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mood distribution
     */
    private function getMoodDistribution($reviews)
    {
        return [
            'excellent' => $reviews->where('mood', 'excellent')->count(),
            'good' => $reviews->where('mood', 'good')->count(),
            'average' => $reviews->where('mood', 'average')->count(),
            'poor' => $reviews->where('mood', 'poor')->count(),
            'terrible' => $reviews->where('mood', 'terrible')->count(),
        ];
    }

    /**
     * Get score distribution
     */
    private function getScoreDistribution($reviews)
    {
        $scores = $reviews->pluck('productivity_score');

        return [
            'excellent' => $scores->where('>=', 9)->count(),
            'good' => $scores->where('>=', 7)->where('<', 9)->count(),
            'average' => $scores->where('>=', 5)->where('<', 7)->count(),
            'poor' => $scores->where('>=', 3)->where('<', 5)->count(),
            'terrible' => $scores->where('<', 3)->count(),
        ];
    }

    /**
     * Calculate consistency score
     */
    private function calculateConsistencyScore($reviews, $period)
    {
        $expectedDays = match ($period) {
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 30,
        };

        $actualDays = $reviews->count();

        return $expectedDays > 0 ? round(($actualDays / $expectedDays) * 100, 2) : 0;
    }

    /**
     * Calculate improvement trend
     */
    private function calculateImprovementTrend($reviews)
    {
        if ($reviews->count() < 2) {
            return 'stable';
        }

        $recent = $reviews->take(7)->avg('productivity_score');
        $previous = $reviews->skip(7)->take(7)->avg('productivity_score');

        if ($recent > $previous + 0.5) {
            return 'improving';
        } elseif ($recent < $previous - 0.5) {
            return 'declining';
        }

        return 'stable';
    }

    /**
     * Get metric score for trends
     */
    private function getMetricScore($review, $metric)
    {
        return match ($metric) {
            'productivity' => $review->productivity_score,
            'focus_time' => $review->focus_time_score,
            'task_completion' => $review->task_completion_score,
            'goal_achievement' => $review->goal_achievement_score,
            'work_life_balance' => $review->work_life_balance_score,
            default => $review->productivity_score,
        };
    }

    /**
     * Get best day
     */
    private function getBestDay($reviews)
    {
        if ($reviews->count() === 0) {
            return null;
        }

        $bestReview = $reviews->sortByDesc('productivity_score')->first();

        return [
            'date' => $bestReview->date->format('Y-m-d'),
            'score' => $bestReview->productivity_score,
            'mood' => $bestReview->mood,
        ];
    }

    /**
     * Get worst day
     */
    private function getWorstDay($reviews)
    {
        if ($reviews->count() === 0) {
            return null;
        }

        $worstReview = $reviews->sortBy('productivity_score')->first();

        return [
            'date' => $worstReview->date->format('Y-m-d'),
            'score' => $worstReview->productivity_score,
            'mood' => $worstReview->mood,
        ];
    }

    /**
     * Get most common mood
     */
    private function getMostCommonMood($reviews)
    {
        if ($reviews->count() === 0) {
            return null;
        }

        $moodCounts = $reviews->groupBy('mood')->map->count();
        $mostCommon = $moodCounts->sortDesc()->first();

        return [
            'mood' => $moodCounts->search($mostCommon),
            'count' => $mostCommon,
            'percentage' => round(($mostCommon / $reviews->count()) * 100, 2),
        ];
    }

    /**
     * Get strongest area
     */
    private function getStrongestArea($reviews)
    {
        if ($reviews->count() === 0) {
            return null;
        }

        $averages = [
            'focus_time' => $reviews->avg('focus_time_score'),
            'task_completion' => $reviews->avg('task_completion_score'),
            'goal_achievement' => $reviews->avg('goal_achievement_score'),
            'work_life_balance' => $reviews->avg('work_life_balance_score'),
        ];

        $strongest = collect($averages)->sortDesc()->first();

        return [
            'area' => collect($averages)->search($strongest),
            'score' => round($strongest, 2),
        ];
    }

    /**
     * Get weakest area
     */
    private function getWeakestArea($reviews)
    {
        if ($reviews->count() === 0) {
            return null;
        }

        $averages = [
            'focus_time' => $reviews->avg('focus_time_score'),
            'task_completion' => $reviews->avg('task_completion_score'),
            'goal_achievement' => $reviews->avg('goal_achievement_score'),
            'work_life_balance' => $reviews->avg('work_life_balance_score'),
        ];

        $weakest = collect($averages)->sort()->first();

        return [
            'area' => collect($averages)->search($weakest),
            'score' => round($weakest, 2),
        ];
    }

    /**
     * Get improvement suggestions
     */
    private function getImprovementSuggestions($reviews)
    {
        if ($reviews->count() === 0) {
            return [];
        }

        $suggestions = [];
        $averages = [
            'focus_time' => $reviews->avg('focus_time_score'),
            'task_completion' => $reviews->avg('task_completion_score'),
            'goal_achievement' => $reviews->avg('goal_achievement_score'),
            'work_life_balance' => $reviews->avg('work_life_balance_score'),
        ];

        foreach ($averages as $area => $score) {
            if ($score < 6) {
                $suggestions[] = $this->getAreaSuggestion($area);
            }
        }

        return $suggestions;
    }

    /**
     * Get area-specific suggestion
     */
    private function getAreaSuggestion($area)
    {
        $suggestions = [
            'focus_time' => 'フォーカス時間を増やすために、ポモドーロテクニックを試してみてください',
            'task_completion' => 'タスク完了率を上げるために、小さなタスクに分割してみてください',
            'goal_achievement' => '目標達成率を上げるために、より具体的で測定可能な目標を設定してください',
            'work_life_balance' => 'ワークライフバランスを改善するために、休息時間を確保してください',
        ];

        return $suggestions[$area] ?? '継続的な改善を心がけてください';
    }

    /**
     * Get achievement patterns
     */
    private function getAchievementPatterns($reviews)
    {
        if ($reviews->count() === 0) {
            return [];
        }

        $allAchievements = $reviews->pluck('achievements')
            ->flatten()  // Already arrays from model auto-casting
            ->filter();

        $achievementCounts = $allAchievements->countBy();

        return $achievementCounts->sortDesc()->take(5)->map(function ($count, $achievement) use ($reviews) {
            return [
                'achievement' => $achievement,
                'count' => $count,
                'frequency' => round(($count / $reviews->count()) * 100, 2),
            ];
        })->values()->toArray();
    }

    /**
     * Get challenge patterns
     */
    private function getChallengePatterns($reviews)
    {
        if ($reviews->count() === 0) {
            return [];
        }

        $allChallenges = $reviews->pluck('challenges')
            ->flatten()  // Already arrays from model auto-casting
            ->filter();

        $challengeCounts = $allChallenges->countBy();

        return $challengeCounts->sortDesc()->take(5)->map(function ($count, $challenge) use ($reviews) {
            return [
                'challenge' => $challenge,
                'count' => $count,
                'frequency' => round(($count / $reviews->count()) * 100, 2),
            ];
        })->values()->toArray();
    }
}
