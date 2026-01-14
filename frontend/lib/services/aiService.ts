// frontend/lib/services/aiService.ts
import apiClient from '@/lib/api';

export interface AISuggestion {
  id: number;
  type: string;
  message: string;
  priority: number;
  is_read: boolean;
  created_at: string;
}

export interface DailyPlan {
  suggested_tasks: any[];
  focus_recommendations: any[];
  schedule_suggestions: any[];
}

export const aiService = {
  // デイリー提案取得
  getDailySuggestions: async () => {
    const response = await apiClient.get('/ai/daily-suggestions');
    return response.data;
  },

  // 提案一覧取得
  getSuggestions: async () => {
    const response = await apiClient.get('/ai/suggestions');
    return response.data;
  },

  // 提案を既読にする
  markSuggestionRead: async (id: number) => {
    const response = await apiClient.put(`/ai/suggestions/${id}/read`);
    return response.data;
  },

  // デイリープラン取得
  getDailyPlan: async () => {
    const response = await apiClient.get('/ai/daily-plan');
    return response.data;
  },

  // モチベーションメッセージ取得
  getMotivationalMessage: async () => {
    const response = await apiClient.post('/ai/motivational-message');
    return response.data;
  },
};
