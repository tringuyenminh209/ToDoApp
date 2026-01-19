// frontend/components/dashboard/RightPanel.tsx
'use client';

import { useEffect, useState, useRef } from 'react';
import { Icon } from '@iconify/react';
import { AiLogo } from '@/components/ui/AiLogo';
import { translations, type Language } from '@/lib/i18n';
import { aiService } from '@/lib/services/aiService';
import { aiChatService, type ChatMessage } from '@/lib/services/aiChatService';
import { statsService } from '@/lib/services/statsService';
import { timetableService, TimetableClass, TimetableStudy } from '@/lib/services/timetableService';

interface RightPanelProps {
  currentLang: Language;
  isCollapsed: boolean;
}

export default function RightPanel({ currentLang, isCollapsed }: RightPanelProps) {
  const [aiMessage, setAiMessage] = useState<string>('');
  const [chatMessages, setChatMessages] = useState<ChatMessage[]>([]);
  const [chatInput, setChatInput] = useState('');
  const [chatLoading, setChatLoading] = useState(true);
  const [chatSending, setChatSending] = useState(false);
  const [chatError, setChatError] = useState<string | null>(null);
  const [conversationId, setConversationId] = useState<number | null>(null);
  const [isChatExpanded, setIsChatExpanded] = useState(false);
  const [isHistoryOpen, setIsHistoryOpen] = useState(false);
  const [historyLoading, setHistoryLoading] = useState(false);
  const [historyItems, setHistoryItems] = useState<any[]>([]);
  const [todayStats, setTodayStats] = useState<{
    total_minutes: number;
    sessions_count: number;
  } | null>(null);
  const [schedule, setSchedule] = useState<(TimetableClass | TimetableStudy)[]>([]);
  const [loading, setLoading] = useState(true);
  const [showScheduleDetail, setShowScheduleDetail] = useState(false);
  const [selectedSchedule, setSelectedSchedule] = useState<TimetableClass | TimetableStudy | null>(null);
  const isLoadingAIRef = useRef(false);
  const lastAILoadTimeRef = useRef<number>(0);
  const chatEndRef = useRef<HTMLDivElement | null>(null);
  const t = translations[currentLang];

  const normalizeMessages = (messages: ChatMessage[]) => {
    return [...messages].sort((a, b) => {
      const idA = typeof a.id === 'number' ? a.id : 0;
      const idB = typeof b.id === 'number' ? b.id : 0;
      if (idA && idB) return idA - idB;
      const timeA = a.created_at ? new Date(a.created_at).getTime() : 0;
      const timeB = b.created_at ? new Date(b.created_at).getTime() : 0;
      return timeA - timeB;
    });
  };

  const appendMessage = (messages: ChatMessage[], message?: ChatMessage | null) => {
    if (!message?.content) return messages;
    const last = messages[messages.length - 1];
    if (last && last.role === message.role && last.content === message.content) return messages;
    return [...messages, message];
  };

  const openScheduleDetail = (item: TimetableClass | TimetableStudy) => {
    setSelectedSchedule(item);
    setShowScheduleDetail(true);
  };

  const closeScheduleDetail = () => {
    setShowScheduleDetail(false);
    setSelectedSchedule(null);
  };

  useEffect(() => {
    const loadData = async () => {
      try {
        // AIメッセージ取得（レート制限を避けるため、最後のリクエストから1分以上経過している場合のみ）
        const now = Date.now();
        const oneMinute = 60 * 1000;
        if (!isLoadingAIRef.current && (now - lastAILoadTimeRef.current > oneMinute || lastAILoadTimeRef.current === 0)) {
          try {
            isLoadingAIRef.current = true;
            lastAILoadTimeRef.current = now;
            const aiData = await aiService.getDailySuggestions();
            if (aiData.success && aiData.data) {
              // レスポンス構造に応じてメッセージを取得
              if (aiData.data.suggestions && Array.isArray(aiData.data.suggestions) && aiData.data.suggestions.length > 0) {
                // suggestions配列から最初のメッセージを取得
                const firstSuggestion = aiData.data.suggestions[0];
                if (typeof firstSuggestion === 'string') {
                  setAiMessage(firstSuggestion);
                } else if (firstSuggestion && firstSuggestion.message) {
                  setAiMessage(firstSuggestion.message);
                } else if (firstSuggestion && firstSuggestion.content) {
                  setAiMessage(firstSuggestion.content);
                }
              } else if (Array.isArray(aiData.data) && aiData.data.length > 0) {
                const firstItem = aiData.data[0];
                if (typeof firstItem === 'string') {
                  setAiMessage(firstItem);
                } else if (firstItem.message) {
                  setAiMessage(firstItem.message);
                } else if (firstItem.content) {
                  setAiMessage(firstItem.content);
                }
              } else if (aiData.data.message) {
                setAiMessage(aiData.data.message);
              }
            }
          } catch (error: any) {
            console.error('Failed to load AI suggestions:', error);
            // 429エラー（レート制限）の場合は、最後のリクエスト時間をリセットしない
            if (error.response?.status === 429) {
              console.warn('AI suggestions rate limited. Will retry after cooldown period.');
              // レート制限の場合は、次回のリクエストを遅らせる
              lastAILoadTimeRef.current = now - oneMinute + 30000; // 30秒後に再試行可能にする
            }
            // エラーが発生してもデフォルトメッセージを表示
            // エラーメッセージは設定しない（デフォルトメッセージを使用）
          } finally {
            isLoadingAIRef.current = false;
          }
        }

        // 今日の統計取得
        try {
          const statsData = await statsService.getSessionStats();
          if (statsData.success && statsData.data && statsData.data.today) {
            setTodayStats({
              total_minutes: statsData.data.today.total_minutes || 0,
              sessions_count: statsData.data.today.sessions_count || 0,
            });
          } else if (statsData.data && statsData.data.today) {
            setTodayStats({
              total_minutes: statsData.data.today.total_minutes || 0,
              sessions_count: statsData.data.today.sessions_count || 0,
            });
          }
        } catch (error) {
          console.error('Failed to load stats:', error);
          setTodayStats({ total_minutes: 0, sessions_count: 0 });
        }

        // スケジュール取得
        try {
          const timetableData = await timetableService.getTimetable();
          if (timetableData.success && timetableData.data) {
            const classes = timetableData.data.classes || [];
            const studies = timetableData.data.studies || [];
            
            // 今日の曜日を取得
            const today = new Date();
            const dayIndex = today.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            const dayNames: string[] = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            const currentDayName = dayNames[dayIndex] || 'monday';
            
            // 今日のクラスをフィルタリング
            const todayClasses = classes.filter((cls: TimetableClass) => cls.day === currentDayName);
            
            // 時間順にソート
            const sortedClasses = todayClasses.sort((a: TimetableClass, b: TimetableClass) => {
              const timeA = a.start_time || '00:00:00';
              const timeB = b.start_time || '00:00:00';
              return timeA.localeCompare(timeB);
            });
            
            // 今日の学習（studies）をフィルタリング（due_dateが今日）
            const todayStudies = studies.filter((study: TimetableStudy) => {
              if (!study.due_date) return false;
              const studyDate = new Date(study.due_date);
              const todayDate = new Date();
              return studyDate.toDateString() === todayDate.toDateString();
            });
            
            // 時間順にソート
            const sortedStudies = todayStudies.sort((a: TimetableStudy, b: TimetableStudy) => {
              const timeA = a.due_date || '';
              const timeB = b.due_date || '';
              return timeA.localeCompare(timeB);
            });
            
            // クラスと学習を結合して時間順にソート
            const allSchedule: (TimetableClass | TimetableStudy)[] = [...sortedClasses, ...sortedStudies];
            allSchedule.sort((a, b) => {
              const timeA = 'start_time' in a ? a.start_time : ('due_date' in a ? a.due_date : '');
              const timeB = 'start_time' in b ? b.start_time : ('due_date' in b ? b.due_date : '');
              return timeA.localeCompare(timeB);
            });
            
            // 今日の全予定を表示
            setSchedule(allSchedule);
          } else if (timetableData.data) {
            // レスポンス構造が異なる場合のフォールバック
            const classes = Array.isArray(timetableData.data.classes) ? timetableData.data.classes : [];
            const studies = Array.isArray(timetableData.data.studies) ? timetableData.data.studies : [];
            setSchedule([...classes, ...studies]);
          }
        } catch (error) {
          console.error('Failed to load timetable:', error);
        }
      } finally {
        setLoading(false);
      }
    };

    loadData();

    // 定期的に統計を更新（5分ごと）- AIリクエストは含めない
    const interval = setInterval(() => {
      // AIリクエストは別途制御するため、ここでは統計とスケジュールのみ更新
      const updateStatsAndSchedule = async () => {
        try {
          // 今日の統計取得
          try {
            const statsData = await statsService.getSessionStats();
            if (statsData.success && statsData.data && statsData.data.today) {
              setTodayStats({
                total_minutes: statsData.data.today.total_minutes || 0,
                sessions_count: statsData.data.today.sessions_count || 0,
              });
            } else if (statsData.data && statsData.data.today) {
              setTodayStats({
                total_minutes: statsData.data.today.total_minutes || 0,
                sessions_count: statsData.data.today.sessions_count || 0,
              });
            }
          } catch (error) {
            console.error('Failed to load stats:', error);
          }

          // スケジュール取得
          try {
            const timetableData = await timetableService.getTimetable();
            if (timetableData.success && timetableData.data) {
              const classes = timetableData.data.classes || [];
              const studies = timetableData.data.studies || [];
              
              const today = new Date();
              const dayIndex = today.getDay();
              const dayNames: string[] = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
              const currentDayName = dayNames[dayIndex] || 'monday';
              
              const todayClasses = classes.filter((cls: TimetableClass) => cls.day === currentDayName);
              const sortedClasses = todayClasses.sort((a: TimetableClass, b: TimetableClass) => {
                const timeA = a.start_time || '00:00:00';
                const timeB = b.start_time || '00:00:00';
                return timeA.localeCompare(timeB);
              });
              
              const todayStudies = studies.filter((study: TimetableStudy) => {
                if (!study.due_date) return false;
                const studyDate = new Date(study.due_date);
                const todayDate = new Date();
                return studyDate.toDateString() === todayDate.toDateString();
              });
              
              const sortedStudies = todayStudies.sort((a: TimetableStudy, b: TimetableStudy) => {
                const timeA = a.due_date || '';
                const timeB = b.due_date || '';
                return timeA.localeCompare(timeB);
              });
              
              const allSchedule: (TimetableClass | TimetableStudy)[] = [...sortedClasses, ...sortedStudies];
              allSchedule.sort((a, b) => {
                const timeA = 'start_time' in a ? a.start_time : ('due_date' in a ? a.due_date : '');
                const timeB = 'start_time' in b ? b.start_time : ('due_date' in b ? b.due_date : '');
                return timeA.localeCompare(timeB);
              });
              
              setSchedule(allSchedule);
            }
          } catch (error) {
            console.error('Failed to load timetable:', error);
          }

          // AIリクエストは別途制御（レート制限を考慮）
          const now = Date.now();
          const oneMinute = 60 * 1000;
          if (!isLoadingAIRef.current && (now - lastAILoadTimeRef.current > oneMinute || lastAILoadTimeRef.current === 0)) {
            try {
              isLoadingAIRef.current = true;
              lastAILoadTimeRef.current = now;
              const aiData = await aiService.getDailySuggestions();
              if (aiData.success && aiData.data) {
                if (aiData.data.suggestions && Array.isArray(aiData.data.suggestions) && aiData.data.suggestions.length > 0) {
                  const firstSuggestion = aiData.data.suggestions[0];
                  if (typeof firstSuggestion === 'string') {
                    setAiMessage(firstSuggestion);
                  } else if (firstSuggestion && firstSuggestion.message) {
                    setAiMessage(firstSuggestion.message);
                  } else if (firstSuggestion && firstSuggestion.content) {
                    setAiMessage(firstSuggestion.content);
                  }
                } else if (Array.isArray(aiData.data) && aiData.data.length > 0) {
                  const firstItem = aiData.data[0];
                  if (typeof firstItem === 'string') {
                    setAiMessage(firstItem);
                  } else if (firstItem.message) {
                    setAiMessage(firstItem.message);
                  } else if (firstItem.content) {
                    setAiMessage(firstItem.content);
                  }
                } else if (aiData.data.message) {
                  setAiMessage(aiData.data.message);
                }
              }
            } catch (error: any) {
              if (error.response?.status === 429) {
                console.warn('AI suggestions rate limited. Will retry after cooldown period.');
                lastAILoadTimeRef.current = now - oneMinute + 30000;
              }
            } finally {
              isLoadingAIRef.current = false;
            }
          }
        } catch (error) {
          console.error('Failed to update stats and schedule:', error);
        }
      };
      updateStatsAndSchedule();
    }, 5 * 60 * 1000);

    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    const loadLatestConversation = async () => {
      setChatLoading(true);
      setChatError(null);
      try {
        const conversations = await aiChatService.getConversations({
          per_page: 1,
          sort_by: 'last_message_at',
          sort_order: 'desc',
        });
        const latestConversation = conversations?.data?.data?.[0];
        if (latestConversation?.id) {
          const conversationDetail = await aiChatService.getConversation(latestConversation.id);
          const conversationData = conversationDetail?.data || latestConversation;
          setConversationId(latestConversation.id);
          setChatMessages(normalizeMessages(conversationData.messages || []));
        } else {
          setConversationId(null);
          setChatMessages([]);
        }
      } catch (error) {
        console.error('Failed to load chat conversations:', error);
        setChatError(t.aiChatError);
      } finally {
        setChatLoading(false);
      }
    };

    loadLatestConversation();
  }, []);

  useEffect(() => {
    chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [chatMessages, chatSending]);

  useEffect(() => {
    const originalOverflow = document.body.style.overflow;
    if (isChatExpanded) {
      document.body.style.overflow = 'hidden';
    }
    return () => {
      document.body.style.overflow = originalOverflow;
    };
  }, [isChatExpanded]);

  const handleNewConversation = () => {
    setConversationId(null);
    setChatMessages([]);
    setChatInput('');
    setChatError(null);
  };

  const loadConversationById = async (id: number) => {
    setChatLoading(true);
    try {
      const conversationDetail = await aiChatService.getConversation(id);
      const conversationData = conversationDetail?.data;
      setConversationId(id);
      setChatMessages(normalizeMessages(conversationData?.messages || []));
    } catch (error) {
      console.error('Failed to load conversation:', error);
      setChatError(t.aiChatError);
    } finally {
      setChatLoading(false);
    }
  };

  const openHistory = async () => {
    setIsHistoryOpen(true);
    setHistoryLoading(true);
    try {
      const response = await aiChatService.getConversations({
        per_page: 20,
        sort_by: 'last_message_at',
        sort_order: 'desc',
      });
      setHistoryItems(response?.data?.data || []);
    } catch (error) {
      console.error('Failed to load chat history:', error);
      setChatError(t.aiChatError);
    } finally {
      setHistoryLoading(false);
    }
  };

  const handleSendMessage = async () => {
    const trimmed = chatInput.trim();
    if (!trimmed || chatSending) return;

    setChatSending(true);
    setChatError(null);
    setChatInput('');

    const optimisticUserMessage: ChatMessage = {
      id: `local-${Date.now()}`,
      role: 'user',
      content: trimmed,
    };
    setChatMessages((prev) => appendMessage(prev, optimisticUserMessage));

    try {
      if (!conversationId) {
        const created = await aiChatService.createConversation({ message: trimmed });
        const conversation = created?.data?.conversation;
        if (conversation?.id) {
          setConversationId(conversation.id);
          setChatMessages(normalizeMessages(conversation.messages || []));
        } else {
          setChatMessages((prev) =>
            appendMessage(prev, { role: 'assistant', content: created?.message || t.aiChatFallback })
          );
        }
      } else {
        const sent = await aiChatService.sendMessage(conversationId, trimmed, true);
        const assistantMessage = sent?.data?.assistant_message;
        if (assistantMessage?.content) {
          setChatMessages((prev) => appendMessage(prev, assistantMessage));
        } else {
          setChatMessages((prev) => appendMessage(prev, { role: 'assistant', content: t.aiChatFallback }));
        }
      }
    } catch (error) {
      console.error('Failed to send chat message:', error);
      setChatError(t.aiChatError);
      setChatMessages((prev) => appendMessage(prev, { role: 'assistant', content: t.aiChatRetry }));
    } finally {
      setChatSending(false);
    }
  };

  const chatPanelContent = (
    <div
      className={`bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl ${
        isChatExpanded ? 'w-[90vw] max-w-5xl' : ''
      }`}
      onClick={(event) => event.stopPropagation()}
    >
      <div className="flex items-center justify-end mb-4">
        <div className="flex items-center gap-2">
          <button
            onClick={openHistory}
            className="w-9 h-9 rounded-lg text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center transition"
            aria-label={t.aiChatHistory}
            title={t.aiChatHistory}
          >
            <Icon icon="mdi:history" className="text-base" />
          </button>
          <button
            onClick={() => setIsChatExpanded((prev) => !prev)}
            className="w-9 h-9 rounded-lg text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center transition"
            aria-label={isChatExpanded ? t.aiChatCollapse : t.aiChatExpand}
            title={isChatExpanded ? t.aiChatCollapse : t.aiChatExpand}
          >
            <Icon icon={isChatExpanded ? 'mdi:arrow-collapse' : 'mdi:arrow-expand'} className="text-base" />
          </button>
          <button
            onClick={handleNewConversation}
            className="w-9 h-9 rounded-lg text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center transition"
            aria-label={t.aiChatNew}
            title={t.aiChatNew}
          >
            <Icon icon="mdi:plus" className="text-base" />
          </button>
        </div>
      </div>
      <div
        className={`bg-white/30 backdrop-blur-sm rounded-xl p-4 border border-white/30 flex flex-col ${
          isChatExpanded ? 'h-[70vh]' : 'h-[360px]'
        }`}
      >
        <div className="flex-1 overflow-y-auto pr-1 space-y-3">
          {chatLoading ? (
            <div className="text-white/70 text-sm text-center py-6">{t.aiChatLoading}</div>
          ) : chatMessages.length > 0 ? (
            chatMessages.map((message, index) => {
              const isUser = message.role === 'user';
              return (
                <div key={message.id || index} className={`flex ${isUser ? 'justify-end' : 'justify-start'}`}>
                  <div
                    className={`max-w-[75%] rounded-xl px-3 py-2 text-sm leading-relaxed ${
                      isUser ? 'bg-[#1F6FEB]/80 text-white' : 'bg-white/10 text-white/90 border border-white/10'
                    }`}
                  >
                    {message.content}
                  </div>
                </div>
              );
            })
          ) : (
            <div className="text-white/80 text-sm space-y-3">
              <div className="flex items-start space-x-3">
                <div className="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                  <AiLogo size={26} className="drop-shadow-sm" title={t.aiChatTitle} />
                </div>
                <div className="flex-1">
                  <p className="font-semibold text-white">{t.aiChatEmptyTitle}</p>
                  <p className="text-xs text-white/70 mt-1">{t.aiChatEmptyBody}</p>
                </div>
              </div>
              {aiMessage && (
                <div className="bg-white/10 rounded-lg p-3 border border-white/10 text-xs text-white/80">
                  <span className="font-semibold">{t.aiChatAssistantLabel}</span> {aiMessage}
                </div>
              )}
            </div>
          )}
          {chatSending && (
            <div className="flex items-start space-x-2">
              <div className="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center">
                <AiLogo size={16} className="drop-shadow-sm" title={t.aiChatTitle} />
              </div>
              <div className="bg-white/10 text-white/70 text-xs px-3 py-2 rounded-lg animate-pulse">
                {t.aiChatTyping}
              </div>
            </div>
          )}
          <div ref={chatEndRef} />
        </div>
        <div className="pt-3 mt-3 border-t border-white/20">
          <div className="flex items-end gap-2">
            <textarea
              value={chatInput}
              onChange={(event) => setChatInput(event.target.value)}
              onKeyDown={(event) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                  event.preventDefault();
                  handleSendMessage();
                }
              }}
              rows={2}
              placeholder={t.aiChatPlaceholder}
              className="flex-1 resize-none bg-white/10 text-white placeholder:text-white/50 text-sm rounded-lg border border-white/20 focus:outline-none focus:ring-2 focus:ring-[#1F6FEB]/60 px-3 py-2"
            />
            <button
              onClick={handleSendMessage}
              disabled={!chatInput.trim() || chatSending}
              className="w-10 h-10 rounded-lg bg-[#1F6FEB] hover:bg-[#1E40AF] disabled:bg-white/20 disabled:text-white/40 text-white flex items-center justify-center transition"
              aria-label={t.aiChatSend}
              title={t.aiChatSend}
            >
              <Icon icon="mdi:send" />
            </button>
          </div>
          <div className="flex items-center justify-between mt-2 text-xs text-white/60">
            <span>{t.aiChatHint}</span>
            {chatError && <span className="text-red-300">{chatError}</span>}
          </div>
        </div>
      </div>
    </div>
  );

  return (
    <aside
      className={`${
        isCollapsed ? 'w-0 -mr-80 opacity-0' : 'w-80'
      } bg-white/10 backdrop-blur-md border-l border-white/20 shadow-xl relative z-10 overflow-y-auto transition-all duration-300 ease-in-out`}
    >
      <div className="p-4 space-y-6">
        {/* AI Assistant Panel */}
        {isChatExpanded ? (
          <div
            className="fixed inset-0 z-[9998] bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-6"
            onClick={() => setIsChatExpanded(false)}
          >
            {chatPanelContent}
          </div>
        ) : (
          chatPanelContent
        )}
        {isHistoryOpen && (
          <div
            className="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-6"
            onClick={() => setIsHistoryOpen(false)}
          >
            <div
              className="w-full max-w-lg bg-[#0B1220] rounded-2xl p-5 border border-white/20 shadow-2xl"
              onClick={(event) => event.stopPropagation()}
            >
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-bold text-white">{t.aiChatHistory}</h3>
                <button
                  onClick={() => setIsHistoryOpen(false)}
                  className="text-white/70 hover:text-white"
                  aria-label={t.close}
                  title={t.close}
                >
                  <Icon icon="mdi:close" />
                </button>
              </div>
              <div className="space-y-2 max-h-[60vh] overflow-y-auto pr-1">
                {historyLoading ? (
                  <div className="text-white/70 text-sm text-center py-6">{t.aiChatLoading}</div>
                ) : historyItems.length > 0 ? (
                  historyItems.map((item) => (
                    <button
                      key={item.id}
                      onClick={() => {
                        setIsHistoryOpen(false);
                        loadConversationById(item.id);
                      }}
                      className="w-full text-left p-3 bg-white/10 hover:bg-white/20 rounded-xl border border-white/10 transition"
                    >
                      <div className="text-sm text-white font-semibold truncate">
                        {item.title || t.aiChatUntitled}
                      </div>
                      {item.messages?.[0]?.content && (
                        <div className="text-xs text-white/60 truncate mt-1">{item.messages[0].content}</div>
                      )}
                      {item.last_message_at && (
                        <div className="text-[11px] text-white/40 mt-1">{item.last_message_at}</div>
                      )}
                    </button>
                  ))
                ) : (
                  <div className="text-white/60 text-sm text-center py-6">{t.aiChatNoHistory}</div>
                )}
              </div>
            </div>
          </div>
        )}

        {/* Today Stats */}
        <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
          <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md flex items-center">
            <Icon icon="mdi:chart-line" className="mr-2" />
            {t.todayStats}
          </h2>
          <div className="space-y-4">
            <div>
              <div className="flex items-center justify-between text-sm text-white/90 mb-2">
                <span>{t.focus}:</span>
                <span className="font-semibold">
                  {todayStats
                    ? `${Math.floor(todayStats.total_minutes / 60)}h ${String(todayStats.total_minutes % 60).padStart(2, '0')}m`
                    : '0h 00m'}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Schedule */}
        <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
          <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md flex items-center">
            <Icon icon="mdi:calendar-alt" className="mr-2" />
            {t.schedule}
          </h2>
          <div className="space-y-3">
            {loading ? (
              <div className="text-white/70 text-sm text-center py-4">
                {currentLang === 'ja' ? '読み込み中...' : currentLang === 'en' ? 'Loading...' : 'Đang tải...'}
              </div>
            ) : schedule.length > 0 ? (
              schedule.map((item, index) => {
                // 時間を取得
                let timeStr = '10:00';
                if ('start_time' in item && item.start_time) {
                  // start_time形式: "09:15:00" または "09:15"
                  timeStr = item.start_time.substring(0, 5);
                } else if ('due_date' in item && item.due_date) {
                  // due_date形式: ISO日時文字列
                  const date = new Date(item.due_date);
                  timeStr = date.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                  });
                }
                
                // タイトルを取得
                const title = 'name' in item ? item.name : ('title' in item ? item.title : '');
                
                // 場所/説明を取得
                const location = 'room' in item 
                  ? item.room 
                  : ('location' in item 
                    ? item.location 
                    : ('timetable_class' in item && item.timetable_class?.name
                      ? item.timetable_class.name
                      : currentLang === 'ja' ? 'クラス' : currentLang === 'en' ? 'Class' : 'Lớp học'));
                
                return (
                  <button
                    key={item.id || index}
                    onClick={() => openScheduleDetail(item)}
                    className="w-full text-left flex items-center space-x-3 p-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 hover:bg-white/15 transition"
                    aria-label={title}
                    title={title}
                  >
                    <div className="w-12 h-12 rounded-lg bg-[#0FA968]/20 flex items-center justify-center flex-shrink-0">
                      <span className="text-white font-bold text-sm">{timeStr}</span>
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-white font-medium truncate">{title}</p>
                      <p className="text-xs text-white/70 truncate">{location}</p>
                    </div>
                  </button>
                );
              })
            ) : (
              <div className="text-white/70 text-sm text-center py-4">
                {t.noSchedule}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Schedule Detail Modal */}
      {showScheduleDetail && selectedSchedule && (
        <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center px-4">
          <div className="w-full max-w-md bg-[#0B1220] rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-bold text-white">
                {'start_time' in selectedSchedule ? t.classDetails : t.studiesTitle}
              </h3>
              <button
                onClick={closeScheduleDetail}
                className="text-white/70 hover:text-white"
                aria-label={t.close}
                title={t.close}
              >
                <Icon icon="mdi:close" />
              </button>
            </div>
            <div className="space-y-3 text-white/90 text-sm">
              {'start_time' in selectedSchedule ? (
                <>
                  <div className="flex items-center justify-between">
                    <span className="text-white/60">{t.className}</span>
                    <span className="font-semibold">{selectedSchedule.name}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-white/60">{t.time}</span>
                    <span>
                      {selectedSchedule.start_time} - {selectedSchedule.end_time}
                    </span>
                  </div>
                  {selectedSchedule.room && (
                    <div className="flex items-center justify-between">
                      <span className="text-white/60">{t.room}</span>
                      <span>{selectedSchedule.room}</span>
                    </div>
                  )}
                  {selectedSchedule.instructor && (
                    <div className="flex items-center justify-between">
                      <span className="text-white/60">{t.instructor}</span>
                      <span>{selectedSchedule.instructor}</span>
                    </div>
                  )}
                  {(selectedSchedule.notes || selectedSchedule.weekly_content?.content) && (
                    <div>
                      <div className="text-white/60 mb-1">{t.weeklyContent}</div>
                      <div className="bg-white/10 rounded-lg p-3 border border-white/10">
                        {selectedSchedule.weekly_content?.content || selectedSchedule.notes}
                      </div>
                    </div>
                  )}
                </>
              ) : (
                <>
                  <div className="flex items-center justify-between">
                    <span className="text-white/60">{t.className}</span>
                    <span className="font-semibold">{selectedSchedule.title}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-white/60">{t.time}</span>
                    <span>{selectedSchedule.due_date}</span>
                  </div>
                  {selectedSchedule.timetable_class?.name && (
                    <div className="flex items-center justify-between">
                      <span className="text-white/60">{t.classDetails}</span>
                      <span>{selectedSchedule.timetable_class.name}</span>
                    </div>
                  )}
                </>
              )}
            </div>
            <div className="mt-6 flex items-center justify-end">
              <button
                onClick={closeScheduleDetail}
                className="px-4 py-2 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition text-sm"
              >
                {t.close}
              </button>
            </div>
          </div>
        </div>
      )}
    </aside>
  );
}
