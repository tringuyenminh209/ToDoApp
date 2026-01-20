'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter, useParams } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { cheatCodeService, CheatCodeSection, CodeExample } from '@/lib/services/cheatCodeService';
import { exerciseService, Exercise } from '@/lib/services/exerciseService';

export default function LanguageDetailPage() {
  const router = useRouter();
  const params = useParams();
  const languageId = params.languageId as string;
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [language, setLanguage] = useState<any>(null);
  const [sections, setSections] = useState<CheatCodeSection[]>([]);
  const [exercises, setExercises] = useState<Exercise[]>([]);
  const [activeTab, setActiveTab] = useState<'sections' | 'exercises'>('sections');
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

  const loadLanguage = useCallback(async () => {
    try {
      const response = await cheatCodeService.getLanguage(languageId);
      if (response.success && response.data) {
        setLanguage(response.data);
      }
    } catch (error) {
      console.error('Failed to load language:', error);
    }
  }, [languageId]);

  const loadSections = useCallback(async () => {
    try {
      const response = await cheatCodeService.getSections(languageId);
      if (response.success && response.data) {
        const data = response.data;
        if (data.sections) {
          setSections(Array.isArray(data.sections) ? data.sections : []);
        } else if (Array.isArray(data)) {
          setSections(data);
        }
      }
    } catch (error) {
      console.error('Failed to load sections:', error);
    }
  }, [languageId]);

  const loadExercises = useCallback(async () => {
    try {
      const response = await exerciseService.getExercises(languageId);
      if (response.success && response.data) {
        const data = response.data;
        if (data.exercises) {
          setExercises(Array.isArray(data.exercises) ? data.exercises : []);
        } else if (Array.isArray(data)) {
          setExercises(data);
        }
      }
    } catch (error) {
      console.error('Failed to load exercises:', error);
    }
  }, [languageId]);

  useEffect(() => {
    const loadData = async () => {
      setLoading(true);
      await Promise.all([loadLanguage(), loadSections(), loadExercises()]);
      setLoading(false);
    };
    loadData();
  }, [loadLanguage, loadSections, loadExercises]);

  const getDifficultyColor = (difficulty: string) => {
    switch (difficulty) {
      case 'easy':
        return 'bg-green-100 text-green-700';
      case 'medium':
        return 'bg-yellow-100 text-yellow-700';
      case 'hard':
        return 'bg-red-100 text-red-700';
      default:
        return 'bg-gray-100 text-gray-700';
    }
  };

  const getDifficultyLabel = (difficulty: string) => {
    switch (difficulty) {
      case 'easy':
        return t.easy;
      case 'medium':
        return t.medium;
      case 'hard':
        return t.hard;
      default:
        return difficulty;
    }
  };

  const getLanguageLogoUrl = (languageData: any): string | null => {
    if (languageData?.logoUrl) {
      return languageData.logoUrl;
    }

    const logoMap: Record<string, string> = {
      php: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg',
      java: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg',
      python: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg',
      javascript: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg',
      typescript: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg',
      go: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/go/go-original.svg',
      cpp: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/cplusplus/cplusplus-original.svg',
      'c++': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/cplusplus/cplusplus-original.svg',
      c: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/c/c-original.svg',
      kotlin: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/kotlin/kotlin-original.svg',
      html: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
      html5: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
      css: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
      css3: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
      bash: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bash/bash-original.svg',
      laravel: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-plain.svg',
      docker: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg',
      react: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg',
      vue: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg',
      nodejs: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg',
      ruby: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/ruby/ruby-original.svg',
      rust: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/rust/rust-plain.svg',
      swift: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/swift/swift-original.svg',
      scala: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/scala/scala-original.svg',
      r: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/r/r-original.svg',
      sql: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg',
      mysql: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original-wordmark.svg',
      yaml: 'https://cdn.simpleicons.org/yaml/CB171E',
      mongodb: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mongodb/mongodb-original.svg',
    };

    const languageName = languageData?.name?.toLowerCase().trim() || '';
    const displayNameLower = languageData?.displayName?.toLowerCase().trim() || '';

    return logoMap[languageName] || logoMap[displayNameLower] || null;
  };

  return (
    <div className="px-6 py-6 relative z-0">
      {/* Header */}
      <div className="mb-6">
        <button
          onClick={() => router.back()}
          className="mb-4 flex items-center text-white/70 hover:text-white transition"
        >
          <Icon icon="mdi:arrow-left" className="mr-2" />
          {t.back}
        </button>
        {language && (
          <div className="flex items-start space-x-4">
            <div
              className="w-16 h-16 rounded-xl flex items-center justify-center shadow-lg overflow-hidden relative"
              style={{
                background: language.color
                  ? `linear-gradient(135deg, ${language.color}20, ${language.color}40)`
                  : 'linear-gradient(135deg, rgba(15, 169, 104, 0.2), rgba(31, 111, 235, 0.2))',
              }}
            >
              {getLanguageLogoUrl(language) ? (
                <>
                  <img
                    src={getLanguageLogoUrl(language)!}
                    alt={language.displayName || language.name}
                    className="w-10 h-10 object-contain"
                    onError={(e) => {
                      const target = e.target as HTMLImageElement;
                      target.style.display = 'none';
                      const fallback = target.nextElementSibling as HTMLElement;
                      if (fallback) {
                        fallback.style.display = 'flex';
                      }
                    }}
                  />
                  <Icon
                    icon={language.icon || 'mdi:code-tags'}
                    className="text-3xl absolute hidden"
                    style={{ color: language.color || '#0FA968' }}
                  />
                </>
              ) : (
                <Icon
                  icon={language.icon || 'mdi:code-tags'}
                  className="text-3xl"
                  style={{ color: language.color || '#0FA968' }}
                />
              )}
            </div>
            <div className="flex-1">
              <h1 className="text-2xl font-bold text-white drop-shadow-lg mb-1">
                {language.displayName || language.name}
              </h1>
              {language.description && (
                <p className="text-sm text-white/80">{language.description}</p>
              )}
              <div className="flex items-center space-x-4 mt-2 text-xs text-white/60">
                <span className="flex items-center">
                  <Icon icon="mdi:folder" className="mr-1" />
                  {language.sectionsCount} {t.sections}
                </span>
                <span className="flex items-center">
                  <Icon icon="mdi:code-braces" className="mr-1" />
                  {language.examplesCount} {t.examples}
                </span>
                <span className="flex items-center">
                  <Icon icon="mdi:school" className="mr-1" />
                  {language.exercisesCount} {t.exercises}
                </span>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Tabs */}
      <div className="mb-6 flex items-center space-x-2 bg-white/15 backdrop-blur-md rounded-xl px-3 py-1.5 border border-white/20">
        <button
          onClick={() => setActiveTab('sections')}
          className={`px-4 py-2 rounded-lg text-sm font-medium transition ${
            activeTab === 'sections'
              ? 'bg-white/30 text-white'
              : 'text-white/70 hover:text-white'
          }`}
        >
          {t.sections}
        </button>
        <button
          onClick={() => setActiveTab('exercises')}
          className={`px-4 py-2 rounded-lg text-sm font-medium transition ${
            activeTab === 'exercises'
              ? 'bg-white/30 text-white'
              : 'text-white/70 hover:text-white'
          }`}
        >
          {t.exercises}
        </button>
      </div>

      {/* Content */}
      {loading ? (
        <div className="text-center py-12 text-white/60">{t.loading}</div>
      ) : activeTab === 'sections' ? (
        <div className="space-y-4">
          {sections.length > 0 ? (
            sections.map((section) => (
              <div
                key={section.id}
                className="bg-white/20 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl"
              >
                <div className="flex items-start justify-between mb-4">
                  <div className="flex-1">
                    <h3 className="text-lg font-bold text-white mb-2 drop-shadow-md">{section.title}</h3>
                    {section.description && (
                      <p className="text-sm text-white/70 mb-4">{section.description}</p>
                    )}
                  </div>
                  <span className="px-3 py-1 bg-white/20 rounded-full text-xs font-bold text-white">
                    {section.examplesCount || 0} {t.examples}
                  </span>
                </div>
                {(section as any).examples && (section as any).examples.length > 0 && (
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {(section as any).examples.map((example: CodeExample) => (
                      <Link
                        key={example.id}
                        href={`/dashboard/cheat-code/${languageId}/sections/${section.id}/examples/${example.id}`}
                        className="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/20 transition"
                      >
                        <div className="flex items-center justify-between mb-2">
                          <h4 className="text-sm font-semibold text-white">{example.title}</h4>
                          <span
                            className={`px-2 py-1 rounded text-xs font-bold ${getDifficultyColor(
                              example.difficulty
                            )}`}
                          >
                            {getDifficultyLabel(example.difficulty)}
                          </span>
                        </div>
                        {example.description && (
                          <p className="text-xs text-white/60 line-clamp-2">{example.description}</p>
                        )}
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            ))
          ) : (
            <div className="text-center py-12 text-white/60">{t.noSectionsFound}</div>
          )}
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {exercises.length > 0 ? (
            exercises.map((exercise) => (
              <Link
                key={exercise.id}
                href={`/dashboard/cheat-code/${languageId}/exercises/${exercise.id}`}
                className="bg-white/20 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl hover:bg-white/30 transition-all duration-300 hover:scale-105 hover:shadow-2xl group"
              >
                <div className="flex items-start justify-between mb-4">
                  <h3 className="text-lg font-bold text-white drop-shadow-md group-hover:text-[#0FA968] transition-colors flex-1">
                    {exercise.title}
                  </h3>
                  <span
                    className={`px-2.5 py-1 rounded-lg text-xs font-bold ml-2 ${getDifficultyColor(
                      exercise.difficulty
                    )}`}
                  >
                    {getDifficultyLabel(exercise.difficulty)}
                  </span>
                </div>
                {exercise.description && (
                  <p className="text-sm text-white/70 mb-4 line-clamp-2">{exercise.description}</p>
                )}
                <div className="flex items-center justify-between text-xs text-white/60">
                  <div className="flex items-center space-x-3">
                    <span className="flex items-center">
                      <Icon icon="mdi:star" className="mr-1" />
                      {exercise.points} {t.points}
                    </span>
                    {exercise.successRate && Number(exercise.successRate) > 0 && (
                      <span className="flex items-center">
                        <Icon icon="mdi:check-circle" className="mr-1" />
                        {Number(exercise.successRate).toFixed(0)}%
                      </span>
                    )}
                  </div>
                </div>
              </Link>
            ))
          ) : (
            <div className="col-span-full text-center py-12 text-white/60">{t.noExercisesFound}</div>
          )}
        </div>
      )}
    </div>
  );
}
