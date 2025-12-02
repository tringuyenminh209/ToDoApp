<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\FocusSession;
use App\Models\Project;
use App\Models\PerformanceMetric;
use App\Models\UserStatsCache;
use App\Jobs\UpdateUserStatsCache;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Get user statistics (for mobile app)
     * GET /api/stats/user
     */
    public function getUserStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userId = $user->id;

            // Try to get cached stats first
            $cache = UserStatsCache::where('user_id', $userId)->first();

            // If cache doesn't exist or is stale (older than 5 minutes), calculate fresh stats
            if (!$cache || $cache->isStale(5)) {
                Log::info("Stats cache miss or stale for user {$userId}, dispatching update job");

                // Dispatch job to update cache asynchronously
                UpdateUserStatsCache::dispatch($userId);

                // If no cache exists at all, calculate stats synchronously for first time
                if (!$cache) {
                    Log::info("No cache exists for user {$userId}, calculating stats synchronously");
                    return $this->calculateFreshStats($userId);
                }
            }

            // Get additional stats not in cache (tasks by priority, weekly stats, monthly productivity)
            $tasksByPriority = [
                'high' => Task::where('user_id', $userId)->where('priority', '>=', 4)->count(),
                'medium' => Task::where('user_id', $userId)->where('priority', 3)->count(),
                'low' => Task::where('user_id', $userId)->where('priority', '<=', 2)->count(),
            ];

            $weekStart = Carbon::now()->subDays(6)->startOfDay();
            $weeklyTasks = Task::where('user_id', $userId)
                ->where('status', 'completed')
                ->where('updated_at', '>=', $weekStart)
                ->count();

            $weeklyFocusSessions = FocusSession::where('user_id', $userId)
                ->where('status', 'completed')
                ->where('created_at', '>=', $weekStart)
                ->get();
            $weeklyFocusTime = $weeklyFocusSessions->sum('actual_minutes');

            $daysActive = FocusSession::where('user_id', $userId)
                ->where('status', 'completed')
                ->where('created_at', '>=', $weekStart)
                ->select(DB::raw('DATE(created_at) as date'))
                ->distinct()
                ->count();

            $weeklyStats = [
                'tasks_completed' => $weeklyTasks,
                'focus_time' => $weeklyFocusTime,
                'days_active' => $daysActive,
            ];

            $monthlyProductivity = $this->getMonthlyProductivity($userId);

            // Combine cached stats with additional stats
            $stats = [
                'total_tasks' => $cache->total_tasks,
                'completed_tasks' => $cache->completed_tasks,
                'pending_tasks' => $cache->pending_tasks,
                'in_progress_tasks' => $cache->in_progress_tasks,
                'completion_rate' => (float) $cache->completion_rate,
                'total_focus_time' => $cache->total_focus_time,
                'total_focus_sessions' => $cache->total_focus_sessions,
                'average_session_duration' => $cache->average_session_duration,
                'current_streak' => $cache->current_streak,
                'longest_streak' => $cache->longest_streak,
                'tasks_by_priority' => $tasksByPriority,
                'weekly_stats' => $weeklyStats,
                'monthly_productivity' => $monthlyProductivity,
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("getUserStats error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate fresh stats without cache (fallback method)
     */
    private function calculateFreshStats(int $userId): JsonResponse
    {
        // Original calculation logic
        $totalTasks = Task::where('user_id', $userId)->count();
        $completedTasks = Task::where('user_id', $userId)->where('status', 'completed')->count();
        $pendingTasks = Task::where('user_id', $userId)->where('status', 'pending')->count();
        $inProgressTasks = Task::where('user_id', $userId)->where('status', 'in_progress')->count();

        $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        $focusSessions = FocusSession::where('user_id', $userId)->where('status', 'completed')->get();
        $totalFocusTime = $focusSessions->sum('actual_minutes');
        $totalFocusSessions = $focusSessions->count();
        $averageSessionDuration = $totalFocusSessions > 0
            ? round($totalFocusTime / $totalFocusSessions)
            : 0;

        $streaks = $this->calculateStreaks($userId);

        $tasksByPriority = [
            'high' => Task::where('user_id', $userId)->where('priority', '>=', 4)->count(),
            'medium' => Task::where('user_id', $userId)->where('priority', 3)->count(),
            'low' => Task::where('user_id', $userId)->where('priority', '<=', 2)->count(),
        ];

        $weekStart = Carbon::now()->subDays(6)->startOfDay();
        $weeklyTasks = Task::where('user_id', $userId)
            ->where('status', 'completed')
            ->where('updated_at', '>=', $weekStart)
            ->count();

        $weeklyFocusSessions = FocusSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->where('created_at', '>=', $weekStart)
            ->get();
        $weeklyFocusTime = $weeklyFocusSessions->sum('actual_minutes');

        $daysActive = FocusSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->where('created_at', '>=', $weekStart)
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->count();

        $weeklyStats = [
            'tasks_completed' => $weeklyTasks,
            'focus_time' => $weeklyFocusTime,
            'days_active' => $daysActive,
        ];

        $monthlyProductivity = $this->getMonthlyProductivity($userId);

        $stats = [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'in_progress_tasks' => $inProgressTasks,
            'completion_rate' => round($completionRate, 1),
            'total_focus_time' => $totalFocusTime,
            'total_focus_sessions' => $totalFocusSessions,
            'average_session_duration' => $averageSessionDuration,
            'current_streak' => $streaks['current'],
            'longest_streak' => $streaks['longest'],
            'tasks_by_priority' => $tasksByPriority,
            'weekly_stats' => $weeklyStats,
            'monthly_productivity' => $monthlyProductivity,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistics retrieved successfully'
        ]);
    }

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
                    'very_high' => $tasks->where('priority', 5)->count(),
                    'high' => $tasks->where('priority', 4)->count(),
                    'medium' => $tasks->where('priority', 3)->count(),
                    'low' => $tasks->where('priority', 2)->count(),
                    'very_low' => $tasks->where('priority', 1)->count(),
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
                $query = $this->applyPeriodFilter($query, $period, 'metric_date');
            }

            $metrics = $query->orderBy('metric_date', 'desc')->get();

            // Calculate overall average
            $overallScore = $metrics->count() > 0 ? round($metrics->avg('metric_value'), 2) : 0;

            // Group by metric type to get category scores
            $byType = $metrics->groupBy('metric_type');

            $performanceData = [
                'current_score' => $overallScore,
                'average_score' => $overallScore,
                'trend' => $this->calculatePerformanceTrend($metrics),
                'categories' => [
                    'daily_completion' => $byType->has('daily_completion') ?
                        round($byType['daily_completion']->avg('metric_value'), 2) : 0,
                    'focus_time' => $byType->has('focus_time') ?
                        round($byType['focus_time']->avg('metric_value'), 2) : 0,
                    'mood_trend' => $byType->has('mood_trend') ?
                        round($byType['mood_trend']->avg('metric_value'), 2) : 0,
                    'streak_maintenance' => $byType->has('streak_maintenance') ?
                        round($byType['streak_maintenance']->avg('metric_value'), 2) : 0,
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
     *
     * Today's Progress Logic (Optimized):
     *
     * WHAT COUNTS AS "TODAY'S TASKS":
     * 1. Daily tasks (learning_milestone_id IS NULL) that are:
     *    - Created today (new tasks for today)
     *    - Have deadline today or in the past (due/overdue)
     *    - Currently in progress or pending (not completed or cancelled)
     *
     * 2. Roadmap tasks WITH deadline today (scheduled work)
     *
     * WHAT COUNTS AS "COMPLETED TODAY":
     * - Tasks from above that are completed (regardless of when)
     * - This includes tasks completed early (before deadline)
     *
     * BENEFITS:
     * - Recognizes early completion (task due today but done yesterday)
     * - Shows overdue tasks (motivates user to catch up)
     * - Clear daily workload without roadmap noise
     * - Realistic progress tracking
     */
    private function getTasksStats($user, $today, $thisWeek, $thisMonth)
    {
        $allTasks = Task::where('user_id', $user->id);

        // Today's tasks: What user should work on today
        // IMPORTANT: Only count tasks that are specifically for TODAY
        // - Daily tasks created today OR with deadline exactly today
        // - Roadmap tasks with deadline exactly today
        $todayTasksQuery = Task::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress']) // Only pending/in_progress for total count
            ->where(function($query) use ($today) {
                // Daily tasks: created today OR has deadline = today
                $query->where(function($q) use ($today) {
                    $q->whereNull('learning_milestone_id')
                      ->where(function($subQ) use ($today) {
                          $subQ->whereDate('created_at', $today)
                               ->orWhere(function($deadlineQ) use ($today) {
                                   $deadlineQ->whereNotNull('deadline')
                                             ->whereDate('deadline', $today);
                               });
                      });
                })
                // OR roadmap tasks with deadline today
                ->orWhere(function($q) use ($today) {
                    $q->whereNotNull('learning_milestone_id')
                      ->whereNotNull('deadline')
                      ->whereDate('deadline', $today);
                });
            });

        $totalToday = $todayTasksQuery->count();

        // Completed: Only tasks completed TODAY (updated_at = today)
        // AND match the same criteria as above (created today OR deadline today)
        $todayCompleted = Task::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('updated_at', $today) // ONLY completed today
            ->where(function($query) use ($today) {
                // Daily tasks
                $query->where(function($q) use ($today) {
                    $q->whereNull('learning_milestone_id')
                      ->where(function($subQ) use ($today) {
                          $subQ->whereDate('created_at', $today)
                               ->orWhere(function($deadlineQ) use ($today) {
                                   $deadlineQ->whereNotNull('deadline')
                                             ->whereDate('deadline', $today);
                               });
                      });
                })
                // OR roadmap tasks with deadline today
                ->orWhere(function($q) use ($today) {
                    $q->whereNotNull('learning_milestone_id')
                      ->whereNotNull('deadline')
                      ->whereDate('deadline', $today);
                });
            })
            ->count();

        // Add completed tasks to total
        $totalToday += $todayCompleted;

        // Weekly and Monthly: Get all tasks once and filter in memory
        $allTasksCollection = $allTasks->get();

        $weekTasksCreated = $allTasksCollection->filter(function($task) use ($thisWeek) {
            return $task->created_at >= $thisWeek;
        });
        $weekTasksCompleted = $allTasksCollection->filter(function($task) use ($thisWeek) {
            return $task->status === 'completed' && $task->updated_at >= $thisWeek;
        });

        $monthTasksCreated = $allTasksCollection->filter(function($task) use ($thisMonth) {
            return $task->created_at >= $thisMonth;
        });
        $monthTasksCompleted = $allTasksCollection->filter(function($task) use ($thisMonth) {
            return $task->status === 'completed' && $task->updated_at >= $thisMonth;
        });

        return [
            'today' => [
                'total' => $totalToday,
                'completed' => $todayCompleted,
                'created' => Task::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count(),
            ],
            'this_week' => [
                'total' => $weekTasksCreated->count(),
                'completed' => $weekTasksCompleted->count(),
                'created' => $weekTasksCreated->count(),
            ],
            'this_month' => [
                'total' => $monthTasksCreated->count(),
                'completed' => $monthTasksCompleted->count(),
                'created' => $monthTasksCreated->count(),
            ],
        ];
    }

    /**
     * Lấy sessions stats cho dashboard
     */
    private function getSessionsStats($user, $today, $thisWeek, $thisMonth)
    {
        // Get all completed sessions once, then filter in memory for better performance
        $allSessions = FocusSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $todaySessions = $allSessions->filter(function($session) use ($today) {
            return $session->started_at->isSameDay($today);
        });

        $weekSessions = $allSessions->filter(function($session) use ($thisWeek) {
            return $session->started_at >= $thisWeek;
        });

        $monthSessions = $allSessions->filter(function($session) use ($thisMonth) {
            return $session->started_at >= $thisMonth;
        });

        return [
            'today' => [
                'count' => $todaySessions->count(),
                'minutes' => $todaySessions->sum('actual_minutes'),
            ],
            'this_week' => [
                'count' => $weekSessions->count(),
                'minutes' => $weekSessions->sum('actual_minutes'),
            ],
            'this_month' => [
                'count' => $monthSessions->count(),
                'minutes' => $monthSessions->sum('actual_minutes'),
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
            'paused' => $projects->where('status', 'paused')->count(),
            'average_progress' => $projects->count() > 0 ? round($projects->avg('progress_percentage'), 2) : 0,
        ];
    }

    /**
     * Lấy performance stats cho dashboard
     */
    private function getPerformanceStats($user, $today, $thisWeek, $thisMonth)
    {
        $metrics = PerformanceMetric::where('user_id', $user->id)
            ->orderBy('metric_date', 'desc')
            ->limit(30)
            ->get();

        // Calculate overall score from all metric types
        $overallScore = $metrics->count() > 0 ? round($metrics->avg('metric_value'), 2) : 0;

        // Get recent daily scores (average all metric types per day)
        $recentScores = $metrics
            ->groupBy(function($item) {
                return $item->metric_date->format('Y-m-d');
            })
            ->map(function($dayMetrics) {
                return round($dayMetrics->avg('metric_value'), 2);
            })
            ->take(7)
            ->values();

        return [
            'current_score' => $recentScores->first() ?? 0,
            'average_score' => $overallScore,
            'trend' => $this->calculatePerformanceTrend($metrics),
            'recent_scores' => $recentScores,
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

        // Group by date and calculate daily averages
        $byDate = $metrics->groupBy(function($item) {
            return $item->metric_date->format('Y-m-d');
        })->map(function($dayMetrics) {
            return $dayMetrics->avg('metric_value');
        })->values();

        if ($byDate->count() < 2) {
            return 'stable';
        }

        $recent = $byDate->take(7)->avg();
        $previous = $byDate->skip(7)->take(7)->avg();

        if ($recent > $previous + 5) {
            return 'up';
        } elseif ($recent < $previous - 5) {
            return 'down';
        }

        return 'stable';
    }

    /**
     * Get weekly performance data
     */
    private function getWeeklyPerformanceData($metrics)
    {
        // Group by date and then by metric_type
        $byDate = $metrics->groupBy(function($item) {
            return $item->metric_date->format('Y-m-d');
        });

        return $byDate->take(7)->map(function ($dayMetrics, $date) {
            $byType = $dayMetrics->groupBy('metric_type');

            return [
                'date' => $date,
                'overall_score' => round($dayMetrics->avg('metric_value'), 2),
                'daily_completion' => $byType->has('daily_completion') ?
                    round($byType['daily_completion']->avg('metric_value'), 2) : 0,
                'focus_time' => $byType->has('focus_time') ?
                    round($byType['focus_time']->avg('metric_value'), 2) : 0,
                'mood_trend' => $byType->has('mood_trend') ?
                    round($byType['mood_trend']->avg('metric_value'), 2) : 0,
                'streak_maintenance' => $byType->has('streak_maintenance') ?
                    round($byType['streak_maintenance']->avg('metric_value'), 2) : 0,
            ];
        })->values();
    }

    /**
     * Calculate current streak and longest streak
     *
     * @param int $userId
     * @return array ['current' => int, 'longest' => int]
     */
    private function calculateStreaks(int $userId): array
    {
        // Get all dates with completed tasks, ordered by date descending
        $completedDates = Task::where('user_id', $userId)
            ->where('status', 'completed')
            ->select(DB::raw('DATE(updated_at) as completion_date'))
            ->distinct()
            ->orderBy('completion_date', 'desc')
            ->pluck('completion_date')
            ->toArray();

        if (empty($completedDates)) {
            return ['current' => 0, 'longest' => 0];
        }

        $currentStreak = 0;
        $longestStreak = 0;
        $tempStreak = 1;

        // Check if today or yesterday has a completion
        $today = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        // If most recent completion is not today or yesterday, current streak is 0
        if ($completedDates[0] !== $today && $completedDates[0] !== $yesterday) {
            $currentStreak = 0;
        } else {
            // Calculate current streak
            $currentStreak = 1;
            $lastDate = Carbon::parse($completedDates[0]);

            for ($i = 1; $i < count($completedDates); $i++) {
                $currentDate = Carbon::parse($completedDates[$i]);
                $daysDiff = $lastDate->diffInDays($currentDate);

                if ($daysDiff === 1) {
                    $currentStreak++;
                    $lastDate = $currentDate;
                } else {
                    break;
                }
            }
        }

        // Calculate longest streak
        for ($i = 0; $i < count($completedDates); $i++) {
            if ($i === 0) {
                continue;
            }

            $prevDate = Carbon::parse($completedDates[$i - 1]);
            $currentDate = Carbon::parse($completedDates[$i]);
            $daysDiff = $prevDate->diffInDays($currentDate);

            if ($daysDiff === 1) {
                $tempStreak++;
            } else {
                $longestStreak = max($longestStreak, $tempStreak);
                $tempStreak = 1;
            }
        }

        $longestStreak = max($longestStreak, $tempStreak, $currentStreak);

        return [
            'current' => $currentStreak,
            'longest' => $longestStreak,
        ];
    }

    /**
     * Get monthly productivity data (last 30 days)
     *
     * @param int $userId
     * @return array Array of daily productivity
     */
    private function getMonthlyProductivity(int $userId): array
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Get completed tasks grouped by date
        $completedTasks = Task::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Get focus time grouped by date (use started_at for accurate date grouping)
        $focusTime = FocusSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereBetween('started_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(started_at) as date'),
                DB::raw('SUM(actual_minutes) as minutes')
            )
            ->groupBy('date')
            ->pluck('minutes', 'date')
            ->toArray();

        // Generate array for all 30 days
        $productivity = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $productivity[] = [
                'date' => $date,
                'tasks_completed' => $completedTasks[$date] ?? 0,
                'focus_minutes' => (int)($focusTime[$date] ?? 0),
            ];
        }

        return $productivity;
    }

    /**
     * Get golden time heatmap (hourly productivity by day of week)
     * GET /api/stats/golden-time
     *
     * Returns a 2D array of productivity scores:
     * - Rows: Time slots (0-23 hours, grouped by 2-hour blocks = 12 rows)
     * - Columns: Days of week (Monday-Sunday = 7 columns)
     * - Values: Total focus minutes in that time slot
     */
    public function goldenTime(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userId = $user->id;

            // Get sessions from last 30 days
            $sessions = FocusSession::where('user_id', $userId)
                ->where('status', 'completed')
                ->where('started_at', '>=', Carbon::now()->subDays(29))
                ->get();

            // Initialize heatmap data: 12 time slots x 7 days
            // Time slots: 0-2h, 2-4h, 4-6h, ..., 22-24h (12 slots)
            // Days: Monday(1), Tuesday(2), ..., Sunday(7)
            $heatmap = array_fill(0, 12, array_fill(0, 7, 0));

            // Fill heatmap with actual data
            foreach ($sessions as $session) {
                $dayOfWeek = $session->started_at->dayOfWeekIso; // 1=Monday, 7=Sunday
                $hour = $session->started_at->hour; // 0-23
                $timeSlot = intdiv($hour, 2); // 0-11 (2-hour blocks)

                // Add focus minutes to the corresponding cell
                $heatmap[$timeSlot][$dayOfWeek - 1] += $session->actual_minutes;
            }

            // Calculate max value for normalization
            $maxMinutes = 0;
            foreach ($heatmap as $row) {
                $maxMinutes = max($maxMinutes, max($row));
            }

            // Format response with intensity levels (0-4)
            $formattedHeatmap = [];
            foreach ($heatmap as $timeSlot => $days) {
                $row = [];
                foreach ($days as $minutes) {
                    // Calculate intensity level (0=no activity, 1-4=light to heavy)
                    if ($maxMinutes > 0) {
                        $percentage = ($minutes / $maxMinutes) * 100;
                        $intensity = match(true) {
                            $percentage == 0 => 0,
                            $percentage <= 25 => 1,
                            $percentage <= 50 => 2,
                            $percentage <= 75 => 3,
                            default => 4,
                        };
                    } else {
                        $intensity = 0;
                    }

                    $row[] = [
                        'minutes' => $minutes,
                        'intensity' => $intensity,
                    ];
                }
                $formattedHeatmap[] = $row;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'heatmap' => $formattedHeatmap,
                    'max_minutes' => $maxMinutes,
                    'time_slots' => 12, // 2-hour blocks
                    'days' => 7,
                ],
                'message' => 'ゴールデンタイムデータを取得しました'
            ]);

        } catch (\Exception $e) {
            Log::error('goldenTime error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ゴールデンタイムデータの取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
