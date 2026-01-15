import apiClient from '@/lib/api';

export interface LearningPathTemplate {
  id: number;
  title: string;
  description?: string;
  category?: string;
  difficulty?: string;
  estimated_hours_total?: number;
  usage_count?: number;
  is_featured?: boolean;
  icon?: string;
  color?: string;
}

export interface StudyScheduleInput {
  day_of_week: number;
  study_time: string;
  duration_minutes?: number;
}

export const learningPathTemplateService = {
  getTemplates: async (params?: {
    category?: string;
    difficulty?: string;
    featured?: boolean;
    sort_by?: string;
    sort_order?: string;
    per_page?: number;
  }) => {
    const response = await apiClient.get('/learning-path-templates', { params });
    return response.data;
  },

  getFeatured: async () => {
    const response = await apiClient.get('/learning-path-templates/featured');
    return response.data;
  },

  getTemplate: async (id: number) => {
    const response = await apiClient.get(`/learning-path-templates/${id}`);
    return response.data;
  },

  cloneTemplate: async (id: number, study_schedules: StudyScheduleInput[]) => {
    const response = await apiClient.post(`/learning-path-templates/${id}/clone`, { study_schedules });
    return response.data;
  },
};
