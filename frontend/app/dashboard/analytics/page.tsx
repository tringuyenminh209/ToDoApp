'use client';

import { useEffect, useMemo, useState } from 'react';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { statsService, type UserStats } from '@/lib/services/statsService';

const formatMinutes = (minutes: number) => {
  const safeMinutes = Number.isFinite(minutes) ? minutes : 0;
  const hours = Math.floor(safeMinutes / 60);
  const mins = Math.round(safeMinutes % 60);
  return `${hours}h ${String(mins).padStart(2, '0')}m`;
};

const buildHeatmapValues = (scores: number[] | undefined, count = 56) => {
  if (!scores || scores.length === 0) {
    return Array.from({ length: count }, () => 0);
  }
  return Array.from({ length: count }, (_, index) => {
    const score = scores[index % scores.length];
    const normalized = Number.isFinite(score) ? Math.max(0, Math.min(score, 100)) : 0;
    return Math.min(4, Math.floor((normalized / 100) * 5));
  });
};

const buildSparklinePoints = (scores: number[] | undefined) => {
  if (!scores || scores.length === 0) return '0,40 100,40';
  const maxScore = Math.max(...scores, 1);
  const step = 100 / Math.max(scores.length - 1, 1);
  return scores
    .map((score, index) => {
      const x = index * step;
      const y = 40 - (Math.max(0, score) / maxScore) * 36;
      return `${x.toFixed(1)},${y.toFixed(1)}`;
    })
    .join(' ');
};

export default function AnalyticsPage() {
  const [currentLang] = useState<Language>('ja');
  const t = useMemo(() => translations[currentLang], [currentLang]);
  const [dashboardStats, setDashboardStats] = useState<any | null>(null);
  const [userStats, setUserStats] = useState<UserStats | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [errorMessage, setErrorMessage] = useState('');
  const [reportPeriod, setReportPeriod] = useState<'week' | 'month' | 'year'>('week');

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
      <div className="mb-6 flex items-start justify-between gap-4 flex-wrap">
        <div>
          <h1 className="text-3xl font-bold text-white drop-shadow-lg">{t.analyticsTitle}</h1>
          <p className="text-white/70 mt-1">{t.analyticsSubtitle}</p>
        </div>
        <div className="flex items-center gap-2">
          <label className="text-sm text-white/60">{t.analyticsPeriod}</label>
          <select
            value={reportPeriod}
            onChange={(event) => setReportPeriod(event.target.value as 'week' | 'month' | 'year')}
            className="bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
            aria-label={t.analyticsPeriod}
          >
            <option value="week" className="text-black">
              {t.analyticsPeriodWeek}
            </option>
            <option value="month" className="text-black">
              {t.analyticsPeriodMonth}
            </option>
            <option value="year" className="text-black">
              {t.analyticsPeriodYear}
            </option>
          </select>
        </div>
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

          {dashboardStats?.performance && (
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
                <div className="flex items-center justify-between mb-4">
                  <div className="flex items-center gap-2 text-white font-semibold">
                    <Icon icon="mdi:chart-line" />
                    {t.analyticsPerformanceTrend}
                  </div>
                  <div className="text-sm text-white/60">
                    {t.analyticsCurrentScore}: {dashboardStats.performance.current_score ?? 0}
                  </div>
                </div>
                <div className="h-40 bg-white/5 border border-white/10 rounded-xl p-4">
                  <svg viewBox="0 0 100 40" className="w-full h-full">
                    <polyline
                      points={buildSparklinePoints(dashboardStats.performance.recent_scores)}
                      fill="none"
                      stroke="#0FA968"
                      strokeWidth="2"
                    />
                    <polyline
                      points={`0,40 ${buildSparklinePoints(dashboardStats.performance.recent_scores)} 100,40`}
                      fill="rgba(15, 169, 104, 0.15)"
                      stroke="none"
                    />
                  </svg>
                </div>
              </div>

              <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
                <div className="flex items-center justify-between mb-4">
                  <div className="flex items-center gap-2 text-white font-semibold">
                    <Icon icon="mdi:calendar-blank-multiple" />
                    {t.analyticsHeatmapTitle}
                  </div>
                  <span className="text-xs text-white/60">{t.analyticsHeatmapSubtitle}</span>
                </div>
                <div className="grid grid-cols-14 gap-1">
                  {buildHeatmapValues(dashboardStats.performance.recent_scores).map((value, index) => (
                    <div
                      key={`${index}-${value}`}
                      className="h-3 w-3 rounded-[3px] transition"
                      style={{
                        backgroundColor: `rgba(15, 169, 104, ${0.15 + value * 0.2})`,
                      }}
                    />
                  ))}
                </div>
                <div className="flex items-center justify-between mt-3 text-xs text-white/60">
                  <span>{t.analyticsHeatmapLess}</span>
                  <span>{t.analyticsHeatmapMore}</span>
                </div>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
