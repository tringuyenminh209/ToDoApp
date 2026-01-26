'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { cheatCodeService, CheatCodeLanguage } from '@/lib/services/cheatCodeService';

export default function CheatCodePage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [languages, setLanguages] = useState<CheatCodeLanguage[]>([]);
  const [categories, setCategories] = useState<string[]>([]);
  const [selectedCategory, setSelectedCategory] = useState<string>('');
  const [searchQuery, setSearchQuery] = useState('');
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

  const loadLanguages = useCallback(async () => {
    try {
      setLoading(true);
      const params: any = {};
      if (selectedCategory) params.category = selectedCategory;
      if (searchQuery) params.search = searchQuery;

      const response = await cheatCodeService.getLanguages(params);
      if (response.success && response.data) {
        setLanguages(Array.isArray(response.data) ? response.data : []);
      } else if (Array.isArray(response.data)) {
        setLanguages(response.data);
      }
    } catch (error) {
      console.error('Failed to load languages:', error);
    } finally {
      setLoading(false);
    }
  }, [selectedCategory, searchQuery]);

  const loadCategories = useCallback(async () => {
    try {
      const response = await cheatCodeService.getCategories();
      if (response.success && response.data) {
        setCategories(Array.isArray(response.data) ? response.data : []);
      } else if (Array.isArray(response.data)) {
        setCategories(response.data);
      }
    } catch (error) {
      console.error('Failed to load categories:', error);
    }
  }, []);

  useEffect(() => {
    loadLanguages();
    loadCategories();
  }, [loadLanguages, loadCategories]);

  // Reload data when locale changes
  useEffect(() => {
    const handleLocaleChange = () => {
      loadLanguages();
      loadCategories();
    };

    window.addEventListener('localeChanged', handleLocaleChange);
    return () => {
      window.removeEventListener('localeChanged', handleLocaleChange);
    };
  }, [loadLanguages, loadCategories]);

  const getLanguageIcon = (icon?: string) => {
    if (icon) return icon;
    return 'mdi:code-tags';
  };

  const getLanguageLogoUrl = (language: CheatCodeLanguage): string | null => {
    if (language.logoUrl) {
      return language.logoUrl;
    }

    const logoMap: Record<string, string> = {
      'php': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg',
      'java': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg',
      'python': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg',
      'javascript': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg',
      'typescript': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg',
      'go': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/go/go-original.svg',
      'cpp': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/cplusplus/cplusplus-original.svg',
      'c++': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/cplusplus/cplusplus-original.svg',
      'c': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/c/c-original.svg',
      'kotlin': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/kotlin/kotlin-original.svg',
      'html': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
      'html5': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
      'css': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
      'css3': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
      'bash': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bash/bash-original.svg',
      'laravel': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-plain.svg',
      'docker': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg',
      'react': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg',
      'vue': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg',
      'nodejs': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg',
      'ruby': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/ruby/ruby-original.svg',
      'rust': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/rust/rust-plain.svg',
      'swift': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/swift/swift-original.svg',
      'scala': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/scala/scala-original.svg',
      'r': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/r/r-original.svg',
      'sql': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg',
      'mysql': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original-wordmark.svg',
      'yaml': 'https://cdn.simpleicons.org/yaml/CB171E',
      'mongodb': 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mongodb/mongodb-original.svg',
    };

    const languageName = language.name.toLowerCase().trim();
    const displayNameLower = language.displayName?.toLowerCase().trim() || '';
    
    const logoUrl = logoMap[languageName] || logoMap[displayNameLower] || null;
    
    return logoUrl;
  };

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

  return (
    <div className="px-6 py-6 relative z-0">
      {/* Header */}
      <div className="mb-6">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h1 className="text-2xl font-bold text-white drop-shadow-lg mb-1 flex items-center">
              <Icon icon="mdi:code-tags" className="mr-3 text-[#0FA968]" />
              {t.cheatCode}
            </h1>
            <p className="text-sm text-white/80">{t.cheatCodeSubtitle}</p>
          </div>
        </div>
      </div>

      {/* Search and Filter */}
      <div className="mb-6 flex items-center space-x-3 flex-wrap gap-3">
        <div className="flex-1 max-w-md">
          <div className="relative">
            <input
              type="text"
              placeholder={t.searchLanguages}
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-10 pr-4 py-2 bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/30"
            />
            <Icon icon="mdi:magnify" className="absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60" />
          </div>
        </div>
        {categories.length > 0 && (
          <select
            value={selectedCategory}
            onChange={(e) => setSelectedCategory(e.target.value)}
            title={t.allCategories}
            aria-label={t.allCategories}
            className="bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/30"
          >
            <option value="" className="bg-[#1f1f1f] text-white">
              {t.allCategories}
            </option>
            {categories.map((category) => (
              <option key={category} value={category} className="bg-[#1f1f1f] text-white">
                {category}
              </option>
            ))}
          </select>
        )}
      </div>

      {/* Languages Grid */}
      {loading ? (
        <div className="text-center py-12 text-white/60">{t.loading}</div>
      ) : languages.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          {languages.map((language) => (
            <Link
              key={language.id}
              href={`/dashboard/cheat-code/${language.id}`}
              className="bg-white/20 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl hover:bg-white/30 transition-all duration-300 hover:scale-105 hover:shadow-2xl group"
            >
              <div className="flex items-start justify-between mb-4">
                <div
                  className="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg overflow-hidden relative"
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
                        className="w-8 h-8 object-contain"
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
                        icon={getLanguageIcon(language.icon)}
                        className="text-2xl absolute hidden"
                        style={{ color: language.color || '#0FA968' }}
                      />
                    </>
                  ) : (
                    <Icon
                      icon={getLanguageIcon(language.icon)}
                      className="text-2xl"
                      style={{ color: language.color || '#0FA968' }}
                    />
                  )}
                </div>
                {language.popularity > 0 && (
                  <div className="flex items-center space-x-1 text-yellow-400">
                    <Icon icon="mdi:star" className="text-sm" />
                    <span className="text-xs font-semibold">{language.popularity}</span>
                  </div>
                )}
              </div>
              <h3 className="text-lg font-bold text-white mb-2 drop-shadow-md group-hover:text-[#0FA968] transition-colors">
                {language.displayName || language.name}
              </h3>
              {language.description && (
                <p className="text-sm text-white/70 mb-4 line-clamp-2">{language.description}</p>
              )}
              <div className="flex items-center justify-between text-xs text-white/60">
                <div className="flex items-center space-x-4">
                  <span className="flex items-center">
                    <Icon icon="mdi:folder" className="mr-1" />
                    {language.sectionsCount}
                  </span>
                  <span className="flex items-center">
                    <Icon icon="mdi:code-braces" className="mr-1" />
                    {language.examplesCount}
                  </span>
                  <span className="flex items-center">
                    <Icon icon="mdi:school" className="mr-1" />
                    {language.exercisesCount}
                  </span>
                </div>
              </div>
            </Link>
          ))}
        </div>
      ) : (
        <div className="text-center py-12 text-white/60">{t.noLanguagesFound}</div>
      )}
    </div>
  );
}
