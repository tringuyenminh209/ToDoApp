// frontend/components/dashboard/Sidebar.tsx
'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { useAuthStore } from '@/store/auth-store';

interface SidebarProps {
  currentLang: Language;
  isCollapsed: boolean;
  onToggle: () => void;
}

export default function Sidebar({ currentLang, isCollapsed, onToggle }: SidebarProps) {
  const pathname = usePathname();
  const router = useRouter();
  const { logout } = useAuthStore();
  const t = translations[currentLang];

  const handleLogout = async () => {
    if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
      await logout();
      router.push('/auth/login');
    }
  };

  const menuItems = [
    { icon: 'mdi:home', label: t.home, href: '/dashboard' },
    { icon: 'mdi:format-list-checks', label: t.tasks, href: '/dashboard/tasks' },
    { icon: 'mdi:school', label: t.learning, href: '/dashboard/learning-paths' },
    { icon: 'mdi:book-open-variant', label: t.knowledge, href: '/dashboard/knowledge' },
    { icon: 'mdi:code-tags', label: t.cheatCode, href: '/dashboard/cheat-code' },
    { icon: 'mdi:chart-bar', label: t.analytics, href: '/dashboard/analytics' },
  ];

  return (
    <aside
      className={`${
        isCollapsed ? 'w-0 -ml-64 opacity-0' : 'w-64'
      } bg-white/10 backdrop-blur-md border-r border-white/20 shadow-xl relative z-10 overflow-y-auto transition-all duration-300 ease-in-out`}
    >
      <div className="p-4">
        <h2 className="text-lg font-bold text-white mb-6 drop-shadow-md">MENU</h2>
        <nav className="space-y-2">
          {menuItems.map((item) => {
            const isActive = pathname === item.href;
            return (
              <Link
                key={item.href}
                href={item.href}
                className={`flex items-center space-x-3 px-4 py-3 rounded-xl backdrop-blur-sm text-white transition ${
                  isActive
                    ? 'bg-white/30 shadow-lg'
                    : 'bg-white/20 hover:bg-white/30'
                }`}
              >
                <Icon icon={item.icon} className="text-lg" />
                <span className="font-medium">{item.label}</span>
              </Link>
            );
          })}
        </nav>
        <div className="border-t border-white/20 my-4"></div>
        <Link
          href="/dashboard/settings"
          className="flex items-center space-x-3 px-4 py-3 rounded-xl bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 transition"
        >
          <Icon icon="mdi:cog" className="text-lg" />
          <span className="font-medium">{t.settings}</span>
        </Link>
        <div className="border-t border-white/20 my-4"></div>
        <button
          onClick={handleLogout}
          className="w-full flex items-center space-x-3 px-4 py-3 rounded-xl bg-red-500/20 backdrop-blur-sm text-white hover:bg-red-500/30 transition border border-red-400/30"
        >
          <Icon icon="mdi:logout" className="text-lg" />
          <span className="font-medium">{t.logout}</span>
        </button>
      </div>
    </aside>
  );
}
