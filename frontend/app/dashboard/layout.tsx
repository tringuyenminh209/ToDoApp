'use client';

import { useEffect, useState, useRef } from 'react';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/store/auth-store';
import Sidebar from '@/components/dashboard/Sidebar';
import Header from '@/components/dashboard/Header';
import RightPanel from '@/components/dashboard/RightPanel';
import { type Language } from '@/lib/i18n';
import { subscribeFocusSession } from '@/lib/echo';

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const router = useRouter();
  const { isAuthenticated, checkAuth, isLoading, hasHydrated, user } = useAuthStore();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);
  const [isRightPanelCollapsed, setIsRightPanelCollapsed] = useState(false);
  const sparkleContainerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (hasHydrated) {
      checkAuth();
    }
  }, [hasHydrated, checkAuth]);

  useEffect(() => {
    const savedLang = localStorage.getItem('selectedLanguage') as Language;
    if (savedLang && (savedLang === 'vi' || savedLang === 'en' || savedLang === 'ja')) {
      setCurrentLang(savedLang);
    } else {
      setCurrentLang('ja');
      localStorage.setItem('selectedLanguage', 'ja');
    }
  }, []);

  useEffect(() => {
    const isMobile = typeof window !== 'undefined' && window.matchMedia('(max-width: 768px)').matches;
    if (isMobile) {
      setIsRightPanelCollapsed(true);
      return;
    }
    const sidebarState = localStorage.getItem('leftSidebarVisible');
    const rightPanelState = localStorage.getItem('rightPanelVisible');
    if (sidebarState === 'false') setIsSidebarCollapsed(true);
    if (rightPanelState === 'false') setIsRightPanelCollapsed(true);
  }, []);

  const handleLanguageChange = (lang: Language) => {
    setCurrentLang(lang);
    localStorage.setItem('selectedLanguage', lang);
    // Dispatch custom event to notify other components
    window.dispatchEvent(new Event('languageChange'));
    // Dispatch localeChanged event to trigger API data reload
    window.dispatchEvent(new CustomEvent('localeChanged', { detail: { locale: lang } }));
  };

  useEffect(() => {
    if (hasHydrated && !isLoading && !isAuthenticated) {
      router.push('/auth/login');
    }
  }, [hasHydrated, isAuthenticated, isLoading, router]);

  // リアルタイム: Reverb 有効時は WebSocket でフォーカスセッション同期（モバイル→Web）
  useEffect(() => {
    if (!user?.id) return;
    const unsubscribe = subscribeFocusSession(user.id);
    return () => {
      unsubscribe?.();
    };
  }, [user?.id]);

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
      star.style.animationDelay = Math.random() * 3 + 's';
      star.style.animationDuration = (2 + Math.random() * 2) + 's';
      container.appendChild(star);
    };

    for (let i = 0; i < 40; i++) {
      setTimeout(() => createSparkle(), i * 150);
    }
    for (let i = 0; i < 25; i++) {
      setTimeout(() => createParticle(), i * 250);
    }
    for (let i = 0; i < 80; i++) {
      setTimeout(() => createStar(), i * 100);
    }

    const sparkleInterval = setInterval(createSparkle, 400);
    const particleInterval = setInterval(createParticle, 1800);
    const starInterval = setInterval(createStar, 3000);

    return () => {
      clearInterval(sparkleInterval);
      clearInterval(particleInterval);
      clearInterval(starInterval);
    };
  }, []);

  const handleToggleSidebar = () => {
    const newState = !isSidebarCollapsed;
    setIsSidebarCollapsed(newState);
    localStorage.setItem('leftSidebarVisible', String(!newState));
  };

  const handleToggleRightPanel = () => {
    const newState = !isRightPanelCollapsed;
    setIsRightPanelCollapsed(newState);
    localStorage.setItem('rightPanelVisible', String(!newState));
  };

  if (!hasHydrated || isLoading) {
    return (
      <div className="min-h-screen bg-black flex items-center justify-center">
        <div className="text-white">Loading...</div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return null;
  }

  return (
    <div className="min-h-screen flex flex-col relative bg-black dashboard-layout">
      {/* Background Effects */}
      <div className="fixed inset-0 bg-black -z-10" />
      <div className="fixed inset-0 bg-gradient-to-br from-[rgba(15,169,104,0.15)] via-transparent to-[rgba(31,111,235,0.15)] animate-[backgroundShift_20s_ease-in-out_infinite] -z-10" />

      {/* Sparkle Container */}
      <div
        ref={sparkleContainerRef}
        className="fixed inset-0 pointer-events-none z-0"
      />

      {/* Animated Gradient Orbs */}
      <div className="fixed top-0 left-0 w-full h-full pointer-events-none z-0">
        <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-[#0FA968] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div className="absolute top-1/3 right-1/4 w-96 h-96 bg-[#1F6FEB] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div className="absolute bottom-1/4 left-1/3 w-96 h-96 bg-[#8B5CF6] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
      </div>

      {/* Header */}
      <Header
        currentLang={currentLang}
        onLanguageChange={handleLanguageChange}
        onToggleSidebar={handleToggleSidebar}
        onToggleRightPanel={handleToggleRightPanel}
        isSidebarCollapsed={isSidebarCollapsed}
        isRightPanelCollapsed={isRightPanelCollapsed}
      />

      {/* Main Layout: モバイルはヘッダー高さに合わせて calc を調整 */}
      <div className="flex h-[calc(100vh-3.5rem)] sm:h-[calc(100vh-4rem)] md:h-[calc(100vh-85px)] relative z-10">
        {/* Sidebar */}
        <Sidebar
          currentLang={currentLang}
          isCollapsed={isSidebarCollapsed}
          onToggle={handleToggleSidebar}
        />

        {/* Main Content: モバイルで横はみ出し防止 */}
        <main className="flex-1 overflow-y-auto overflow-x-hidden relative z-0 min-w-0 overscroll-behavior-y-auto">{children}</main>

        {/* Right Panel */}
        <RightPanel currentLang={currentLang} isCollapsed={isRightPanelCollapsed} onToggle={handleToggleRightPanel} />
      </div>
    </div>
  );
}
