'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter, useParams } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { learningPathService, LearningPath, LearningMilestone } from '@/lib/services/learningPathService';

interface KnowledgeItemRef {
  id: number;
  title?: string;
  item_type?: string;
}

interface Task {
  id: number;
  title: string;
  description?: string;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  priority: number;
  estimated_minutes?: number;
  deadline?: string;
  subtasks?: Subtask[];
  knowledge_items?: KnowledgeItemRef[];
  knowledgeItems?: KnowledgeItemRef[];
}

interface Subtask {
  id: number;
  title: string;
  is_completed: boolean;
  estimated_minutes?: number;
}

export default function LearningPathDetailPage() {
  const router = useRouter();
  const params = useParams();
  const pathId = params?.id ? parseInt(params.id as string) : null;
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [learningPath, setLearningPath] = useState<LearningPath | null>(null);
  const [loading, setLoading] = useState(true);
  const [expandedMilestones, setExpandedMilestones] = useState<Set<number>>(new Set());
  const [expandedTasks, setExpandedTasks] = useState<Set<number>>(new Set());
  const t = translations[currentLang];

  const toggleTaskDetail = (taskId: number) => {
    setExpandedTasks((prev) => {
      const next = new Set(prev);
      if (next.has(taskId)) next.delete(taskId);
      else next.add(taskId);
      return next;
    });
  };

  const expandTaskToShowKnowledge = (taskId: number) => {
    setExpandedTasks((prev) => new Set(prev).add(taskId));
  };

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

  const loadLearningPath = useCallback(async () => {
    if (!pathId) return;

    try {
      setLoading(true);
      const response = await learningPathService.getLearningPath(pathId);
      if (response.success && response.data) {
        const pathData = response.data.data || response.data;
        setLearningPath(pathData);
        const milestones = pathData?.milestones || [];
        if (milestones.length > 0) {
          setExpandedMilestones(new Set([milestones[0].id]));
        }
      }
    } catch (error) {
      console.error('Failed to load learning path:', error);
    } finally {
      setLoading(false);
    }
  }, [pathId]);

  useEffect(() => {
    loadLearningPath();
  }, [loadLearningPath]);

  // 言語切替時に再取得し、Milestone/Task の翻訳を反映
  useEffect(() => {
    const handleLocaleChange = () => {
      const savedLang = localStorage.getItem('selectedLanguage') as Language;
      if (savedLang && (savedLang === 'vi' || savedLang === 'en' || savedLang === 'ja')) {
        setCurrentLang(savedLang);
      }
      loadLearningPath();
    };

    window.addEventListener('localeChanged', handleLocaleChange);
    return () => {
      window.removeEventListener('localeChanged', handleLocaleChange);
    };
  }, [loadLearningPath]);

  const toggleMilestone = (milestoneId: number) => {
    setExpandedMilestones((prev) => {
      const newSet = new Set(prev);
      if (newSet.has(milestoneId)) {
        newSet.delete(milestoneId);
      } else {
        newSet.add(milestoneId);
      }
      return newSet;
    });
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active':
        return 'bg-green-500/20 text-green-400 border-green-500/30';
      case 'paused':
        return 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30';
      case 'completed':
        return 'bg-blue-500/20 text-blue-400 border-blue-500/30';
      case 'abandoned':
        return 'bg-red-500/20 text-red-400 border-red-500/30';
      default:
        return 'bg-gray-500/20 text-gray-400 border-gray-500/30';
    }
  };

  const getStatusLabel = (status: string) => {
    switch (status) {
      case 'active':
        return t.active;
      case 'paused':
        return t.paused;
      case 'completed':
        return t.completed;
      case 'abandoned':
        return t.abandoned;
      default:
        return status;
    }
  };

  const getTaskStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
        return 'bg-green-500/20 text-green-400';
      case 'in_progress':
        return 'bg-blue-500/20 text-blue-400';
      case 'pending':
        return 'bg-gray-500/20 text-gray-400';
      case 'cancelled':
        return 'bg-red-500/20 text-red-400';
      default:
        return 'bg-gray-500/20 text-gray-400';
    }
  };

  const getTaskStatusLabel = (status: string) => {
    switch (status) {
      case 'completed':
        return t.completed;
      case 'in_progress':
        return t.inProgress;
      case 'pending':
        return t.pending;
      case 'cancelled':
        return t.cancelled;
      default:
        return status;
    }
  };

  const formatDate = (dateString?: string) => {
    if (!dateString) return null;
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString(currentLang === 'ja' ? 'ja-JP' : currentLang === 'vi' ? 'vi-VN' : 'en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    } catch {
      return dateString;
    }
  };

  if (!pathId) {
    return (
      <div className="px-6 py-6 relative z-0 min-w-0">
        <div className="text-center py-12 text-white/60">{t.error || 'Invalid path ID'}</div>
      </div>
    );
  }

  if (loading) {
    return (
      <div className="px-6 py-6 relative z-0 min-w-0">
        <div className="text-center py-12 text-white/60">{t.loading}</div>
      </div>
    );
  }

  if (!learningPath) {
    return (
      <div className="px-6 py-6 relative z-0 min-w-0">
        <div className="text-center py-12">
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-8 border border-white/20 shadow-xl">
            <Icon icon="mdi:alert-circle" className="text-6xl text-white/40 mx-auto mb-4" />
            <p className="text-white/60 text-lg mb-4">{t.notFound || 'Learning path not found'}</p>
            <Link
              href="/dashboard/learning-paths"
              className="inline-flex items-center space-x-2 px-6 py-3 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold"
            >
              <Icon icon="mdi:arrow-left" />
              <span>{t.back || 'Back'}</span>
            </Link>
          </div>
        </div>
      </div>
    );
  }

  const milestones = learningPath.milestones || [];
  const completedMilestones = milestones.filter((m) => m.status === 'completed').length;
  const totalMilestones = milestones.length;

  return (
    <div className="px-6 py-6 relative z-0 min-w-0">
      {/* Header */}
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div className="flex items-center space-x-4">
          <Link
            href="/dashboard/learning-paths"
            className="p-2 hover:bg-white/10 rounded-xl transition"
            title={t.back || 'Back'}
          >
            <Icon icon="mdi:arrow-left" className="text-2xl text-white" />
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-white drop-shadow-lg mb-1 flex items-center">
              <Icon icon="mdi:school" className="mr-3 text-[#1F6FEB]" />
              {learningPath.title}
            </h1>
            {learningPath.description && (
              <p className="text-sm text-white/80">{learningPath.description}</p>
            )}
          </div>
        </div>
        <div className="flex items-center space-x-3">
          <Link
            href={`/dashboard/learning-paths/create?id=${learningPath.id}`}
            className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm flex items-center space-x-2"
            title={t.edit}
          >
            <Icon icon="mdi:pencil" />
            <span>{t.edit}</span>
          </Link>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {/* Status */}
        <div className="bg-gradient-to-br from-[#0FA968]/15 via-white/10 to-[#1F6FEB]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
          <div className="text-sm text-white/80 mb-2">{t.status}</div>
          <div className="flex items-center space-x-2">
            <span className={`px-3 py-1 rounded-lg text-xs font-bold border ${getStatusColor(learningPath.status)}`}>
              {getStatusLabel(learningPath.status)}
            </span>
          </div>
        </div>

        {/* Progress */}
        <div className="bg-gradient-to-br from-[#1F6FEB]/15 via-white/10 to-[#0FA968]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
          <div className="text-sm text-white/80 mb-2">{t.progress}</div>
          <div className="flex items-center space-x-2">
            <div className="flex-1 bg-white/20 rounded-full h-2">
              <div
                className="bg-[#1F6FEB] h-2 rounded-full transition-all duration-300"
                style={{ width: `${learningPath.progress_percentage || 0}%` }}
              ></div>
            </div>
            <span className="text-lg font-bold text-white">{learningPath.progress_percentage || 0}%</span>
          </div>
        </div>

        {/* Milestones */}
        <div className="bg-gradient-to-br from-[#8B5CF6]/15 via-white/10 to-[#1F6FEB]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
          <div className="text-sm text-white/80 mb-2">{t.milestone}</div>
          <div className="text-2xl font-bold text-white">
            {completedMilestones}/{totalMilestones}
          </div>
        </div>

        {/* Estimated Hours */}
        {learningPath.estimated_hours_total && (
          <div className="bg-gradient-to-br from-[#0FA968]/15 via-white/10 to-[#1F6FEB]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
            <div className="text-sm text-white/80 mb-2">{t.estimatedHours || 'Estimated Hours'}</div>
            <div className="text-2xl font-bold text-white flex items-center space-x-2">
              <Icon icon="mdi:clock" />
              <span>{learningPath.estimated_hours_total}h</span>
            </div>
          </div>
        )}
      </div>

      {/* Dates */}
      {(learningPath.target_start_date || learningPath.target_end_date) && (
        <div className="bg-gradient-to-br from-[#1F6FEB]/15 via-white/10 to-[#0FA968]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl mb-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {learningPath.target_start_date && (
              <div>
                <div className="text-sm text-white/80 mb-2">{t.startDate}</div>
                <div className="text-white flex items-center space-x-2">
                  <Icon icon="mdi:calendar-start" />
                  <span>{formatDate(learningPath.target_start_date)}</span>
                </div>
              </div>
            )}
            {learningPath.target_end_date && (
              <div>
                <div className="text-sm text-white/80 mb-2">{t.endDate}</div>
                <div className="text-white flex items-center space-x-2">
                  <Icon icon="mdi:calendar-end" />
                  <span>{formatDate(learningPath.target_end_date)}</span>
                </div>
              </div>
            )}
          </div>
        </div>
      )}

      {/* Milestones List */}
      <div className="bg-gradient-to-br from-[#1F6FEB]/15 via-white/10 to-[#0FA968]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
        <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md flex items-center">
          <Icon icon="mdi:flag" className="mr-2" />
          {t.milestone} ({totalMilestones})
        </h2>

        {milestones.length === 0 ? (
          <div className="text-center py-8 text-white/60">
            <Icon icon="mdi:flag-outline" className="text-4xl mb-2 mx-auto" />
            <p>{t.noMilestones || 'No milestones yet'}</p>
          </div>
        ) : (
          <div className="space-y-4">
            {milestones.map((milestone) => {
              const tasks = (milestone.tasks as Task[]) || [];
              const completedTasks = tasks.filter((t) => t.status === 'completed').length;
              const isExpanded = expandedMilestones.has(milestone.id);

              return (
                <div
                  key={milestone.id}
                  className="bg-gradient-to-r from-white/10 via-white/5 to-[#1F6FEB]/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 border-l-4 border-[#1F6FEB]/40 hover:from-[#0FA968]/10 hover:to-[#1F6FEB]/15 transition"
                >
                  {/* Milestone Header */}
                  <div
                    className="flex items-center justify-between cursor-pointer"
                    onClick={() => toggleMilestone(milestone.id)}
                  >
                    <div className="flex-1">
                      <div className="flex items-center space-x-3 mb-2">
                        <Icon
                          icon={isExpanded ? 'mdi:chevron-down' : 'mdi:chevron-right'}
                          className="text-white/60"
                        />
                        <h3 className="text-lg font-bold text-white">{milestone.title}</h3>
                        <span
                          className={`px-2 py-1 rounded text-xs font-medium ${getStatusColor(milestone.status)}`}
                        >
                          {milestone.status === 'completed'
                            ? t.completed
                            : milestone.status === 'in_progress'
                            ? t.inProgress
                            : milestone.status === 'pending'
                            ? t.pending
                            : milestone.status}
                        </span>
                      </div>
                      {milestone.description && (
                        <p className="text-sm text-white/70 ml-8">{milestone.description}</p>
                      )}
                      <div className="flex items-center space-x-4 mt-2 ml-8 text-xs text-white/60">
                        <span className="flex items-center">
                          <Icon icon="mdi:check-circle" className="mr-1" />
                          {completedTasks}/{tasks.length} {t.tasks || 'Tasks'}
                        </span>
                        {milestone.progress_percentage && Number(milestone.progress_percentage) > 0 && (
                          <span className="flex items-center">
                            <Icon icon="mdi:progress-check" className="mr-1" />
                            {Number(milestone.progress_percentage).toFixed(0)}%
                          </span>
                        )}
                        {milestone.estimated_hours && (
                          <span className="flex items-center">
                            <Icon icon="mdi:clock" className="mr-1" />
                            {milestone.estimated_hours}h
                          </span>
                        )}
                      </div>
                    </div>
                  </div>

                  {/* Milestone Progress Bar */}
                  {milestone.progress_percentage && Number(milestone.progress_percentage) > 0 && (
                    <div className="mt-3 ml-8">
                      <div className="w-full bg-white/20 rounded-full h-1.5">
                        <div
                          className="bg-[#1F6FEB] h-1.5 rounded-full transition-all duration-300"
                          style={{ width: `${Number(milestone.progress_percentage)}%` }}
                        ></div>
                      </div>
                    </div>
                  )}

                  {/* Tasks List */}
                  {isExpanded && tasks.length > 0 && (
                    <div className="mt-4 ml-8 space-y-2">
                      {tasks.map((task) => {
                        const isTaskExpanded = expandedTasks.has(task.id);
                        const knowledgeItems = (task.knowledge_items ?? task.knowledgeItems ?? []) as KnowledgeItemRef[];
                        return (
                          <div
                            key={task.id}
                            className="bg-white/10 rounded-lg p-3 border border-white/20 border-l-2 border-[#0FA968]/40 hover:bg-white/15 transition"
                          >
                            <div className="flex items-start justify-between gap-3">
                              <div className="flex-1 min-w-0">
                                <div className="flex items-center flex-wrap gap-2 mb-1">
                                  <Icon icon="mdi:checkbox-marked-circle-outline" className="text-white/60 shrink-0" />
                                  <h4 className="text-white font-medium">{task.title}</h4>
                                  <span
                                    className={`px-2 py-0.5 rounded text-xs shrink-0 ${getTaskStatusColor(task.status)}`}
                                  >
                                    {getTaskStatusLabel(task.status)}
                                  </span>
                                  <button
                                    type="button"
                                    onClick={(e) => { e.stopPropagation(); toggleTaskDetail(task.id); }}
                                    className="ml-auto shrink-0 px-2 py-1 rounded-lg bg-[#1F6FEB]/30 hover:bg-[#1F6FEB]/50 text-white text-xs font-medium flex items-center space-x-1 transition"
                                  >
                                    <Icon icon={isTaskExpanded ? 'mdi:chevron-up' : 'mdi:chevron-down'} />
                                    <span>{t.viewTaskDetail ?? '詳細を見る'}</span>
                                  </button>
                                </div>
                                {task.description && (
                                  <p className="text-sm text-white/60 ml-6 mb-2">{task.description}</p>
                                )}
                                <div className="flex items-center space-x-3 ml-6 text-xs text-white/50 flex-wrap gap-x-3">
                                  {task.priority != null && task.priority > 0 && (
                                    <span className="flex items-center">
                                      <Icon icon="mdi:flag" className="mr-1" />
                                      {t.priority}: {task.priority}
                                    </span>
                                  )}
                                  {task.estimated_minutes != null && task.estimated_minutes > 0 && (
                                    <span className="flex items-center">
                                      <Icon icon="mdi:clock-outline" className="mr-1" />
                                      {Math.floor(task.estimated_minutes / 60)}h {task.estimated_minutes % 60}m
                                    </span>
                                  )}
                                  {task.deadline && (
                                    <span className="flex items-center">
                                      <Icon icon="mdi:calendar" className="mr-1" />
                                      {formatDate(task.deadline)}
                                    </span>
                                  )}
                                </div>
                                {/* Subtasks */}
                                {task.subtasks && task.subtasks.length > 0 && (
                                  <div className="mt-2 ml-6 space-y-1" id={`task-${task.id}-subtasks`}>
                                    {task.subtasks.map((subtask) => (
                                      <div
                                        key={subtask.id}
                                        className="flex items-center justify-between gap-2 text-sm text-white/70 group"
                                      >
                                        <div className="flex items-center space-x-2 min-w-0">
                                          <Icon
                                            icon={subtask.is_completed ? 'mdi:check-circle' : 'mdi:circle-outline'}
                                            className={subtask.is_completed ? 'text-green-400 shrink-0' : 'text-white/40 shrink-0'}
                                          />
                                          <span
                                            className={subtask.is_completed ? 'line-through text-white/50' : ''}
                                          >
                                            {subtask.title}
                                          </span>
                                        </div>
                                        <button
                                          type="button"
                                          onClick={(e) => { e.stopPropagation(); expandTaskToShowKnowledge(task.id); }}
                                          className="shrink-0 px-2 py-0.5 rounded bg-white/10 hover:bg-[#1F6FEB]/30 text-white/80 hover:text-white text-xs flex items-center space-x-1 transition opacity-80 group-hover:opacity-100"
                                          title={t.knowledgeContent}
                                        >
                                          <Icon icon="mdi:book-open-page-variant" className="text-sm" />
                                          <span>{currentLang === 'ja' ? '表示' : currentLang === 'vi' ? 'Xem' : 'View'}</span>
                                        </button>
                                      </div>
                                    ))}
                                  </div>
                                )}
                              </div>
                            </div>
                            {/* Task detail: Knowledge content (expandable) */}
                            {isTaskExpanded && (
                              <div className="mt-3 ml-6 pt-3 border-t border-white/10" id={`task-${task.id}-knowledge`}>
                                <div className="flex items-center space-x-2 mb-2 text-white/90">
                                  <Icon icon="mdi:book-open-variant" />
                                  <span className="font-medium text-sm">{t.knowledgeContent}</span>
                                </div>
                                {knowledgeItems.length > 0 ? (
                                  <ul className="space-y-1.5">
                                    {knowledgeItems.map((item) => (
                                      <li key={item.id}>
                                        <Link
                                          href={`/dashboard/knowledge/${item.id}`}
                                          className="flex items-center space-x-2 text-sm text-[#6fb3d9] hover:text-[#9cdcfe] hover:underline"
                                        >
                                          <Icon icon="mdi:file-document-outline" className="shrink-0" />
                                          <span>{item.title || `#${item.id}`}</span>
                                          <Icon icon="mdi:open-in-new" className="text-xs shrink-0" />
                                        </Link>
                                      </li>
                                    ))}
                                  </ul>
                                ) : (
                                  <p className="text-sm text-white/50 italic">{t.noKnowledgeItems}</p>
                                )}
                              </div>
                            )}
                          </div>
                        );
                      })}
                    </div>
                  )}

                  {/* No Tasks Message */}
                  {isExpanded && tasks.length === 0 && (
                    <div className="mt-4 ml-8 text-sm text-white/60 italic">
                      {t.noTasks || 'No tasks in this milestone'}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        )}
      </div>
    </div>
  );
}
