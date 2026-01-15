'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { taskService, Task } from '@/lib/services/taskService';
import { sessionService } from '@/lib/services/sessionService';
import { dailyCheckinService } from '@/lib/services/dailyCheckinService';
import { learningPathService, LearningPath } from '@/lib/services/learningPathService';
import { statsService } from '@/lib/services/statsService';
import { useAuthStore } from '@/store/auth-store';
import TaskCard from '@/components/dashboard/TaskCard';
import CustomDropdown from '@/components/dashboard/CustomDropdown';

export default function TasksPage() {
  const router = useRouter();
  const { user } = useAuthStore();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [tasks, setTasks] = useState<Task[]>([]);
  const [learningPaths, setLearningPaths] = useState<LearningPath[]>([]);
  const [currentSession, setCurrentSession] = useState<any>(null);
  const [todayCheckin, setTodayCheckin] = useState<any>(null);
  const [userStats, setUserStats] = useState<any>(null);
  const [visibleCounts, setVisibleCounts] = useState({
    pending: 5,
    in_progress: 5,
    completed: 5,
  });
  const [filters, setFilters] = useState({
    priority: '',
    category: '',
    energy_level: '',
  });
  const [draggedTask, setDraggedTask] = useState<Task | null>(null);

  useEffect(() => {
    const loadLanguage = () => {
      const savedLang = localStorage.getItem('selectedLanguage') as Language;
      if (savedLang && (savedLang === 'vi' || savedLang === 'en' || savedLang === 'ja')) {
        setCurrentLang(savedLang);
      } else {
        setCurrentLang('ja');
        localStorage.setItem('selectedLanguage', 'ja');
      }
    };

    loadLanguage();

    const handleLanguageChange = () => {
      loadLanguage();
    };

    window.addEventListener('languageChange', handleLanguageChange);
    window.addEventListener('storage', handleLanguageChange);

    return () => {
      window.removeEventListener('languageChange', handleLanguageChange);
      window.removeEventListener('storage', handleLanguageChange);
    };
  }, []);

  const t = translations[currentLang];

  const loadTasks = useCallback(async () => {
    try {
      const params: any = {};
      if (filters.priority) params.priority = parseInt(filters.priority);
      if (filters.category) params.category = filters.category;
      if (filters.energy_level) params.energy_level = filters.energy_level;

      const response = await taskService.getTasks(params);
      if (response.success && response.data) {
        const tasksData = Array.isArray(response.data) 
          ? response.data 
          : response.data.data || [];
        setTasks(tasksData);
      } else if (Array.isArray(response.data)) {
        setTasks(response.data);
      }
    } catch (error) {
      console.error('Failed to load tasks:', error);
    }
  }, [filters.priority, filters.category, filters.energy_level]);

  const loadLearningPaths = useCallback(async () => {
    try {
      const response = await learningPathService.getLearningPaths({ status: 'active' });
      if (response.success && response.data) {
        const paths = Array.isArray(response.data) ? response.data : [];
        setLearningPaths(paths.slice(0, 2));
      } else if (Array.isArray(response.data)) {
        setLearningPaths(response.data.slice(0, 2));
      }
    } catch (error) {
      console.error('Failed to load learning paths:', error);
    }
  }, []);

  const loadCurrentSession = useCallback(async () => {
    try {
      const response = await sessionService.getCurrentSession();
      if (response.data) {
        setCurrentSession(response.data);
      }
    } catch (error) {
    }
  }, []);

  const loadTodayCheckin = useCallback(async () => {
    try {
      const response = await dailyCheckinService.getTodayCheckin();
      if (response.data) {
        setTodayCheckin(response.data);
      }
    } catch (error) {
    }
  }, []);

  const loadUserStats = useCallback(async () => {
    try {
      const response = await statsService.getUserStats();
      if (response.data) {
        setUserStats(response.data);
      }
    } catch (error) {
      console.error('Failed to load user stats:', error);
    }
  }, []);

  useEffect(() => {
    loadTasks();
    loadLearningPaths();
    loadCurrentSession();
    loadTodayCheckin();
    loadUserStats();
  }, [loadTasks, loadLearningPaths, loadCurrentSession, loadTodayCheckin, loadUserStats]);

  const handleDragStart = (e: React.DragEvent, task: Task) => {
    setDraggedTask(task);
    e.dataTransfer.effectAllowed = 'move';
  };

  const handleDragEnd = () => {
    setDraggedTask(null);
  };

  const handleDrop = async (e: React.DragEvent, newStatus: string) => {
    e.preventDefault();
    if (!draggedTask) return;

    try {
      await taskService.updateTask(draggedTask.id, { status: newStatus as any });
      await loadTasks();
      setDraggedTask(null);
    } catch (error) {
      console.error('Failed to update task:', error);
    }
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
  };

  const handleStartFocus = async (taskId: number) => {
    try {
      await sessionService.startSession({
        task_id: taskId,
        duration_minutes: 25,
        session_type: 'work',
      });
      await loadCurrentSession();
    } catch (error) {
      console.error('Failed to start session:', error);
    }
  };

  const handleDailyCheckin = async () => {
    try {
      await dailyCheckinService.createCheckin({
        energy_level: 'medium',
        mood_score: 3,
      });
      await loadTodayCheckin();
    } catch (error) {
      console.error('Failed to create checkin:', error);
    }
  };

  const tasksByStatus = {
    pending: tasks.filter((t) => t.status === 'pending'),
    in_progress: tasks.filter((t) => t.status === 'in_progress'),
    completed: tasks.filter((t) => t.status === 'completed'),
  };

  const formatMinutes = (minutes: number) => {
    const total = Math.max(0, Math.floor(minutes));
    const hours = Math.floor(total / 60);
    const mins = total % 60;
    return `${hours}:${String(mins).padStart(2, '0')}`;
  };

  const getRemainingMinutes = (session: any) => {
    if (!session?.duration_minutes) return 25;
    if (!session?.started_at) return session.duration_minutes;
    const startedAt = new Date(session.started_at).getTime();
    if (Number.isNaN(startedAt)) return session.duration_minutes;
    const elapsedMinutes = Math.floor((Date.now() - startedAt) / 60000);
    return Math.max(session.duration_minutes - elapsedMinutes, 0);
  };

  const nextTask =
    tasksByStatus.in_progress.length > 0
      ? tasksByStatus.in_progress[0]
      : tasksByStatus.pending.length > 0
      ? tasksByStatus.pending[0]
      : null;

  const handleViewMore = (status: 'pending' | 'in_progress' | 'completed') => {
    setVisibleCounts((prev) => ({
      ...prev,
      [status]: prev[status] + 5,
    }));
  };

  const getPriorityLabel = (priority: number) => {
    if (priority >= 5) return t.veryHigh;
    if (priority >= 4) return t.high;
    if (priority >= 3) return t.medium;
    return t.low;
  };

  return (
    <div className="px-6 py-6 relative z-0">
      {/* Header Section: Daily Check-in */}
      <div className="mb-6">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h1 className="text-2xl font-bold text-white drop-shadow-lg mb-1 flex items-center">
              {t.goodMorning}, {user?.name || 'User'}!
              <Icon icon="mdi:fire" className="text-orange-400 ml-2" />
            </h1>
            <p className="text-sm text-white/80">
              {userStats?.current_streak || 0} {t.dayStreak}
            </p>
          </div>
          <button
            onClick={handleDailyCheckin}
            className="px-5 py-2.5 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:check-circle" />
            <span>
              {todayCheckin ? t.dailyCheckin : `${t.dailyCheckinPending}`}
            </span>
          </button>
        </div>
      </div>

      {/* Focus Zone */}
      <div className="mb-6 bg-white/20 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
        <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.focusZone}</h2>
        <div className="bg-white/30 backdrop-blur-sm rounded-xl p-5 border border-white/30">
          {currentSession ? (
            <>
              <div className="flex items-center justify-between mb-4">
                <div className="flex-1">
                  <p className="text-sm text-white/80 mb-2">{t.currentTask}</p>
                  <h3 className="text-xl font-bold text-white drop-shadow-sm">
                    {currentSession.task?.title || t.noTask}
                  </h3>
                </div>
              </div>
              <div className="flex items-center justify-between pt-4 border-t border-white/30">
                <div className="flex items-center space-x-3">
                  <div className="text-3xl font-bold text-white drop-shadow-lg flex items-center">
                    <Icon icon="mdi:clock" className="mr-3 text-2xl" />
                    <span>{formatMinutes(getRemainingMinutes(currentSession))}</span>
                  </div>
                </div>
                <button
                  onClick={async () => {
                    try {
                      await sessionService.stopSession(currentSession.id);
                      await loadCurrentSession();
                      await loadTasks();
                    } catch (error) {
                      console.error('Failed to stop session:', error);
                    }
                  }}
                  className="px-6 py-3 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
                >
                  <Icon icon="mdi:stop" />
                  <span>{t.stop}</span>
                </button>
              </div>
            </>
          ) : (
            <>
              <div className="flex items-center justify-between mb-4">
                <div className="flex-1">
                  <p className="text-sm text-white/80 mb-2">{t.currentTask}</p>
                  <h3 className="text-xl font-bold text-white drop-shadow-sm">
                    {tasksByStatus.in_progress.length > 0
                      ? tasksByStatus.in_progress[0].title
                      : tasksByStatus.pending.length > 0
                      ? tasksByStatus.pending[0].title
                      : t.noTask}
                  </h3>
                </div>
              </div>
              <div className="flex items-center justify-between pt-4 border-t border-white/30">
                <div className="flex items-center space-x-3">
                  <div className="text-3xl font-bold text-white drop-shadow-lg flex items-center">
                    <Icon icon="mdi:clock" className="mr-3 text-2xl" />
                    <span>{formatMinutes(nextTask?.estimated_minutes ?? 25)}</span>
                  </div>
                </div>
                <button
                  onClick={async () => {
                    if (nextTask) {
                      try {
                        await handleStartFocus(nextTask.id);
                      } catch (error) {
                        console.error('Failed to start focus:', error);
                      }
                    }
                  }}
                  className="px-6 py-3 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
                >
                  <Icon icon="mdi:play" />
                  <span>{t.startFocus}</span>
                </button>
              </div>
            </>
          )}
        </div>
      </div>

      {/* Filter Bar */}
      <div className="mb-6 flex items-center space-x-3 flex-wrap gap-3 relative z-20">
        <CustomDropdown
          label={t.priority}
          icon="mdi:flag"
          options={[
            { value: '', label: t.all },
            { value: '5', label: t.veryHigh },
            { value: '4', label: t.high },
            { value: '3', label: t.medium },
            { value: '2', label: t.low },
          ]}
          selectedValue={filters.priority}
          onSelect={(value) => setFilters({ ...filters, priority: value })}
          currentLang={currentLang}
        />
        <CustomDropdown
          label={t.category}
          icon="mdi:tag"
          options={[
            { value: '', label: t.all },
            { value: 'study', label: t.study },
            { value: 'work', label: t.work },
            { value: 'personal', label: t.personal },
          ]}
          selectedValue={filters.category}
          onSelect={(value) => setFilters({ ...filters, category: value })}
          currentLang={currentLang}
        />
        <CustomDropdown
          label={t.energy}
          icon="mdi:bolt"
          options={[
            { value: '', label: t.all },
            { value: 'high', label: t.high },
            { value: 'medium', label: t.medium },
            { value: 'low', label: t.low },
          ]}
          selectedValue={filters.energy_level}
          onSelect={(value) => setFilters({ ...filters, energy_level: value })}
          currentLang={currentLang}
        />
      </div>

      {/* Kanban Board */}
      <div className="mb-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-bold text-white drop-shadow-md">{t.myPlan}</h2>
          <div className="flex items-center space-x-2 bg-white/15 backdrop-blur-md rounded-xl px-3 py-1.5 border border-white/20">
            <button className="px-3 py-1 bg-white/30 rounded-lg text-sm font-medium text-white">
              {t.kanban}
            </button>
            <Link
              href="/dashboard/timetable"
              className="px-3 py-1 text-white/70 hover:text-white text-sm font-medium transition"
            >
              {t.timetables}
            </Link>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-1 mb-6">
        {/* To Do Column */}
        <div
          className="kanban-column column-pending min-h-[600px]"
          data-status="pending"
          onDragOver={handleDragOver}
          onDrop={(e) => handleDrop(e, 'pending')}
        >
          <div className="column-header bg-white/15 backdrop-blur-md rounded-xl p-3 mb-4 border-l-4 border-gray-400">
            <div className="flex items-center justify-between">
              <h2 className="font-bold text-white drop-shadow-md flex items-center text-lg">
                <span className="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                {t.toDo}
              </h2>
              <span className="px-3 py-1 bg-white/30 backdrop-blur-sm rounded-full text-xs font-bold text-white border border-white/30">
                {tasksByStatus.pending.length}
              </span>
            </div>
          </div>
          <div className="space-y-4">
            {tasksByStatus.pending.slice(0, visibleCounts.pending).map((task) => (
              <TaskCard
                key={task.id}
                task={task}
                currentLang={currentLang}
                onDragStart={(e) => handleDragStart(e, task)}
                onDragEnd={handleDragEnd}
              />
            ))}
          </div>
          {tasksByStatus.pending.length > visibleCounts.pending && (
            <button
              onClick={() => handleViewMore('pending')}
              className="mt-4 w-full px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold text-sm"
            >
              {t.viewMoreTasks}
            </button>
          )}
        </div>

        {/* Doing Column */}
        <div
          className="kanban-column column-in-progress min-h-[600px]"
          data-status="in_progress"
          onDragOver={handleDragOver}
          onDrop={(e) => handleDrop(e, 'in_progress')}
        >
          <div className="column-header bg-white/15 backdrop-blur-md rounded-xl p-3 mb-4 border-l-4 border-[#0FA968]">
            <div className="flex items-center justify-between">
              <h2 className="font-bold text-white drop-shadow-md flex items-center text-lg">
                <span className="w-3 h-3 rounded-full mr-2 bg-[#0FA968]"></span>
                {t.doing}
              </h2>
              <span className="px-3 py-1 bg-white/30 backdrop-blur-sm rounded-full text-xs font-bold text-white border border-white/30">
                {tasksByStatus.in_progress.length}
              </span>
            </div>
          </div>
          <div className="space-y-4">
            {tasksByStatus.in_progress.slice(0, visibleCounts.in_progress).map((task) => (
              <TaskCard
                key={task.id}
                task={task}
                currentLang={currentLang}
                onDragStart={(e) => handleDragStart(e, task)}
                onDragEnd={handleDragEnd}
              />
            ))}
          </div>
          {tasksByStatus.in_progress.length > visibleCounts.in_progress && (
            <button
              onClick={() => handleViewMore('in_progress')}
              className="mt-4 w-full px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold text-sm"
            >
              {t.viewMoreTasks}
            </button>
          )}
        </div>

        {/* Done Column */}
        <div
          className="kanban-column column-completed min-h-[600px]"
          data-status="completed"
          onDragOver={handleDragOver}
          onDrop={(e) => handleDrop(e, 'completed')}
        >
          <div className="column-header bg-white/15 backdrop-blur-md rounded-xl p-3 mb-4 border-l-4 border-[#22C55E]">
            <div className="flex items-center justify-between">
              <h2 className="font-bold text-white drop-shadow-md flex items-center text-lg">
                <span className="w-3 h-3 rounded-full mr-2 bg-[#22C55E]"></span>
                {t.done}
              </h2>
              <span className="px-3 py-1 bg-white/30 backdrop-blur-sm rounded-full text-xs font-bold text-white border border-white/30">
                {tasksByStatus.completed.length}
              </span>
            </div>
          </div>
          <div className="space-y-4">
            {tasksByStatus.completed.slice(0, visibleCounts.completed).map((task) => (
              <TaskCard
                key={task.id}
                task={task}
                currentLang={currentLang}
                onDragStart={(e) => handleDragStart(e, task)}
                onDragEnd={handleDragEnd}
              />
            ))}
          </div>
          {tasksByStatus.completed.length > visibleCounts.completed && (
            <button
              onClick={() => handleViewMore('completed')}
              className="mt-4 w-full px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold text-sm"
            >
              {t.viewMoreTasks}
            </button>
          )}
        </div>
      </div>

      {/* Active Learning Paths */}
      <div className="mt-6">
        <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">
          {t.activeLearningPaths}
        </h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {learningPaths.length > 0 ? (
            learningPaths.map((path) => (
              <div
                key={path.id}
                className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl"
              >
                <div className="flex items-center justify-between mb-3">
                  <h3 className="font-bold text-white text-lg">{path.title}</h3>
                  <button className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold text-sm flex items-center space-x-2">
                    <span>{t.resume}</span>
                    <Icon icon="mdi:play" />
                  </button>
                </div>
                <div className="mb-2">
                  <div className="flex items-center justify-between text-xs text-white/80 mb-1">
                    <span>{t.progress}</span>
                    <span className="font-semibold">{path.progress_percentage}%</span>
                  </div>
                  <div className="w-full bg-white/20 rounded-full h-2">
                    <div
                      className="bg-[#0FA968] h-2 rounded-full progress-bar"
                      style={{ width: `${path.progress_percentage}%` }}
                    ></div>
                  </div>
                </div>
              </div>
            ))
            ) : (
              <div className="col-span-2 text-center py-8 text-white/60">
                {t.noActiveLearningPaths}
              </div>
            )}
        </div>
      </div>
    </div>
  );
}
