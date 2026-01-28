// frontend/app/dashboard/knowledge/create/page.tsx
'use client';

import { useState, useEffect, useCallback, useRef } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { knowledgeService, KnowledgeItem, KnowledgeCategory } from '@/lib/services/knowledgeService';

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

/** Decode HTML entities (named + numeric + hex, including double-encode), loop until stable */
function decodeHtmlEntities(text: string): string {
  if (!text || typeof text !== 'string') return text;
  let prev = '';
  let s = text;
  while (prev !== s) {
    prev = s;
    s = s
      .replace(/&amp;/g, '&')
      .replace(/&lt;/g, '<')
      .replace(/&gt;/g, '>')
      .replace(/&quot;/g, '"')
      .replace(/&apos;/g, "'")
      .replace(/&#(\d+);?/g, (_, n) => String.fromCharCode(parseInt(n, 10)))
      .replace(/&#x([0-9a-f]+);?/gi, (_, n) => String.fromCharCode(parseInt(n, 16)));
  }
  return s;
}

function highlightCode(code: string, language: string): string {
  const raw = decodeHtmlEntities(code);

  const escapeHtml = (text: string) => {
    return text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  };

  const lang = language.toLowerCase();

  // Language-specific token patterns
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
    return `<pre class="code-block"><code>${escapeHtml(raw)}</code></pre>`;
  }

  let highlighted = escapeHtml(raw);

  // Apply patterns in order
  langPatterns.forEach(({ regex, className }) => {
    highlighted = highlighted.replace(regex, (match) => {
      return `<span class="hljs-${className}">${match}</span>`;
    });
  });

  return `<pre class="code-block"><code class="language-${lang}">${highlighted}</code></pre>`;
}

export default function KnowledgeEditorPage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');
  const [itemType, setItemType] = useState<'note' | 'code_snippet' | 'exercise' | 'resource_link' | 'attachment'>('note');
  const [categoryId, setCategoryId] = useState<number | null>(null);
  const [tags, setTags] = useState<string[]>([]);
  const [newTag, setNewTag] = useState('');
  const [categories, setCategories] = useState<KnowledgeCategory[]>([]);
  const [categoryTree, setCategoryTree] = useState<KnowledgeCategory[]>([]);
  const [selectedCategoryId, setSelectedCategoryId] = useState<number | null>(null);
  const [isLeftSidebarCollapsed, setIsLeftSidebarCollapsed] = useState(false);
  const [isRightPanelCollapsed, setIsRightPanelCollapsed] = useState(false);
  const [isSplitView, setIsSplitView] = useState(true);
  const [saving, setSaving] = useState(false);
  const [wordCount, setWordCount] = useState(0);
  const [charCount, setCharCount] = useState(0);
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

  const loadCategories = useCallback(async () => {
    try {
      const response = await knowledgeService.getCategoryTree();
      if (response.success && response.data) {
        setCategoryTree(Array.isArray(response.data.data) ? response.data.data : (Array.isArray(response.data) ? response.data : []));
      } else if (Array.isArray(response.data)) {
        setCategoryTree(response.data);
      }

      const flatResponse = await knowledgeService.getCategories();
      if (flatResponse.success && flatResponse.data) {
        setCategories(Array.isArray(flatResponse.data.data) ? flatResponse.data.data : (Array.isArray(flatResponse.data) ? flatResponse.data : []));
      } else if (Array.isArray(flatResponse.data)) {
        setCategories(flatResponse.data);
      }
    } catch (error) {
      console.error('Failed to load categories:', error);
    }
  }, []);

  useEffect(() => {
    loadCategories();
  }, [loadCategories]);

  useEffect(() => {
    const words = content.trim().split(/\s+/).filter((word) => word.length > 0);
    setWordCount(words.length);
    setCharCount(content.length);
  }, [content]);

  const handleSave = async () => {
    if (!title.trim()) {
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

      const response = await knowledgeService.createKnowledgeItem(data);
      if (response.success && response.data) {
        const itemId = response.data.data?.id || response.data?.id;
        if (itemId) {
          router.push(`/dashboard/knowledge/${itemId}`);
        } else {
          router.push('/dashboard/knowledge');
        }
      }
    } catch (error) {
      console.error('Failed to save knowledge item:', error);
      alert(t.error || 'Failed to save');
    } finally {
      setSaving(false);
    }
  };

  const addTag = () => {
    if (newTag.trim() && !tags.includes(newTag.trim())) {
      setTags([...tags, newTag.trim()]);
      setNewTag('');
    }
  };

  const removeTag = (tag: string) => {
    setTags(tags.filter((t) => t !== tag));
  };

  const formatText = (type: 'bold' | 'italic' | 'code') => {
    const textarea = document.getElementById('markdown-editor') as HTMLTextAreaElement;
    if (!textarea) return;

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = content.substring(start, end);
    let replacement = '';

    switch (type) {
      case 'bold':
        replacement = `**${selectedText || t.boldText || 'bold text'}**`;
        break;
      case 'italic':
        replacement = `*${selectedText || t.italicText || 'italic text'}*`;
        break;
      case 'code':
        replacement = `\`${selectedText || t.codeText || 'code'}\``;
        break;
    }

    const newContent = content.substring(0, start) + replacement + content.substring(end);
    setContent(newContent);

    setTimeout(() => {
      textarea.focus();
      textarea.setSelectionRange(start + replacement.length, start + replacement.length);
    }, 0);
  };

  const insertHeading = (level: number) => {
    const textarea = document.getElementById('markdown-editor') as HTMLTextAreaElement;
    if (!textarea) return;

    const start = textarea.selectionStart;
    const heading = '#'.repeat(level) + ' ';
    const newContent = content.substring(0, start) + heading + content.substring(start);
    setContent(newContent);

    setTimeout(() => {
      textarea.focus();
      textarea.setSelectionRange(start + heading.length, start + heading.length);
    }, 0);
  };

  const insertList = () => {
    const textarea = document.getElementById('markdown-editor') as HTMLTextAreaElement;
    if (!textarea) return;

    const start = textarea.selectionStart;
    const listItem = '- ';
    const newContent = content.substring(0, start) + listItem + content.substring(start);
    setContent(newContent);

    setTimeout(() => {
      textarea.focus();
      textarea.setSelectionRange(start + listItem.length, start + listItem.length);
    }, 0);
  };

  const insertLink = () => {
    const textarea = document.getElementById('markdown-editor') as HTMLTextAreaElement;
    if (!textarea) return;

    const start = textarea.selectionStart;
    const link = `[${t.linkText || 'link text'}](https://example.com)`;
    const newContent = content.substring(0, start) + link + content.substring(start);
    setContent(newContent);

    setTimeout(() => {
      textarea.focus();
      textarea.setSelectionRange(start + link.length, start + link.length);
    }, 0);
  };

  const renderCategoryTree = (categories: KnowledgeCategory[], level = 0) => {
    return categories.map((category) => (
      <div key={category.id}>
        <div
          className={`category-item rounded-lg px-3 py-2 cursor-pointer ${selectedCategoryId === category.id ? 'active' : ''}`}
          onClick={() => {
            setSelectedCategoryId(category.id);
            setCategoryId(category.id);
          }}
          style={{ marginLeft: `${level * 1.5}rem` }}
        >
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <Icon icon="mdi:folder" className="text-[#0FA968]" />
              <span className="text-white font-medium text-sm">{category.name}</span>
            </div>
            {category.item_count !== undefined && (
              <span className="text-xs text-white/50">{category.item_count}</span>
            )}
          </div>
        </div>
        {category.children && category.children.length > 0 && (
          <div className="ml-6">
            {renderCategoryTree(category.children, level + 1)}
          </div>
        )}
      </div>
    ));
  };

  return (
    <div className="flex flex-col h-[calc(100vh-85px)] relative z-10 w-full">
      {/* Header */}
      <header className="bg-white/10 backdrop-blur-md shadow-xl border-b border-white/20 relative z-50">
        <div className="w-full px-4 sm:px-6 lg:px-8 py-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-4">
              <Link href="/dashboard/knowledge" className="p-2 hover:bg-white/10 rounded-xl transition">
                <Icon icon="mdi:arrow-left" className="text-2xl text-white" />
              </Link>
              <div className="flex flex-col">
                <h1 className="text-xl font-bold text-white drop-shadow-lg">{t.knowledgeEditor || 'Knowledge Editor'}</h1>
                <p className="text-xs text-white/70">{t.createKnowledgeItem}</p>
              </div>
            </div>
            <div className="flex items-center space-x-3">
              <button
                onClick={() => setIsLeftSidebarCollapsed(!isLeftSidebarCollapsed)}
                className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
                title={t.show || 'Toggle Sidebar'}
              >
                <Icon icon={isLeftSidebarCollapsed ? 'mdi:chevron-right' : 'mdi:chevron-left'} />
              </button>
              <button
                onClick={handleSave}
                disabled={saving}
                className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2 disabled:opacity-50"
              >
                <Icon icon="mdi:content-save" />
                <span>{saving ? t.saving || 'Saving...' : t.save}</span>
              </button>
              <button
                onClick={() => setIsSplitView(!isSplitView)}
                className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
                title={t.toggleView || 'Toggle View'}
              >
                <Icon icon={isSplitView ? 'mdi:view-split-vertical' : 'mdi:view-column'} />
              </button>
              <button
                onClick={() => setIsRightPanelCollapsed(!isRightPanelCollapsed)}
                className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
                title={t.show || 'Toggle Panel'}
              >
                <Icon icon={isRightPanelCollapsed ? 'mdi:chevron-left' : 'mdi:chevron-right'} />
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* Main Layout */}
      <div className="flex flex-1 overflow-hidden relative z-10">
        {/* Left Sidebar */}
        <aside
          className={`${isLeftSidebarCollapsed ? 'w-0 -ml-64 opacity-0' : 'w-64'
            } bg-white/10 backdrop-blur-md border-r border-white/20 shadow-xl overflow-y-auto transition-all duration-300 ease-in-out flex-shrink-0`}
        >
          <div className="p-4">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-lg font-bold text-white drop-shadow-md">{t.categories || 'CATEGORIES'}</h2>
              <button
                onClick={() => {/* TODO: Create category */ }}
                className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                title={t.createCategory || 'Create Category'}
              >
                <Icon icon="mdi:folder-plus" />
              </button>
            </div>
            <nav className="space-y-1">
              {categoryTree.length > 0 ? (
                renderCategoryTree(categoryTree)
              ) : (
                <p className="text-white/60 text-sm">{t.noCategories || 'No categories'}</p>
              )}
            </nav>
          </div>
        </aside>

        {/* Main Content */}
        <main className="flex-1 overflow-hidden relative z-10">
          {/* Toolbar */}
          <div className="bg-white/10 backdrop-blur-md border-b border-white/20 px-6 py-3 flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <button
                onClick={() => formatText('bold')}
                className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                title={t.bold || 'Bold'}
              >
                <Icon icon="mdi:format-bold" />
              </button>
              <button
                onClick={() => formatText('italic')}
                className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                title={t.italic || 'Italic'}
              >
                <Icon icon="mdi:format-italic" />
              </button>
              <button
                onClick={() => formatText('code')}
                className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                title={t.code || 'Code'}
              >
                <Icon icon="mdi:code-tags" />
              </button>
              <div className="w-px h-6 bg-white/20 mx-2"></div>
              <button
                onClick={() => insertHeading(1)}
                className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                title={t.heading1 || 'Heading 1'}
              >
                <Icon icon="mdi:format-header-1" />
              </button>
              <button
                onClick={() => insertHeading(2)}
                className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                title={t.heading2 || 'Heading 2'}
              >
                <span className="text-xs">H2</span>
              </button>
              <button
                onClick={insertList}
                className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                title={t.list || 'List'}
              >
                <Icon icon="mdi:format-list-bulleted" />
              </button>
              <button
                onClick={insertLink}
                className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                title={t.link || 'Link'}
              >
                <Icon icon="mdi:link" />
              </button>
            </div>
            <div className="flex items-center space-x-2 text-sm text-white/70">
              <span>{wordCount} {t.words || 'words'}</span>
              <span className="text-white/40">•</span>
              <span>{charCount} {t.characters || 'characters'}</span>
            </div>
          </div>

          {/* Editor & Preview */}
          <div className={`flex h-[calc(100%-60px)] ${isSplitView ? 'grid grid-cols-2' : ''}`}>
            {/* Editor */}
            <div className={`flex flex-col h-full ${!isSplitView ? 'w-full' : ''}`}>
              <div className="bg-white/5 backdrop-blur-sm border-b border-white/10 px-4 py-2 flex items-center justify-between">
                <span className="text-sm font-medium text-white/80">EDITOR</span>
              </div>
              <textarea
                id="markdown-editor"
                value={content}
                onChange={(e) => setContent(e.target.value)}
                className="markdown-editor w-full h-full bg-transparent text-white p-6 resize-none focus:outline-none font-mono text-sm"
                placeholder={`# ${t.startWriting || 'Start writing...'}\n\n${t.markdownHint || 'You can use Markdown to format text.'}\n\n## ${t.heading || 'Heading'}\n\n- ${t.list || 'List'}\n- ${t.item || 'Item'}\n\n\`\`\`javascript\n// ${t.codeBlock || 'Code block'}\nfunction hello() {\n    console.log('Hello World');\n}\n\`\`\`\n\n> ${t.blockquote || 'Blockquote'}\n\n**${t.bold || 'Bold'}** ${t.and || 'and'} *${t.italic || 'Italic'}*`}
              />
            </div>

            {/* Preview */}
            {isSplitView && (
              <div className="flex flex-col h-full border-l border-white/10">
                <div className="bg-white/5 backdrop-blur-sm border-b border-white/10 px-4 py-2 flex items-center justify-between">
                  <span className="text-sm font-medium text-white/80">PREVIEW</span>
                </div>
                <div
                  className="markdown-preview w-full h-full overflow-y-auto p-6 text-white prose prose-invert max-w-none"
                  dangerouslySetInnerHTML={{
                    __html: content ? markdownToHtml(content) : `<p class="text-white/50 italic">${t.previewWillAppear || 'Preview will appear here...'}</p>`,
                  }}
                />
              </div>
            )}
          </div>
        </main>

        {/* Right Panel */}
        <aside
          className={`${isRightPanelCollapsed ? 'w-0 -mr-80 opacity-0' : 'w-80'
            } bg-white/10 backdrop-blur-md border-l border-white/20 shadow-xl overflow-y-auto transition-all duration-300 ease-in-out flex-shrink-0`}
        >
          <div className="p-4 space-y-6">
            {/* Document Info */}
            <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
              <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.information || 'INFORMATION'}</h2>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-white/80 mb-2">{t.title}</label>
                  <input
                    type="text"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                    placeholder={t.enterTitle || 'Enter title...'}
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-white/80 mb-2">{t.category}</label>
                  <select
                    value={categoryId || ''}
                    onChange={(e) => setCategoryId(e.target.value ? parseInt(e.target.value) : null)}
                    className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                    style={{ color: 'white' }}
                    title={t.category}
                    aria-label={t.category}
                  >
                    <option value="" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.none || 'None'}</option>
                    {categories.map((cat) => (
                      <option key={cat.id} value={cat.id} style={{ backgroundColor: '#1a1a1a', color: 'white' }}>
                        {cat.name}
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-white/80 mb-2">{t.type}</label>
                  <select
                    value={itemType}
                    onChange={(e) => setItemType(e.target.value as any)}
                    className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                    style={{ color: 'white' }}
                    title={t.type}
                    aria-label={t.type}
                  >
                    <option value="note" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.note}</option>
                    <option value="code_snippet" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.codeSnippet}</option>
                    <option value="resource_link" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.resourceLink}</option>
                    <option value="exercise" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.exercise}</option>
                    <option value="attachment" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.attachment}</option>
                  </select>
                </div>
              </div>
            </div>

            {/* Tags */}
            <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-lg font-bold text-white drop-shadow-md">TAGS</h2>
                <div className="flex items-center space-x-2">
                  <input
                    type="text"
                    value={newTag}
                    onChange={(e) => setNewTag(e.target.value)}
                    onKeyPress={(e) => e.key === 'Enter' && addTag()}
                    className="px-2 py-1 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white text-sm placeholder-white/40 focus:outline-none focus:ring-1 focus:ring-[#0FA968]"
                    placeholder={t.addTag || 'Add tag...'}
                  />
                  <button
                    onClick={addTag}
                    className="p-1.5 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
                    title={t.addTag || 'Add Tag'}
                  >
                    <Icon icon="mdi:plus" className="text-sm" />
                  </button>
                </div>
              </div>
              <div className="flex flex-wrap gap-2">
                {tags.map((tag, idx) => {
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
                      className={`px-3 py-1.5 rounded-lg text-sm font-medium border flex items-center space-x-2 ${color.bg.startsWith('#')
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
                      <span>{tag}</span>
                      <button
                        onClick={() => removeTag(tag)}
                        className="hover:opacity-70"
                        title={`${t.removeTag || 'Remove'} ${tag}`}
                      >
                        <Icon icon="mdi:close" className="text-xs" />
                      </button>
                    </span>
                  );
                })}
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  );
}
