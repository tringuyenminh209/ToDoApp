'use client';

import { useState, useEffect, useCallback, useRef } from 'react';
import { useRouter, useParams } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { knowledgeService, KnowledgeItem } from '@/lib/services/knowledgeService';

declare global {
  interface Window {
    monaco: any;
    require: any;
  }
}

function markdownToHtml(markdown: string): string {
  const codeBlockRegex = /```(\w+)?\n?([\s\S]*?)```/g;
  const codeBlocks: Array<{ lang: string; code: string; placeholder: string }> = [];
  let placeholderIndex = 0;
  
  let processedMarkdown = markdown.replace(codeBlockRegex, (match, lang, code) => {
    const placeholder = `__CODE_BLOCK_${placeholderIndex}__`;
    codeBlocks.push({
      lang: lang || 'plaintext',
      code: code.trim(),
      placeholder,
    });
    placeholderIndex++;
    return placeholder;
  });
  
  let html = processedMarkdown
    .replace(/^### (.*$)/gim, '<h3>$1</h3>')
    .replace(/^## (.*$)/gim, '<h2>$1</h2>')
    .replace(/^# (.*$)/gim, '<h1>$1</h1>')
    .replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/gim, '<em>$1</em>')
    .replace(/`([^`]+)`/gim, '<code class="inline-code">$1</code>')
    .replace(/^> (.*$)/gim, '<blockquote>$1</blockquote>')
    .replace(/^- (.*$)/gim, '<li>$1</li>')
    .replace(/^\* (.*$)/gim, '<li>$1</li>')
    .replace(/\n/gim, '<br>');
  
  html = html.replace(/(<li>.*?<\/li>(?:<br>)?)+/gim, (match) => {
    return '<ul>' + match.replace(/<br>/gim, '') + '</ul>';
  });
  
  codeBlocks.forEach(({ lang, code, placeholder }) => {
    const highlightedCode = highlightCode(code, lang);
    html = html.replace(placeholder, highlightedCode);
  });
  
  return html;
}

function highlightCode(code: string, language: string): string {
  const escapeHtml = (text: string) => {
    return text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  };

  const lang = language.toLowerCase();
  
  const patterns: Record<string, Array<{ regex: RegExp; className: string }>> = {
    java: [
      { regex: /\/\*[\s\S]*?\*\//g, className: 'comment' },
      { regex: /\/\/.*/g, className: 'comment' },
      { regex: /("(?:\\.|[^"\\])*")/g, className: 'string' },
      { regex: /\b(public|private|protected|static|final|class|interface|extends|implements|new|return|if|else|for|while|do|switch|case|break|continue|try|catch|finally|throw|throws|import|package|void|int|long|double|float|char|boolean|String|true|false|null|this|super)\b/g, className: 'keyword' },
      { regex: /\b[A-Z][a-zA-Z0-9]*\b/g, className: 'class-name' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
      { regex: /[-+*\/=<>!&|]+/g, className: 'operator' },
    ],
    javascript: [
      { regex: /\/\*[\s\S]*?\*\//g, className: 'comment' },
      { regex: /\/\/.*/g, className: 'comment' },
      { regex: /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|`(?:\\.|[^`\\])*`)/g, className: 'string' },
      { regex: /\b(const|let|var|function|class|extends|implements|return|if|else|for|while|do|switch|case|break|continue|try|catch|finally|throw|import|export|default|async|await|true|false|null|undefined|this|super|new)\b/g, className: 'keyword' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
      { regex: /[-+*\/=<>!&|]+/g, className: 'operator' },
    ],
    python: [
      { regex: /#.*/g, className: 'comment' },
      { regex: /("""[\s\S]*?"""|'''[\s\S]*?''')/g, className: 'comment' },
      { regex: /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*')/g, className: 'string' },
      { regex: /\b(def|class|if|else|elif|for|while|try|except|finally|raise|import|from|return|yield|lambda|and|or|not|in|is|True|False|None|self|pass|break|continue)\b/g, className: 'keyword' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
      { regex: /[-+*\/=<>!&|]+/g, className: 'operator' },
    ],
    php: [
      { regex: /\/\*[\s\S]*?\*\//g, className: 'comment' },
      { regex: /\/\/.*/g, className: 'comment' },
      { regex: /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*')/g, className: 'string' },
      { regex: /\b(echo|print|return|if|else|elseif|foreach|for|while|function|class|public|private|protected|static|const|new|extends|implements|namespace|use|try|catch|finally|throw|as|array|die|exit|include|require|true|false|null)\b/g, className: 'keyword' },
      { regex: /\$[a-zA-Z_][a-zA-Z0-9_]*/g, className: 'variable' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
      { regex: /[-+*\/=<>!&|]+/g, className: 'operator' },
    ],
    typescript: [
      { regex: /\/\*[\s\S]*?\*\//g, className: 'comment' },
      { regex: /\/\/.*/g, className: 'comment' },
      { regex: /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|`(?:\\.|[^`\\])*`)/g, className: 'string' },
      { regex: /\b(const|let|var|function|class|interface|type|extends|implements|return|if|else|for|while|do|switch|case|break|continue|try|catch|finally|throw|import|export|default|async|await|true|false|null|undefined|this|super|new|public|private|protected|static|readonly)\b/g, className: 'keyword' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
      { regex: /[-+*\/=<>!&|]+/g, className: 'operator' },
    ],
    cpp: [
      { regex: /\/\*[\s\S]*?\*\//g, className: 'comment' },
      { regex: /\/\/.*/g, className: 'comment' },
      { regex: /("(?:\\.|[^"\\])*")/g, className: 'string' },
      { regex: /\b(int|char|float|double|void|bool|class|struct|namespace|using|return|if|else|for|while|do|switch|case|break|continue|try|catch|throw|include|define|public|private|protected|static|const|new|delete|true|false|nullptr)\b/g, className: 'keyword' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
      { regex: /[-+*\/=<>!&|]+/g, className: 'operator' },
    ],
    c: [
      { regex: /\/\*[\s\S]*?\*\//g, className: 'comment' },
      { regex: /\/\/.*/g, className: 'comment' },
      { regex: /("(?:\\.|[^"\\])*")/g, className: 'string' },
      { regex: /\b(int|char|float|double|void|struct|return|if|else|for|while|do|switch|case|break|continue|include|define|static|const|true|false|NULL)\b/g, className: 'keyword' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
      { regex: /[-+*\/=<>!&|]+/g, className: 'operator' },
    ],
    html: [
      { regex: /<!--[\s\S]*?-->/g, className: 'comment' },
      { regex: /<[^>]+>/g, className: 'tag' },
      { regex: /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*')/g, className: 'string' },
    ],
    css: [
      { regex: /\/\*[\s\S]*?\*\//g, className: 'comment' },
      { regex: /([a-zA-Z-]+)\s*:/g, className: 'property' },
      { regex: /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*')/g, className: 'string' },
      { regex: /#[\da-fA-F]{3,6}/g, className: 'number' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
    ],
    sql: [
      { regex: /--.*/g, className: 'comment' },
      { regex: /\/\*[\s\S]*?\*\//g, className: 'comment' },
      { regex: /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*')/g, className: 'string' },
      { regex: /\b(SELECT|FROM|WHERE|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER|TABLE|INDEX|PRIMARY|KEY|FOREIGN|REFERENCES|JOIN|INNER|LEFT|RIGHT|FULL|ON|GROUP|BY|ORDER|HAVING|AS|AND|OR|NOT|IN|LIKE|IS|NULL|DISTINCT|COUNT|SUM|AVG|MAX|MIN|UNION|ALL)\b/gi, className: 'keyword' },
      { regex: /\b\d+\.?\d*\b/g, className: 'number' },
    ],
  };

  const langPatterns = patterns[lang] || [];
  
  if (langPatterns.length === 0) {
    // No highlighting for unknown languages
    return `<pre class="code-block"><code>${escapeHtml(code)}</code></pre>`;
  }

  let highlighted = escapeHtml(code);
  
  // Track positions that are already highlighted to avoid double highlighting
  const highlightedRanges: Array<{ start: number; end: number }> = [];
  
  const allMatches: Array<{ match: string; index: number; className: string }> = [];
  
  langPatterns.forEach(({ regex, className }) => {
    const regexCopy = new RegExp(regex.source, regex.flags);
    let match;
    
    while ((match = regexCopy.exec(highlighted)) !== null) {
      const start = match.index;
      const end = start + match[0].length;
      
      const overlaps = highlightedRanges.some(
        (range) => !(end <= range.start || start >= range.end)
      );
      
      if (!overlaps) {
        allMatches.push({ match: match[0], index: start, className });
        highlightedRanges.push({ start, end });
      }
    }
  });
  
  allMatches.sort((a, b) => b.index - a.index);
  
  allMatches.forEach(({ match: matchText, index, className }) => {
    const before = highlighted.substring(0, index);
    const after = highlighted.substring(index + matchText.length);
    highlighted = before + `<span class="hljs-${className}">${matchText}</span>` + after;
  });

  return `<pre class="code-block"><code class="language-${lang}">${highlighted}</code></pre>`;
}

export default function KnowledgeDetailPage() {
  const router = useRouter();
  const params = useParams();
  const itemId = params?.id ? parseInt(params.id as string) : null;
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [knowledgeItem, setKnowledgeItem] = useState<KnowledgeItem | null>(null);
  const [loading, setLoading] = useState(true);
  const [isEditing, setIsEditing] = useState(false);
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');
  const [itemType, setItemType] = useState<'note' | 'code_snippet' | 'exercise' | 'resource_link' | 'attachment'>('note');
  const [categoryId, setCategoryId] = useState<number | null>(null);
  const [tags, setTags] = useState<string[]>([]);
  const [saving, setSaving] = useState(false);
  const editorRef = useRef<HTMLDivElement>(null);
  const monacoEditorRef = useRef<any>(null);
  const t = translations[currentLang];
  const handleBack = () => {
    if (typeof window !== 'undefined' && window.history.length > 1) {
      router.back();
      return;
    }
    router.push('/dashboard/knowledge');
  };

  // Enhanced markdown to HTML converter that handles code snippets
  const convertContentToHtml = (content: string, itemType: string, codeLanguage?: string): string => {
    // If it's a code snippet and no code blocks detected, wrap entire content as code
    if (itemType === 'code_snippet' && !content.includes('```')) {
      const lang = codeLanguage || 'java';
      return highlightCode(content, lang);
    }
    
    return markdownToHtml(content);
  };

  const getMonacoLanguage = (itemType: string, codeLanguage?: string): string => {
    if (itemType === 'code_snippet') {
      const lang = (codeLanguage || 'java').toLowerCase();
      const langMap: Record<string, string> = {
        'java': 'java',
        'javascript': 'javascript',
        'typescript': 'typescript',
        'python': 'python',
        'php': 'php',
        'cpp': 'cpp',
        'c': 'c',
        'csharp': 'csharp',
        'html': 'html',
        'css': 'css',
        'sql': 'sql',
        'json': 'json',
        'xml': 'xml',
        'markdown': 'markdown',
      };
      return langMap[lang] || 'plaintext';
    }
    return 'markdown';
  };

  useEffect(() => {
    if (!isEditing || !editorRef.current) {
      if (monacoEditorRef.current) {
        monacoEditorRef.current.dispose();
        monacoEditorRef.current = null;
      }
      return;
    }

    const loadMonaco = () => {
      if (window.monaco && window.require) {
        setTimeout(() => initializeEditor(), 100);
        return;
      }

      const existingScript = document.querySelector('script[src*="monaco-editor"]');
      if (existingScript) {
        setTimeout(() => {
          if (window.monaco && window.require) {
            initializeEditor();
          }
        }, 100);
        return;
      }

      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js';
      script.async = true;
      script.onload = () => {
        window.require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' } });
        window.require(['vs/editor/editor.main'], () => {
          initializeEditor();
        });
      };
      document.body.appendChild(script);
    };

    const initializeEditor = () => {
      if (!editorRef.current || monacoEditorRef.current) return;

      const language = getMonacoLanguage(itemType, knowledgeItem?.code_language);
      
      monacoEditorRef.current = window.monaco.editor.create(editorRef.current, {
        value: content,
        language: language,
        theme: 'vs-dark',
        automaticLayout: true,
        minimap: { enabled: false },
        scrollBeyondLastLine: false,
        fontSize: 14,
        lineNumbers: 'on',
        roundedSelection: false,
        scrollbar: {
          verticalScrollbarSize: 10,
          horizontalScrollbarSize: 10,
        },
        wordWrap: 'on',
        formatOnPaste: true,
        formatOnType: true,
      });

      monacoEditorRef.current.onDidChangeModelContent(() => {
        const newValue = monacoEditorRef.current.getValue();
        setContent(newValue);
      });
    };

    loadMonaco();

    return () => {
      if (monacoEditorRef.current) {
        monacoEditorRef.current.dispose();
        monacoEditorRef.current = null;
      }
    };
  }, [isEditing, itemType, knowledgeItem?.code_language]);

  useEffect(() => {
    if (monacoEditorRef.current && isEditing) {
      const currentValue = monacoEditorRef.current.getValue();
      if (currentValue !== content) {
        monacoEditorRef.current.setValue(content);
      }
    }
  }, [content, isEditing]);

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

  const loadKnowledgeItem = useCallback(async () => {
    if (!itemId) return;

    try {
      setLoading(true);
      const response = await knowledgeService.getKnowledgeItem(itemId);
      if (response.success && response.data) {
        const item = response.data.data || response.data;
        setKnowledgeItem(item);
        setTitle(item.title || '');
        setContent(item.content || '');
        setItemType(item.item_type || 'note');
        setCategoryId(item.category_id || null);
        setTags(item.tags || []);
      }
    } catch (error) {
      console.error('Failed to load knowledge item:', error);
    } finally {
      setLoading(false);
    }
  }, [itemId]);

  useEffect(() => {
    loadKnowledgeItem();
  }, [loadKnowledgeItem]);

  const handleSave = async () => {
    if (!itemId || !title.trim()) {
      alert(t.titleRequired || 'Title is required');
      return;
    }

    try {
      setSaving(true);
      const data: Partial<KnowledgeItem> = {
        title: title.trim(),
        content: content.trim(),
        item_type: itemType,
        category_id: categoryId || undefined,
        tags: tags.length > 0 ? tags : undefined,
      };

      const response = await knowledgeService.updateKnowledgeItem(itemId, data);
      if (response.success) {
        setIsEditing(false);
        await loadKnowledgeItem();
      }
    } catch (error) {
      console.error('Failed to update knowledge item:', error);
      alert(t.error || 'Failed to save');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async () => {
    if (!itemId) return;
    if (!confirm(t.confirmDelete || 'Are you sure you want to delete this item?')) return;

    try {
      await knowledgeService.deleteKnowledgeItem(itemId);
      router.push('/dashboard/knowledge');
    } catch (error) {
      console.error('Failed to delete knowledge item:', error);
      alert(t.error || 'Failed to delete');
    }
  };

  const formatDate = (dateString?: string) => {
    if (!dateString) return null;
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString(currentLang === 'ja' ? 'ja-JP' : currentLang === 'vi' ? 'vi-VN' : 'en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    } catch {
      return dateString;
    }
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

  if (!itemId) {
    return (
      <div className="px-6 py-6 relative z-0 min-w-0">
        <div className="text-center py-12 text-white/60">{t.error || 'Invalid item ID'}</div>
      </div>
    );
  }

  if (loading) {
    return (
      <div className="px-6 py-6 relative z-0 min-w-0">
        <div className="text-center py-12 text-white/60">{t.loading}</div>
      </div>
    );
  }

  if (!knowledgeItem) {
    return (
      <div className="px-6 py-6 relative z-0 min-w-0">
        <div className="text-center py-12">
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-8 border border-white/20 shadow-xl">
            <Icon icon="mdi:alert-circle" className="text-6xl text-white/40 mx-auto mb-4" />
            <p className="text-white/60 text-lg mb-4">{t.notFound || 'Knowledge item not found'}</p>
            <Link
              href="/dashboard/knowledge"
              className="inline-flex items-center space-x-2 px-6 py-3 bg-[#8B5CF6] hover:bg-[#7C3AED] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold"
            >
              <Icon icon="mdi:arrow-left" />
              <span>{t.back}</span>
            </Link>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="px-6 py-6 relative z-0 min-w-0">
      <div className="mb-4 flex items-center space-x-2 flex-wrap">
        <Link
          href="/dashboard"
          className="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-sm text-white transition flex items-center"
        >
          <Icon icon="mdi:home" className="mr-1" />
          {t.home}
        </Link>
        <div className="flex items-center space-x-2">
          <Icon icon="mdi:chevron-right" className="text-white/40" />
          <Link
            href="/dashboard/knowledge"
            className="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-sm text-white transition"
          >
            {t.knowledge}
          </Link>
        </div>
        {knowledgeItem?.category?.name && knowledgeItem.category_id && (
          <div className="flex items-center space-x-2">
            <Icon icon="mdi:chevron-right" className="text-white/40" />
            <Link
              href={`/dashboard/knowledge?category=${knowledgeItem.category_id}`}
              className="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-sm text-white transition"
            >
              {knowledgeItem.category.name}
            </Link>
          </div>
        )}
        {knowledgeItem?.title && (
          <div className="flex items-center space-x-2">
            <Icon icon="mdi:chevron-right" className="text-white/40" />
            <span className="px-3 py-1.5 bg-white/5 rounded-lg text-sm text-white/80">
              {knowledgeItem.title}
            </span>
          </div>
        )}
      </div>
      {/* Header */}
      <div className="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div className="flex items-center space-x-4">
          <button
            onClick={handleBack}
            className="p-2 hover:bg-white/10 rounded-xl transition"
            title={t.back}
            aria-label={t.back}
          >
            <Icon icon="mdi:arrow-left" className="text-2xl text-white" />
          </button>
          <div>
            {isEditing ? (
              <input
                type="text"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                className="text-2xl font-bold text-white bg-transparent border-b-2 border-white/30 focus:outline-none focus:border-[#8B5CF6]"
                placeholder={t.title}
              />
            ) : (
              <h1 className="text-2xl font-bold text-white drop-shadow-lg mb-1 flex items-center">
                <Icon icon={getItemTypeIcon(knowledgeItem.item_type)} className="mr-3 text-[#8B5CF6]" />
                {knowledgeItem.title}
              </h1>
            )}
            <div className="flex items-center space-x-3 mt-2">
              <span className={`px-2 py-1 rounded text-xs font-medium border ${getItemTypeColor(knowledgeItem.item_type)}`}>
                {getItemTypeLabel(knowledgeItem.item_type)}
              </span>
              {knowledgeItem.category && (
                <span className="text-sm text-white/60 flex items-center">
                  <Icon icon="mdi:folder" className="mr-1" />
                  {knowledgeItem.category.name}
                </span>
              )}
              {knowledgeItem.view_count !== undefined && (
                <span className="text-sm text-white/60 flex items-center">
                  <Icon icon="mdi:eye" className="mr-1" />
                  {knowledgeItem.view_count} {t.views || 'views'}
                </span>
              )}
            </div>
          </div>
        </div>
        <div className="flex items-center space-x-3">
          {isEditing ? (
            <>
              <button
                onClick={() => {
                  setIsEditing(false);
                  setTitle(knowledgeItem.title || '');
                  setContent(knowledgeItem.content || '');
                  setItemType(knowledgeItem.item_type || 'note');
                  setCategoryId(knowledgeItem.category_id || null);
                  setTags(knowledgeItem.tags || []);
                }}
                className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm"
              >
                {t.cancel || 'Cancel'}
              </button>
              <button
                onClick={handleSave}
                disabled={saving}
                className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold text-sm disabled:opacity-50 flex items-center space-x-2"
              >
                <Icon icon="mdi:content-save" />
                <span>{saving ? t.saving || 'Saving...' : t.save}</span>
              </button>
            </>
          ) : (
            <>
              <button
                onClick={() => setIsEditing(true)}
                className="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition text-sm flex items-center space-x-2"
                title={t.edit}
              >
                <Icon icon="mdi:pencil" />
                <span>{t.edit}</span>
              </button>
              <button
                onClick={handleDelete}
                className="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-white rounded-xl transition text-sm flex items-center space-x-2 border border-red-500/30"
                title={t.delete}
              >
                <Icon icon="mdi:delete" />
                <span>{t.delete}</span>
              </button>
            </>
          )}
        </div>
      </div>

      {/* Content */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Content */}
        <div className="lg:col-span-2">
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
            {isEditing ? (
              <div
                ref={editorRef}
                className="w-full h-96 rounded-xl overflow-hidden border border-white/20"
                style={{ minHeight: '384px' }}
              />
            ) : (
              <div
                className="markdown-preview prose prose-invert max-w-none text-white"
                dangerouslySetInnerHTML={{
                  __html: knowledgeItem.content
                    ? convertContentToHtml(knowledgeItem.content, knowledgeItem.item_type, knowledgeItem.code_language)
                    : '<p class="text-white/50 italic">No content</p>',
                }}
              />
            )}
          </div>

          {/* Tags */}
          {knowledgeItem.tags && knowledgeItem.tags.length > 0 && (
            <div className="mt-4 flex flex-wrap gap-2">
              {knowledgeItem.tags.map((tag, idx) => {
                const colors = [
                  { bg: '#0FA968', text: '#0FA968' },
                  { bg: '#1F6FEB', text: '#1F6FEB' },
                  { bg: 'purple-500', text: 'purple-400' },
                  { bg: 'orange-500', text: 'orange-400' },
                ];
                const color = colors[idx % colors.length];
                return (
                  <span
                    key={tag}
                    className={`px-3 py-1.5 rounded-lg text-sm font-medium border ${
                      color.bg.startsWith('#')
                        ? `bg-[${color.bg}]/20 text-[${color.text}] border-[${color.bg}]/30`
                        : `bg-${color.bg}/20 text-${color.text} border-${color.bg}/30`
                    }`}
                    style={
                      color.bg.startsWith('#')
                        ? {
                            backgroundColor: `${color.bg}20`,
                            color: color.text,
                            borderColor: `${color.bg}30`,
                          }
                        : undefined
                    }
                  >
                    #{tag}
                  </span>
                );
              })}
            </div>
          )}
        </div>

        {/* Sidebar */}
        <div className="space-y-6">
          {/* Metadata */}
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
            <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.information || 'INFORMATION'}</h2>
            <div className="space-y-3 text-sm">
              <div className="flex items-center justify-between text-white/80">
                <span>{t.createdAt || 'Created'}:</span>
                <span>{formatDate(knowledgeItem.created_at) || '-'}</span>
              </div>
              <div className="flex items-center justify-between text-white/80">
                <span>{t.updatedAt || 'Updated'}:</span>
                <span>{formatDate(knowledgeItem.updated_at) || '-'}</span>
              </div>
              {knowledgeItem.last_reviewed_at && (
                <div className="flex items-center justify-between text-white/80">
                  <span>{t.lastReviewed || 'Last Reviewed'}:</span>
                  <span>{formatDate(knowledgeItem.last_reviewed_at) || '-'}</span>
                </div>
              )}
              {knowledgeItem.next_review_date && (
                <div className="flex items-center justify-between text-white/80">
                  <span>{t.nextReview || 'Next Review'}:</span>
                  <span className="text-[#0FA968]">{formatDate(knowledgeItem.next_review_date) || '-'}</span>
                </div>
              )}
              {knowledgeItem.review_count !== undefined && (
                <div className="flex items-center justify-between text-white/80">
                  <span>{t.reviewCount || 'Review Count'}:</span>
                  <span>{knowledgeItem.review_count}</span>
                </div>
              )}
            </div>
          </div>

          {/* Related Items */}
          {(knowledgeItem.source_task || knowledgeItem.learning_path) && (
            <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
              <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.related || 'RELATED'}</h2>
              <div className="space-y-3">
                {knowledgeItem.source_task && (
                  <div className="p-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                    <div className="flex items-center space-x-2 mb-1">
                      <Icon icon="mdi:tasks" className="text-[#0FA968]" />
                      <span className="text-sm font-medium text-white">
                        {t.task || 'Task'}: {knowledgeItem.source_task.title || 'N/A'}
                      </span>
                    </div>
                    <p className="text-xs text-white/60">{t.relatedTask || 'Related task'}</p>
                  </div>
                )}
                {knowledgeItem.learning_path && (
                  <div className="p-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                    <div className="flex items-center space-x-2 mb-1">
                      <Icon icon="mdi:route" className="text-[#1F6FEB]" />
                      <span className="text-sm font-medium text-white">
                        {t.learningPath || 'Learning Path'}: {knowledgeItem.learning_path.title || 'N/A'}
                      </span>
                    </div>
                    <p className="text-xs text-white/60">{t.relatedLearningPath || 'Related learning path'}</p>
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Spaced Repetition */}
          {knowledgeItem.review_count !== undefined && (
            <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
              <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.spacedRepetition || 'SPACED REPETITION'}</h2>
              <div className="space-y-3">
                <div className="flex items-center justify-between text-sm text-white/80">
                  <span>{t.reviewCount || 'Review Count'}:</span>
                  <span className="font-semibold">{knowledgeItem.review_count || 0}</span>
                </div>
                {knowledgeItem.last_reviewed_at && (
                  <div className="flex items-center justify-between text-sm text-white/80">
                    <span>{t.lastReviewed || 'Last Reviewed'}:</span>
                    <span className="font-semibold">{formatDate(knowledgeItem.last_reviewed_at) || '-'}</span>
                  </div>
                )}
                {knowledgeItem.next_review_date && (
                  <div className="flex items-center justify-between text-sm text-white/80 mb-2">
                    <span>{t.nextReview || 'Next Review'}:</span>
                    <span className="font-semibold text-[#0FA968]">{formatDate(knowledgeItem.next_review_date) || '-'}</span>
                  </div>
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
