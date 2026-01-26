// frontend/hooks/useLanguage.ts
'use client';

import { useState, useEffect, useCallback } from 'react';
import { type Language } from '@/lib/i18n';

/**
 * Hook to manage language/locale state globally
 * Automatically syncs with localStorage and triggers API reload
 */
export function useLanguage() {
  const [currentLang, setCurrentLang] = useState<Language>('ja');

  // Load language from localStorage on mount
  useEffect(() => {
    const loadLanguage = () => {
      if (typeof window === 'undefined') return;
      
      const savedLang = localStorage.getItem('selectedLanguage') as Language;
      if (savedLang && (savedLang === 'vi' || savedLang === 'en' || savedLang === 'ja')) {
        setCurrentLang(savedLang);
      } else {
        setCurrentLang('ja');
        localStorage.setItem('selectedLanguage', 'ja');
      }
    };

    loadLanguage();

    // Listen for language changes from other components
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

  // Change language and persist to localStorage
  const changeLanguage = useCallback((lang: Language) => {
    if (typeof window === 'undefined') return;
    
    setCurrentLang(lang);
    localStorage.setItem('selectedLanguage', lang);
    
    // Dispatch custom event to notify other components
    window.dispatchEvent(new Event('languageChange'));
    
    // Force reload of API data by dispatching a custom event
    // Components can listen to this to refetch data
    window.dispatchEvent(new CustomEvent('localeChanged', { detail: { locale: lang } }));
  }, []);

  return {
    currentLang,
    changeLanguage,
  };
}
