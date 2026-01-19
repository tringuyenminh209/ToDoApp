'use client';

import { useEffect, useMemo, useState } from 'react';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { statsService, type DashboardStats, type UserStats } from '@/lib/services/statsService';

const formatMinutes = (minutes: number) => {
  const safeMinutes = Number.isFinite(minutes) ? minutes : 0;
  const hours = Math.floor(safeMinutes / 60);
  const mins = Math.round(safeMinutes % 60);
  return `${hours}h ${String(mins).padStart(2, '0')}m`;
};

export default function AnalyticsPage() {
  const [currentLang] = useState<Language>('ja');
  const t = useMemo(() => translations[currentLang], [currentLang]);
  const [dashboardStats, setDashboardStats] = useState<any | null>(null);
  const [userStats, setUserStats] = useState<UserStats | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [errorMessage, setErrorMessage] = useState('');

  useEffect(() => {
    const loadAnalytics = async () => {
      setIsLoading(true);
      setErrorMessage('');
      try {
        const [dashboardResponse, userResponse] = await Promise.all([
          statsService.getDashboardStats(),
          statsService.getUserStats(),
        ]);
        if (!dashboardResponse?.success || !userResponse?.success) {
          throw new Error('Failed to load analytics');
        }
        setDashboardStats(dashboardResponse.data);
        setUserStats(userResponse.data);
      } catch (error) {
        console.error('Failed to load analytics:', error);
        setErrorMessage(t.analyticsLoadError);
      } finally {
        setIsLoading(false);
      }
    };

    loadAnalytics();
  }, []);

  return (
    <div className="p-6">
      <div className="mb-6">
        <h1 className="text-3xl font-bold text-white drop-shadow-lg">{t.analyticsTitle}</h1>
        <p className="text-white/70 mt-1">{t.analyticsSubtitle}</p>
      </div>

      {isLoading ? (
        <div className="text-white/70">{t.loading}</div>
      ) : errorMessage ? (
        <div className="text-red-300">{errorMessage}</div>
      ) : (
        <div className="space-y-6">
          {userStats && (
            <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
              <div className="flex items-center gap-2 text-white font-semibold mb-4">
                <Icon icon="mdi:chart-box" />
                {t.analyticsOverview}
              </div>
              <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                  <div className="text-xs text-white/60">{t.analyticsTotalTasks}</div>
                  <div className="text-2xl text-white font-bold mt-1">{userStats.total_tasks}</div>
                </div>
                <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                  <div className="text-xs text-white/60">{t.analyticsCompletedTasks}</div>
                  <div className="text-2xl text-white font-bold mt-1">{userStats.completed_tasks}</div>
                </div>
                <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                  <div className="text-xs text-white/60">{t.analyticsCompletionRate}</div>
                  <div className="text-2xl text-white font-bold mt-1">{userStats.completion_rate}%</div>
                </div>
                <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                  <div className="text-xs text-white/60">{t.analyticsFocusTime}</div>
                  <div className="text-2xl text-white font-bold mt-1">{formatMinutes(userStats.total_focus_time)}</div>
                </div>
              </div>
            </div>
          )}

          <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {userStats && (
              <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
                <div className="flex items-center gap-2 text-white font-semibold mb-4">
                  <Icon icon="mdi:clipboard-check-outline" />
                  {t.analyticsTaskStatus}
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div className="text-xs text-white/60">{t.analyticsPendingTasks}</div>
                    <div className="text-xl text-white font-bold mt-1">{userStats.pending_tasks}</div>
                  </div>
                  <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div className="text-xs text-white/60">{t.analyticsInProgressTasks}</div>
                    <div className="text-xl text-white font-bold mt-1">{userStats.in_progress_tasks}</div>
                  </div>
                  <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div className="text-xs text-white/60">{t.analyticsHighPriority}</div>
                    <div className="text-xl text-white font-bold mt-1">{userStats.tasks_by_priority.high}</div>
                  </div>
                  <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div className="text-xs text-white/60">{t.analyticsMediumPriority}</div>
                    <div className="text-xl text-white font-bold mt-1">{userStats.tasks_by_priority.medium}</div>
                  </div>
                </div>
              </div>
            )}

            {userStats && (
              <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
                <div className="flex items-center gap-2 text-white font-semibold mb-4">
                  <Icon icon="mdi:timer-outline" />
                  {t.analyticsFocusStats}
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div className="text-xs text-white/60">{t.analyticsFocusSessions}</div>
                    <div className="text-xl text-white font-bold mt-1">{userStats.total_focus_sessions}</div>
                  </div>
                  <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div className="text-xs text-white/60">{t.analyticsAvgSession}</div>
                    <div className="text-xl text-white font-bold mt-1">
                      {formatMinutes(userStats.average_session_duration)}
                    </div>
                  </div>
                  <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div className="text-xs text-white/60">{t.analyticsCurrentStreak}</div>
                    <div className="text-xl text-white font-bold mt-1">{userStats.current_streak}</div>
                  </div>
                  <div className="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div className="text-xs text-white/60">{t.analyticsLongestStreak}</div>
                    <div className="text-xl text-white font-bold mt-1">{userStats.longest_streak}</div>
                  </div>
                </div>
              </div>
            )}
          </div>

          {dashboardStats && (
            <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
              <div className="flex items-center gap-2 text-white font-semibold mb-4">
                <Icon icon="mdi:calendar-range" />
                {t.analyticsTimeBreakdown}
              </div>
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {(['today', 'this_week', 'this_month'] as const).map((period) => (
                  <div key={period} className="bg-white/5 border border-white/10 rounded-xl p-4 space-y-3">
                    <div className="text-sm text-white font-semibold">
                      {period === 'today'
                        ? t.analyticsToday
                        : period === 'this_week'
                          ? t.analyticsThisWeek
                          : t.analyticsThisMonth}
                    </div>
                    <div className="text-xs text-white/60">{t.analyticsTasks}</div>
                    <div className="text-lg text-white font-bold">
                      {dashboardStats.tasks?.[period]?.completed ?? 0} / {dashboardStats.tasks?.[period]?.total ?? 0}
                    </div>
                    <div className="text-xs text-white/60">{t.analyticsSessions}</div>
                    <div className="text-lg text-white font-bold">
                      {dashboardStats.sessions?.[period]?.count ?? 0} •{' '}
                      {formatMinutes(dashboardStats.sessions?.[period]?.minutes ?? 0)}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
