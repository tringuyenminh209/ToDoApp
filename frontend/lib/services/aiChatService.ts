import apiClient from '@/lib/api';

export type ChatRole = 'user' | 'assistant' | 'system';

export interface ChatMessage {
  id?: number | string;
  role: ChatRole;
  content: string;
  created_at?: string;
}

export interface ChatConversation {
  id: number;
  title?: string | null;
  status?: string;
  last_message_at?: string;
  messages?: ChatMessage[];
}

export const aiChatService = {
  getConversations: async (params?: {
    status?: string;
    per_page?: number;
    sort_by?: string;
    sort_order?: 'asc' | 'desc';
  }) => {
    const response = await apiClient.get('/ai/chat/conversations', { params });
    return response.data;
  },

  getConversation: async (id: number | string) => {
    const response = await apiClient.get(`/ai/chat/conversations/${id}`);
    return response.data;
  },

  createConversation: async (payload: { title?: string; message: string }) => {
    const response = await apiClient.post('/ai/chat/conversations', payload);
    return response.data;
  },

  sendMessage: async (id: number | string, message: string, contextAware = true) => {
    const endpoint = contextAware
      ? `/ai/chat/conversations/${id}/messages/context-aware`
      : `/ai/chat/conversations/${id}/messages`;
    const response = await apiClient.post(endpoint, { message });
    return response.data;
  },
};
