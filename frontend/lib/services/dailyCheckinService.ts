// frontend/lib/services/dailyCheckinService.ts
import apiClient from '@/lib/api';

export interface DailyCheckin {
  id: number;
  user_id: number;
  date: string;
  energy_level: 'low' | 'medium' | 'high';
  mood_score: number;
  mood?: string;
  sleep_hours?: number;
  stress_level?: string;
  schedule_note?: string;
  priorities?: string[];
  goals?: string[];
  notes?: string;
  created_at: string;
}

export const dailyCheckinService = {
  // 今日のチェックイン取得
  getTodayCheckin: async () => {
    const response = await apiClient.get('/daily-checkin/today');
    return response.data;
  },

  // チェックイン作成
  createCheckin: async (data: Partial<DailyCheckin>) => {
    const response = await apiClient.post('/daily-checkin', data);
    return response.data;
  },

  // チェックイン更新
  updateCheckin: async (id: number, data: Partial<DailyCheckin>) => {
    const response = await apiClient.put(`/daily-checkin/${id}`, data);
    return response.data;
  },

  // チェックイン統計取得
  getStats: async () => {
    const response = await apiClient.get('/daily-checkin/stats');
    return response.data;
  },

  // チェックイン一覧取得
  getCheckins: async (params?: { start_date?: string; end_date?: string }) => {
    const response = await apiClient.get('/daily-checkin', { params });
    return response.data;
  },
};
