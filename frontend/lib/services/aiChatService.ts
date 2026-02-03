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
    // AI 応答が 90 秒以上かかることがあるためタイムアウト 5 分（Cloudflare 等は 100s で切る場合あり）
    const response = await apiClient.post('/ai/chat/conversations', payload, {
      timeout: 300000, // 5 min
    });
    return response.data;
  },

  sendMessage: async (id: number | string, message: string, contextAware = true) => {
    const endpoint = contextAware
      ? `/ai/chat/conversations/${id}/messages/context-aware`
      : `/ai/chat/conversations/${id}/messages`;
    const response = await apiClient.post(endpoint, { message }, { timeout: 300000 });
    return response.data;
  },

  /**
   * Send message with streaming response (Server-Sent Events)
   * @param id Conversation ID
   * @param message User message
   * @param onChunk Callback for each chunk received
   * @param onDone Callback when streaming is complete
   * @param onError Callback for errors
   * @param onCreatedTask Callback when backend created a task from intent (optional)
   */
  sendMessageStream: async (
    id: number | string,
    message: string,
    onChunk: (chunk: string) => void,
    onDone: (fullMessage: string, messageId?: number) => void,
    onError: (error: string) => void,
    onCreatedTask?: (task: Record<string, unknown>) => void
  ) => {
    try {
      // Get API base URL from apiClient
      const baseURL = apiClient.defaults.baseURL || '';
      const token = localStorage.getItem('auth_token');
      
      // Create the request body
      const body = JSON.stringify({ message });
      
      // Use fetch API for streaming
      const response = await fetch(`${baseURL}/ai/chat/conversations/${id}/messages/stream`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
          'Accept': 'text/event-stream',
        },
        body: body,
      });

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({ message: 'Unknown error' }));
        onError(errorData.message || 'ストリーミングリクエストに失敗しました');
        return;
      }

      const reader = response.body?.getReader();
      const decoder = new TextDecoder();
      let buffer = '';
      let fullMessage = '';

      if (!reader) {
        onError('ストリーミングリーダーを取得できませんでした');
        return;
      }

      while (true) {
        const { done, value } = await reader.read();
        
        if (done) {
          break;
        }

        buffer += decoder.decode(value, { stream: true });
        const lines = buffer.split('\n');
        
        // Keep the last incomplete line in buffer
        buffer = lines.pop() || '';

        for (const line of lines) {
          if (line.startsWith('data: ')) {
            try {
              const data = JSON.parse(line.slice(6));
              
              if (data.type === 'chunk') {
                fullMessage += data.content;
                onChunk(data.content);
              } else if (data.type === 'created_task' && data.task && onCreatedTask) {
                onCreatedTask(data.task as Record<string, unknown>);
              } else if (data.type === 'done') {
                if (data.full_content) {
                  fullMessage = data.full_content;
                }
                onDone(fullMessage, data.message_id);
                return;
              } else if (data.type === 'error') {
                onError(data.content);
                return;
              }
            } catch (e) {
              console.error('Failed to parse SSE data:', e);
            }
          }
        }
      }

      // If we exit the loop without a done event, call onDone with what we have
      onDone(fullMessage);
    } catch (error: any) {
      console.error('Streaming error:', error);
      onError(error.message || 'ストリーミング中にエラーが発生しました');
    }
  },
};
