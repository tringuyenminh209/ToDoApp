'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { learningPathService, LearningPath } from '@/lib/services/learningPathService';
import {
  learningPathTemplateService,
  type LearningPathTemplate,
  type StudyScheduleInput,
} from '@/lib/services/learningPathTemplateService';

export default function LearningPathsPage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [learningPaths, setLearningPaths] = useState<LearningPath[]>([]);
  const [loading, setLoading] = useState(true);
  const [filterStatus, setFilterStatus] = useState<string>('');
  const [filterGoalType, setFilterGoalType] = useState<string>('');
  const [templates, setTemplates] = useState<LearningPathTemplate[]>([]);
  const [isTemplateLoading, setIsTemplateLoading] = useState(false);
  const [showTemplateModal, setShowTemplateModal] = useState(false);
  const [selectedTemplate, setSelectedTemplate] = useState<LearningPathTemplate | null>(null);
  const [showTemplateDetail, setShowTemplateDetail] = useState(false);
  const [templateDetail, setTemplateDetail] = useState<any>(null);
  const [isTemplateDetailLoading, setIsTemplateDetailLoading] = useState(false);
  const [templateIconErrors, setTemplateIconErrors] = useState<Record<number, boolean>>({});
  const [scheduleRows, setScheduleRows] = useState<StudyScheduleInput[]>([
    { day_of_week: 1, study_time: '20:00', duration_minutes: 60 },
  ]);
  const [templateError, setTemplateError] = useState('');
  const [isCloning, setIsCloning] = useState(false);
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

  const loadLearningPaths = useCallback(async () => {
    try {
      setLoading(true);
      const params: any = {};
      if (filterStatus) params.status = filterStatus;
      if (filterGoalType) params.goal_type = filterGoalType;

      const response = await learningPathService.getLearningPaths(params);
      if (response.success && response.data) {
        setLearningPaths(Array.isArray(response.data.data) ? response.data.data : (Array.isArray(response.data) ? response.data : []));
      } else if (Array.isArray(response.data)) {
        setLearningPaths(response.data);
      }
    } catch (error) {
      console.error('Failed to load learning paths:', error);
    } finally {
      setLoading(false);
    }
  }, [filterStatus, filterGoalType]);

  useEffect(() => {
    loadLearningPaths();
  }, [loadLearningPaths]);

  const loadTemplates = useCallback(async () => {
    try {
      setIsTemplateLoading(true);
      const response = await learningPathTemplateService.getTemplates({ featured: true });
      if (response.success && response.data) {
        setTemplates(Array.isArray(response.data.data) ? response.data.data : response.data);
      } else if (Array.isArray(response.data)) {
        setTemplates(response.data);
      }
    } catch (error) {
      console.error('Failed to load templates:', error);
    } finally {
      setIsTemplateLoading(false);
    }
  }, []);

  useEffect(() => {
    loadTemplates();
  }, [loadTemplates]);

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

  const getGoalTypeLabel = (goalType: string) => {
    switch (goalType) {
      case 'career':
        return t.career;
      case 'skill':
        return t.skill;
      case 'certification':
        return t.certification;
      case 'hobby':
        return t.hobby;
      default:
        return goalType;
    }
  };

  const openTemplateModal = (template: LearningPathTemplate) => {
    setSelectedTemplate(template);
    setTemplateError('');
    setScheduleRows([{ day_of_week: 1, study_time: '20:00', duration_minutes: 60 }]);
    setShowTemplateModal(true);
  };

  const openTemplateList = () => {
    setSelectedTemplate(null);
    setTemplateError('');
    setShowTemplateModal(true);
  };

  const closeTemplateModal = () => {
    setShowTemplateModal(false);
    setSelectedTemplate(null);
  };

  const updateScheduleRow = (index: number, updates: Partial<StudyScheduleInput>) => {
    setScheduleRows((prev) =>
      prev.map((row, i) => (i === index ? { ...row, ...updates } : row))
    );
  };

  const addScheduleRow = () => {
    setScheduleRows((prev) => [...prev, { day_of_week: 1, study_time: '20:00', duration_minutes: 60 }]);
  };

  const removeScheduleRow = (index: number) => {
    setScheduleRows((prev) => prev.filter((_, i) => i !== index));
  };

  const handleCloneTemplate = async () => {
    if (!selectedTemplate) return;
    if (scheduleRows.length === 0) {
      setTemplateError(t.scheduleRequired);
      return;
    }
    if (scheduleRows.some((row) => !row.study_time)) {
      setTemplateError(t.timeRequired);
      return;
    }
    try {
      setTemplateError('');
      setIsCloning(true);
      await learningPathTemplateService.cloneTemplate(selectedTemplate.id, scheduleRows);
      closeTemplateModal();
      await loadLearningPaths();
    } catch (error) {
      console.error('Failed to clone template:', error);
      setTemplateError(t.errorMessage);
    } finally {
      setIsCloning(false);
    }
  };

  const openTemplateDetail = async (templateId: number) => {
    try {
      setIsTemplateDetailLoading(true);
      setShowTemplateDetail(true);
      const response = await learningPathTemplateService.getTemplate(templateId);
      if (response.success && response.data) {
        setTemplateDetail(response.data);
      } else if (response.data) {
        setTemplateDetail(response.data);
      }
    } catch (error) {
      console.error('Failed to load template detail:', error);
    } finally {
      setIsTemplateDetailLoading(false);
    }
  };

  const closeTemplateDetail = () => {
    setShowTemplateDetail(false);
    setTemplateDetail(null);
  };

  const isImageIcon = (icon?: string | null) => {
    if (!icon) return false;
    return icon.startsWith('http') || icon.startsWith('/') || /\.(png|jpg|jpeg|svg|webp)$/i.test(icon);
  };

  const resolveCourseIcon = useCallback((icon?: string | null) => {
    if (!icon) return 'mdi:school';
    const normalized = icon.trim().toLowerCase().replace(/^ic_/, '');
    const iconMap: Record<string, string> = {
      javascript: 'logos:javascript',
      js: 'logos:javascript',
      typescript: 'logos:typescript-icon',
      react: 'logos:react',
      php: 'logos:php',
      java: 'logos:java',
      python: 'logos:python',
      html: 'logos:html-5',
      docker: 'logos:docker-icon',
      git: 'logos:git-icon',
      laravel: 'logos:laravel',
      go: 'logos:go',
      mysql: 'logos:mysql',
      database: 'mdi:database',
      bash: 'mdi:console',
      'c++': 'mdi:language-cpp',
      cpp: 'mdi:language-cpp',
    };

    if (iconMap[normalized]) return iconMap[normalized];
    if (normalized.includes(':')) return normalized;
    return `mdi:${normalized}`;
  }, []);

  const markTemplateIconError = useCallback((templateId: number) => {
    setTemplateIconErrors((prev) => (prev[templateId] ? prev : { ...prev, [templateId]: true }));
  }, []);

  const renderTemplateIcon = (template: LearningPathTemplate) => {
    if (template.icon && isImageIcon(template.icon) && !templateIconErrors[template.id]) {
      return (
        <img
          src={template.icon}
          alt={template.title}
          className="w-6 h-6 object-contain"
          onError={(e) => {
            const target = e.target as HTMLImageElement;
            target.style.display = 'none';
            markTemplateIconError(template.id);
          }}
        />
      );
    }
    return (
      <Icon
        icon={resolveCourseIcon(template.icon)}
        className="text-xl"
      />
    );
  };

  const getColorTheme = (color?: string | null) => {
    switch ((color || '').toLowerCase()) {
      case '#1f6feb':
        return { bg: 'from-[#1F6FEB]/20 to-[#1F6FEB]/40', text: 'text-[#1F6FEB]' };
      case '#0fa968':
        return { bg: 'from-[#0FA968]/20 to-[#0FA968]/40', text: 'text-[#0FA968]' };
      case '#8b5cf6':
        return { bg: 'from-[#8B5CF6]/20 to-[#8B5CF6]/40', text: 'text-[#8B5CF6]' };
      case '#ec4899':
        return { bg: 'from-[#EC4899]/20 to-[#EC4899]/40', text: 'text-[#EC4899]' };
      case '#f59e0b':
        return { bg: 'from-[#F59E0B]/20 to-[#F59E0B]/40', text: 'text-[#F59E0B]' };
      default:
        return { bg: 'from-[#0FA968]/20 to-[#1F6FEB]/40', text: 'text-[#0FA968]' };
    }
  };

  return (
    <div className="px-6 py-6 relative z-0 min-w-0">
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="text-2xl font-bold text-white drop-shadow-lg mb-1 flex items-center">
            <Icon icon="mdi:school" className="mr-3 text-[#1F6FEB]" />
            {t.learning}
          </h1>
          <p className="text-sm text-white/80">{t.trackLearning}</p>
        </div>
        <div className="flex items-center space-x-2">
          <button
            onClick={openTemplateList}
            className="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:view-grid" />
            <span>{t.templateRoadmaps}</span>
          </button>
          <Link
            href="/dashboard/learning-paths/create"
            className="px-5 py-2.5 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:plus" />
            <span>{t.newLearningPath}</span>
          </Link>
        </div>
      </div>

      {/* Filters */}
      <div className="mb-6 flex items-center space-x-3 flex-wrap gap-3">
        <select
          value={filterStatus}
          onChange={(e) => setFilterStatus(e.target.value)}
          className="bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/30"
          title={t.status}
          aria-label={t.status}
        >
          <option value="">{t.all} ({t.status})</option>
          <option value="active">{t.active}</option>
          <option value="paused">{t.paused}</option>
          <option value="completed">{t.completed}</option>
          <option value="abandoned">{t.abandoned}</option>
        </select>
        <select
          value={filterGoalType}
          onChange={(e) => setFilterGoalType(e.target.value)}
          className="bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/30"
          title={t.goal}
          aria-label={t.goal}
        >
          <option value="">{t.all} ({t.goal})</option>
          <option value="career">{t.career}</option>
          <option value="skill">{t.skill}</option>
          <option value="certification">{t.certification}</option>
          <option value="hobby">{t.hobby}</option>
        </select>
      </div>

      {/* Learning Paths List */}
      {loading ? (
        <div className="text-center py-12 text-white/60">{t.loading}</div>
      ) : learningPaths.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {learningPaths.map((path) => (
            <div
              key={path.id}
              className="bg-white/20 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl hover:bg-white/30 transition-all duration-300 hover:scale-105 hover:shadow-2xl group"
            >
              <div className="flex items-start justify-between mb-4">
                <div className="flex-1">
                  <h3 className="text-lg font-bold text-white mb-2 drop-shadow-md group-hover:text-[#1F6FEB] transition-colors">
                    {path.title}
                  </h3>
                  {path.description && (
                    <p className="text-sm text-white/70 mb-3 line-clamp-2">{path.description}</p>
                  )}
                </div>
                <div
                  className={`w-12 h-12 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 bg-gradient-to-br ${getColorTheme(path.color).bg}`}
                >
                  <Icon
                    icon={resolveCourseIcon(path.icon)}
                    className={`${getColorTheme(path.color).text} text-2xl`}
                  />
                </div>
              </div>

              <div className="flex items-center space-x-2 mb-4 flex-wrap gap-2">
                <span
                  className={`px-3 py-1 rounded-lg text-xs font-bold border ${getStatusColor(path.status)}`}
                >
                  {path.status === 'active' ? t.active : path.status === 'paused' ? t.paused : path.status === 'completed' ? t.completed : t.abandoned}
                </span>
                <span className="px-3 py-1 bg-white/20 rounded-lg text-xs font-medium text-white/80">
                  {getGoalTypeLabel(path.goal_type)}
                </span>
              </div>

              <div className="mb-4">
                <div className="flex items-center justify-between text-xs text-white/80 mb-1">
                  <span>{t.progress}</span>
                  <span className="font-semibold">{path.progress_percentage || 0}%</span>
                </div>
                <progress
                  value={Math.min(Math.max(path.progress_percentage || 0, 0), 100)}
                  max={100}
                  className="w-full h-2 rounded-full overflow-hidden bg-white/20 accent-[#1F6FEB]"
                />
              </div>

              <div className="flex items-center justify-between text-xs text-white/60 mb-4">
                <span className="flex items-center">
                  <Icon icon="mdi:flag" className="mr-1" />
                  {path.milestones?.length || 0} {t.milestone}
                </span>
                {path.estimated_hours_total && (
                  <span className="flex items-center">
                    <Icon icon="mdi:clock" className="mr-1" />
                    {path.estimated_hours_total}h
                  </span>
                )}
              </div>

              <div className="flex items-center space-x-2">
                <Link
                  href={`/dashboard/learning-paths/${path.id}`}
                  className="flex-1 px-4 py-2 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold text-sm flex items-center justify-center space-x-2"
                >
                  <Icon icon="mdi:eye" />
                  <span>{t.view}</span>
                </Link>
                <Link
                  href={`/dashboard/learning-paths/create?id=${path.id}`}
                  className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm flex items-center justify-center"
                  title={t.edit}
                >
                  <Icon icon="mdi:pencil" />
                </Link>
              </div>
            </div>
          ))}
        </div>
      ) : (
        <div className="text-center py-12">
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-8 border border-white/20 shadow-xl">
            <Icon icon="mdi:school-outline" className="text-6xl text-white/40 mx-auto mb-4" />
            <p className="text-white/60 text-lg mb-4">{t.noActiveLearningPaths}</p>
            <Link
              href="/dashboard/learning-paths/create"
              className="inline-flex items-center space-x-2 px-6 py-3 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold"
            >
              <Icon icon="mdi:plus" />
              <span>{t.newLearningPath}</span>
            </Link>
          </div>
        </div>
      )}

      {/* Template Section */}
      <div className="mt-8">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-bold text-white drop-shadow-md">{t.templateRoadmaps}</h2>
          <button
            onClick={loadTemplates}
            className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm flex items-center space-x-2"
          >
            <Icon icon="mdi:refresh" />
            <span>{t.view}</span>
          </button>
        </div>
        {isTemplateLoading ? (
          <div className="text-center py-8 text-white/60">{t.loading}</div>
        ) : templates.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {templates.map((template) => (
              <div
                key={template.id}
                className="bg-white/20 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl hover:bg-white/30 transition-all duration-300"
              >
                <div className="flex items-start justify-between mb-3">
                  <div className="flex-1">
                    <h3 className="text-lg font-bold text-white mb-2">{template.title}</h3>
                    {template.description && (
                      <p className="text-sm text-white/70 line-clamp-2">{template.description}</p>
                    )}
                  </div>
                  <div
                    className={`w-10 h-10 rounded-xl flex items-center justify-center shadow-lg bg-gradient-to-br ${getColorTheme(template.color).bg}`}
                  >
                    <span className={getColorTheme(template.color).text}>
                      {renderTemplateIcon(template)}
                    </span>
                  </div>
                </div>
                <div className="flex items-center flex-wrap gap-2 text-xs text-white/70 mb-4">
                  {template.category && (
                    <span className="px-2 py-1 bg-white/10 rounded-lg">{template.category}</span>
                  )}
                  {template.difficulty && (
                    <span className="px-2 py-1 bg-white/10 rounded-lg">{template.difficulty}</span>
                  )}
                  {template.estimated_hours_total && (
                    <span className="px-2 py-1 bg-white/10 rounded-lg">
                      {template.estimated_hours_total}h
                    </span>
                  )}
                </div>
                <button
                  onClick={() => openTemplateModal(template)}
                  className="w-full px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold text-sm"
                >
                  {t.useTemplate}
                </button>
                <button
                  onClick={() => openTemplateDetail(template.id)}
                  className="w-full mt-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl transition text-sm"
                >
                  {t.view}
                </button>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-8 text-white/60">{t.noTemplates}</div>
        )}
      </div>

      {showTemplateModal && (
        <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center px-4">
          <div className="w-full max-w-xl bg-[#0B1220] rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-bold text-white">{t.templateRoadmaps}</h3>
              <button
                onClick={closeTemplateModal}
                className="text-white/70 hover:text-white"
                aria-label={t.close}
                title={t.close}
              >
                <Icon icon="mdi:close" />
              </button>
            </div>
            {!selectedTemplate ? (
              <>
                {isTemplateLoading ? (
                  <div className="text-center py-8 text-white/60">{t.loading}</div>
                ) : templates.length > 0 ? (
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {templates.map((template) => (
                      <button
                        key={template.id}
                        onClick={() => openTemplateModal(template)}
                        className="text-left bg-white/10 rounded-xl p-4 border border-white/20 hover:bg-white/20 transition"
                      >
                        <div className="text-white font-semibold mb-1">{template.title}</div>
                        {template.description && (
                          <div className="text-xs text-white/70 line-clamp-2">{template.description}</div>
                        )}
                      </button>
                    ))}
                  </div>
                ) : (
                  <div className="text-center py-8 text-white/60">{t.noTemplates}</div>
                )}
              </>
            ) : (
              <>
                <div className="text-white/80 text-sm mb-4">
                  {selectedTemplate.title}
                </div>
                <div className="space-y-3">
                  {scheduleRows.map((row, index) => (
                    <div key={`${row.day_of_week}-${index}`} className="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                      <select
                        value={row.day_of_week}
                        onChange={(e) => updateScheduleRow(index, { day_of_week: Number(e.target.value) })}
                        className="bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-sm text-white"
                    aria-label={t.day}
                    title={t.day}
                      >
                        <option value={0} className="text-black">{t.sunday}</option>
                        <option value={1} className="text-black">{t.monday}</option>
                        <option value={2} className="text-black">{t.tuesday}</option>
                        <option value={3} className="text-black">{t.wednesday}</option>
                        <option value={4} className="text-black">{t.thursday}</option>
                        <option value={5} className="text-black">{t.friday}</option>
                        <option value={6} className="text-black">{t.saturday}</option>
                      </select>
                      <input
                        type="time"
                        value={row.study_time}
                        onChange={(e) => updateScheduleRow(index, { study_time: e.target.value })}
                        className="bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-sm text-white"
                    aria-label={t.time}
                    title={t.time}
                      />
                      <div className="flex items-center space-x-2">
                        <input
                          type="number"
                          min={15}
                          max={480}
                          value={row.duration_minutes || 60}
                          onChange={(e) => updateScheduleRow(index, { duration_minutes: Number(e.target.value) })}
                          className="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-sm text-white"
                      aria-label={t.estimatedMinutes}
                      title={t.estimatedMinutes}
                        />
                        <button
                          onClick={() => removeScheduleRow(index)}
                          className="px-2 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg"
                          aria-label={t.delete}
                          title={t.delete}
                        >
                          <Icon icon="mdi:delete" />
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
                <button
                  onClick={addScheduleRow}
                  className="mt-4 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl transition text-sm flex items-center space-x-2"
                >
                  <Icon icon="mdi:plus" />
                  <span>{t.addSchedule}</span>
                </button>
                {templateError && <div className="text-red-300 text-sm mt-3">{templateError}</div>}
                <div className="mt-6 flex items-center justify-end space-x-2">
                  <button
                    onClick={closeTemplateModal}
                    className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm"
                    disabled={isCloning}
                  >
                    {t.cancel}
                  </button>
                  <button
                    onClick={handleCloneTemplate}
                    className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition text-sm"
                    disabled={isCloning}
                  >
                    {isCloning ? t.saving : t.useTemplate}
                  </button>
                </div>
              </>
            )}
          </div>
        </div>
      )}

      {showTemplateDetail && (
        <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center px-4 pt-24 pb-6">
          <div className="w-full max-w-2xl bg-[#0B1220] rounded-2xl p-6 border border-white/20 shadow-2xl max-h-[calc(100vh-140px)] overflow-y-auto">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-bold text-white">{t.templateDetails}</h3>
              <button
                onClick={closeTemplateDetail}
                className="text-white/70 hover:text-white"
                aria-label={t.close}
                title={t.close}
              >
                <Icon icon="mdi:close" />
              </button>
            </div>
            {isTemplateDetailLoading ? (
              <div className="text-center py-8 text-white/60">{t.loading}</div>
            ) : templateDetail ? (
              <div className="space-y-4 text-white/90 text-sm">
                <div>
                  <div className="text-white/60 mb-1">{t.templateRoadmaps}</div>
                  <div className="text-lg font-semibold text-white">{templateDetail.title}</div>
                </div>
                {templateDetail.description && (
                  <div>
                    <div className="text-white/60 mb-1">{t.taskDescription}</div>
                    <div className="bg-white/10 rounded-lg p-3 border border-white/10">
                      {templateDetail.description}
                    </div>
                  </div>
                )}
                {Array.isArray(templateDetail.milestones) && templateDetail.milestones.length > 0 && (
                  <div>
                    <div className="text-white/60 mb-2">{t.milestone}</div>
                    <div className="space-y-2">
                      {templateDetail.milestones.map((milestone: any) => (
                        <div key={milestone.id} className="bg-white/10 rounded-lg p-3 border border-white/10">
                          <div className="font-semibold text-white">{milestone.title}</div>
                          {milestone.description && (
                            <div className="text-xs text-white/70 mt-1">{milestone.description}</div>
                          )}
                          {Array.isArray(milestone.tasks) && milestone.tasks.length > 0 && (
                            <div className="mt-2 text-xs text-white/60">
                              {milestone.tasks.length} {t.tasks}
                            </div>
                          )}
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            ) : (
              <div className="text-center py-8 text-white/60">{t.noTemplates}</div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
