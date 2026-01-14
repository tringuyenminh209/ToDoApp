// frontend/lib/services/knowledgeService.ts
import apiClient from '@/lib/api';

export interface KnowledgeItem {
  id: number;
  user_id: number;
  category_id?: number;
  title: string;
  item_type: 'note' | 'code_snippet' | 'exercise' | 'resource_link' | 'attachment';
  content?: string;
  code_language?: string;
  url?: string;
  question?: string;
  answer?: string;
  difficulty?: 'easy' | 'medium' | 'hard';
  tags?: string[];
  learning_path_id?: number;
  source_task_id?: number;
  review_count?: number;
  last_reviewed_at?: string;
  next_review_date?: string;
  retention_score?: number;
  ai_summary?: string;
  view_count?: number;
  is_favorite?: boolean;
  is_archived?: boolean;
  created_at?: string;
  updated_at?: string;
  category?: KnowledgeCategory;
  learning_path?: any;
  source_task?: any;
}

export interface KnowledgeCategory {
  id: number;
  user_id: number;
  parent_id?: number;
  name: string;
  description?: string;
  sort_order?: number;
  color?: string;
  icon?: string;
  item_count?: number;
  children?: KnowledgeCategory[];
}

export const knowledgeService = {
  // ナレッジアイテム一覧取得
  getKnowledgeItems: async (params?: {
    type?: string;
    category_id?: number;
    learning_path_id?: number;
    source_task_id?: number;
    search?: string;
    sort_by?: string;
    sort_order?: 'asc' | 'desc';
    due_review?: boolean;
  }) => {
    const response = await apiClient.get('/knowledge', { params });
    return response.data;
  },

  // ナレッジアイテム詳細取得
  getKnowledgeItem: async (id: number) => {
    const response = await apiClient.get(`/knowledge/${id}`);
    return response.data;
  },

  // ナレッジアイテム作成
  createKnowledgeItem: async (data: Partial<KnowledgeItem>) => {
    const response = await apiClient.post('/knowledge', data);
    return response.data;
  },

  // ナレッジアイテム更新
  updateKnowledgeItem: async (id: number, data: Partial<KnowledgeItem>) => {
    const response = await apiClient.put(`/knowledge/${id}`, data);
    return response.data;
  },

  // ナレッジアイテム削除
  deleteKnowledgeItem: async (id: number) => {
    const response = await apiClient.delete(`/knowledge/${id}`);
    return response.data;
  },

  // カテゴリ一覧取得
  getCategories: async () => {
    const response = await apiClient.get('/knowledge/categories');
    return response.data;
  },

  // カテゴリツリー取得
  getCategoryTree: async () => {
    const response = await apiClient.get('/knowledge/categories/tree');
    return response.data;
  },

  // カテゴリ作成
  createCategory: async (data: Partial<KnowledgeCategory>) => {
    const response = await apiClient.post('/knowledge/categories', data);
    return response.data;
  },

  // カテゴリ更新
  updateCategory: async (id: number, data: Partial<KnowledgeCategory>) => {
    const response = await apiClient.put(`/knowledge/categories/${id}`, data);
    return response.data;
  },

  // カテゴリ削除
  deleteCategory: async (id: number) => {
    const response = await apiClient.delete(`/knowledge/categories/${id}`);
    return response.data;
  },

  // 統計取得
  getStats: async () => {
    const response = await apiClient.get('/knowledge/stats');
    return response.data;
  },

  // レビュー対象取得
  getDueReview: async () => {
    const response = await apiClient.get('/knowledge/due-review');
    return response.data;
  },
};
