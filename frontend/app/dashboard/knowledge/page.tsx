'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { knowledgeService, KnowledgeItem, KnowledgeCategory } from '@/lib/services/knowledgeService';

interface CategoryWithItems extends KnowledgeCategory {
  items?: KnowledgeItem[];
  expanded?: boolean;
}

export default function KnowledgePage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [allCategories, setAllCategories] = useState<KnowledgeCategory[]>([]);
  const [allItems, setAllItems] = useState<KnowledgeItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [filterType, setFilterType] = useState<string>('');
  const [currentCategoryId, setCurrentCategoryId] = useState<number | null>(null);
  const [breadcrumbPath, setBreadcrumbPath] = useState<KnowledgeCategory[]>([]);
  const t = translations[currentLang];

  useEffect(() => {
    const loadLanguage = () => {
      const savedLang = localStorage.getItem('selectedLanguage') as Language;
      if (savedLang && (savedLang === 'vi' || savedLang === 'en' || savedLang === 'ja')) {
        setCurrentLang(savedLang);
      } else {
        setCurrentLang('ja');
        localStorage.setItem('selectedLanguage', 'ja');
      }
    };

    loadLanguage();

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

  const loadData = useCallback(async () => {
    try {
      setLoading(true);
      
      const categoryResponse = await knowledgeService.getCategories();
      let categories: KnowledgeCategory[] = [];
      if (categoryResponse.success && categoryResponse.data) {
        categories = Array.isArray(categoryResponse.data.data) 
          ? categoryResponse.data.data 
          : (Array.isArray(categoryResponse.data) ? categoryResponse.data : []);
      } else if (Array.isArray(categoryResponse.data)) {
        categories = categoryResponse.data;
      }
      
      setAllCategories(categories);

      // Load all knowledge items
      const params: any = {};
      if (filterType) params.type = filterType;
      if (searchQuery) params.search = searchQuery;

      const itemsResponse = await knowledgeService.getKnowledgeItems(params);
      let items: KnowledgeItem[] = [];
      if (itemsResponse.success && itemsResponse.data) {
        items = Array.isArray(itemsResponse.data.data) 
          ? itemsResponse.data.data 
          : (Array.isArray(itemsResponse.data) ? itemsResponse.data : []);
      } else if (Array.isArray(itemsResponse.data)) {
        items = itemsResponse.data;
      }
      
      setAllItems(items);
    } catch (error) {
      console.error('Failed to load data:', error);
    } finally {
      setLoading(false);
    }
  }, [filterType, searchQuery]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const navigateToRoot = () => {
    setCurrentCategoryId(null);
    setBreadcrumbPath([]);
  };

  const navigateToCategory = (category: KnowledgeCategory) => {
    setCurrentCategoryId(category.id);
    const path: KnowledgeCategory[] = [];
    let current: KnowledgeCategory | undefined = category;
    while (current) {
      path.unshift(current);
      current = allCategories.find(c => c.id === current!.parent_id);
    }
    setBreadcrumbPath(path);
  };

  const navigateToBreadcrumb = (index: number) => {
    if (index === -1) {
      navigateToRoot();
    } else {
      const category = breadcrumbPath[index];
      if (category) {
        navigateToCategory(category);
      }
    }
  };

  const getCurrentFolderContents = () => {
    const subFolders = allCategories.filter(cat => cat.parent_id === currentCategoryId);
    
    const files = allItems.filter(item => {
      if (currentCategoryId === null) {
        return !item.category_id;
      } else {
        return item.category_id === currentCategoryId;
      }
    });

    return { subFolders, files };
  };

  const getItemTypeLabel = (type: string) => {
    switch (type) {
      case 'note':
        return t.note || 'Note';
      case 'code_snippet':
        return t.codeSnippet || 'Code Snippet';
      case 'exercise':
        return t.exercise || 'Exercise';
      case 'resource_link':
        return t.resourceLink || 'Resource Link';
      case 'attachment':
        return t.attachment || 'Attachment';
      default:
        return type;
    }
  };

  const getItemTypeIcon = (type: string) => {
    switch (type) {
      case 'note':
        return 'mdi:note-text';
      case 'code_snippet':
        return 'mdi:code-tags';
      case 'exercise':
        return 'mdi:book-open-variant';
      case 'resource_link':
        return 'mdi:link';
      case 'attachment':
        return 'mdi:attachment';
      default:
        return 'mdi:file-document';
    }
  };

  const getItemTypeColor = (type: string) => {
    switch (type) {
      case 'note':
        return 'bg-blue-500/20 text-blue-400 border-blue-500/30';
      case 'code_snippet':
        return 'bg-green-500/20 text-green-400 border-green-500/30';
      case 'exercise':
        return 'bg-purple-500/20 text-purple-400 border-purple-500/30';
      case 'resource_link':
        return 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30';
      case 'attachment':
        return 'bg-red-500/20 text-red-400 border-red-500/30';
      default:
        return 'bg-gray-500/20 text-gray-400 border-gray-500/30';
    }
  };

  const formatDate = (dateString?: string) => {
    if (!dateString) return null;
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString(currentLang === 'ja' ? 'ja-JP' : currentLang === 'vi' ? 'vi-VN' : 'en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    } catch {
      return dateString;
    }
  };

  const hexToRgba = (hex: string, alpha: number): string => {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
  };

  const getFolderIcon = (iconName?: string): string => {
    if (!iconName) return 'mdi:folder';
    
    const iconMap: Record<string, string> = {
      'code': 'mdi:code-tags',
      'school': 'mdi:school',
      'web': 'mdi:web',
      'build': 'mdi:tools',
      'work': 'mdi:briefcase',
      'lightbulb': 'mdi:lightbulb',
      'note': 'mdi:note-text',
      'python': 'mdi:language-python',
      'java': 'mdi:language-java',
      'javascript': 'mdi:language-javascript',
      'php': 'mdi:language-php',
      'cpp': 'mdi:language-cpp',
      'go': 'mdi:language-go',
    };
    
    return iconMap[iconName.toLowerCase()] || `mdi:${iconName}`;
  };
  const KnowledgeItemCard = ({ 
    item, 
    getItemTypeLabel, 
    getItemTypeIcon, 
    getItemTypeColor, 
    formatDate 
  }: {
    item: KnowledgeItem;
    getItemTypeLabel: (type: string) => string;
    getItemTypeIcon: (type: string) => string;
    getItemTypeColor: (type: string) => string;
    formatDate: (date?: string) => string | null;
  }) => (
    <Link
      href={`/dashboard/knowledge/${item.id}`}
      className="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10 shadow-lg hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl group"
    >
      <div className="flex items-start justify-between mb-3">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-2">
            <Icon icon={getItemTypeIcon(item.item_type)} className="text-lg text-[#8B5CF6]" />
            <span className={`px-2 py-1 rounded text-xs font-medium border ${getItemTypeColor(item.item_type)}`}>
              {getItemTypeLabel(item.item_type)}
            </span>
          </div>
          <h3 className="text-base font-bold text-white mb-1 drop-shadow-md group-hover:text-[#8B5CF6] transition-colors line-clamp-2">
            {item.title}
          </h3>
          {item.content && (
            <p className="text-xs text-white/60 mb-2 line-clamp-2">{item.content}</p>
          )}
        </div>
        {item.is_favorite && (
          <Icon icon="mdi:star" className="text-yellow-400 text-lg flex-shrink-0" />
        )}
      </div>

      {/* Tags */}
      {item.tags && item.tags.length > 0 && (
        <div className="flex flex-wrap gap-1 mb-2">
          {item.tags.slice(0, 2).map((tag, idx) => (
            <span
              key={idx}
              className="px-1.5 py-0.5 bg-white/10 rounded text-xs text-white/70"
            >
              #{tag}
            </span>
          ))}
          {item.tags.length > 2 && (
            <span className="px-1.5 py-0.5 bg-white/10 rounded text-xs text-white/50">
              +{item.tags.length - 2}
            </span>
          )}
        </div>
      )}

      {/* Meta Info */}
      <div className="flex items-center justify-between text-xs text-white/50">
        {item.view_count !== undefined && (
          <span className="flex items-center">
            <Icon icon="mdi:eye" className="mr-1" />
            {item.view_count}
          </span>
        )}
        {item.created_at && (
          <span>{formatDate(item.created_at)}</span>
        )}
      </div>
    </Link>
  );

  return (
    <div className="px-6 py-6 relative z-0 min-w-0">
      {/* Header */}
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="text-2xl font-bold text-white drop-shadow-lg mb-1 flex items-center">
            <Icon icon="mdi:book-open-variant" className="mr-3 text-[#8B5CF6]" />
            {t.knowledge}
          </h1>
          <p className="text-sm text-white/80">{t.buildKnowledge}</p>
        </div>
        <Link
          href="/dashboard/knowledge/create"
          className="px-5 py-2.5 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2"
        >
          <Icon icon="mdi:plus" />
          <span>{t.createKnowledgeItem || 'Create Knowledge Item'}</span>
        </Link>
      </div>

      {/* Filters and Search */}
      <div className="mb-6 space-y-4">
        {/* Search */}
        <div className="relative">
          <Icon icon="mdi:magnify" className="absolute left-4 top-1/2 transform -translate-y-1/2 text-white/60 text-xl" />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder={t.searchKnowledge || 'Search knowledge items...'}
            className="w-full pl-12 pr-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#8B5CF6]"
          />
        </div>

        {/* Filters */}
        <div className="flex items-center space-x-3 flex-wrap gap-3">
          <select
            value={filterType}
            onChange={(e) => setFilterType(e.target.value)}
            className="bg-white/20 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/30"
            title={t.type || 'Type'}
            aria-label={t.type || 'Type'}
            style={{ color: 'white' }}
          >
            <option value="" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.all} ({t.type || 'Type'})</option>
            <option value="note" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.note || 'Note'}</option>
            <option value="code_snippet" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.codeSnippet || 'Code Snippet'}</option>
            <option value="exercise" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.exercise || 'Exercise'}</option>
            <option value="resource_link" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.resourceLink || 'Resource Link'}</option>
            <option value="attachment" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.attachment || 'Attachment'}</option>
          </select>
        </div>
      </div>

      {/* Breadcrumb Navigation */}
      {breadcrumbPath.length > 0 && (
        <div className="mb-4 flex items-center space-x-2 flex-wrap">
          <button
            onClick={navigateToRoot}
            className="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-sm text-white transition flex items-center"
          >
            <Icon icon="mdi:home" className="mr-1" />
            {t.home || 'Home'}
          </button>
          {breadcrumbPath.map((category, index) => (
            <div key={category.id} className="flex items-center space-x-2">
              <Icon icon="mdi:chevron-right" className="text-white/40" />
              <button
                onClick={() => navigateToBreadcrumb(index)}
                className="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-sm text-white transition"
              >
                {category.name}
              </button>
            </div>
          ))}
        </div>
      )}

      {/* Folder-Based View (like Android app) */}
      {loading ? (
        <div className="text-center py-12 text-white/60">{t.loading}</div>
      ) : (() => {
        const { subFolders, files } = getCurrentFolderContents();
        const isRoot = currentCategoryId === null;
        const hasSubFolders = subFolders.length > 0;
        const hasFiles = files.length > 0;
        const isEmpty = !hasSubFolders && !hasFiles;

        return isEmpty ? (
          <div className="text-center py-12">
            <div className="bg-white/20 backdrop-blur-md rounded-2xl p-8 border border-white/20 shadow-xl">
              <Icon icon="mdi:folder-outline" className="text-6xl text-white/40 mx-auto mb-4" />
              <p className="text-white/60 text-lg mb-4">
                {isRoot 
                  ? (t.noKnowledgeItems || 'No knowledge items yet')
                  : 'This folder is empty'}
              </p>
              {isRoot && (
                <Link
                  href="/dashboard/knowledge/create"
                  className="inline-flex items-center space-x-2 px-6 py-3 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold"
                >
                  <Icon icon="mdi:plus" />
                  <span>{t.createKnowledgeItem || 'Create Knowledge Item'}</span>
                </Link>
              )}
            </div>
          </div>
        ) : (
          <div className="space-y-6">
            {/* Sub-folders Section */}
            {hasSubFolders && (
              <div>
                <h2 className="text-lg font-bold text-white mb-4 flex items-center">
                  <Icon icon="mdi:folder" className="mr-2" />
                  {t.folders || 'Folders'} ({subFolders.length})
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                  {subFolders.map((folder) => {
                    const itemCount = allItems.filter(item => item.category_id === folder.id && !item.is_archived).length;
                    const subfolderCount = allCategories.filter(cat => cat.parent_id === folder.id).length;
                    const totalCount = itemCount + subfolderCount;

                    return (
                      <button
                        key={folder.id}
                        onClick={() => navigateToCategory(folder)}
                        className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl hover:bg-white/30 transition-all duration-300 hover:scale-105 hover:shadow-2xl text-left group"
                      >
                        <div className="flex items-start justify-between mb-3">
                          <div
                            className="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg"
                            style={{
                              background: folder.color
                                ? `linear-gradient(135deg, ${hexToRgba(folder.color, 0.125)}, ${hexToRgba(folder.color, 0.25)})`
                                : 'linear-gradient(135deg, rgba(139, 92, 246, 0.125), rgba(139, 92, 246, 0.25))',
                            }}
                          >
                            <Icon
                              icon={getFolderIcon(folder.icon)}
                              className="text-2xl"
                              style={{ color: folder.color || '#8B5CF6' }}
                            />
                          </div>
                          {totalCount > 0 && (
                            <span className="px-2 py-1 bg-white/20 rounded-lg text-xs text-white/80">
                              {totalCount}
                            </span>
                          )}
                        </div>
                        <h3 className="text-base font-bold text-white mb-1 group-hover:text-[#8B5CF6] transition-colors">
                          {folder.name}
                        </h3>
                        {folder.description && (
                          <p className="text-xs text-white/60 line-clamp-2">{folder.description}</p>
                        )}
                      </button>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Files Section */}
            {hasFiles && (
              <div>
                <h2 className="text-lg font-bold text-white mb-4 flex items-center">
                  <Icon icon="mdi:file-document" className="mr-2" />
                  {t.files || 'Files'} ({files.length})
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {files.map((item) => (
                    <KnowledgeItemCard
                      key={item.id}
                      item={item}
                      getItemTypeLabel={getItemTypeLabel}
                      getItemTypeIcon={getItemTypeIcon}
                      getItemTypeColor={getItemTypeColor}
                      formatDate={formatDate}
                    />
                  ))}
                </div>
              </div>
            )}
          </div>
        );
      })()}
      
      {/* Empty state fallback */}
      {!loading && allCategories.length === 0 && allItems.length === 0 && (
        <div className="text-center py-12">
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-8 border border-white/20 shadow-xl">
            <Icon icon="mdi:book-open-variant-outline" className="text-6xl text-white/40 mx-auto mb-4" />
            <p className="text-white/60 text-lg mb-4">{t.noKnowledgeItems || 'No knowledge items yet'}</p>
            <Link
              href="/dashboard/knowledge/create"
              className="inline-flex items-center space-x-2 px-6 py-3 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold"
            >
              <Icon icon="mdi:plus" />
              <span>{t.createKnowledgeItem || 'Create Knowledge Item'}</span>
            </Link>
          </div>
        </div>
      )}
    </div>
  );
}
