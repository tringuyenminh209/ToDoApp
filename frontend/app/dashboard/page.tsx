'use client';

import { useState } from 'react';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { useAuthStore } from '@/store/auth-store';
import { taskService } from '@/lib/services/taskService';

export default function DashboardPage() {
  const { user } = useAuthStore();
  const [currentLang] = useState<Language>('ja');
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
  const t = translations[currentLang];

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
    } catch (error) {
      console.error('Failed to create task:', error);
      setFormError(t.errorMessage);
    } finally {
      setIsSubmitting(false);
    }
  };

  const dashboardCards = [
    {
      title: t.tasks,
      description: t.manageTasks,
      href: '/dashboard/tasks',
      icon: 'mdi:format-list-checks',
      color: 'from-[#0FA968] to-[#0B8C57]',
    },
    {
      title: t.learning,
      description: t.trackLearning,
      href: '/dashboard/learning-paths',
      icon: 'mdi:school',
      color: 'from-[#1F6FEB] to-[#1E40AF]',
    },
    {
      title: t.knowledge,
      description: t.buildKnowledge,
      href: '/dashboard/knowledge',
      icon: 'mdi:book-open-variant',
      color: 'from-[#8B5CF6] to-[#7C3AED]',
    },
    {
      title: t.analytics,
      description: t.viewStatistics,
      href: '/dashboard/analytics',
      icon: 'mdi:chart-bar',
      color: 'from-[#EC4899] to-[#DB2777]',
    },
  ];

  return (
    <div className="p-4 sm:p-6">
      <div className="mb-6 sm:mb-8">
        <h1 className="text-2xl sm:text-3xl font-bold text-white drop-shadow-lg mb-2">
          {t.goodMorning}, {user?.name || 'User'}!
        </h1>
        <p className="text-white/70">{t.dashboardSubtitle}</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        {dashboardCards.map((card) => (
          <Link
            key={card.href}
            href={card.href}
            className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl hover:bg-white/20 transition-all group"
          >
            <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${card.color} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform`}>
              <Icon icon={card.icon} className="text-2xl text-white" />
            </div>
            <h3 className="text-xl font-bold text-white mb-2">{card.title}</h3>
            <p className="text-white/70 text-sm">{card.description}</p>
          </Link>
        ))}
      </div>

      <div className="mt-6 sm:mt-8 bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-6 border border-white/20 shadow-xl">
        <h2 className="text-lg sm:text-xl font-bold text-white mb-3 sm:mb-4">{t.quickActions}</h2>
        <div className="flex flex-wrap gap-3 sm:gap-4">
          <button
            onClick={openCreateTask}
            className="px-6 py-3 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:plus" />
            <span>{t.createTask}</span>
          </button>
          <Link
            href="/dashboard/learning-paths"
            className="px-6 py-3 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:plus" />
            <span>{t.newLearningPath}</span>
          </Link>
        </div>
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
                <label className="text-sm text-white/70" htmlFor="task-title">{t.taskTitle}</label>
                <input
                  id="task-title"
                  value={taskForm.title}
                  onChange={(e) => setTaskForm({ ...taskForm, title: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="task-category">{t.category}</label>
                <select
                  id="task-category"
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
                <label className="text-sm text-white/70" htmlFor="task-priority">{t.priority}</label>
                <select
                  id="task-priority"
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
                <label className="text-sm text-white/70" htmlFor="task-energy">{t.energy}</label>
                <select
                  id="task-energy"
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
                <label className="text-sm text-white/70" htmlFor="task-estimated">{t.estimatedMinutes}</label>
                <input
                  id="task-estimated"
                  type="number"
                  min={1}
                  max={600}
                  value={taskForm.estimated_minutes}
                  onChange={(e) => setTaskForm({ ...taskForm, estimated_minutes: Number(e.target.value) })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="task-deadline">{t.deadline}</label>
                <input
                  id="task-deadline"
                  type="date"
                  value={taskForm.deadline}
                  onChange={(e) => setTaskForm({ ...taskForm, deadline: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor="task-time">{t.scheduledTime}</label>
                <input
                  id="task-time"
                  type="time"
                  value={taskForm.scheduled_time}
                  onChange={(e) => setTaskForm({ ...taskForm, scheduled_time: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div className="md:col-span-2">
                <label className="text-sm text-white/70" htmlFor="task-desc">{t.taskDescription}</label>
                <textarea
                  id="task-desc"
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
    </div>
  );
}
