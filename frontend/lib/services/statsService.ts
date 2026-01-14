// frontend/lib/services/statsService.ts
import apiClient from '@/lib/api';

export interface DashboardStats {
  today: {
    sessions_count: number;
    total_minutes: number;
    work_sessions: number;
    break_sessions: number;
  };
  this_week: {
    sessions_count: number;
    total_minutes: number;
    work_sessions: number;
    break_sessions: number;
  };
  this_month: {
    sessions_count: number;
    total_minutes: number;
    work_sessions: number;
    break_sessions: number;
  };
}

export interface UserStats {
  total_tasks: number;
  completed_tasks: number;
  pending_tasks: number;
  in_progress_tasks: number;
  completion_rate: number;
  total_focus_time: number;
  total_focus_sessions: number;
  average_session_duration: number;
  current_streak: number;
  longest_streak: number;
  tasks_by_priority: {
    high: number;
    medium: number;
    low: number;
  };
  weekly_stats: {
    tasks_completed: number;
    focus_time: number;
    days_active: number;
  };
}

export const statsService = {
  // ダッシュボード統計取得
  getDashboardStats: async () => {
    const response = await apiClient.get('/stats/dashboard');
    return response.data;
  },

  // ユーザー統計取得
  getUserStats: async () => {
    const response = await apiClient.get('/stats/user');
    return response.data;
  },

  // セッション統計取得
  getSessionStats: async () => {
    const response = await apiClient.get('/sessions/stats');
    return response.data;
  },
};
