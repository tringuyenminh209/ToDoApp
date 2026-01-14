// frontend/lib/services/learningPathService.ts
import apiClient from '@/lib/api';

export interface LearningPath {
  id: number;
  user_id: number;
  title: string;
  description?: string;
  goal_type: 'career' | 'skill' | 'certification' | 'hobby';
  status: 'active' | 'paused' | 'completed' | 'abandoned';
  progress_percentage: number;
  target_start_date?: string;
  target_end_date?: string;
  estimated_hours_total?: number;
  tags?: string[];
  color?: string;
  icon?: string;
  milestones?: LearningMilestone[];
}

export interface LearningMilestone {
  id: number;
  learning_path_id: number;
  title: string;
  description?: string;
  status: string;
  progress_percentage: number;
  target_start_date?: string;
  target_end_date?: string;
}

export const learningPathService = {
  // 学習パス一覧取得
  getLearningPaths: async (params?: {
    status?: string;
    goal_type?: string;
  }) => {
    const response = await apiClient.get('/learning-paths', { params });
    return response.data;
  },

  // 学習パス詳細取得
  getLearningPath: async (id: number) => {
    const response = await apiClient.get(`/learning-paths/${id}`);
    return response.data;
  },

  // 学習パス作成
  createLearningPath: async (data: Partial<LearningPath>) => {
    const response = await apiClient.post('/learning-paths', data);
    return response.data;
  },

  // 学習パス更新
  updateLearningPath: async (id: number, data: Partial<LearningPath>) => {
    const response = await apiClient.put(`/learning-paths/${id}`, data);
    return response.data;
  },

  // 学習パス完了
  completeLearningPath: async (id: number) => {
    const response = await apiClient.put(`/learning-paths/${id}/complete`);
    return response.data;
  },

  // 学習パス統計取得
  getStats: async () => {
    const response = await apiClient.get('/learning-paths/stats');
    return response.data;
  },
};
