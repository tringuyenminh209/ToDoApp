// frontend/components/dashboard/Header.tsx
'use client';

import { useState, useRef, useEffect } from 'react';
import Link from 'next/link';
import Image from 'next/image';
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
  const [logoError, setLogoError] = useState(false);
  const langMenuRef = useRef<HTMLDivElement>(null);
  const t = translations[currentLang];

  const langNames: Record<Language, string> = {
    vi: 'Tiếng Việt',
    en: 'English',
    ja: '日本語',
  };
  const langFlags: Record<Language, string> = {
    vi: '/flags/vi.svg',
    en: '/flags/en.svg',
    ja: '/flags/ja.svg',
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
    // APIデータの再読み込みをトリガー
    window.dispatchEvent(new CustomEvent('localeChanged', { detail: { locale: lang } }));
  };

  return (
    <header className="bg-white/10 backdrop-blur-md shadow-xl border-b border-white/20 relative z-50 overflow-visible">
      <div className="w-full px-3 sm:px-6 lg:px-8 py-2.5 sm:py-3 overflow-visible">
        <div className="flex items-center justify-between gap-2 overflow-visible flex-wrap">
          <div className="flex items-center gap-2 sm:gap-4 min-w-0 flex-1">
            <div className="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shadow-lg relative overflow-hidden flex-shrink-0">
              {!logoError ? (
                <Image
                  src="/logo/logo.svg"
                  alt="ToDoKizamu Logo"
                  width={40}
                  height={40}
                  className="object-contain rounded-2xl w-full h-full"
                  style={{ objectFit: 'contain' }}
                  onError={() => setLogoError(true)}
                />
              ) : (
                <div className="flex items-center justify-center space-x-0.5">
                  <Icon icon="mdi:leaf" className="text-xs text-white" />
                  <Icon icon="mdi:leaf" className="text-xs text-white" />
                  <Icon icon="mdi:leaf" className="text-xs text-white" />
                </div>
              )}
            </div>
            <div className="flex flex-col min-w-0">
              <h1 className="text-base sm:text-xl font-bold text-white drop-shadow-lg truncate">{t.dashboardTitle}</h1>
              <p className="text-[10px] sm:text-xs text-white/70 hidden sm:block">{t.dashboardSubtitle}</p>
            </div>
            <div className="hidden md:flex flex-1 max-w-md">
              <div className="relative w-full">
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
          <div className="flex items-center gap-2 sm:gap-3 overflow-visible flex-shrink-0">
            <button
              onClick={onToggleSidebar}
              className="p-2.5 min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20 inline-flex items-center justify-center"
              title="Ẩn/Hiện Menu"
              aria-label="Toggle sidebar"
            >
              <Icon
                icon={isSidebarCollapsed ? 'mdi:chevron-right' : 'mdi:chevron-left'}
                className="text-base"
              />
            </button>
            <button
              className="p-2.5 min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20 relative inline-flex items-center justify-center"
              title="Thông báo"
              aria-label="Notifications"
            >
              <Icon icon="mdi:bell" className="text-base" />
              <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" />
            </button>
            <div className="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center cursor-pointer hover:bg-white/30 transition flex-shrink-0" role="img" aria-label="Account">
              <Icon icon="mdi:account" className="text-white" />
            </div>
            <button
              onClick={onToggleRightPanel}
              className="p-2.5 min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20 inline-flex items-center justify-center"
              title="Ẩn/Hiện Side Panel"
              aria-label="Toggle right panel"
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
                className="flex items-center justify-center gap-1.5 sm:space-x-2 px-2.5 sm:px-4 py-2.5 rounded-xl bg-white/20 backdrop-blur-sm text-white font-medium text-sm hover:bg-white/30 transition-all duration-200 shadow-lg hover:shadow-xl border border-white/20 min-w-[44px] sm:min-w-[100px]"
                aria-label="Language"
                aria-haspopup="listbox"
              >
                <Icon icon="mdi:globe" className="text-base flex-shrink-0" />
                <span className="hidden sm:inline truncate max-w-[5rem]">{langNames[currentLang]}</span>
                <Icon
                  icon="mdi:chevron-down"
                  className={`text-xs flex-shrink-0 transition-transform duration-200 ${showLangMenu ? 'rotate-180' : ''}`}
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
                        <Image src={langFlags[lang]} alt={langNames[lang]} width={18} height={12} />
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
