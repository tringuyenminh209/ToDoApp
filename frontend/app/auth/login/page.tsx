// frontend/app/auth/login/page.tsx
'use client';

import { useState, useEffect, useRef } from 'react';
import { useRouter } from 'next/navigation';
import { Icon } from '@iconify/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { login } from '@/lib/auth';
import { translations, type Language } from '@/lib/i18n';
import type { LoginCredentials } from '@/lib/auth';
import Image from 'next/image';

export default function LoginPage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('vi');
  const [showLangMenu, setShowLangMenu] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [rememberMe, setRememberMe] = useState(false);
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [logoError, setLogoError] = useState(false);
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

    // Sparkle効果
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

    // Particle効果
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

    // Star効果
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

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setIsLoading(true);

    try {
      const credentials: LoginCredentials = { email, password };
      const response = await login(credentials);
      
      if (response.token) {
        router.push('/dashboard');
      }
    } catch (err: any) {
      setError(t.errorMessage);
      setTimeout(() => setError(''), 3000);
    } finally {
      setIsLoading(false);
    }
  };

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
      <div className="w-full max-w-5xl mx-auto flex flex-col lg:flex-row gap-0 items-stretch min-h-[600px] relative z-10">
        {/* Left Section: Logo & Welcome */}
        <div className="w-full lg:w-1/2 flex">
          <div className="bg-white/20 backdrop-blur-sm rounded-2xl lg:rounded-l-2xl lg:rounded-r-none lg:border-r border-white/20 shadow-2xl p-6 md:p-8 w-full flex flex-col justify-center h-full">
            <div className="text-center mb-8">
              {/* Logo */}
              <div className="inline-flex items-center justify-center mb-4">
                {!logoError ? (
                  <Image
                    src="/logo/logo.svg"
                    alt="ToDoKizamu"
                    width={96}
                    height={96}
                    className="object-contain rounded-2xl"
                    priority
                    onError={() => setLogoError(true)}
                  />
                ) : (
                  <div className="flex items-center justify-center space-x-1">
                    <Icon icon="mdi:leaf" className="text-4xl md:text-5xl text-white" />
                    <Icon icon="mdi:leaf" className="text-4xl md:text-5xl text-white" />
                    <Icon icon="mdi:leaf" className="text-4xl md:text-5xl text-white" />
                  </div>
                )}
              </div>
              <h1 className="text-3xl md:text-4xl font-bold mb-4 text-white drop-shadow-lg">ToDoKizamu</h1>
              
              {/* Welcome Text */}
              <h2 className="text-xl md:text-2xl font-semibold mb-3 text-white drop-shadow-md">{t.welcome}</h2>
              <p className="text-sm md:text-base mb-2 leading-relaxed text-white/90 drop-shadow-sm">
                {t.description}
              </p>
              <div className="mt-4 space-y-2">
                <div className="flex items-center justify-center space-x-2 text-sm text-white/90">
                  <Icon icon="mdi:check-circle" className="text-white" />
                  <span>{t.feature1}</span>
                </div>
                <div className="flex items-center justify-center space-x-2 text-sm text-white/90">
                  <Icon icon="mdi:check-circle" className="text-white" />
                  <span>{t.feature2}</span>
                </div>
                <div className="flex items-center justify-center space-x-2 text-sm text-white/90">
                  <Icon icon="mdi:check-circle" className="text-white" />
                  <span>{t.feature3}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Right Section: Login Form */}
        <div className="w-full lg:w-1/2 flex">
          <div className="bg-white/20 backdrop-blur-sm rounded-2xl lg:rounded-r-2xl lg:rounded-l-none shadow-2xl p-6 md:p-8 w-full flex flex-col h-full justify-center">
            <h2 className="text-2xl md:text-3xl font-bold mb-6 text-center text-white drop-shadow-lg">{t.login}</h2>
            <form onSubmit={handleSubmit} className="space-y-5 flex flex-col">
              {/* Email Field */}
              <div>
                <label className="block text-sm font-medium mb-2 text-white drop-shadow-sm">
                  {t.email}
                </label>
                <div className="relative">
                  <div className="absolute left-4 top-1/2 transform -translate-y-1/2 z-10">
                    <Icon icon="mdi:email" className="text-gray-400" />
                  </div>
                  <Input
                    type="email"
                    id="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-[#0FA968] transition h-12 text-base"
                    placeholder={t.emailPlaceholder}
                  />
                </div>
              </div>

              {/* Password Field */}
              <div>
                <label className="block text-sm font-medium mb-2 text-white drop-shadow-sm">
                  {t.password}
                </label>
                <div className="relative">
                  <div className="absolute left-4 top-1/2 transform -translate-y-1/2 z-10">
                    <Icon icon="mdi:lock" className="text-gray-400" />
                  </div>
                  <Input
                    type="password"
                    id="password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-[#0FA968] transition h-12 text-base"
                    placeholder={t.passwordPlaceholder}
                  />
                </div>
              </div>

              {/* Remember & Forgot */}
              <div className="flex items-center justify-between">
                <label className="flex items-center cursor-pointer">
                  <input 
                    type="checkbox" 
                    checked={rememberMe}
                    onChange={(e) => setRememberMe(e.target.checked)}
                    className="w-5 h-5 rounded border-white/30 bg-white/20 text-[#0FA968] focus:ring-[#0FA968] cursor-pointer"
                  />
                  <span className="ml-2 text-sm text-white drop-shadow-sm">{t.rememberMe}</span>
                </label>
                <a href="#" className="text-sm font-medium hover:underline transition text-yellow-300 drop-shadow-sm">
                  {t.forgotPassword}
                </a>
              </div>

              {/* Error Message */}
              {error && (
                <div className="bg-red-500/20 backdrop-blur-sm border border-red-300/50 text-red-100 px-4 py-3 rounded-xl text-sm drop-shadow-sm">
                  {error}
                </div>
              )}

              {/* Login Button */}
              <Button
                type="submit"
                disabled={isLoading}
                className="w-full bg-[#0FA968] hover:bg-[#0B8C57] active:bg-[#09764B] text-white py-4 rounded-xl font-semibold transition shadow-lg hover:shadow-xl h-14 flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:ring-offset-2"
              >
                <Icon icon="mdi:arrow-right" />
                <span>{t.loginButton}</span>
              </Button>

              {/* Separator */}
              <div className="flex items-center my-6">
                <div className="flex-1 border-t border-white/30"></div>
                <span className="px-4 text-sm text-white/80">{t.or}</span>
                <div className="flex-1 border-t border-white/30"></div>
              </div>

              {/* Social Login Buttons */}
              <div className="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  className="flex items-center justify-center space-x-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl py-3 hover:bg-white/30 transition h-12 font-medium text-white drop-shadow-sm"
                >
                  <svg className="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                  </svg>
                  <span>Google</span>
                </button>
                <button
                  type="button"
                  className="flex items-center justify-center space-x-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl py-3 hover:bg-white/30 transition h-12 font-medium text-white drop-shadow-sm"
                >
                  <Icon icon="mdi:apple" className="text-xl" />
                  <span>Apple</span>
                </button>
              </div>
            </form>

            {/* Register Link */}
            <div className="mt-6 text-center">
              <p className="text-sm text-white/90">
                {t.noAccount}{' '}
                <a href="/auth/register" className="font-medium hover:underline transition text-blue-300 drop-shadow-sm">
                  {t.register}
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}