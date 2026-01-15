'use client';

import { useState, useEffect, useCallback } from 'react';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { timetableService, TimetableClass, TimetableStudy } from '@/lib/services/timetableService';

interface TimetableResponse {
  classes: TimetableClass[];
  studies: TimetableStudy[];
  current_class?: TimetableClass | null;
  next_class?: TimetableClass | null;
  current_time?: string;
  current_day?: string;
  year?: number;
  week_number?: number;
}

export default function TimetablePage() {
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [timetable, setTimetable] = useState<TimetableResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [showAddModal, setShowAddModal] = useState(false);
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [selectedClass, setSelectedClass] = useState<TimetableClass | null>(null);
  const [formError, setFormError] = useState<string>('');
  const [formData, setFormData] = useState<Partial<TimetableClass>>({
    name: '',
    day: 'monday',
    period: 1,
    start_time: '09:00',
    end_time: '10:00',
    room: '',
    instructor: '',
    description: '',
  });
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

  const getWeekNumber = (date: Date): number => {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    return Math.ceil(((d.getTime() - yearStart.getTime()) / 86400000 + 1) / 7);
  };

  const loadTimetable = useCallback(async () => {
    try {
      setLoading(true);
      const now = new Date();
      const response = await timetableService.getTimetable({
        year: now.getFullYear(),
        week: getWeekNumber(now),
      });
      if (response.success && response.data) {
        setTimetable(response.data as TimetableResponse);
      } else if (response.data) {
        setTimetable(response.data as TimetableResponse);
      }
    } catch (error) {
      console.error('Failed to load timetable:', error);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadTimetable();
  }, [loadTimetable]);

  const dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
  const dayLabels: Record<string, string> = {
    monday: t.monday,
    tuesday: t.tuesday,
    wednesday: t.wednesday,
    thursday: t.thursday,
    friday: t.friday,
    saturday: t.saturday,
    sunday: t.sunday,
  };

  const classes = timetable?.classes || [];
  const studies = timetable?.studies || [];
  const currentClassId = timetable?.current_class?.id;
  const nextClassId = timetable?.next_class?.id;
  const formIdPrefix = showEditModal ? 'edit' : 'add';

  const getAccentClass = (color?: string) => {
    switch ((color || '').toLowerCase()) {
      case '#0fa968':
        return 'border-[#0FA968]';
      case '#1f6feb':
        return 'border-[#1F6FEB]';
      case '#8b5cf6':
        return 'border-[#8B5CF6]';
      case '#ec4899':
        return 'border-[#EC4899]';
      case '#f59e0b':
        return 'border-[#F59E0B]';
      case '#22c55e':
        return 'border-[#22C55E]';
      default:
        return 'border-[#1F6FEB]';
    }
  };

  const openDetail = (cls: TimetableClass) => {
    setSelectedClass(cls);
    setShowDetailModal(true);
  };

  const openEdit = (cls: TimetableClass) => {
    setSelectedClass(cls);
    setFormError('');
    setFormData({
      id: cls.id,
      name: cls.name,
      day: cls.day,
      period: cls.period,
      start_time: cls.start_time?.slice(0, 5),
      end_time: cls.end_time?.slice(0, 5),
      room: cls.room || '',
      instructor: cls.instructor || '',
      description: cls.notes || cls.description || '',
      color: cls.color,
      icon: cls.icon,
    });
    setShowEditModal(true);
  };

  const openAdd = () => {
    setSelectedClass(null);
    setFormError('');
    setFormData({
      name: '',
      day: 'monday',
      period: 1,
      start_time: '09:00',
      end_time: '10:00',
      room: '',
      instructor: '',
      description: '',
    });
    setShowAddModal(true);
  };

  const closeModals = () => {
    setShowAddModal(false);
    setShowDetailModal(false);
    setShowEditModal(false);
  };

  const handleSubmit = async (isEdit: boolean) => {
    if (!formData.name || !formData.day || !formData.start_time || !formData.end_time) {
      setFormError(t.required);
      return;
    }
    try {
      setFormError('');
      if (isEdit && formData.id) {
        await timetableService.updateClass(formData.id, {
          name: formData.name,
          day: formData.day,
          period: formData.period,
          start_time: formData.start_time,
          end_time: formData.end_time,
          room: formData.room,
          instructor: formData.instructor,
          description: formData.description,
          color: formData.color,
          icon: formData.icon,
        });
      } else {
        await timetableService.createClass({
          name: formData.name,
          day: formData.day,
          period: formData.period || 1,
          start_time: formData.start_time,
          end_time: formData.end_time,
          room: formData.room,
          instructor: formData.instructor,
          description: formData.description,
          color: formData.color,
          icon: formData.icon,
        });
      }
      closeModals();
      await loadTimetable();
    } catch (error) {
      console.error('Failed to save class:', error);
      setFormError(t.errorMessage || 'Failed to save');
    }
  };

  return (
    <div className="px-6 py-6 relative z-0 min-w-0">
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="text-2xl font-bold text-white drop-shadow-lg flex items-center">
            <Icon icon="mdi:calendar-clock" className="mr-3 text-[#1F6FEB]" />
            {t.timetableTitle}
          </h1>
          <p className="text-sm text-white/70 mt-1">
            {t.week} {timetable?.week_number ?? '-'} · {t.year} {timetable?.year ?? new Date().getFullYear()}
          </p>
        </div>
        <div className="flex items-center space-x-2">
          <a
            href="/dashboard/tasks"
            className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm flex items-center space-x-2"
          >
            <Icon icon="mdi:arrow-left" />
            <span>{t.backToTasks}</span>
          </a>
          <button
            onClick={loadTimetable}
            className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm flex items-center space-x-2"
          >
            <Icon icon="mdi:refresh" />
            <span>{t.view}</span>
          </button>
          <button
            onClick={openAdd}
            className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition text-sm flex items-center space-x-2"
          >
            <Icon icon="mdi:plus" />
            <span>{t.addClass}</span>
          </button>
        </div>
      </div>

      {/* Current / Next Class */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div className="bg-gradient-to-br from-[#1F6FEB]/15 via-white/10 to-[#0FA968]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
          <div className="text-sm text-white/80 mb-2 flex items-center">
            <Icon icon="mdi:clock-outline" className="mr-2" />
            {t.currentClass}
          </div>
          {timetable?.current_class ? (
            <div className="text-white">
              <div className="text-lg font-bold">{timetable.current_class.name}</div>
              <div className="text-sm text-white/70">
                {timetable.current_class.start_time} - {timetable.current_class.end_time}
                {timetable.current_class.room ? ` · ${timetable.current_class.room}` : ''}
              </div>
            </div>
          ) : (
            <div className="text-white/60">{t.noClasses}</div>
          )}
        </div>
        <div className="bg-gradient-to-br from-[#0FA968]/15 via-white/10 to-[#1F6FEB]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
          <div className="text-sm text-white/80 mb-2 flex items-center">
            <Icon icon="mdi:arrow-right-circle-outline" className="mr-2" />
            {t.nextClass}
          </div>
          {timetable?.next_class ? (
            <div className="text-white">
              <div className="text-lg font-bold">{timetable.next_class.name}</div>
              <div className="text-sm text-white/70">
                {timetable.next_class.start_time} - {timetable.next_class.end_time}
                {timetable.next_class.room ? ` · ${timetable.next_class.room}` : ''}
              </div>
            </div>
          ) : (
            <div className="text-white/60">{t.noClasses}</div>
          )}
        </div>
      </div>

      {/* Weekly Timetable */}
      <div className="bg-gradient-to-br from-[#1F6FEB]/15 via-white/10 to-[#0FA968]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl mb-6">
        <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md flex items-center">
          <Icon icon="mdi:view-week" className="mr-2" />
          {t.weeklyTimetable}
        </h2>

        {loading ? (
          <div className="text-white/70 text-sm text-center py-8">{t.loading}</div>
        ) : (
          <div className="space-y-4">
            {dayOrder.map((day) => {
              const dayClasses = classes.filter((cls) => cls.day === day);
              return (
                <div key={day} className="bg-white/10 rounded-xl p-4 border border-white/20">
                  <div className="flex items-center justify-between mb-3">
                    <div className="text-white font-semibold">{dayLabels[day]}</div>
                    <span className="text-xs text-white/60">{dayClasses.length} {t.classesLabel}</span>
                  </div>
                  {dayClasses.length === 0 ? (
                    <div className="text-white/50 text-sm">{t.noClasses}</div>
                  ) : (
                    <div className="space-y-3">
                      {dayClasses.map((cls) => {
                        const isCurrent = cls.id === currentClassId;
                        const isNext = cls.id === nextClassId;
                        return (
                          <div
                            key={cls.id}
                            className={`bg-white/10 rounded-lg p-3 border border-white/20 border-l-4 ${getAccentClass(cls.color)} flex items-center justify-between cursor-pointer hover:bg-white/15 transition`}
                            onClick={() => openDetail(cls)}
                          >
                            <div className="min-w-0">
                              <div className="flex items-center space-x-2">
                                <span className="text-white font-medium truncate">{cls.name}</span>
                                {isCurrent && (
                                  <span className="px-2 py-0.5 text-xs rounded bg-green-500/20 text-green-300 border border-green-500/30">
                                    {t.currentClass}
                                  </span>
                                )}
                                {isNext && (
                                  <span className="px-2 py-0.5 text-xs rounded bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    {t.nextClass}
                                  </span>
                                )}
                              </div>
                              <div className="text-xs text-white/70 mt-1">
                                {cls.start_time} - {cls.end_time}
                                {cls.room ? ` · ${cls.room}` : ''}
                                {cls.instructor ? ` · ${cls.instructor}` : ''}
                              </div>
                            </div>
                            <div className="text-xs text-white/60">
                              {t.period} {cls.period}
                            </div>
                          </div>
                        );
                      })}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        )}
      </div>

      {/* Studies */}
      <div className="bg-gradient-to-br from-[#0FA968]/15 via-white/10 to-[#1F6FEB]/15 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
        <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md flex items-center">
          <Icon icon="mdi:clipboard-text-outline" className="mr-2" />
          {t.studiesTitle}
        </h2>
        {studies.length === 0 ? (
          <div className="text-white/60 text-sm">{t.noStudies}</div>
        ) : (
          <div className="space-y-3">
            {studies.map((study) => (
              <div
                key={study.id}
                className="bg-white/10 rounded-lg p-3 border border-white/20 flex items-center justify-between"
              >
                <div className="min-w-0">
                  <div className="text-white font-medium truncate">{study.title}</div>
                  <div className="text-xs text-white/70 mt-1">
                    {study.due_date}
                    {study.timetable_class?.name ? ` · ${study.timetable_class.name}` : ''}
                  </div>
                </div>
                <div className="text-xs text-white/60">
                  {study.type}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Detail Modal */}
      {showDetailModal && selectedClass && (
        <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center px-4">
          <div className="w-full max-w-lg bg-[#0B1220] rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-bold text-white">{t.classDetails}</h3>
              <button
                onClick={closeModals}
                className="text-white/70 hover:text-white"
                aria-label={t.close}
                title={t.close}
              >
                <Icon icon="mdi:close" />
              </button>
            </div>
            <div className="space-y-3 text-white/90 text-sm">
              <div className="flex items-center justify-between">
                <span className="text-white/60">{t.className}</span>
                <span className="font-semibold">{selectedClass.name}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-white/60">{t.day}</span>
                <span>{dayLabels[selectedClass.day]}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-white/60">{t.time}</span>
                <span>{selectedClass.start_time} - {selectedClass.end_time}</span>
              </div>
              {selectedClass.room && (
                <div className="flex items-center justify-between">
                  <span className="text-white/60">{t.room}</span>
                  <span>{selectedClass.room}</span>
                </div>
              )}
              {selectedClass.instructor && (
                <div className="flex items-center justify-between">
                  <span className="text-white/60">{t.instructor}</span>
                  <span>{selectedClass.instructor}</span>
                </div>
              )}
              {(selectedClass.notes || selectedClass.description) && (
                <div>
                  <div className="text-white/60 mb-1">{t.description}</div>
                  <div className="bg-white/10 rounded-lg p-3 border border-white/10">
                    {selectedClass.notes || selectedClass.description}
                  </div>
                </div>
              )}
              {selectedClass.weekly_content?.content && (
                <div>
                  <div className="text-white/60 mb-1">{t.weeklyContent}</div>
                  <div className="bg-white/10 rounded-lg p-3 border border-white/10">
                    {selectedClass.weekly_content.content}
                  </div>
                </div>
              )}
            </div>
            <div className="mt-6 flex items-center justify-end space-x-2">
              <button
                onClick={() => {
                  closeModals();
                  openEdit(selectedClass);
                }}
                className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm"
              >
                {t.edit}
              </button>
              <button
                onClick={closeModals}
                className="px-4 py-2 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition text-sm"
              >
                {t.close}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Add/Edit Modal */}
      {(showAddModal || showEditModal) && (
        <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center px-4">
          <div className="w-full max-w-xl bg-[#0B1220] rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-bold text-white">
                {showEditModal ? t.editClass : t.addClass}
              </h3>
              <button
                onClick={closeModals}
                className="text-white/70 hover:text-white"
                aria-label={t.close}
                title={t.close}
              >
                <Icon icon="mdi:close" />
              </button>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="md:col-span-2">
                <label className="text-sm text-white/70" htmlFor={`${formIdPrefix}-class-name`}>{t.className}</label>
                <input
                  id={`${formIdPrefix}-class-name`}
                  value={formData.name || ''}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor={`${formIdPrefix}-class-day`}>{t.day}</label>
                <select
                  id={`${formIdPrefix}-class-day`}
                  value={formData.day || 'monday'}
                  onChange={(e) => setFormData({ ...formData, day: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                >
                  {dayOrder.map((day) => (
                    <option key={day} value={day} className="text-black">
                      {dayLabels[day]}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor={`${formIdPrefix}-class-period`}>{t.period}</label>
                <input
                  id={`${formIdPrefix}-class-period`}
                  type="number"
                  min={1}
                  max={10}
                  value={formData.period || 1}
                  onChange={(e) => setFormData({ ...formData, period: Number(e.target.value) })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor={`${formIdPrefix}-class-start`}>{t.startTime}</label>
                <input
                  id={`${formIdPrefix}-class-start`}
                  type="time"
                  value={formData.start_time || '09:00'}
                  onChange={(e) => setFormData({ ...formData, start_time: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor={`${formIdPrefix}-class-end`}>{t.endTime}</label>
                <input
                  id={`${formIdPrefix}-class-end`}
                  type="time"
                  value={formData.end_time || '10:00'}
                  onChange={(e) => setFormData({ ...formData, end_time: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor={`${formIdPrefix}-class-room`}>{t.room}</label>
                <input
                  id={`${formIdPrefix}-class-room`}
                  value={formData.room || ''}
                  onChange={(e) => setFormData({ ...formData, room: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div>
                <label className="text-sm text-white/70" htmlFor={`${formIdPrefix}-class-instructor`}>{t.instructor}</label>
                <input
                  id={`${formIdPrefix}-class-instructor`}
                  value={formData.instructor || ''}
                  onChange={(e) => setFormData({ ...formData, instructor: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                />
              </div>
              <div className="md:col-span-2">
                <label className="text-sm text-white/70" htmlFor={`${formIdPrefix}-class-description`}>{t.description}</label>
                <textarea
                  id={`${formIdPrefix}-class-description`}
                  value={formData.description || ''}
                  onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                  className="w-full mt-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  rows={3}
                />
              </div>
            </div>
            {formError && <div className="text-red-300 text-sm mt-3">{formError}</div>}
            <div className="mt-6 flex items-center justify-end space-x-2">
              <button
                onClick={closeModals}
                className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm"
              >
                {t.cancel}
              </button>
              <button
                onClick={() => handleSubmit(showEditModal)}
                className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition text-sm"
              >
                {t.save}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
