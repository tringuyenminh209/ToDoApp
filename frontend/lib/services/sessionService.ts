// frontend/lib/services/sessionService.ts
import apiClient from '@/lib/api';

export interface FocusSession {
  id: number;
  user_id: number;
  task_id: number;
  session_type: 'work' | 'break' | 'long_break';
  duration_minutes: number;
  actual_minutes?: number;
  started_at: string;
  ended_at?: string;
  status: 'active' | 'paused' | 'completed';
  notes?: string;
  task?: {
    id: number;
    title: string;
    status: string;
  };
}

export const sessionService = {
  // セッション開始
  startSession: async (data: {
    task_id: number;
    duration_minutes: number;
    session_type: 'work' | 'break' | 'long_break';
  }) => {
    const response = await apiClient.post('/sessions/start', data);
    return response.data;
  },

  // 現在のセッション取得
  getCurrentSession: async () => {
    const response = await apiClient.get('/sessions/current');
    return response.data;
  },

  // セッション停止
  stopSession: async (id: number, data?: { notes?: string; force_complete_task?: boolean }) => {
    const response = await apiClient.put(`/sessions/${id}/stop`, data);
    return response.data;
  },

  // セッション一時停止
  pauseSession: async (id: number) => {
    const response = await apiClient.put(`/sessions/${id}/pause`);
    return response.data;
  },

  // セッション再開
  resumeSession: async (id: number) => {
    const response = await apiClient.put(`/sessions/${id}/resume`);
    return response.data;
  },

  // セッション一覧取得
  getSessions: async (params?: {
    session_type?: string;
    status?: string;
    date?: string;
    task_id?: number;
    per_page?: number;
    sort_by?: string;
    sort_order?: string;
  }) => {
    const response = await apiClient.get('/sessions', { params });
    return response.data;
  },

  // セッション統計取得
  getStats: async () => {
    const response = await apiClient.get('/sessions/stats');
    return response.data;
  },
};
