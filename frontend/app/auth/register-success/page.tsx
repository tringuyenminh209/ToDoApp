// frontend/app/auth/register-success/page.tsx
'use client';

import { useState, useEffect, useRef } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { Button } from '@/components/ui/button';
import { translations, type Language } from '@/lib/i18n';

export default function RegisterSuccessPage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('vi');
  const [showLangMenu, setShowLangMenu] = useState(false);
  const langMenuRef = useRef<HTMLDivElement>(null);
  const sparkleContainerRef = useRef<HTMLDivElement>(null);

  const t = translations[currentLang];

  // 言語メニューの外側クリックで閉じる
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (langMenuRef.current && !langMenuRef.current.contains(event.target as Node)) {
        setShowLangMenu(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  // アニメーション効果の生成
  useEffect(() => {
    const container = sparkleContainerRef.current;
    if (!container) return;

    const createSparkle = () => {
      const sparkle = document.createElement('div');
      sparkle.className = 'sparkle';
      sparkle.style.left = Math.random() * 100 + '%';
      sparkle.style.top = Math.random() * 100 + '%';
      sparkle.style.animationDelay = Math.random() * 3 + 's';
      sparkle.style.animationDuration = (2 + Math.random() * 2) + 's';
      container.appendChild(sparkle);
      setTimeout(() => sparkle.remove(), 5000);
    };

    const createParticle = () => {
      const particle = document.createElement('div');
      particle.className = 'particle';
      particle.style.left = Math.random() * 100 + '%';
      particle.style.top = '100%';
      particle.style.animationDelay = Math.random() * 2 + 's';
      particle.style.animationDuration = (6 + Math.random() * 4) + 's';
      container.appendChild(particle);
      setTimeout(() => particle.remove(), 12000);
    };

    const createStar = () => {
      const star = document.createElement('div');
      star.className = 'star';
      star.style.left = Math.random() * 100 + '%';
      star.style.top = Math.random() * 100 + '%';
      star.style.animationDelay = Math.random() * 2 + 's';
      container.appendChild(star);
    };

    // 初期化
    for (let i = 0; i < 30; i++) {
      setTimeout(() => createSparkle(), i * 200);
    }
    for (let i = 0; i < 20; i++) {
      setTimeout(() => createParticle(), i * 300);
    }
    for (let i = 0; i < 50; i++) {
      createStar();
    }

    // 継続的な生成
    const sparkleInterval = setInterval(createSparkle, 500);
    const particleInterval = setInterval(createParticle, 2000);

    return () => {
      clearInterval(sparkleInterval);
      clearInterval(particleInterval);
    };
  }, []);

  const selectLanguage = (lang: Language, langName: string) => {
    setCurrentLang(lang);
    setShowLangMenu(false);
  };

  const langNames: Record<Language, string> = {
    vi: 'Tiếng Việt',
    en: 'English',
    ja: '日本語',
  };

  return (
    <div className="min-h-screen flex p-4 md:p-8 relative overflow-x-hidden">
      {/* Background Effects */}
      <div className="fixed inset-0 bg-black -z-10" />
      <div className="fixed inset-0 bg-gradient-to-br from-[rgba(15,169,104,0.15)] via-transparent to-[rgba(31,111,235,0.15)] animate-[backgroundShift_20s_ease-in-out_infinite] -z-10" />
      
      {/* Sparkle Container */}
      <div 
        ref={sparkleContainerRef}
        className="fixed inset-0 pointer-events-none z-0"
      />

      {/* Language Selector */}
      <div className="absolute top-4 right-4 md:top-6 md:right-6 z-20">
        <div className="relative" ref={langMenuRef}>
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
            <div className="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
              {(['vi', 'en', 'ja'] as Language[]).map((lang, index) => (
                <button
                  key={lang}
                  onClick={() => selectLanguage(lang, langNames[lang])}
                  className={`w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors flex items-center justify-between text-sm font-medium ${
                    index > 0 ? 'border-t border-gray-100' : ''
                  }`}
                >
                  <div className="flex items-center space-x-3">
                    <span className="text-lg">
                      {lang === 'vi' ? '🇻🇳' : lang === 'en' ? '🇺🇸' : '🇯🇵'}
                    </span>
                    <span>{langNames[lang]}</span>
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

      {/* Main Container */}
      <div className="w-full max-w-4xl mx-auto flex items-center justify-center min-h-[600px] relative z-10">
        {/* Success Card */}
        <div className="bg-white/20 backdrop-blur-sm rounded-2xl shadow-2xl p-8 md:p-12 w-full flex flex-col items-center">
          {/* Success Checkmark */}
          <div className="success-checkmark mb-6">
            <div className="check-icon">
              <span className="icon-line line-tip"></span>
              <span className="icon-line line-long"></span>
            </div>
          </div>

          {/* Success Message */}
          <h1 className="text-3xl md:text-4xl font-bold mb-4 text-center text-white drop-shadow-lg success-message">
            {t.successTitle}
          </h1>
          <p className="text-lg md:text-xl mb-2 text-center text-white/90 drop-shadow-sm">
            {t.successMessage}
          </p>
          <p className="text-sm md:text-base mb-8 text-center text-white/80 drop-shadow-sm">
            {t.successDescription}
          </p>

          {/* Action Buttons */}
          <div className="flex flex-col sm:flex-row gap-4 w-full max-w-md">
            <Link href="/auth/login">
              <Button className="flex-1 w-full bg-[#0FA968] hover:bg-[#0B8C57] active:bg-[#09764B] text-white py-4 rounded-xl font-semibold transition shadow-lg hover:shadow-xl h-14 flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:ring-offset-2">
                <Icon icon="mdi:arrow-right" />
                <span>{t.successLoginButton}</span>
              </Button>
            </Link>
            <Link href="/">
              <Button className="flex-1 w-full bg-white/20 backdrop-blur-sm border border-white/30 hover:bg-white/30 text-white py-4 rounded-xl font-semibold transition shadow-lg hover:shadow-xl h-14 flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2">
                <Icon icon="mdi:home" />
                <span>{t.successHomeButton}</span>
              </Button>
            </Link>
          </div>

          {/* Additional Info */}
          <div className="mt-8 p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 w-full max-w-md">
            <p className="text-xs text-center flex items-center justify-center text-white/80">
              <Icon icon="mdi:information" className="mr-2" />
              <span>{t.successInfo}</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}