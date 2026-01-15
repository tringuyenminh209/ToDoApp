// frontend/components/dashboard/TaskCard.tsx
'use client';

import { Icon } from '@iconify/react';
import { Task } from '@/lib/services/taskService';
import { translations, type Language } from '@/lib/i18n';

interface TaskCardProps {
  task: Task;
  currentLang: Language;
  onDragStart?: (e: React.DragEvent) => void;
  onDragEnd?: (e: React.DragEvent) => void;
  onClick?: () => void;
}

export default function TaskCard({ task, currentLang, onDragStart, onDragEnd, onClick }: TaskCardProps) {
  const t = translations[currentLang];

  const getPriorityBadge = (priority: number) => {
    if (priority >= 5) return { label: t.veryHigh, shortLabel: 'RC', className: 'bg-red-100 text-red-700' };
    if (priority >= 4) return { label: t.high, shortLabel: 'Cao', className: 'bg-red-100 text-red-700' };
    if (priority >= 3) return { label: t.medium, shortLabel: 'TB', className: 'bg-yellow-100 text-yellow-700' };
    return { label: t.low, shortLabel: currentLang === 'ja' ? '低' : currentLang === 'en' ? 'Low' : 'Thấp', className: 'bg-green-100 text-green-700' };
  };

  const getCategoryIcon = (category: string) => {
    switch (category) {
      case 'work':
        return 'mdi:briefcase';
      case 'study':
        return 'mdi:book';
      case 'personal':
        return 'mdi:account';
      default:
        return 'mdi:folder';
    }
  };

  const getCategoryLabel = (category: string) => {
    switch (category) {
      case 'work':
        return t.work;
      case 'study':
        return t.study;
      case 'personal':
        return t.personal;
      default:
        return category;
    }
  };

  const getCategoryColor = (category: string) => {
    switch (category) {
      case 'work':
        return 'bg-indigo-100 text-indigo-700';
      case 'study':
        return 'bg-emerald-100 text-emerald-700';
      case 'personal':
        return 'bg-purple-100 text-purple-700';
      default:
        return 'bg-gray-100 text-gray-700';
    }
  };

  const priorityBadge = getPriorityBadge(task.priority);
  const isCompleted = task.status === 'completed';

  return (
    <button
      type="button"
      onClick={onClick}
      className={`task-card w-full text-left rounded-2xl p-5 shadow-xl border transition ${
        isCompleted
          ? 'bg-white/90 opacity-80 border-gray-200 shadow-lg'
          : 'bg-white border-gray-100 hover:bg-gray-50'
      }`}
      draggable
      onDragStart={onDragStart}
      onDragEnd={onDragEnd}
      aria-label={task.title}
      title={task.title}
    >
      <div className="flex items-start justify-between mb-3">
        <h3
          className={`font-bold text-base flex-1 leading-tight ${
            isCompleted ? 'line-through text-gray-500' : 'text-gray-900'
          }`}
        >
          {task.title}
        </h3>
        <span
          className={`px-2.5 py-1 rounded-lg text-xs font-bold ml-2 ${
            isCompleted ? 'bg-gray-200 text-gray-600' : priorityBadge.className
          }`}
        >
          {isCompleted
            ? currentLang === 'ja'
              ? '完了'
              : currentLang === 'en'
              ? 'Done'
              : 'Xong'
            : priorityBadge.shortLabel || priorityBadge.label}
        </span>
      </div>
      {task.description && (
        <p
          className={`text-sm mb-4 ${
            isCompleted ? 'line-through text-gray-400' : 'text-gray-600'
          }`}
        >
          {task.description}
        </p>
      )}
      {task.status === 'in_progress' && task.estimated_minutes && (
        <div className="mb-3">
          <div className="flex items-center justify-between text-xs text-gray-600 mb-1">
            <span>{t.progress}</span>
            <span className="font-semibold">
              {task.total_focus_minutes && task.estimated_minutes
                ? Math.round((task.total_focus_minutes / task.estimated_minutes) * 100)
                : 0}
              %
            </span>
          </div>
          <div className="w-full bg-gray-200 rounded-full h-2">
            <div
              className="bg-[#0FA968] h-2 rounded-full progress-bar"
              style={{
                width: `${
                  task.total_focus_minutes && task.estimated_minutes
                    ? Math.min(
                        (task.total_focus_minutes / task.estimated_minutes) * 100,
                        100
                      )
                    : 0
                }%`,
              }}
            ></div>
          </div>
        </div>
      )}
      <div className="flex items-center flex-wrap gap-2 mb-3">
        <span
          className={`px-3 py-1 rounded-lg text-xs font-semibold ${
            isCompleted
              ? 'bg-gray-200 text-gray-600'
              : getCategoryColor(task.category)
          }`}
        >
          <Icon icon={getCategoryIcon(task.category)} className="inline mr-1" />
          {getCategoryLabel(task.category)}
        </span>
      </div>
      {isCompleted && (
        <div className="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-200">
          <span>
            <Icon icon="mdi:check-circle" className="text-[#22C55E] mr-1 inline" />
            {currentLang === 'ja' ? '完了' : currentLang === 'en' ? 'Completed' : 'Hoàn thành'}
          </span>
          <span className="flex items-center text-[#22C55E] font-semibold">
            {currentLang === 'ja' ? '完了済み' : currentLang === 'en' ? 'Done' : 'Đã xong'}
          </span>
        </div>
      )}
    </button>
  );
}
