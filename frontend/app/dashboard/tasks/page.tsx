'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { taskService, Task } from '@/lib/services/taskService';
import { sessionService } from '@/lib/services/sessionService';
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
  const [showCreateTask, setShowCreateTask] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [formError, setFormError] = useState('');
  const [taskForm, setTaskForm] = useState({
    title: '',
    description: '',
    category: 'other' as 'study' | 'work' | 'personal' | 'other',
    priority: 3,
    energy_level: 'medium' as 'low' | 'medium' | 'high',
    estimated_minutes: 60,
    deadline: '',
    scheduled_time: '',
  });
  const [userStats, setUserStats] = useState<any>(null);
  const [selectedTask, setSelectedTask] = useState<Task | null>(null);
  const [showTaskDetail, setShowTaskDetail] = useState(false);
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
    loadUserStats();
  }, [loadTasks, loadLearningPaths, loadCurrentSession, loadUserStats]);

  const handleDragStart = (e: React.DragEvent, task: Task) => {
    setDraggedTask(task);
    e.dataTransfer.effectAllowed = 'move';
  };

  const handleDragEnd = () => {
    setDraggedTask(null);
  };

  const openTaskDetail = (task: Task) => {
    setSelectedTask(task);
    setShowTaskDetail(true);
  };

  const closeTaskDetail = () => {
    setShowTaskDetail(false);
    setSelectedTask(null);
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

  const openCreateTask = () => {
    setFormError('');
    setTaskForm({
      title: '',
      description: '',
      category: 'other',
      priority: 3,
      energy_level: 'medium',
      estimated_minutes: 60,
      deadline: '',
      scheduled_time: '',
    });
    setShowCreateTask(true);
  };

  const closeCreateTask = () => {
    setShowCreateTask(false);
  };

  const handleCreateTask = async () => {
    if (!taskForm.title.trim()) {
      setFormError(t.titleRequired);
      return;
    }
    setIsSubmitting(true);
    setFormError('');
    try {
      await taskService.createTask({
        title: taskForm.title.trim(),
        description: taskForm.description?.trim() || undefined,
        category: taskForm.category,
        priority: taskForm.priority,
        energy_level: taskForm.energy_level,
        estimated_minutes: taskForm.estimated_minutes || undefined,
        deadline: taskForm.deadline || undefined,
        scheduled_time: taskForm.scheduled_time || undefined,
      });
      setShowCreateTask(false);
      await loadTasks();
    } catch (error) {
      console.error('Failed to create task:', error);
      setFormError(t.errorMessage);
    } finally {
      setIsSubmitting(false);
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
            onClick={openCreateTask}
            className="px-5 py-2.5 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:plus" />
            <span>{t.createTask}</span>
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

      {showCreateTask && (
        <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center px-4">
          <div className="w-full max-w-xl bg-[#0B1220] rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-bold text-white">{t.createTask}</h3>
              <button
                onClick={closeCreateTask}
                className="text-white/70 hover:text-white"
                aria-label={t.close}
                title={t.close}
              >
                <Icon icon="mdi:close" />
              </button>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="md:col-span-2">
                <label className="text-sm text-white/70" htmlFor="tasks-create-title">{t.taskTitle}</label>
                <input
                  id="tasks-create-title"
                  value={taskForm.title}
                  onChange={(e) => setTaskForm({ ...taskForm, title: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="tasks-create-category">{t.category}</label>
                <select
                  id="tasks-create-category"
                  value={taskForm.category}
                  onChange={(e) =>
                    setTaskForm({ ...taskForm, category: e.target.value as 'study' | 'work' | 'personal' | 'other' })
                  }
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                >
                  <option value="study" className="text-black">{t.study}</option>
                  <option value="work" className="text-black">{t.work}</option>
                  <option value="personal" className="text-black">{t.personal}</option>
                  <option value="other" className="text-black">{t.all}</option>
                </select>
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="tasks-create-priority">{t.priority}</label>
                <select
                  id="tasks-create-priority"
                  value={taskForm.priority}
                  onChange={(e) => setTaskForm({ ...taskForm, priority: Number(e.target.value) })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                >
                  <option value={5} className="text-black">{t.veryHigh}</option>
                  <option value={4} className="text-black">{t.high}</option>
                  <option value={3} className="text-black">{t.medium}</option>
                  <option value={2} className="text-black">{t.low}</option>
                  <option value={1} className="text-black">{t.low}</option>
                </select>
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="tasks-create-energy">{t.energy}</label>
                <select
                  id="tasks-create-energy"
                  value={taskForm.energy_level}
                  onChange={(e) => setTaskForm({ ...taskForm, energy_level: e.target.value as 'low' | 'medium' | 'high' })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                >
                  <option value="high" className="text-black">{t.high}</option>
                  <option value="medium" className="text-black">{t.medium}</option>
                  <option value="low" className="text-black">{t.low}</option>
                </select>
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="tasks-create-estimated">{t.estimatedMinutes}</label>
                <input
                  id="tasks-create-estimated"
                  type="number"
                  min={1}
                  max={600}
                  value={taskForm.estimated_minutes}
                  onChange={(e) => setTaskForm({ ...taskForm, estimated_minutes: Number(e.target.value) })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="tasks-create-deadline">{t.deadline}</label>
                <input
                  id="tasks-create-deadline"
                  type="date"
                  value={taskForm.deadline}
                  onChange={(e) => setTaskForm({ ...taskForm, deadline: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="tasks-create-time">{t.scheduledTime}</label>
                <input
                  id="tasks-create-time"
                  type="time"
                  value={taskForm.scheduled_time}
                  onChange={(e) => setTaskForm({ ...taskForm, scheduled_time: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div className="md:col-span-2">
                <label className="text-sm text-white/70" htmlFor="tasks-create-desc">{t.taskDescription}</label>
                <textarea
                  id="tasks-create-desc"
                  value={taskForm.description}
                  onChange={(e) => setTaskForm({ ...taskForm, description: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  rows={3}
                />
              </div>
            </div>
            {formError && <div className="text-red-300 text-sm mt-3">{formError}</div>}
            <div className="mt-6 flex items-center justify-end space-x-2">
              <button
                onClick={closeCreateTask}
                className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm"
                disabled={isSubmitting}
              >
                {t.cancel}
              </button>
              <button
                onClick={handleCreateTask}
                className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition text-sm"
                disabled={isSubmitting}
              >
                {isSubmitting ? t.saving : t.createTask}
              </button>
            </div>
          </div>
        </div>
      )}

      {showTaskDetail && selectedTask && (
        <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center px-4">
          <div className="w-full max-w-lg bg-[#0B1220] rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-bold text-white">{t.taskDetails}</h3>
              <button
                onClick={closeTaskDetail}
                className="text-white/70 hover:text-white"
                aria-label={t.close}
                title={t.close}
              >
                <Icon icon="mdi:close" />
              </button>
            </div>
            <div className="space-y-3 text-white/90 text-sm">
              <div className="flex items-center justify-between">
                <span className="text-white/60">{t.taskTitle}</span>
                <span className="font-semibold">{selectedTask.title}</span>
              </div>
              {selectedTask.description && (
                <div>
                  <div className="text-white/60 mb-1">{t.taskDescription}</div>
                  <div className="bg-white/10 rounded-lg p-3 border border-white/10">
                    {selectedTask.description}
                  </div>
                </div>
              )}
              <div className="flex items-center justify-between">
                <span className="text-white/60">{t.status}</span>
                <span>{selectedTask.status}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-white/60">{t.priority}</span>
                <span>{selectedTask.priority}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-white/60">{t.energy}</span>
                <span>{selectedTask.energy_level}</span>
              </div>
              {selectedTask.estimated_minutes && (
                <div className="flex items-center justify-between">
                  <span className="text-white/60">{t.estimatedMinutes}</span>
                  <span>{selectedTask.estimated_minutes}m</span>
                </div>
              )}
              {selectedTask.deadline && (
                <div className="flex items-center justify-between">
                  <span className="text-white/60">{t.deadline}</span>
                  <span>{selectedTask.deadline}</span>
                </div>
              )}
              {selectedTask.scheduled_time && (
                <div className="flex items-center justify-between">
                  <span className="text-white/60">{t.scheduledTime}</span>
                  <span>{selectedTask.scheduled_time}</span>
                </div>
              )}
            </div>
            <div className="mt-6 flex items-center justify-end">
              <button
                onClick={closeTaskDetail}
                className="px-4 py-2 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition text-sm"
              >
                {t.close}
              </button>
            </div>
          </div>
        </div>
      )}

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
                onClick={() => openTaskDetail(task)}
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
                onClick={() => openTaskDetail(task)}
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
                onClick={() => openTaskDetail(task)}
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
                  <progress
                    value={Math.min(Math.max(path.progress_percentage, 0), 100)}
                    max={100}
                    className="w-full h-2 rounded-full overflow-hidden bg-white/20 accent-[#0FA968]"
                  />
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
