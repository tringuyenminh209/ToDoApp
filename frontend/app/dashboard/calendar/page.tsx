'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { taskService, Task } from '@/lib/services/taskService';
import { timetableService, TimetableClass, TimetableStudy } from '@/lib/services/timetableService';

interface CalendarDay {
  date: Date;
  isCurrentMonth: boolean;
  isToday: boolean;
  tasks: Task[];
  schedule: (TimetableClass | TimetableStudy)[];
}

export default function CalendarPage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [currentDate, setCurrentDate] = useState(new Date());
  const [viewMode, setViewMode] = useState<'month' | 'week' | 'day'>('month');
  const [tasks, setTasks] = useState<Task[]>([]);
  const [schedule, setSchedule] = useState<(TimetableClass | TimetableStudy)[]>([]);
  const [loading, setLoading] = useState(true);
  const t = translations[currentLang];

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

  const loadTasks = useCallback(async () => {
    try {
      const response = await taskService.getTasks();
      if (response.success && response.data) {
        const tasksData = response.data.data || response.data;
        setTasks(Array.isArray(tasksData) ? tasksData : []);
      } else if (Array.isArray(response.data)) {
        setTasks(response.data);
      }
    } catch (error) {
      console.error('Failed to load tasks:', error);
    }
  }, []);

  const loadSchedule = useCallback(async () => {
    try {
      const year = currentDate.getFullYear();
      const week = getWeekNumber(currentDate);
      const response = await timetableService.getTimetable({ year, week });
      if (response.success && response.data) {
        const scheduleData = response.data;
        const allSchedule: (TimetableClass | TimetableStudy)[] = [];
        
        if (scheduleData.classes && Array.isArray(scheduleData.classes)) {
          scheduleData.classes.forEach((cls: TimetableClass) => {
            allSchedule.push(cls);
          });
        }
        
        if (scheduleData.studies && Array.isArray(scheduleData.studies)) {
          scheduleData.studies.forEach((study: TimetableStudy) => {
            allSchedule.push(study);
          });
        }
        
        setSchedule(allSchedule);
      }
    } catch (error) {
      console.error('Failed to load schedule:', error);
    }
  }, [currentDate]);

  useEffect(() => {
    const loadData = async () => {
      setLoading(true);
      await Promise.all([loadTasks(), loadSchedule()]);
      setLoading(false);
    };
    loadData();
  }, [loadTasks, loadSchedule]);

  const getWeekNumber = (date: Date): number => {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    return Math.ceil(((d.getTime() - yearStart.getTime()) / 86400000 + 1) / 7);
  };

  const getTasksForDate = (date: Date): Task[] => {
    const dateStr = date.toISOString().split('T')[0];
    const filteredTasks = tasks.filter((task) => {
      if (!task.deadline) return false;
      
      try {
        let taskDateStr: string | null = null;
        
        if (task.deadline.includes('T') || task.deadline.includes(' ')) {
          const parsedDate = new Date(task.deadline);
          if (!isNaN(parsedDate.getTime())) {
            taskDateStr = parsedDate.toISOString().split('T')[0];
          }
        } else if (task.deadline.match(/^\d{4}-\d{2}-\d{2}$/)) {
          taskDateStr = task.deadline;
        } else {
          const parsedDate = new Date(task.deadline);
          if (!isNaN(parsedDate.getTime())) {
            taskDateStr = parsedDate.toISOString().split('T')[0];
          }
        }
        
        return taskDateStr === dateStr;
      } catch (error) {
        console.error('Error parsing task deadline:', task.deadline, error);
        return false;
      }
    });
    
    return filteredTasks.sort((a, b) => {
      if (b.priority !== a.priority) {
        return b.priority - a.priority;
      }
      if (a.created_at && b.created_at) {
        return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
      }
      return 0;
    });
  };

  const getScheduleForDate = (date: Date): (TimetableClass | TimetableStudy)[] => {
    const dayOfWeek = date.getDay();
    const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    const dayName = dayNames[dayOfWeek];

    return schedule.filter((item) => {
      if ('day' in item) {
        return item.day.toLowerCase() === dayName;
      } else if ('due_date' in item) {
        const dueDate = new Date(item.due_date);
        return (
          dueDate.getDate() === date.getDate() &&
          dueDate.getMonth() === date.getMonth() &&
          dueDate.getFullYear() === date.getFullYear()
        );
      }
      return false;
    });
  };

  const generateCalendarDays = (): CalendarDay[] => {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - startDate.getDay()); // Start from Sunday

    const days: CalendarDay[] = [];
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = 0; i < 42; i++) {
      const date = new Date(startDate);
      date.setDate(startDate.getDate() + i);
      const dateCopy = new Date(date);
      dateCopy.setHours(0, 0, 0, 0);

      days.push({
        date: dateCopy,
        isCurrentMonth: date.getMonth() === month,
        isToday: dateCopy.getTime() === today.getTime(),
        tasks: getTasksForDate(dateCopy),
        schedule: getScheduleForDate(dateCopy),
      });
    }

    return days;
  };

  const changeMonth = (direction: number) => {
    setCurrentDate((prev) => {
      const newDate = new Date(prev);
      newDate.setMonth(prev.getMonth() + direction);
      return newDate;
    });
  };

  const goToToday = () => {
    setCurrentDate(new Date());
  };

  const getMonthName = (date: Date): string => {
    const monthNames = {
      vi: [
        'Tháng 1',
        'Tháng 2',
        'Tháng 3',
        'Tháng 4',
        'Tháng 5',
        'Tháng 6',
        'Tháng 7',
        'Tháng 8',
        'Tháng 9',
        'Tháng 10',
        'Tháng 11',
        'Tháng 12',
      ],
      en: [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
      ],
      ja: [
        '1月',
        '2月',
        '3月',
        '4月',
        '5月',
        '6月',
        '7月',
        '8月',
        '9月',
        '10月',
        '11月',
        '12月',
      ],
    };
    return `${monthNames[currentLang][date.getMonth()]}, ${date.getFullYear()}`;
  };

  const getPriorityClass = (priority: number): string => {
    if (priority >= 4) return 'high';
    if (priority >= 2) return 'medium';
    return 'low';
  };

  const getPriorityLabel = (priority: number): string => {
    if (priority >= 4) return t.highPriority;
    if (priority >= 2) return t.mediumPriority;
    return t.lowPriority;
  };

  const calendarDays = generateCalendarDays();
  const weekDays = [t.sunday, t.monday, t.tuesday, t.wednesday, t.thursday, t.friday, t.saturday];

  return (
    <div className="px-6 py-6 relative z-0">
      {/* Calendar Header */}
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div className="flex items-center space-x-4">
          <button
            onClick={() => changeMonth(-1)}
            className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
            title={t.previousMonth}
          >
            <Icon icon="mdi:chevron-left" />
          </button>
          <h2 className="text-2xl font-bold text-white drop-shadow-lg">{getMonthName(currentDate)}</h2>
          <button
            onClick={() => changeMonth(1)}
            className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
            title={t.nextMonth}
          >
            <Icon icon="mdi:chevron-right" />
          </button>
        </div>
        <div className="flex items-center space-x-3 flex-wrap gap-3">
          <button
            onClick={goToToday}
            className="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/20 text-white rounded-xl hover:bg-white/30 transition"
          >
            <Icon icon="mdi:calendar-today" className="inline mr-2" />
            {t.goToToday}
          </button>
          <button
            onClick={() => router.push('/dashboard/tasks?action=create')}
            className="px-5 py-2.5 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:plus" />
            <span>{t.addTask}</span>
          </button>
          <div className="flex items-center space-x-2 bg-white/15 backdrop-blur-md rounded-xl px-3 py-1.5 border border-white/20">
            <button
              onClick={() => setViewMode('month')}
              className={`px-3 py-1 rounded-lg text-sm font-medium transition ${
                viewMode === 'month'
                  ? 'bg-white/30 text-white'
                  : 'text-white/70 hover:text-white'
              }`}
            >
              {t.month}
            </button>
            <button
              onClick={() => setViewMode('week')}
              className={`px-3 py-1 rounded-lg text-sm font-medium transition ${
                viewMode === 'week'
                  ? 'bg-white/30 text-white'
                  : 'text-white/70 hover:text-white'
              }`}
            >
              {t.week}
            </button>
            <button
              onClick={() => setViewMode('day')}
              className={`px-3 py-1 rounded-lg text-sm font-medium transition ${
                viewMode === 'day'
                  ? 'bg-white/30 text-white'
                  : 'text-white/70 hover:text-white'
              }`}
            >
              {t.day}
            </button>
          </div>
        </div>
      </div>

      {/* Week View Header */}
      <div className="grid grid-cols-7 gap-3 mb-3">
        {weekDays.map((day, index) => (
          <div key={index} className="text-center text-sm font-semibold text-white/80 py-2">
            {day}
          </div>
        ))}
      </div>

      {/* Calendar Grid */}
      {loading ? (
        <div className="text-center py-12 text-white/60">{t.loading}</div>
      ) : (
        <>
          <div className="grid grid-cols-7 gap-3">
            {calendarDays.map((day, index) => (
              <div
                key={index}
                className={`calendar-day rounded-xl p-3 min-h-[140px] ${
                  !day.isCurrentMonth ? 'opacity-30' : ''
                } ${day.isToday ? 'today' : ''}`}
              >
                <div
                  className={`text-sm font-medium mb-2 flex items-center justify-between ${
                    day.isToday ? 'text-white' : 'text-white/70'
                  }`}
                >
                  <span>{day.date.getDate()}</span>
                  {day.isToday && (
                    <span className="text-xs bg-[#0FA968] text-white px-2 py-0.5 rounded-full font-semibold">
                      {t.goToToday}
                    </span>
                  )}
                </div>
                <div className="space-y-1">
                  {day.tasks.slice(0, 3).map((task) => (
                    <div
                      key={task.id}
                      className={`task-item text-xs px-2 py-1.5 rounded-lg cursor-pointer transition ${
                        getPriorityClass(task.priority) === 'high'
                          ? 'high'
                          : getPriorityClass(task.priority) === 'medium'
                          ? 'medium'
                          : 'low'
                      }`}
                      onClick={() => router.push(`/dashboard/tasks?task=${task.id}`)}
                      title={task.title}
                    >
                      <span className="truncate block">{task.title}</span>
                    </div>
                  ))}
                  {day.tasks.length > 3 && (
                    <div className="text-xs text-white/60 px-2">
                      +{day.tasks.length - 3} {t.tasks}
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>

          {/* Legend */}
          <div className="mt-6 flex items-center space-x-6 flex-wrap gap-4">
            <div className="flex items-center space-x-2">
              <div className="w-4 h-4 rounded bg-gradient-to-r from-red-200 to-red-300 border-l-2 border-red-500"></div>
              <span className="text-sm text-white/80">{t.highPriority}</span>
            </div>
            <div className="flex items-center space-x-2">
              <div className="w-4 h-4 rounded bg-gradient-to-r from-yellow-200 to-yellow-300 border-l-2 border-yellow-500"></div>
              <span className="text-sm text-white/80">{t.mediumPriority}</span>
            </div>
            <div className="flex items-center space-x-2">
              <div className="w-4 h-4 rounded bg-gradient-to-r from-green-200 to-green-300 border-l-2 border-green-500"></div>
              <span className="text-sm text-white/80">{t.lowPriority}</span>
            </div>
          </div>
        </>
      )}
    </div>
  );
}
