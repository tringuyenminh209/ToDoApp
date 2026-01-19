import apiClient from '@/lib/api';

export interface UserSettingsPayload {
  theme?: 'light' | 'dark' | 'auto';
  default_focus_minutes?: number;
  pomodoro_duration?: number;
  break_minutes?: number;
  long_break_minutes?: number;
  auto_start_break?: boolean;
  block_notifications?: boolean;
  background_sound?: boolean;
  daily_target_tasks?: number;
  notification_enabled?: boolean;
  push_notifications?: boolean;
  daily_reminders?: boolean;
  goal_reminders?: boolean;
  reminder_times?: string[];
  language?: 'vi' | 'en' | 'ja';
  timezone?: string;
}

export const settingsService = {
  getSettings: async () => {
    const response = await apiClient.get('/settings');
    return response.data;
  },

  updateSettings: async (payload: UserSettingsPayload) => {
    const response = await apiClient.put('/settings', payload);
    return response.data;
  },

  resetSettings: async () => {
    const response = await apiClient.post('/settings/reset');
    return response.data;
  },
};
