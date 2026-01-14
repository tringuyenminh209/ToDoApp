// frontend/lib/services/exerciseService.ts
import apiClient from '@/lib/api';

export interface Exercise {
  id: number;
  languageId: number;
  title: string;
  slug: string;
  description?: string;
  question: string;
  difficulty: string;
  points: number;
  tags?: string[];
  timeLimit?: number;
  submissionsCount: number;
  successCount: number;
  successRate: number;
  sortOrder: number;
  createdAt?: string;
  updatedAt?: string;
}

export interface ExerciseTestCase {
  id: number;
  exerciseId: number;
  input: string;
  expectedOutput: string;
  isHidden: boolean;
  sortOrder: number;
}

export interface ExerciseDetail extends Exercise {
  testCases: ExerciseTestCase[];
  starterCode?: string;
  hints?: string[];
}

export interface ExerciseSubmission {
  code: string;
  language?: string;
}

export const exerciseService = {
  // 演習一覧取得
  getExercises: async (languageId: string | number, params?: { difficulty?: string; search?: string; sort_by?: string; sort_order?: string }) => {
    const response = await apiClient.get(`/cheat-code/languages/${languageId}/exercises`, { params });
    return response.data;
  },

  // 演習詳細取得
  getExercise: async (languageId: string | number, exerciseId: string | number) => {
    const response = await apiClient.get(`/cheat-code/languages/${languageId}/exercises/${exerciseId}`);
    return response.data;
  },

  // 解答取得
  getSolution: async (languageId: string | number, exerciseId: string | number) => {
    const response = await apiClient.get(`/cheat-code/languages/${languageId}/exercises/${exerciseId}/solution`);
    return response.data;
  },

  // 統計取得
  getStatistics: async (languageId: string | number, exerciseId: string | number) => {
    const response = await apiClient.get(`/cheat-code/languages/${languageId}/exercises/${exerciseId}/statistics`);
    return response.data;
  },

  // 解答提出
  submitSolution: async (languageId: string | number, exerciseId: string | number, data: ExerciseSubmission) => {
    const response = await apiClient.post(`/cheat-code/languages/${languageId}/exercises/${exerciseId}/submit`, data);
    return response.data;
  },
};
