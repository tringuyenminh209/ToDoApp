// frontend/lib/services/cheatCodeService.ts
import apiClient from '@/lib/api';

export interface CheatCodeLanguage {
  id: number;
  name: string;
  displayName: string;
  icon?: string;
  color?: string;
  logoUrl?: string;
  description?: string;
  popularity: number;
  category: string;
  sectionsCount: number;
  examplesCount: number;
  exercisesCount: number;
  createdAt?: string;
  updatedAt?: string;
}

export interface CheatCodeSection {
  id: number;
  languageId: number;
  title: string;
  slug: string;
  description?: string;
  icon?: string;
  examplesCount: number;
  examples?: CodeExample[];
  sortOrder: number;
  isPublished: boolean;
  createdAt?: string;
  updatedAt?: string;
}

export interface CodeExample {
  id: number;
  sectionId: number;
  languageId: number;
  title: string;
  slug: string;
  code: string;
  description?: string;
  output?: string;
  difficulty: string;
  tags?: string[];
  viewsCount: number;
  favoritesCount: number;
  sortOrder: number;
  isPublished: boolean;
  createdAt?: string;
  updatedAt?: string;
}

export const cheatCodeService = {
  // 言語一覧取得
  getLanguages: async (params?: { category?: string; search?: string; sort_by?: string; sort_order?: string }) => {
    const response = await apiClient.get('/cheat-code/languages', { params });
    return response.data;
  },

  // 言語詳細取得
  getLanguage: async (identifier: string | number) => {
    const response = await apiClient.get(`/cheat-code/languages/${identifier}`);
    return response.data;
  },

  // セクション一覧取得
  getSections: async (languageId: string | number, params?: { search?: string; sort_by?: string; sort_order?: string }) => {
    const response = await apiClient.get(`/cheat-code/languages/${languageId}/sections`, { params });
    return response.data;
  },

  // セクション詳細取得
  getSection: async (languageId: string | number, sectionId: string | number) => {
    const response = await apiClient.get(`/cheat-code/languages/${languageId}/sections/${sectionId}`);
    return response.data;
  },

  // 例一覧取得
  getExamples: async (languageId: string | number, sectionId: string | number, params?: { difficulty?: string; search?: string; sort_by?: string; sort_order?: string }) => {
    const response = await apiClient.get(`/cheat-code/languages/${languageId}/sections/${sectionId}/examples`, { params });
    return response.data;
  },

  // 例詳細取得
  getExample: async (languageId: string | number, sectionId: string | number, exampleId: string | number) => {
    const response = await apiClient.get(`/cheat-code/languages/${languageId}/sections/${sectionId}/examples/${exampleId}`);
    return response.data;
  },

  // カテゴリ一覧取得
  getCategories: async () => {
    const response = await apiClient.get('/cheat-code/categories');
    return response.data;
  },
};
