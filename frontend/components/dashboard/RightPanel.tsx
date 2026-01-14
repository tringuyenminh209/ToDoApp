// frontend/components/dashboard/RightPanel.tsx
'use client';

import { useEffect, useState } from 'react';
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
  const [todayStats, setTodayStats] = useState<any>(null);
  const [schedule, setSchedule] = useState<(TimetableClass | TimetableStudy)[]>([]);
  const [loading, setLoading] = useState(true);
  const t = translations[currentLang];

  useEffect(() => {
    const loadData = async () => {
      try {
        // AIメッセージ取得
        try {
          const aiData = await aiService.getDailySuggestions();
          if (aiData.success && aiData.data && aiData.data.length > 0) {
            setAiMessage(aiData.data[0].message || '');
          } else if (aiData.data && Array.isArray(aiData.data) && aiData.data.length > 0) {
            setAiMessage(aiData.data[0].message || '');
          }
        } catch (error) {
          console.error('Failed to load AI suggestions:', error);
        }

        // 今日の統計取得
        try {
          const statsData = await statsService.getSessionStats();
          if (statsData.success && statsData.data) {
            setTodayStats(statsData.data.today);
          } else if (statsData.data) {
            setTodayStats(statsData.data.today);
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
            
            // 次の2つの予定を表示
            setSchedule(allSchedule.slice(0, 2));
          } else if (timetableData.data) {
            // レスポンス構造が異なる場合のフォールバック
            const classes = Array.isArray(timetableData.data.classes) ? timetableData.data.classes : [];
            const studies = Array.isArray(timetableData.data.studies) ? timetableData.data.studies : [];
            setSchedule([...classes, ...studies].slice(0, 2));
          }
        } catch (error) {
          console.error('Failed to load timetable:', error);
        }
      } finally {
        setLoading(false);
      }
    };

    loadData();
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
                    ? `${Math.floor(todayStats.total_minutes / 60)}h ${todayStats.total_minutes % 60}m`
                    : '0h 0m'}
                </span>
              </div>
              <div className="flex items-center justify-between text-sm text-white/90 mb-2">
                <span>{t.target}:</span>
                <span className="font-semibold">4h 00m</span>
              </div>
              <div className="w-full bg-white/20 rounded-full h-3 mt-3">
                <div
                  className="bg-[#0FA968] h-3 rounded-full"
                  style={{
                    width: todayStats
                      ? `${Math.min((todayStats.total_minutes / 240) * 100, 100)}%`
                      : '0%',
                  }}
                ></div>
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
                  <div
                    key={item.id || index}
                    className="flex items-center space-x-3 p-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20"
                  >
                    <div className="w-12 h-12 rounded-lg bg-[#0FA968]/20 flex items-center justify-center flex-shrink-0">
                      <span className="text-white font-bold text-sm">{timeStr}</span>
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-white font-medium truncate">{title}</p>
                      <p className="text-xs text-white/70 truncate">{location}</p>
                    </div>
                  </div>
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
    </aside>
  );
}
