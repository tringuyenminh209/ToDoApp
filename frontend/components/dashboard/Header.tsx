// frontend/components/dashboard/Header.tsx
'use client';

import { useState, useRef, useEffect } from 'react';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { useAuthStore } from '@/store/auth-store';

interface HeaderProps {
  currentLang: Language;
  onLanguageChange: (lang: Language) => void;
  onToggleSidebar: () => void;
  onToggleRightPanel: () => void;
  isSidebarCollapsed: boolean;
  isRightPanelCollapsed: boolean;
}

export default function Header({
  currentLang,
  onLanguageChange,
  onToggleSidebar,
  onToggleRightPanel,
  isSidebarCollapsed,
  isRightPanelCollapsed,
}: HeaderProps) {
  const { user } = useAuthStore();
  const [showLangMenu, setShowLangMenu] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const langMenuRef = useRef<HTMLDivElement>(null);
  const t = translations[currentLang];

  const langNames: Record<Language, string> = {
    vi: 'Tiếng Việt',
    en: 'English',
    ja: '日本語',
  };

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (langMenuRef.current && !langMenuRef.current.contains(event.target as Node)) {
        setShowLangMenu(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleLanguageSelect = (lang: Language) => {
    onLanguageChange(lang);
    setShowLangMenu(false);
    // カスタムイベントを発火（同じタブ内の他のコンポーネントに通知）
    window.dispatchEvent(new Event('languageChange'));
  };

  return (
    <header className="bg-white/10 backdrop-blur-md shadow-xl border-b border-white/20 relative z-50 overflow-visible">
      <div className="w-full px-4 sm:px-6 lg:px-8 py-3 overflow-visible">
        <div className="flex items-center justify-between overflow-visible">
          <div className="flex items-center space-x-4">
            <div className="w-10 h-10 bg-gradient-to-br from-[#0FA968] to-[#1F6FEB] rounded-xl flex items-center justify-center shadow-lg">
              <Icon icon="mdi:leaf" className="text-xl text-white" />
            </div>
            <div className="flex flex-col">
              <h1 className="text-xl font-bold text-white drop-shadow-lg">{t.dashboardTitle}</h1>
              <p className="text-xs text-white/70">{t.dashboardSubtitle}</p>
            </div>
            <div className="flex-1 max-w-md">
              <div className="relative">
                <input
                  type="text"
                  placeholder={t.searchPlaceholder}
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="w-full pl-10 pr-4 py-2 bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/30"
                />
                <Icon
                  icon="mdi:magnify"
                  className="absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60"
                />
              </div>
            </div>
          </div>
          <div className="flex items-center space-x-3 overflow-visible">
            <button
              onClick={onToggleSidebar}
              className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
              title="Ẩn/Hiện Menu"
            >
              <Icon
                icon={isSidebarCollapsed ? 'mdi:chevron-right' : 'mdi:chevron-left'}
                className="text-base"
              />
            </button>
            <button
              className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20 relative"
              title="Thông báo"
            >
              <Icon icon="mdi:bell" className="text-base" />
              <span className="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            <div className="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center cursor-pointer hover:bg-white/30 transition">
              <Icon icon="mdi:account" className="text-white" />
            </div>
            <button
              onClick={onToggleRightPanel}
              className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
              title="Ẩn/Hiện Side Panel"
            >
              <Icon
                icon={isRightPanelCollapsed ? 'mdi:chevron-left' : 'mdi:chevron-right'}
                className="text-base"
              />
            </button>
            {/* Language Selector */}
            <div className="relative z-[9999] overflow-visible" ref={langMenuRef}>
              <button
                onClick={() => setShowLangMenu(!showLangMenu)}
                className="flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-white/20 backdrop-blur-sm text-white font-medium text-sm hover:bg-white/30 transition-all duration-200 shadow-lg hover:shadow-xl border border-white/20 min-w-[100px]"
              >
                <Icon icon="mdi:globe" className="text-base" />
                <span>{langNames[currentLang]}</span>
                <Icon
                  icon="mdi:chevron-down"
                  className={`text-xs transition-transform duration-200 ${showLangMenu ? 'rotate-180' : ''}`}
                />
              </button>
              {showLangMenu && (
                <div className="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-[9999]">
                  {(['vi', 'en', 'ja'] as Language[]).map((lang, index) => (
                    <button
                      key={lang}
                      onClick={() => handleLanguageSelect(lang)}
                      className={`w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors flex items-center justify-between text-sm font-medium ${
                        index > 0 ? 'border-t border-gray-100' : ''
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <span className="text-lg">
                          {lang === 'vi' ? '🇻🇳' : lang === 'en' ? '🇺🇸' : '🇯🇵'}
                        </span>
                        <span className="text-gray-900">{langNames[lang]}</span>
                      </div>
                      {currentLang === lang && (
                        <Icon icon="mdi:check" className="text-[#0FA968]" />
                      )}
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}
