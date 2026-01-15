// frontend/components/dashboard/RightPanel.tsx
'use client';

import { useEffect, useState, useRef } from 'react';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { aiService } from '@/lib/services/aiService';
import { statsService } from '@/lib/services/statsService';
import { timetableService, TimetableClass, TimetableStudy } from '@/lib/services/timetableService';

interface RightPanelProps {
  currentLang: Language;
  isCollapsed: boolean;
}

export default function RightPanel({ currentLang, isCollapsed }: RightPanelProps) {
  const [aiMessage, setAiMessage] = useState<string>('');
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
  const t = translations[currentLang];

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

  return (
    <aside
      className={`${
        isCollapsed ? 'w-0 -mr-80 opacity-0' : 'w-80'
      } bg-white/10 backdrop-blur-md border-l border-white/20 shadow-xl relative z-10 overflow-y-auto transition-all duration-300 ease-in-out`}
    >
      <div className="p-4 space-y-6">
        {/* AI Assistant Panel */}
        <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
          <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.sidePanelAI}</h2>
          <div className="bg-white/30 backdrop-blur-sm rounded-xl p-4 border border-white/30">
            <div className="flex items-start space-x-3">
              <div className="w-10 h-10 rounded-full bg-gradient-to-br from-[#0FA968] to-[#1F6FEB] flex items-center justify-center flex-shrink-0">
                <Icon icon="mdi:robot" className="text-white" />
              </div>
              <div className="flex-1">
                <p className="text-sm text-white drop-shadow-sm leading-relaxed">
                  <span className="font-semibold">AI Assistant:</span>{' '}
                  {aiMessage ||
                    'Bạn có 1 deadline vào 14:00 hôm nay. Hãy ưu tiên hoàn thành task API integration trước.'}
                </p>
              </div>
            </div>
          </div>
        </div>

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
