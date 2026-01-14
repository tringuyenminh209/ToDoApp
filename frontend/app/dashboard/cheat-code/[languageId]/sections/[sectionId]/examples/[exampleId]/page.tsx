// frontend/app/dashboard/cheat-code/[languageId]/sections/[sectionId]/examples/[exampleId]/page.tsx
'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { cheatCodeService, CodeExample } from '@/lib/services/cheatCodeService';

declare global {
  interface Window {
    monaco: any;
    require: any;
  }
}

export default function ExampleDetailPage() {
  const router = useRouter();
  const params = useParams();
  const languageId = params.languageId as string;
  const sectionId = params.sectionId as string;
  const exampleId = params.exampleId as string;
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [example, setExample] = useState<CodeExample | null>(null);
  const [language, setLanguage] = useState<any>(null);
  const [section, setSection] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const editorRef = useRef<any>(null);
  const editorContainerRef = useRef<HTMLDivElement>(null);
  const t = translations[currentLang];

  // 言語設定をlocalStorageから復元
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

  const loadExample = useCallback(async () => {
    try {
      const response = await cheatCodeService.getExample(languageId, sectionId, exampleId);
      if (response.success && response.data) {
        setExample(response.data);
      }
    } catch (error) {
      console.error('Failed to load example:', error);
    }
  }, [languageId, sectionId, exampleId]);

  const loadLanguage = useCallback(async () => {
    try {
      const response = await cheatCodeService.getLanguage(languageId);
      if (response.success && response.data) {
        setLanguage(response.data);
      }
    } catch (error) {
      console.error('Failed to load language:', error);
    }
  }, [languageId]);

  const loadSection = useCallback(async () => {
    try {
      const response = await cheatCodeService.getSection(languageId, sectionId);
      if (response.success && response.data) {
        setSection(response.data);
      }
    } catch (error) {
      console.error('Failed to load section:', error);
    }
  }, [languageId, sectionId]);

  useEffect(() => {
    const loadData = async () => {
      setLoading(true);
      await Promise.all([loadExample(), loadLanguage(), loadSection()]);
      setLoading(false);
    };
    loadData();
  }, [loadExample, loadLanguage, loadSection]);

  // Monaco Editor初期化
  useEffect(() => {
    if (!editorContainerRef.current || !example || editorRef.current) return;

    const loadMonaco = async () => {
      if (window.monaco && window.monaco.editor) {
        initializeEditor();
        return;
      }

      const existingScript = document.querySelector('script[src*="monaco-editor"]');
      if (existingScript) {
        setTimeout(() => {
          if (window.monaco && window.monaco.editor) {
            initializeEditor();
          }
        }, 100);
        return;
      }

      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js';
      script.onload = () => {
        window.require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' } });
        window.require(['vs/editor/editor.main'], () => {
          initializeEditor();
        });
      };
      script.onerror = () => {
        console.error('Failed to load Monaco Editor');
      };
      document.head.appendChild(script);
    };

    const initializeEditor = () => {
      if (!editorContainerRef.current || !example || editorRef.current) return;

      const languageMap: Record<string, string> = {
        javascript: 'javascript',
        js: 'javascript',
        typescript: 'typescript',
        ts: 'typescript',
        python: 'python',
        py: 'python',
        java: 'java',
        php: 'php',
        go: 'go',
        cpp: 'cpp',
        c: 'c',
        csharp: 'csharp',
        cs: 'csharp',
        kotlin: 'kotlin',
        rust: 'rust',
        ruby: 'ruby',
        swift: 'swift',
        bash: 'shell',
        shell: 'shell',
        sql: 'sql',
        html: 'html',
        css: 'css',
        json: 'json',
        yaml: 'yaml',
        xml: 'xml',
      };

      const editorLanguage = languageMap[language?.name?.toLowerCase() || 'javascript'] || 'javascript';

      const editor = window.monaco.editor.create(editorContainerRef.current, {
        value: example.code || '',
        language: editorLanguage,
        theme: 'vs-dark',
        fontSize: 14,
        minimap: { enabled: true },
        automaticLayout: true,
        scrollBeyondLastLine: false,
        wordWrap: 'on',
        lineNumbers: 'on',
        roundedSelection: false,
        cursorStyle: 'line',
        readOnly: true, // 例は読み取り専用
      });

      editorRef.current = editor;
    };

    loadMonaco();

    return () => {
      if (editorRef.current) {
        editorRef.current.dispose();
        editorRef.current = null;
      }
    };
  }, [example, language]);

  const getDifficultyColor = (difficulty: string) => {
    switch (difficulty) {
      case 'easy':
        return 'bg-green-100 text-green-700';
      case 'medium':
        return 'bg-yellow-100 text-yellow-700';
      case 'hard':
        return 'bg-red-100 text-red-700';
      default:
        return 'bg-gray-100 text-gray-700';
    }
  };

  const getDifficultyLabel = (difficulty: string) => {
    switch (difficulty) {
      case 'easy':
        return t.easy;
      case 'medium':
        return t.medium;
      case 'hard':
        return t.hard;
      default:
        return difficulty;
    }
  };

  return (
    <div className="flex h-[calc(100vh-85px)] relative z-10 min-w-0">
      {/* Description Panel */}
      <aside className="w-96 bg-white/10 backdrop-blur-md border-r border-white/20 shadow-xl overflow-y-auto flex-shrink-0">
        <div className="p-6">
          {loading ? (
            <div className="text-white/60 text-center py-8">{t.loading}</div>
          ) : example ? (
            <>
              <div className="mb-6">
                <button
                  onClick={() => router.back()}
                  className="mb-4 flex items-center text-white/70 hover:text-white transition"
                >
                  <Icon icon="mdi:arrow-left" className="mr-2" />
                  {t.back}
                </button>
                <div className="flex items-start justify-between mb-4">
                  <h2 className="text-lg font-semibold text-white mb-2 drop-shadow-md flex-1">{example.title}</h2>
                  {example.difficulty && (
                    <span
                      className={`px-2.5 py-1 rounded-lg text-xs font-bold ml-2 ${getDifficultyColor(
                        example.difficulty
                      )}`}
                    >
                      {getDifficultyLabel(example.difficulty)}
                    </span>
                  )}
                </div>
                {example.description && (
                  <p className="text-white/80 text-sm leading-relaxed">{example.description}</p>
                )}
              </div>

              {example.tags && example.tags.length > 0 && (
                <div className="mb-6">
                  <div className="flex flex-wrap gap-2">
                    {example.tags.map((tag, index) => (
                      <span
                        key={index}
                        className="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-lg text-xs text-white/90 border border-white/20"
                      >
                        #{tag}
                      </span>
                    ))}
                  </div>
                </div>
              )}

              {example.output && (
                <div className="mb-6">
                  <h3 className="text-md font-semibold text-white mb-2 drop-shadow-sm">{t.expectedOutput}</h3>
                  <div className="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/20">
                    <pre className="text-[#0FA968] font-mono text-sm whitespace-pre-wrap">{example.output}</pre>
                  </div>
                </div>
              )}

              <div className="flex items-center justify-between text-xs text-white/60 pt-4 border-t border-white/20">
                <span className="flex items-center">
                  <Icon icon="mdi:eye" className="mr-1" />
                  {example.viewsCount || 0} {t.views}
                </span>
                {example.favoritesCount > 0 && (
                  <span className="flex items-center">
                    <Icon icon="mdi:star" className="mr-1" />
                    {example.favoritesCount} {t.favorites}
                  </span>
                )}
              </div>
            </>
          ) : (
            <div className="text-white/60 text-center py-8">{t.exampleNotFound}</div>
          )}
        </div>
      </aside>

      {/* Code Editor Area */}
      <main className="flex-1 flex flex-col bg-transparent min-w-0">
        {/* Editor Header */}
        <div className="bg-white/10 backdrop-blur-md border-b border-white/20 px-4 py-2 flex items-center justify-between flex-wrap gap-2">
          <div className="flex items-center space-x-2 flex-wrap gap-2 min-w-0">
            <div className="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-t-xl text-sm text-white border-b-2 border-[#0FA968] flex-shrink-0">
              <Icon icon="mdi:code-tags" className="inline mr-2" />
              {example?.title || 'example'}.{language?.name?.toLowerCase() || 'js'}
            </div>
            {section && (
              <div className="text-xs text-white/70 flex-shrink-0">
                {section.title}
              </div>
            )}
          </div>
          <div className="flex items-center space-x-2 flex-shrink-0">
            <button
              onClick={() => {
                if (example?.code && navigator.clipboard) {
                  navigator.clipboard.writeText(example.code);
                  const copiedMsg = currentLang === 'ja' ? 'コードをコピーしました' : currentLang === 'en' ? 'Code copied' : 'Đã sao chép mã';
                  alert(copiedMsg);
                }
              }}
              className="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/20 text-white rounded-xl hover:bg-white/30 transition text-sm"
            >
              <Icon icon="mdi:content-copy" className="inline mr-2" />
              {t.copy}
            </button>
          </div>
        </div>

        {/* Monaco Editor Container */}
        <div className="flex-1 relative bg-zinc-950/50 backdrop-blur-sm">
          <div ref={editorContainerRef} className="w-full h-full" />
        </div>

        {/* Output Panel (if exists) */}
        {example?.output && (
          <div className="bg-white/10 backdrop-blur-md border-t border-white/20">
            <div className="px-4 py-3 border-b border-white/20 flex items-center justify-between">
              <h3 className="text-sm font-semibold text-white drop-shadow-sm flex items-center">
                <Icon icon="mdi:terminal" className="mr-2" />
                {t.output}
              </h3>
            </div>
            <div className="p-4 font-mono text-sm min-h-[100px] max-h-[200px] overflow-y-auto">
              <div className="text-white/80 whitespace-pre-wrap">{example.output}</div>
            </div>
          </div>
        )}
      </main>
    </div>
  );
}
