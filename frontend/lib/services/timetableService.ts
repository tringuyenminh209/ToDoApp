// frontend/lib/services/timetableService.ts
import apiClient from '@/lib/api';

export interface TimetableClass {
  id: number;
  user_id: number;
  name: string;
  description?: string;
  day: string;
  period: number;
  start_time: string;
  end_time: string;
  room?: string;
  location?: string;
  instructor?: string;
  color?: string;
  icon?: string;
  notes?: string;
  learning_path_id?: number;
  weekly_content?: TimetableWeeklyContent;
}

export interface TimetableWeeklyContent {
  id: number;
  class_id: number;
  year: number;
  week_number: number;
  content?: string;
  notes?: string;
}

export interface TimetableStudy {
  id: number;
  user_id: number;
  class_id?: number;
  task_id?: number;
  title: string;
  type: 'homework' | 'review';
  due_date: string;
  status: 'pending' | 'in_progress' | 'completed';
  timetable_class?: TimetableClass;
  task?: {
    id: number;
    title: string;
  };
}

export const timetableService = {
  // 時間割取得
  getTimetable: async (params?: { year?: number; week?: number }) => {
    const response = await apiClient.get('/timetable', { params });
    return response.data;
  },

  // クラス一覧取得
  getClasses: async () => {
    const response = await apiClient.get('/timetable/classes');
    return response.data;
  },

  // クラス作成
  createClass: async (data: Partial<TimetableClass>) => {
    const response = await apiClient.post('/timetable/classes', data);
    return response.data;
  },

  // クラス更新
  updateClass: async (id: number, data: Partial<TimetableClass>) => {
    const response = await apiClient.put(`/timetable/classes/${id}`, data);
    return response.data;
  },

  // クラス削除
  deleteClass: async (id: number) => {
    const response = await apiClient.delete(`/timetable/classes/${id}`);
    return response.data;
  },

  // 学習（宿題/復習）取得
  getStudies: async () => {
    const response = await apiClient.get('/timetable/studies');
    return response.data;
  },

  // 学習作成
  createStudy: async (data: Partial<TimetableStudy>) => {
    const response = await apiClient.post('/timetable/studies', data);
    return response.data;
  },
};
