'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { learningPathService, LearningPath } from '@/lib/services/learningPathService';

export default function LearningPathsPage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [learningPaths, setLearningPaths] = useState<LearningPath[]>([]);
  const [loading, setLoading] = useState(true);
  const [filterStatus, setFilterStatus] = useState<string>('');
  const [filterGoalType, setFilterGoalType] = useState<string>('');
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
        <Link
          href="/dashboard/learning-paths/create"
          className="px-5 py-2.5 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
        >
          <Icon icon="mdi:plus" />
          <span>{t.newLearningPath}</span>
        </Link>
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
                  className="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
                  style={{
                    background: path.color
                      ? `linear-gradient(135deg, ${path.color}20, ${path.color}40)`
                      : 'linear-gradient(135deg, rgba(31, 111, 235, 0.2), rgba(31, 111, 235, 0.2))',
                  }}
                >
                  <Icon
                    icon={path.icon || 'mdi:school'}
                    className="text-2xl"
                    style={{ color: path.color || '#1F6FEB' }}
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
                <div className="w-full bg-white/20 rounded-full h-2">
                  <div
                    className="bg-[#1F6FEB] h-2 rounded-full transition-all duration-300"
                    style={{ width: `${path.progress_percentage || 0}%` }}
                  ></div>
                </div>
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
    </div>
  );
}
