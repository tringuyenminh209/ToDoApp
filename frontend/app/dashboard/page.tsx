'use client';

import { useState } from 'react';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { useAuthStore } from '@/store/auth-store';

export default function DashboardPage() {
  const { user } = useAuthStore();
  const [currentLang] = useState<Language>('ja');
  const t = translations[currentLang];

  const dashboardCards = [
    {
      title: t.tasks,
      description: t.manageTasks,
      href: '/dashboard/tasks',
      icon: 'mdi:format-list-checks',
      color: 'from-[#0FA968] to-[#0B8C57]',
    },
    {
      title: t.learning,
      description: t.trackLearning,
      href: '/dashboard/learning-paths',
      icon: 'mdi:school',
      color: 'from-[#1F6FEB] to-[#1E40AF]',
    },
    {
      title: t.knowledge,
      description: t.buildKnowledge,
      href: '/dashboard/knowledge',
      icon: 'mdi:book-open-variant',
      color: 'from-[#8B5CF6] to-[#7C3AED]',
    },
    {
      title: t.analytics,
      description: t.viewStatistics,
      href: '/dashboard/analytics',
      icon: 'mdi:chart-bar',
      color: 'from-[#EC4899] to-[#DB2777]',
    },
  ];

  return (
    <div className="p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-white drop-shadow-lg mb-2">
          {t.goodMorning}, {user?.name || 'User'}!
        </h1>
        <p className="text-white/70">{t.dashboardSubtitle}</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {dashboardCards.map((card) => (
          <Link
            key={card.href}
            href={card.href}
            className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl hover:bg-white/20 transition-all group"
          >
            <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${card.color} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform`}>
              <Icon icon={card.icon} className="text-2xl text-white" />
            </div>
            <h3 className="text-xl font-bold text-white mb-2">{card.title}</h3>
            <p className="text-white/70 text-sm">{card.description}</p>
          </Link>
        ))}
      </div>

      <div className="mt-8 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
        <h2 className="text-xl font-bold text-white mb-4">{t.quickActions}</h2>
        <div className="flex flex-wrap gap-4">
          <Link
            href="/dashboard/tasks"
            className="px-6 py-3 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:plus" />
            <span>{t.createTask}</span>
          </Link>
          <Link
            href="/dashboard/learning-paths"
            className="px-6 py-3 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
          >
            <Icon icon="mdi:plus" />
            <span>{t.newLearningPath}</span>
          </Link>
        </div>
      </div>
    </div>
  );
}
