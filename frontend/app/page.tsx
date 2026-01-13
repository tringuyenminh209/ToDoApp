'use client';

import { useState, useEffect, useRef } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';

const langNames: Record<Language, string> = {
  vi: 'Tiếng Việt',
  en: 'English',
  ja: '日本語',
};

export default function LandingPage() {
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const t = translations[currentLang];
  const [showLangMenu, setShowLangMenu] = useState(false);
  const [logoError, setLogoError] = useState(false);
  const langMenuRef = useRef<HTMLDivElement>(null);
  const sparkleContainerRef = useRef<HTMLDivElement>(null);

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

  // 背景エフェクトの生成
  useEffect(() => {
    const container = sparkleContainerRef.current;
    if (!container) return;

    const createSparkle = () => {
      const sparkle = document.createElement('div');
      sparkle.className = 'sparkle';
      sparkle.style.cssText = `
        position: absolute;
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        opacity: 0;
        animation: sparkle 4s infinite;
        box-shadow: 0 0 10px rgba(15, 169, 104, 0.6), 0 0 20px rgba(31, 111, 235, 0.4);
        left: ${Math.random() * 100}%;
        top: ${Math.random() * 100}%;
        animation-delay: ${Math.random() * 3}s;
        animation-duration: ${2 + Math.random() * 2}s;
      `;
      container.appendChild(sparkle);
      setTimeout(() => sparkle.remove(), 5000);
    };

    const createParticle = () => {
      const particle = document.createElement('div');
      particle.className = 'particle';
      const isEven = Math.random() > 0.5;
      particle.style.cssText = `
        position: absolute;
        width: 3px;
        height: 3px;
        background: ${isEven ? 'rgba(15, 169, 104, 0.8)' : 'rgba(31, 111, 235, 0.8)'};
        border-radius: 50%;
        animation: float 10s infinite ease-in-out;
        box-shadow: 0 0 6px ${isEven ? 'rgba(15, 169, 104, 0.6)' : 'rgba(31, 111, 235, 0.6)'};
        left: ${Math.random() * 100}%;
        top: 100%;
        animation-delay: ${Math.random() * 2}s;
        animation-duration: ${6 + Math.random() * 4}s;
      `;
      container.appendChild(particle);
      setTimeout(() => particle.remove(), 12000);
    };

    const createStar = () => {
      const star = document.createElement('div');
      star.className = 'star';
      star.style.cssText = `
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        animation: twinkle 3s infinite;
        box-shadow: 0 0 8px rgba(15, 169, 104, 0.5);
        left: ${Math.random() * 100}%;
        top: ${Math.random() * 100}%;
        animation-delay: ${Math.random() * 2}s;
      `;
      container.appendChild(star);
    };

    // 初期化
    for (let i = 0; i < 25; i++) {
      setTimeout(() => createSparkle(), i * 250);
    }
    for (let i = 0; i < 15; i++) {
      setTimeout(() => createParticle(), i * 400);
    }
    for (let i = 0; i < 40; i++) {
      createStar();
    }

    // 継続的な生成
    const sparkleInterval = setInterval(createSparkle, 600);
    const particleInterval = setInterval(createParticle, 2500);

    return () => {
      clearInterval(sparkleInterval);
      clearInterval(particleInterval);
    };
  }, []);

  // ナビゲーションバーのスクロールエフェクト
  useEffect(() => {
    const navbar = document.querySelector('nav');
    if (!navbar) return;

    const handleScroll = () => {
      if (window.scrollY > 100) {
        (navbar as HTMLElement).style.background = 'rgba(0, 0, 0, 0.8)';
      } else {
        (navbar as HTMLElement).style.background = 'rgba(255, 255, 255, 0.05)';
      }
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const selectLanguage = (lang: Language) => {
    setCurrentLang(lang);
    setShowLangMenu(false);
    if (typeof window !== 'undefined') {
      localStorage.setItem('selectedLanguage', lang);
    }
  };

  // ページ読み込み時に保存された言語を復元
  useEffect(() => {
    if (typeof window !== 'undefined') {
      const savedLang = localStorage.getItem('selectedLanguage') as Language;
      if (savedLang && (savedLang === 'vi' || savedLang === 'en' || savedLang === 'ja')) {
        setCurrentLang(savedLang);
      }
    }
  }, []);

  return (
    <div className="min-h-screen bg-black text-white relative overflow-x-hidden">
      {/* Background Effects */}
      <div className="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_20%_50%,rgba(15,169,104,0.15)_0%,transparent_50%),radial-gradient(circle_at_80%_80%,rgba(31,111,235,0.15)_0%,transparent_50%),radial-gradient(circle_at_40%_20%,rgba(139,92,246,0.1)_0%,transparent_50%)] animate-[backgroundShift_20s_ease-in-out_infinite]" />
      <div
        ref={sparkleContainerRef}
        className="fixed inset-0 pointer-events-none z-[1]"
      />

      {/* Navbar */}
      <nav className="fixed top-0 left-0 right-0 z-50 glass py-4">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-3">
              {!logoError ? (
                <Image
                  src="/logo/logo.svg"
                  alt="ToDoKizamu"
                  width={40}
                  height={40}
                  className="object-contain rounded-xl"
                  priority
                  onError={() => setLogoError(true)}
                />
              ) : (
                <div className="w-10 h-10 bg-gradient-to-br from-[#0FA968] to-[#1F6FEB] rounded-xl flex items-center justify-center">
                  <Icon icon="mdi:leaf" className="text-2xl text-white" />
                </div>
              )}
              <span className="text-xl font-bold text-white">ToDoKizamu</span>
            </div>
            <div className="flex items-center space-x-3">
              {/* Language Selector */}
              <div className="relative" ref={langMenuRef}>
                <button
                  onClick={() => setShowLangMenu(!showLangMenu)}
                  className="flex items-center justify-center space-x-2 px-4 py-2 rounded-xl bg-white/10 backdrop-blur-sm text-white font-medium text-sm hover:bg-white/20 transition-all duration-200 border border-white/20 min-w-[100px]"
                >
                  <Icon icon="mdi:globe" className="text-base" />
                  <span>{langNames[currentLang]}</span>
                  <Icon
                    icon="mdi:chevron-down"
                    className={`text-xs transition-transform duration-200 ${showLangMenu ? 'rotate-180' : ''}`}
                  />
                </button>
                {showLangMenu && (
                  <div className="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50">
                    {(['vi', 'en', 'ja'] as Language[]).map((lang, index) => (
                      <button
                        key={lang}
                        onClick={() => selectLanguage(lang)}
                        className={`w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors flex items-center justify-between text-sm font-medium text-[#0B1220] ${
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
              <Link href="/auth/login" className="px-4 py-2 text-white/80 hover:text-white transition">
                {t.landingNavLogin}
              </Link>
              <Link
                href="/auth/register"
                className="px-5 py-2 bg-white text-black rounded-lg font-semibold hover:bg-white/90 transition"
              >
                {t.register}
              </Link>
            </div>
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <section className="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8 min-h-screen flex items-center">
        <div className="max-w-7xl mx-auto w-full relative z-10">
          <div className="text-center mb-12">
            {/* Badge */}
            <div className="inline-flex items-center space-x-2 px-4 py-2 rounded-full glass mb-8">
              <Icon icon="mdi:sparkles" className="text-[#0FA968]" />
              <span className="text-sm text-white/90">{t.landingBadge}</span>
            </div>

            {/* Headline */}
            <h1 className="text-6xl md:text-7xl lg:text-8xl font-black mb-6 leading-tight">
              <span className="gradient-text block">{t.landingHeadline1}</span>
              <span className="gradient-text block">{t.landingHeadline2}</span>
          </h1>

            {/* Subtext */}
            <p className="text-xl md:text-2xl text-zinc-400 max-w-2xl mx-auto mb-10 leading-relaxed whitespace-pre-line">
              {t.landingSubtext}
            </p>

            {/* CTA Buttons */}
            <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
              <Link
                href="/auth/register"
                className="px-8 py-4 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl font-semibold text-lg transition shadow-lg hover:shadow-xl"
              >
                {t.landingCTAStart}
              </Link>
              <Link
                href="/dashboard/tasks"
                className="px-8 py-4 glass text-white rounded-xl font-semibold text-lg transition hover:bg-white/10 flex items-center space-x-2"
              >
                <span>{t.landingCTADemo}</span>
                <Icon icon="mdi:arrow-right" />
              </Link>
            </div>

            {/* Hero Image / Mockup */}
            <div className="relative mt-16">
              <div className="dashboard-mockup bg-zinc-900 rounded-2xl p-6 shadow-2xl border border-zinc-800 max-w-4xl mx-auto">
                <div className="bg-zinc-950 rounded-xl p-4 border border-zinc-800">
                  <div className="space-y-4">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center space-x-2">
                        <div className="w-3 h-3 bg-red-500 rounded-full" />
                        <div className="w-3 h-3 bg-yellow-500 rounded-full" />
                        <div className="w-3 h-3 bg-green-500 rounded-full" />
                      </div>
                      <div className="text-zinc-500 text-sm">{t.landingDashboardTitle}</div>
                    </div>
                    <div className="grid grid-cols-3 gap-4 mt-6">
                      <div className="bg-zinc-800 rounded-lg p-3 border border-zinc-700">
                        <div className="text-zinc-400 text-xs mb-2">{t.landingTodo}</div>
                        <div className="h-20 bg-zinc-700 rounded" />
                      </div>
                      <div className="bg-zinc-800 rounded-lg p-3 border border-zinc-700">
                        <div className="text-zinc-400 text-xs mb-2">{t.landingInProgress}</div>
                        <div className="h-20 bg-[#0FA968]/20 border border-[#0FA968]/30 rounded" />
                      </div>
                      <div className="bg-zinc-800 rounded-lg p-3 border border-zinc-700">
                        <div className="text-zinc-400 text-xs mb-2">{t.landingDone}</div>
                        <div className="h-20 bg-zinc-700 rounded" />
                      </div>
                    </div>
                    <div className="flex items-center justify-center space-x-4 mt-6">
                      <div className="flex items-center space-x-2 text-zinc-400">
                        <Icon icon="mdi:clock" />
                        <span className="text-sm">25:00</span>
                      </div>
                      <div className="w-32 h-2 bg-zinc-800 rounded-full overflow-hidden">
                        <div className="h-full bg-[#0FA968] rounded-full w-[60%]" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="absolute bottom-[-50px] left-1/2 transform -translate-x-1/2 w-4/5 h-[200px] -z-10 bg-[radial-gradient(ellipse,rgba(15,169,104,0.4)_0%,transparent_70%)] blur-[40px]" />
            </div>
          </div>
        </div>
      </section>

      {/* Social Proof Section */}
      <section className="py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="max-w-7xl mx-auto">
          <p className="text-center text-zinc-500 text-sm mb-8">{t.landingSocialProof}</p>
          <div className="flex flex-wrap items-center justify-center gap-8 md:gap-12 opacity-60">
            <div className="text-white text-2xl font-bold">Next.js</div>
            <div className="text-white text-2xl font-bold">Laravel</div>
            <div className="text-white text-2xl font-bold">Docker</div>
            <div className="text-white text-2xl font-bold">Tailwind</div>
          </div>
        </div>
      </section>

      {/* Bento Grid Section */}
      <section id="features" className="py-20 px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-4">{t.landingFeaturesTitle}</h2>
            <p className="text-xl text-zinc-400">{t.landingFeaturesSubtitle}</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* Card 1: Deep Work */}
            <div className="bento-card bg-zinc-900 rounded-3xl p-8 border border-zinc-800">
              <div className="w-12 h-12 bg-[#0FA968]/20 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="mdi:clock" className="text-2xl text-[#0FA968]" />
              </div>
              <h3 className="text-2xl font-bold text-white mb-3">{t.landingFeatureFocusTitle}</h3>
              <p className="text-zinc-400 mb-6">{t.landingFeatureFocusDesc}</p>
              <div className="bg-zinc-800 rounded-xl p-4 h-32 flex items-center justify-center border border-zinc-700">
                <Icon icon="mdi:hourglass" className="text-4xl text-zinc-600" />
              </div>
            </div>

            {/* Card 2: Learning */}
            <div className="bento-card bg-zinc-900 rounded-3xl p-8 border border-zinc-800">
              <div className="w-12 h-12 bg-[#1F6FEB]/20 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="mdi:routes" className="text-2xl text-[#1F6FEB]" />
              </div>
              <h3 className="text-2xl font-bold text-white mb-3">{t.landingFeatureSkillTitle}</h3>
              <p className="text-zinc-400 mb-6">{t.landingFeatureSkillDesc}</p>
              <div className="bg-zinc-800 rounded-xl p-4 h-32 flex items-center justify-center border border-zinc-700">
                <Icon icon="mdi:sitemap" className="text-4xl text-zinc-600" />
              </div>
            </div>

            {/* Card 3: AI */}
            <div className="bento-card bg-zinc-900 rounded-3xl p-8 border border-zinc-800">
              <div className="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="mdi:robot" className="text-2xl text-purple-400" />
              </div>
              <h3 className="text-2xl font-bold text-white mb-3">{t.landingFeatureAITitle}</h3>
              <p className="text-zinc-400 mb-6">{t.landingFeatureAIDesc}</p>
              <div className="bg-zinc-800 rounded-xl p-4 h-32 flex items-center justify-center border border-zinc-700">
                <Icon icon="mdi:chat" className="text-4xl text-zinc-600" />
              </div>
            </div>

            {/* Card 4: CheatCode IDE */}
            <div className="bento-card md:col-span-2 bg-zinc-900 rounded-3xl p-8 border border-zinc-800">
              <div className="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="mdi:code-tags" className="text-2xl text-orange-400" />
              </div>
              <h3 className="text-2xl font-bold text-white mb-3">{t.landingFeatureCodeTitle}</h3>
              <p className="text-zinc-400 mb-6">{t.landingFeatureCodeDesc}</p>
              <div className="bg-zinc-800 rounded-xl p-4 h-40 flex items-center justify-center border border-zinc-700">
                <div className="text-left font-mono text-zinc-600 text-sm">
                  <div className="mb-2">
                    <span className="text-purple-400">function</span>{' '}
                    <span className="text-blue-400">solve</span>() {'{'}
                  </div>
                  <div className="ml-4 mb-2">
                    <span className="text-green-400">return</span>{' '}
                    <span className="text-yellow-400">&quot;Hello World&quot;</span>;
                  </div>
                  <div>{'}'}</div>
                </div>
              </div>
            </div>

            {/* Card 5: Analytics */}
            <div className="bento-card bg-zinc-900 rounded-3xl p-8 border border-zinc-800">
              <div className="w-12 h-12 bg-pink-500/20 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="mdi:chart-line" className="text-2xl text-pink-400" />
              </div>
              <h3 className="text-2xl font-bold text-white mb-3">{t.landingFeatureHeatmapTitle}</h3>
              <p className="text-zinc-400 mb-6">{t.landingFeatureHeatmapDesc}</p>
              <div className="bg-zinc-800 rounded-xl p-4 h-40 border border-zinc-700">
                <div className="flex flex-col h-full">
                  {/* Legend */}
                  <div className="flex items-center justify-between mb-2">
                    <span className="text-xs text-zinc-400">Less</span>
                    <div className="flex items-center gap-1">
                      <div className="w-2.5 h-2.5 rounded bg-zinc-700" />
                      <div className="w-2.5 h-2.5 rounded bg-[#0FA968]/20" />
                      <div className="w-2.5 h-2.5 rounded bg-[#0FA968]/40" />
                      <div className="w-2.5 h-2.5 rounded bg-[#0FA968]/60" />
                      <div className="w-2.5 h-2.5 rounded bg-[#0FA968]/80" />
                      <div className="w-2.5 h-2.5 rounded bg-[#0FA968]" />
                    </div>
                    <span className="text-xs text-zinc-400">More</span>
                  </div>
                  {/* Heatmap Grid */}
                  <div className="flex-1 flex items-center justify-center">
                    <div className="grid grid-cols-7 gap-1">
                      {[
                        // Week 1
                        'bg-[#0FA968]', 'bg-[#0FA968]/80', 'bg-[#0FA968]/60', 'bg-zinc-700', 'bg-[#0FA968]/40', 'bg-[#0FA968]', 'bg-[#0FA968]/80',
                        // Week 2
                        'bg-[#0FA968]/60', 'bg-[#0FA968]', 'bg-[#0FA968]/80', 'bg-[#0FA968]/40', 'bg-zinc-700', 'bg-[#0FA968]/60', 'bg-[#0FA968]',
                        // Week 3
                        'bg-[#0FA968]', 'bg-[#0FA968]/60', 'bg-zinc-700', 'bg-[#0FA968]/80', 'bg-[#0FA968]', 'bg-[#0FA968]/40', 'bg-[#0FA968]/60',
                        // Week 4
                        'bg-[#0FA968]/80', 'bg-[#0FA968]', 'bg-[#0FA968]/60', 'bg-[#0FA968]/40', 'bg-[#0FA968]', 'bg-zinc-700', 'bg-[#0FA968]/80',
                        // Week 5
                        'bg-[#0FA968]/40', 'bg-[#0FA968]/60', 'bg-[#0FA968]', 'bg-[#0FA968]/80', 'bg-zinc-700', 'bg-[#0FA968]/60', 'bg-[#0FA968]',
                      ].map((bgClass, i) => (
                        <div key={i} className={`w-3.5 h-3.5 rounded ${bgClass} transition-all hover:scale-110`} />
                      ))}
                    </div>
                  </div>
                  {/* Month Label */}
                  <div className="flex items-center justify-center mt-2">
                    <span className="text-xs text-zinc-500">Last 5 weeks</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Detailed Breakdown Section */}
      <section id="method" className="py-20 px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="max-w-7xl mx-auto space-y-32">
          {/* Knowledge Base */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div className="order-2 lg:order-1">
              <div className="bg-zinc-900 rounded-2xl p-6 border border-zinc-800 shadow-2xl">
                <div className="bg-zinc-950 rounded-lg p-4 font-mono text-sm text-zinc-400">
                  <div className="mb-2"># ナレッジベース</div>
                  <div className="mb-2">## あなたの第二の脳</div>
                  <div className="text-zinc-500">知識を無駄にしないで...</div>
                </div>
              </div>
            </div>
            <div className="order-1 lg:order-2">
              <div className="inline-block px-3 py-1 bg-[#0FA968]/20 text-[#0FA968] rounded-full text-sm font-semibold mb-4">
                {t.landingKnowledgeBadge}
              </div>
              <h3 className="text-4xl font-bold text-white mb-4">{t.landingKnowledgeTitle}</h3>
              <p className="text-xl text-zinc-400 leading-relaxed">
                {t.landingKnowledgeDesc}
              </p>
            </div>
          </div>

          {/* Gamification */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
              <div className="inline-block px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm font-semibold mb-4">
                {t.landingGamificationBadge}
              </div>
              <h3 className="text-4xl font-bold text-white mb-4">{t.landingGamificationTitle}</h3>
              <p className="text-xl text-zinc-400 leading-relaxed">
                {t.landingGamificationDesc}
              </p>
            </div>
            <div>
              <div className="bg-zinc-900 rounded-2xl p-8 border border-zinc-800 shadow-2xl">
                <div className="flex items-center justify-between mb-6">
                  <div>
                    <div className="text-zinc-400 text-sm mb-1">{t.landingGamificationLevel}</div>
                    <div className="text-3xl font-bold text-white">{t.landingGamificationLevelValue}</div>
                  </div>
                  <div className="text-4xl">
                    <Icon icon="mdi:fire" className="text-orange-400" />
                  </div>
                </div>
                <div className="mb-4">
                  <div className="flex items-center justify-between text-sm text-zinc-400 mb-2">
                    <span>{t.landingGamificationProgress}</span>
                    <span>2,450 / 3,000</span>
                  </div>
                  <div className="w-full bg-zinc-800 rounded-full h-3">
                    <div className="bg-gradient-to-r from-[#0FA968] to-[#1F6FEB] h-3 rounded-full w-[82%]" />
                  </div>
                </div>
                <div className="flex items-center space-x-4">
                  <div className="flex -space-x-2">
                    <div className="w-8 h-8 bg-yellow-500 rounded-full border-2 border-zinc-900 flex items-center justify-center">
                      <Icon icon="mdi:trophy" className="text-xs text-white" />
                    </div>
                    <div className="w-8 h-8 bg-blue-500 rounded-full border-2 border-zinc-900 flex items-center justify-center">
                      <Icon icon="mdi:star" className="text-xs text-white" />
                    </div>
                    <div className="w-8 h-8 bg-green-500 rounded-full border-2 border-zinc-900 flex items-center justify-center">
                      <Icon icon="mdi:medal" className="text-xs text-white" />
                    </div>
                  </div>
                  <div className="text-zinc-400 text-sm">
                    <span className="font-semibold text-white">12 {t.landingGamificationStreak}</span> 🔥
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Final CTA Section */}
      <section className="py-32 px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-5xl md:text-6xl font-bold text-white mb-6">{t.landingCTATitle}</h2>
          <p className="text-2xl text-zinc-400 mb-10">{t.landingCTASubtitle}</p>
          <Link
            href="/auth/register"
            className="inline-block px-10 py-5 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl font-semibold text-lg transition shadow-lg hover:shadow-xl mb-4"
          >
            {t.landingCTAButton}
          </Link>
          <p className="text-sm text-zinc-500">{t.landingCTANoCard}</p>
        </div>
      </section>

      {/* Footer */}
      <footer className="border-t border-zinc-800 py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
            <div>
              <div className="flex items-center space-x-2 mb-4">
                {!logoError ? (
                  <Image
                    src="/logo/logo.svg"
                    alt="ToDoKizamu"
                    width={32}
                    height={32}
                    className="object-contain rounded-lg"
                    onError={() => setLogoError(true)}
                  />
                ) : (
                  <div className="w-8 h-8 bg-gradient-to-br from-[#0FA968] to-[#1F6FEB] rounded-lg flex items-center justify-center">
                    <Icon icon="mdi:leaf" className="text-lg text-white" />
                  </div>
                )}
                <span className="font-bold text-white">ToDoKizamu</span>
              </div>
            </div>
            <div>
              <h4 className="text-white font-semibold mb-4">{t.landingFooterProduct}</h4>
              <ul className="space-y-2 text-zinc-400 text-sm">
                <li>
                  <a href="#" className="hover:text-white transition">
                    {t.landingFooterUpdates}
                  </a>
                </li>
                <li>
                  <a href="#pricing" className="hover:text-white transition">
                    {t.landingNavPricing}
                  </a>
                </li>
              </ul>
            </div>
            <div>
              <h4 className="text-white font-semibold mb-4">{t.landingFooterResources}</h4>
              <ul className="space-y-2 text-zinc-400 text-sm">
                <li>
                  <a href="#" className="hover:text-white transition">
                    {t.landingFooterCommunity}
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition">
                    {t.landingFooterHelp}
                  </a>
                </li>
              </ul>
            </div>
            <div>
              <h4 className="text-white font-semibold mb-4">{t.landingFooterCompany}</h4>
              <ul className="space-y-2 text-zinc-400 text-sm">
                <li>
                  <a href="#" className="hover:text-white transition">
                    {t.landingFooterAbout}
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition">
                    {t.landingFooterContact}
                  </a>
                </li>
              </ul>
            </div>
            <div>
              <h4 className="text-white font-semibold mb-4">{t.landingFooterLegal}</h4>
              <ul className="space-y-2 text-zinc-400 text-sm">
                <li>
                  <a href="#" className="hover:text-white transition">
                    {t.landingFooterPrivacy}
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition">
                    {t.landingFooterTerms}
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div className="border-t border-zinc-800 pt-8 text-center text-zinc-500 text-sm">
            <p>{t.landingFooterCopyright}</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
