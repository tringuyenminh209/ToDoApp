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
  status: 'pending' | 'in_progress' | 'completed' | 'skipped';
  progress_percentage: number;
  sort_order: number;
  target_start_date?: string;
  target_end_date?: string;
  estimated_hours?: number;
  actual_hours?: number;
  deliverables?: any[];
  notes?: string;
  tasks?: any[];
  // For visual editor
  position?: { x: number; y: number };
  color?: string;
}

/** タスク作成用（API送信用） */
export interface TaskInput {
  title: string;
  description?: string;
  estimated_minutes?: number;
  priority?: number;
  subtasks?: { title: string }[];
  knowledge_items?: { title: string; content?: string; item_type?: string }[];
}

/** マイルストーン作成用（API送信用） */
export interface MilestoneInput {
  title: string;
  description?: string;
  estimated_hours?: number;
  sort_order?: number;
  position_x?: number;
  position_y?: number;
  tasks?: TaskInput[];
}

/** 学習パス一括作成用（API送信時は milestones に MilestoneInput[] を渡す） */
export interface CreateLearningPathData extends Omit<Partial<LearningPath>, 'milestones'> {
  milestones?: MilestoneInput[];
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

  // 学習パス作成（milestones + tasks + subtasks を一括送信可能）
  createLearningPath: async (data: CreateLearningPathData) => {
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

  // Milestone作成（Learning Pathに含まれる）
  createMilestone: async (pathId: number, data: Partial<LearningMilestone>) => {
    // Note: MilestoneはLearning Pathの更新時に含めるか、別途エンドポイントが必要
    // 現在はLearning Pathの更新時にmilestonesを配列で送信する想定
    const response = await apiClient.post(`/learning-paths/${pathId}/milestones`, data);
    return response.data;
  },
};
