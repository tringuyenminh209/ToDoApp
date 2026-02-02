// frontend/app/auth/register/page.tsx
'use client';

import { useState, useEffect, useRef } from 'react';

/** APIエラーから表示用メッセージを抽出。auth.tsは response.data を throw するため err.response は存在しない */
function getRegisterErrorMessage(err: unknown, fallback: string): string {
  if (!err || typeof err !== 'object') return fallback;
  const e = err as { message?: string; errors?: Record<string, string[]> };
  if (e.errors && typeof e.errors === 'object') {
    const first = Object.values(e.errors)[0];
    if (Array.isArray(first) && typeof first[0] === 'string') return first[0];
  }
  if (typeof e.message === 'string' && e.message.trim()) return e.message;
  return fallback;
}
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { register } from '@/lib/auth';
import { translations, type Language } from '@/lib/i18n';
import type { RegisterData } from '@/lib/auth';
import Image from 'next/image';

export default function RegisterPage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [showLangMenu, setShowLangMenu] = useState(false);
  const [fullName, setFullName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [terms, setTerms] = useState(false);
  const [notifications, setNotifications] = useState(false);
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

  // アニメーション効果の生成（ログインページと同じ）
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

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    // バリデーション
    if (!fullName || !email || !password || !confirmPassword) {
      setError(t.errorFillAll);
      return;
    }

    if (password !== confirmPassword) {
      setError(t.errorPasswordMismatch);
      return;
    }

    if (password.length < 8) {
      setError(t.errorPasswordLength);
      return;
    }

    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/;
    if (!passwordRegex.test(password)) {
      setError(t.errorPasswordStrength); // 新しい翻訳キーが必要
      return;
    }

    if (!terms) {
      setError(t.errorTermsRequired);
      return;
    }

    setIsLoading(true);

    try {
      const data: RegisterData = {
        name: fullName,
        email,
        password,
      };
      const response = await register(data);
      
      if (response.token) {
        router.push('/auth/register-success');
      }
    } catch (err: any) {
      setError(getRegisterErrorMessage(err, t.errorRegisterFailed));
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
              <Link href="/" className="inline-block cursor-pointer hover:opacity-80 transition-opacity">
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
              </Link>
              
              {/* Welcome Text */}
              <h2 className="text-xl md:text-2xl font-semibold mb-3 text-white drop-shadow-md">{t.registerWelcome}</h2>
              <p className="text-sm md:text-base mb-2 leading-relaxed text-white/90 drop-shadow-sm">
                {t.registerDescription}
              </p>
              <div className="mt-4 space-y-2">
                <div className="flex items-center justify-center space-x-2 text-sm text-white/90">
                  <Icon icon="mdi:check-circle" className="text-white" />
                  <span>{t.registerFeature1}</span>
                </div>
                <div className="flex items-center justify-center space-x-2 text-sm text-white/90">
                  <Icon icon="mdi:check-circle" className="text-white" />
                  <span>{t.registerFeature2}</span>
                </div>
                <div className="flex items-center justify-center space-x-2 text-sm text-white/90">
                  <Icon icon="mdi:check-circle" className="text-white" />
                  <span>{t.registerFeature3}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Right Section: Register Form */}
        <div className="w-full lg:w-1/2 flex">
          <div className="bg-white/20 backdrop-blur-sm rounded-2xl lg:rounded-r-2xl lg:rounded-l-none shadow-2xl p-6 md:p-8 w-full flex flex-col h-full justify-center">
            <h2 className="text-2xl md:text-3xl font-bold mb-6 text-center text-white drop-shadow-lg">{t.register}</h2>
            <form onSubmit={handleSubmit} className="space-y-4 flex flex-col">
              {/* Full Name Field */}
              <div>
                <label className="block text-sm font-medium mb-2 text-white drop-shadow-sm">
                  {t.fullNamePlaceholder}
                </label>
                <div className="relative">
                  <div className="absolute left-4 top-1/2 transform -translate-y-1/2 z-10">
                    <Icon icon="mdi:account" className="text-gray-400" />
                  </div>
                  <Input
                    type="text"
                    id="fullname"
                    required
                    value={fullName}
                    onChange={(e) => setFullName(e.target.value)}
                    className="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-[#0FA968] transition h-12 text-base"
                    placeholder={t.fullNamePlaceholder}
                  />
                </div>
              </div>

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
                    type={showPassword ? 'text' : 'password'}
                    id="password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="w-full pl-12 pr-12 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-[#0FA968] transition h-12 text-base"
                    placeholder={t.passwordPlaceholder}
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword((prev) => !prev)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 z-10 p-1 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition"
                    aria-label={showPassword ? t.hidePasswordLabel : t.showPasswordLabel}
                  >
                    <Icon icon={showPassword ? 'mdi:eye-off' : 'mdi:eye'} className="text-xl" />
                  </button>
                </div>
              </div>

              {/* Confirm Password Field */}
              <div>
                <label className="block text-sm font-medium mb-2 text-white drop-shadow-sm">
                  {t.confirmPassword}
                </label>
                <div className="relative">
                  <div className="absolute left-4 top-1/2 transform -translate-y-1/2 z-10">
                    <Icon icon="mdi:lock" className="text-gray-400" />
                  </div>
                  <Input
                    type={showConfirmPassword ? 'text' : 'password'}
                    id="confirmPassword"
                    required
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    className="w-full pl-12 pr-12 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-[#0FA968] transition h-12 text-base"
                    placeholder={t.confirmPasswordPlaceholder}
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword((prev) => !prev)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 z-10 p-1 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition"
                    aria-label={showConfirmPassword ? t.hidePasswordLabel : t.showPasswordLabel}
                  >
                    <Icon icon={showConfirmPassword ? 'mdi:eye-off' : 'mdi:eye'} className="text-xl" />
                  </button>
                </div>
              </div>

              {/* Terms and Privacy Policy Checkbox */}
              <div>
                <label className="flex items-start cursor-pointer">
                  <input 
                    type="checkbox" 
                    checked={terms}
                    onChange={(e) => setTerms(e.target.checked)}
                    required
                    className="w-5 h-5 rounded border-white/30 bg-white/20 text-[#0FA968] focus:ring-[#0FA968] cursor-pointer mt-0.5"
                  />
                  <span className="ml-2 text-sm text-white drop-shadow-sm leading-relaxed">
                    {t.termsAgreement}{' '}
                    <a href="#" className="text-yellow-300 hover:underline font-medium">{t.termsOfService}</a>{' '}
                    {'and'}{' '}
                    <a href="#" className="text-yellow-300 hover:underline font-medium">{t.privacyPolicy}</a>
                  </span>
                </label>
              </div>

              {/* Notifications Checkbox */}
              <div>
                <label className="flex items-start cursor-pointer">
                  <input 
                    type="checkbox" 
                    checked={notifications}
                    onChange={(e) => setNotifications(e.target.checked)}
                    className="w-5 h-5 rounded border-white/30 bg-white/20 text-[#0FA968] focus:ring-[#0FA968] cursor-pointer mt-0.5"
                  />
                  <span className="ml-2 text-sm text-white drop-shadow-sm leading-relaxed">
                    {t.notifications}
                  </span>
                </label>
              </div>

              {/* Error Message */}
              {error && (
                <div className="bg-red-500/20 backdrop-blur-sm border border-red-300/50 text-red-100 px-4 py-3 rounded-xl text-sm drop-shadow-sm">
                  {error}
                </div>
              )}

              {/* Register Button */}
              <Button
                type="submit"
                disabled={isLoading}
                className="w-full bg-[#0FA968] hover:bg-[#0B8C57] active:bg-[#09764B] text-white py-4 rounded-xl font-semibold transition shadow-lg hover:shadow-xl h-14 flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:ring-offset-2 mt-2"
              >
                <Icon icon="mdi:account-plus" />
                <span>{t.registerButton}</span>
              </Button>
            </form>

            {/* Login Link */}
            <div className="mt-6 text-center">
              <p className="text-sm text-white/90">
                {t.hasAccount}{' '}
                <a href="/auth/login" className="font-medium hover:underline transition text-blue-300 drop-shadow-sm">
                  {t.loginNow}
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}