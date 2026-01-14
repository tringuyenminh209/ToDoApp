// frontend/lib/services/taskService.ts
import apiClient from '@/lib/api';

export interface Task {
  id: number;
  title: string;
  description?: string;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  priority: number;
  category: 'study' | 'work' | 'personal' | 'other';
  energy_level: 'low' | 'medium' | 'high';
  estimated_minutes?: number;
  total_focus_minutes?: number;
  deadline?: string;
  scheduled_time?: string;
  project_id?: number;
  learning_milestone_id?: number;
  created_at?: string;
  updated_at?: string;
}

export const taskService = {
  // タスク一覧取得
  getTasks: async (params?: {
    status?: string;
    priority?: number;
    category?: string;
    energy_level?: string;
  }) => {
    const response = await apiClient.get('/tasks', { params });
    return response.data;
  },

  // タスク更新
  updateTask: async (id: number, data: Partial<Task>) => {
    const response = await apiClient.put(`/tasks/${id}`, data);
    return response.data;
  },

  // タスク作成
  createTask: async (data: Partial<Task>) => {
    const response = await apiClient.post('/tasks', data);
    return response.data;
  },
};