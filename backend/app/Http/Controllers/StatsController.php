<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\FocusSession;
use App\Models\Project;
use App\Models\PerformanceMetric;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Lấy dashboard stats tổng quan
     * GET /api/stats/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = today();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        try {
            // Tasks stats
            $tasksStats = $this->getTasksStats($user, $today, $thisWeek, $thisMonth);

            // Focus sessions stats
            $sessionsStats = $this->getSessionsStats($user, $today, $thisWeek, $thisMonth);

            // Projects stats
            $projectsStats = $this->getProjectsStats($user);

            // Performance metrics
            $performanceStats = $this->getPerformanceStats($user, $today, $thisWeek, $thisMonth);

            $dashboardData = [
                'tasks' => $tasksStats,
                'sessions' => $sessionsStats,
                'projects' => $projectsStats,
                'performance' => $performanceStats,
                'generated_at' => now()->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboardData,
                'message' => 'ダッシュボード統計を取得しました'
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
     * Lấy task statistics
     * GET /api/stats/tasks
     */
    public function tasks(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'in:today,week,month,year,all',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
        ]);

        $period = $request->get('period', 'week');
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        try {
            $query = Task::where('user_id', $user->id);

            // Apply date filters
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } else {
                $query = $this->applyPeriodFilter($query, $period);
            }

            $tasks = $query->get();

            $stats = [
                'total_tasks' => $tasks->count(),
                'completed_tasks' => $tasks->where('status', 'completed')->count(),
                'in_progress_tasks' => $tasks->where('status', 'in_progress')->count(),
                'pending_tasks' => $tasks->where('status', 'pending')->count(),
                'overdue_tasks' => $tasks->filter(function ($task) {
                    return $task->deadline && $task->deadline < now() && $task->status !== 'completed';
                })->count(),
                'completion_rate' => $tasks->count() > 0 ?
                    round(($tasks->where('status', 'completed')->count() / $tasks->count()) * 100, 2) : 0,
                'average_completion_time' => $this->calculateAverageCompletionTime($tasks),
                'priority_distribution' => [
                    'high' => $tasks->where('priority', 'high')->count(),
                    'medium' => $tasks->where('priority', 'medium')->count(),
                    'low' => $tasks->where('priority', 'low')->count(),
                ],
                'energy_level_distribution' => [
                    'high' => $tasks->where('energy_level', 'high')->count(),
                    'medium' => $tasks->where('energy_level', 'medium')->count(),
                    'low' => $tasks->where('energy_level', 'low')->count(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'タスク統計を取得しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'タスク統計の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy focus session statistics
     * GET /api/stats/sessions
     */
    public function sessions(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'in:today,week,month,year,all',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
        ]);

        $period = $request->get('period', 'week');
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        try {
            $query = FocusSession::where('user_id', $user->id)
                ->where('status', 'completed');

            // Apply date filters
            if ($startDate && $endDate) {
                $query->whereBetween('started_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } else {
                $query = $this->applyPeriodFilter($query, $period, 'started_at');
            }

            $sessions = $query->get();

            $stats = [
                'total_sessions' => $sessions->count(),
                'total_minutes' => $sessions->sum('actual_minutes'),
                'total_hours' => round($sessions->sum('actual_minutes') / 60, 2),
                'average_session_duration' => $sessions->count() > 0 ?
                    round($sessions->avg('actual_minutes'), 2) : 0,
                'session_types' => [
                    'work' => $sessions->where('session_type', 'work')->count(),
                    'break' => $sessions->where('session_type', 'break')->count(),
                    'long_break' => $sessions->where('session_type', 'long_break')->count(),
                ],
                'daily_average' => $this->calculateDailyAverage($sessions, $period),
                'efficiency_score' => $this->calculateEfficiencyScore($sessions),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'フォーカスセッション統計を取得しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'セッション統計の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy productivity trends
     * GET /api/stats/trends
     */
    public function trends(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'required|in:week,month,year',
            'metric' => 'in:tasks,sessions,productivity',
        ]);

        $period = $request->get('period', 'week');
        $metric = $request->get('metric', 'productivity');

        try {
            $trends = [];

            switch ($metric) {
                case 'tasks':
                    $trends = $this->getTaskTrends($user, $period);
                    break;
                case 'sessions':
                    $trends = $this->getSessionTrends($user, $period);
                    break;
                case 'productivity':
                default:
                    $trends = $this->getProductivityTrends($user, $period);
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $trends,
                'message' => 'トレンドデータを取得しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'トレンドデータの取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy performance metrics
     * GET /api/stats/performance
     */
    public function performance(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'period' => 'in:week,month,year,all',
        ]);

        $period = $request->get('period', 'month');

        try {
            $query = PerformanceMetric::where('user_id', $user->id);

            if ($period !== 'all') {
                $query = $this->applyPeriodFilter($query, $period, 'date');
            }

            $metrics = $query->orderBy('date', 'desc')->get();

            $performanceData = [
                'current_score' => $metrics->first()->productivity_score ?? 0,
                'average_score' => $metrics->count() > 0 ? round($metrics->avg('productivity_score'), 2) : 0,
                'trend' => $this->calculatePerformanceTrend($metrics),
                'categories' => [
                    'focus_time' => $metrics->avg('focus_time_score') ?? 0,
                    'task_completion' => $metrics->avg('task_completion_score') ?? 0,
                    'goal_achievement' => $metrics->avg('goal_achievement_score') ?? 0,
                    'work_life_balance' => $metrics->avg('work_life_balance_score') ?? 0,
                ],
                'weekly_data' => $this->getWeeklyPerformanceData($metrics),
            ];

            return response()->json([
                'success' => true,
                'data' => $performanceData,
                'message' => 'パフォーマンス指標を取得しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'パフォーマンス指標の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy tasks stats cho dashboard
     */
    private function getTasksStats($user, $today, $thisWeek, $thisMonth)
    {
        $allTasks = Task::where('user_id', $user->id);
        $todayTasks = clone $allTasks;
        $weekTasks = clone $allTasks;
        $monthTasks = clone $allTasks;

        return [
            'today' => [
                'total' => $todayTasks->whereDate('created_at', $today)->count(),
                'completed' => $todayTasks->whereDate('updated_at', $today)->where('status', 'completed')->count(),
                'created' => $todayTasks->whereDate('created_at', $today)->count(),
            ],
            'this_week' => [
                'total' => $weekTasks->where('created_at', '>=', $thisWeek)->count(),
                'completed' => $weekTasks->where('updated_at', '>=', $thisWeek)->where('status', 'completed')->count(),
                'created' => $weekTasks->where('created_at', '>=', $thisWeek)->count(),
            ],
            'this_month' => [
                'total' => $monthTasks->where('created_at', '>=', $thisMonth)->count(),
                'completed' => $monthTasks->where('updated_at', '>=', $thisMonth)->where('status', 'completed')->count(),
                'created' => $monthTasks->where('created_at', '>=', $thisMonth)->count(),
            ],
        ];
    }

    /**
     * Lấy sessions stats cho dashboard
     */
    private function getSessionsStats($user, $today, $thisWeek, $thisMonth)
    {
        $allSessions = FocusSession::where('user_id', $user->id)->where('status', 'completed');
        $todaySessions = clone $allSessions;
        $weekSessions = clone $allSessions;
        $monthSessions = clone $allSessions;

        return [
            'today' => [
                'count' => $todaySessions->whereDate('started_at', $today)->count(),
                'minutes' => $todaySessions->whereDate('started_at', $today)->sum('actual_minutes'),
            ],
            'this_week' => [
                'count' => $weekSessions->where('started_at', '>=', $thisWeek)->count(),
                'minutes' => $weekSessions->where('started_at', '>=', $thisWeek)->sum('actual_minutes'),
            ],
            'this_month' => [
                'count' => $monthSessions->where('started_at', '>=', $thisMonth)->count(),
                'minutes' => $monthSessions->where('started_at', '>=', $thisMonth)->sum('actual_minutes'),
            ],
        ];
    }

    /**
     * Lấy projects stats cho dashboard
     */
    private function getProjectsStats($user)
    {
        $projects = Project::where('user_id', $user->id)->get();

        return [
            'total' => $projects->count(),
            'active' => $projects->where('status', 'active')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'on_hold' => $projects->where('status', 'on_hold')->count(),
            'average_progress' => $projects->count() > 0 ? round($projects->avg('progress'), 2) : 0,
        ];
    }

    /**
     * Lấy performance stats cho dashboard
     */
    private function getPerformanceStats($user, $today, $thisWeek, $thisMonth)
    {
        $metrics = PerformanceMetric::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return [
            'current_score' => $metrics->first()->productivity_score ?? 0,
            'average_score' => $metrics->count() > 0 ? round($metrics->avg('productivity_score'), 2) : 0,
            'trend' => $this->calculatePerformanceTrend($metrics),
            'recent_scores' => $metrics->take(7)->pluck('productivity_score'),
        ];
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, $period, $dateField = 'created_at')
    {
        switch ($period) {
            case 'today':
                return $query->whereDate($dateField, today());
            case 'week':
                return $query->where($dateField, '>=', now()->startOfWeek());
            case 'month':
                return $query->where($dateField, '>=', now()->startOfMonth());
            case 'year':
                return $query->where($dateField, '>=', now()->startOfYear());
            case 'all':
            default:
                return $query;
        }
    }

    /**
     * Calculate average completion time
     */
    private function calculateAverageCompletionTime($tasks)
    {
        $completedTasks = $tasks->where('status', 'completed')->whereNotNull('updated_at');

        if ($completedTasks->count() === 0) {
            return 0;
        }

        $totalHours = $completedTasks->sum(function ($task) {
            return $task->created_at->diffInHours($task->updated_at);
        });

        return round($totalHours / $completedTasks->count(), 2);
    }

    /**
     * Calculate daily average
     */
    private function calculateDailyAverage($sessions, $period)
    {
        $days = match ($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 7,
        };

        return $days > 0 ? round($sessions->sum('actual_minutes') / $days, 2) : 0;
    }

    /**
     * Calculate efficiency score
     */
    private function calculateEfficiencyScore($sessions)
    {
        if ($sessions->count() === 0) {
            return 0;
        }

        $totalPlanned = $sessions->sum('duration_minutes');
        $totalActual = $sessions->sum('actual_minutes');

        if ($totalPlanned === 0) {
            return 0;
        }

        // Efficiency = (actual / planned) * 100, capped at 100%
        return min(round(($totalActual / $totalPlanned) * 100, 2), 100);
    }

    /**
     * Get task trends
     */
    private function getTaskTrends($user, $period)
    {
        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };

        $tasks = Task::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($task) use ($period) {
                return $task->created_at->format($period === 'week' ? 'Y-m-d' : ($period === 'month' ? 'Y-m' : 'Y'));
            });

        return $tasks->map(function ($dayTasks, $date) {
            return [
                'date' => $date,
                'created' => $dayTasks->count(),
                'completed' => $dayTasks->where('status', 'completed')->count(),
            ];
        })->values();
    }

    /**
     * Get session trends
     */
    private function getSessionTrends($user, $period)
    {
        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };

        $sessions = FocusSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('started_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($session) use ($period) {
                return $session->started_at->format($period === 'week' ? 'Y-m-d' : ($period === 'month' ? 'Y-m' : 'Y'));
            });

        return $sessions->map(function ($daySessions, $date) {
            return [
                'date' => $date,
                'count' => $daySessions->count(),
                'minutes' => $daySessions->sum('actual_minutes'),
            ];
        })->values();
    }

    /**
     * Get productivity trends
     */
    private function getProductivityTrends($user, $period)
    {
        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        };

        $metrics = PerformanceMetric::where('user_id', $user->id)
            ->where('date', '>=', $startDate)
            ->orderBy('date')
            ->get();

        return $metrics->map(function ($metric) {
            return [
                'date' => $metric->date->format('Y-m-d'),
                'score' => $metric->productivity_score,
            ];
        });
    }

    /**
     * Calculate performance trend
     */
    private function calculatePerformanceTrend($metrics)
    {
        if ($metrics->count() < 2) {
            return 'stable';
        }

        $recent = $metrics->take(7)->avg('productivity_score');
        $previous = $metrics->skip(7)->take(7)->avg('productivity_score');

        if ($recent > $previous + 5) {
            return 'improving';
        } elseif ($recent < $previous - 5) {
            return 'declining';
        }

        return 'stable';
    }

    /**
     * Get weekly performance data
     */
    private function getWeeklyPerformanceData($metrics)
    {
        return $metrics->take(7)->map(function ($metric) {
            return [
                'date' => $metric->date->format('Y-m-d'),
                'score' => $metric->productivity_score,
                'focus_time' => $metric->focus_time_score,
                'task_completion' => $metric->task_completion_score,
                'goal_achievement' => $metric->goal_achievement_score,
                'work_life_balance' => $metric->work_life_balance_score,
            ];
        });
    }
}
