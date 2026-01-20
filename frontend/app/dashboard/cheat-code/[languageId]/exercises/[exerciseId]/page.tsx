'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { exerciseService, ExerciseDetail, ExerciseTestCase } from '@/lib/services/exerciseService';
import { cheatCodeService } from '@/lib/services/cheatCodeService';

declare global {
  interface Window {
    monaco: any;
    require: any;
  }
}

export default function ExerciseIDEPage() {
  const router = useRouter();
  const params = useParams();
  const languageId = params.languageId as string;
  const exerciseId = params.exerciseId as string;
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [exercise, setExercise] = useState<ExerciseDetail | null>(null);
  const [language, setLanguage] = useState<any>(null);
  const [code, setCode] = useState<string>('');
  const [output, setOutput] = useState<string>('');
  const [testResults, setTestResults] = useState<any[]>([]);
  const [isRunning, setIsRunning] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [allPassed, setAllPassed] = useState(false);
  const editorRef = useRef<any>(null);
  const editorContainerRef = useRef<HTMLDivElement>(null);
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

  const loadExercise = useCallback(async () => {
    try {
      const response = await exerciseService.getExercise(languageId, exerciseId);
      if (response.success && response.data) {
        setExercise(response.data);
        setCode(response.data.starterCode || '');
      }
    } catch (error) {
      console.error('Failed to load exercise:', error);
    }
  }, [languageId, exerciseId]);

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

  useEffect(() => {
    loadExercise();
    loadLanguage();
  }, [loadExercise, loadLanguage]);

  useEffect(() => {
    if (!editorContainerRef.current || !exercise || editorRef.current) return;

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
      if (!editorContainerRef.current || !exercise || editorRef.current) return;

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
      };

      const editorLanguage = languageMap[language?.name?.toLowerCase() || 'javascript'] || 'javascript';

      const editor = window.monaco.editor.create(editorContainerRef.current, {
        value: exercise.starterCode || '',
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
        readOnly: false,
      });

      editor.onDidChangeModelContent(() => {
        setCode(editor.getValue());
      });

      editorRef.current = editor;
      setCode(exercise.starterCode || '');
    };

    loadMonaco();

    return () => {
      if (editorRef.current) {
        editorRef.current.dispose();
        editorRef.current = null;
      }
    };
  }, [exercise, language]);

  const handleRun = async () => {
    if (!exercise || !code) return;

    setIsRunning(true);
    setOutput('');
    setTestResults([]);

    try {
      const response = await exerciseService.submitSolution(languageId, exerciseId, { code });
      if (response.success && response.data) {
        setAllPassed(response.data.allPassed);
        setTestResults(response.data.results || []);

        const outputLines: string[] = [];
        response.data.results?.forEach((result: any, index: number) => {
          if (result.is_sample) {
            outputLines.push(`${result.passed ? '✓' : '✗'} Test Case ${index + 1}: ${result.passed ? t.testCaseSuccess : t.testCaseFailed}`);
            if (result.expectedOutput) {
              outputLines.push(`  ${t.expectedOutput}: ${result.expectedOutput}`);
            }
            if (result.actualOutput !== null && result.actualOutput !== undefined && result.actualOutput !== '') {
              outputLines.push(`  ${t.actualOutput}: ${result.actualOutput}`);
            }
            if (result.error) {
              outputLines.push(`  ${t.error}: ${result.error}`);
            }
          }
        });

        if (response.data.allPassed) {
          outputLines.push('');
          outputLines.push(t.allTestCasesPassed);
        } else {
          outputLines.push('');
          outputLines.push(t.testCasesPassed.replace('{passed}', String(response.data.passedCount)).replace('{total}', String(response.data.totalCount)));
        }

        setOutput(outputLines.length > 0 ? outputLines.join('\n') : t.noRunOutput);
      } else {
        setOutput(`${t.error}: ${response?.message || t.noRunOutput}`);
      }
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || error.message || 'Unknown error';
      setOutput(`${t.error}: ${errorMessage}`);
    } finally {
      setIsRunning(false);
    }
  };

  const handleReset = () => {
    if (editorRef.current && exercise) {
      const starterCode = exercise.starterCode || '';
      editorRef.current.setValue(starterCode);
      editorRef.current.setPosition({ lineNumber: 1, column: 1 });
      setCode(starterCode);
    }
    setOutput('');
    setTestResults([]);
    setAllPassed(false);
    setIsRunning(false);
  };

  const handleSubmit = async () => {
    if (!exercise || !code) return;

    setIsSubmitting(true);
    try {
      const response = await exerciseService.submitSolution(languageId, exerciseId, { code });
      if (response.success && response.data) {
        if (response.data.allPassed) {
          alert(t.submissionSuccessful);
        } else {
          alert(t.submissionCompleted.replace('{passed}', String(response.data.passedCount)).replace('{total}', String(response.data.totalCount)));
        }
      }
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || error.message || 'Unknown error';
      alert(`${t.error}: ${errorMessage}`);
    } finally {
      setIsSubmitting(false);
    }
  };

  const getTestCaseStatus = (index: number): 'waiting' | 'running' | 'passed' | 'failed' => {
    if (isRunning) return 'running';
    if (testResults.length === 0) return 'waiting';
    const result = testResults[index];
    if (!result) return 'waiting';
    if (result.passed) return 'passed';
    return 'failed';
  };

  const getTestCaseStatusText = (status: string) => {
    switch (status) {
      case 'waiting':
        return t.waiting;
      case 'running':
        return t.running;
      case 'passed':
        return t.passed;
      case 'failed':
        return t.failed;
      default:
        return '';
    }
  };

  return (
    <div className="flex h-[calc(100vh-85px)] relative z-10 min-w-0">
      {/* Problem Description Panel */}
      <aside className="w-96 bg-white/10 backdrop-blur-md border-r border-white/20 shadow-xl overflow-y-auto flex-shrink-0">
        <div className="p-6">
          {exercise ? (
            <>
              <div className="mb-6">
                <h2 className="text-lg font-semibold text-white mb-2 drop-shadow-md">{t.problemDescription}</h2>
                <p className="text-white/80 text-sm leading-relaxed">{exercise.description || exercise.question}</p>
              </div>

              {exercise.question && (
                <div className="mb-6">
                  <h3 className="text-md font-semibold text-white mb-2 drop-shadow-sm">{t.question}</h3>
                  <div className="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/20">
                    <p className="text-white/80 text-sm whitespace-pre-wrap">{exercise.question}</p>
                  </div>
                </div>
              )}

              {exercise.testCases && exercise.testCases.length > 0 && (
                <div className="mb-6">
                  <h3 className="text-md font-semibold text-white mb-2 drop-shadow-sm">{t.examples}</h3>
                  {exercise.testCases.map((testCase, index) => (
                    <div key={index} className="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/20 mb-3">
                      <div className="text-white/60 text-xs mb-1">{t.input}:</div>
                      <div className="text-[#0FA968] font-mono text-sm mb-3">{testCase.input}</div>
                      <div className="text-white/60 text-xs mb-1">{t.expectedOutput}:</div>
                      <div className="text-[#0FA968] font-mono text-sm">{testCase.expectedOutput}</div>
                    </div>
                  ))}
                </div>
              )}

              {exercise.hints && exercise.hints.length > 0 && (
                <div className="mb-6">
                  <h3 className="text-md font-semibold text-white mb-2 drop-shadow-sm">{t.hints}</h3>
                  <ul className="text-white/80 text-sm space-y-2">
                    {exercise.hints.map((hint, index) => (
                      <li key={index}>• {hint}</li>
                    ))}
                  </ul>
                </div>
              )}

              {/* Test Cases */}
              <div>
                <h3 className="text-md font-semibold text-white mb-3 drop-shadow-sm">{t.testCases}</h3>
                <div className="space-y-3" id="test-cases">
                  {exercise.testCases?.map((testCase, index) => {
                    const status = getTestCaseStatus(index);
                    return (
                      <div
                        key={index}
                        className={`test-case border rounded-xl p-4 backdrop-blur-sm ${
                          status === 'passed'
                            ? 'border-green-500/50 bg-green-500/10'
                            : status === 'failed'
                            ? 'border-red-500/50 bg-red-500/10'
                            : status === 'running'
                            ? 'border-blue-500/50 bg-blue-500/10'
                            : 'border-white/20 bg-white/5'
                        }`}
                      >
                        <div className="flex items-center justify-between mb-2">
                          <span className="text-sm font-medium text-white">Test Case {index + 1}</span>
                          <span
                            className={`text-xs ${
                              status === 'passed'
                                ? 'text-green-400'
                                : status === 'failed'
                                ? 'text-red-400'
                                : status === 'running'
                                ? 'text-blue-400'
                                : 'text-white/60'
                            }`}
                          >
                            {getTestCaseStatusText(status)}
                          </span>
                        </div>
                        <div className="text-xs text-white/70 font-mono">
                          {testCase.input} → {testCase.expectedOutput}
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            </>
          ) : (
            <div className="text-white/60 text-center py-8">{t.loading}</div>
          )}
        </div>
      </aside>

      {/* Editor Area */}
      <main className="flex-1 flex flex-col bg-transparent min-w-0">
        {/* Editor Header */}
        <div className="bg-white/10 backdrop-blur-md border-b border-white/20 px-4 py-2 flex items-center justify-between flex-wrap gap-2">
          <div className="flex items-center space-x-2 flex-wrap gap-2 min-w-0">
            <div className="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-t-xl text-sm text-white border-b-2 border-[#0FA968] flex-shrink-0">
              <Icon icon="mdi:code-tags" className="inline mr-2" />
              solution.{language?.name?.toLowerCase() || 'js'}
            </div>
            {exercise && (
              <div className="flex items-center space-x-3 text-xs text-white/70 flex-wrap gap-2">
                {exercise.timeLimit && (
                  <span className="flex items-center flex-shrink-0">
                    <Icon icon="mdi:clock-outline" className="mr-1" />
                    {exercise.timeLimit}
                    {currentLang === 'ja' ? '分' : currentLang === 'en' ? 'min' : 'phút'}
                  </span>
                )}
                <span className="flex items-center flex-shrink-0">
                  <Icon icon="mdi:star" className="mr-1" />
                  {exercise.points} {t.points}
                </span>
                {exercise.difficulty && (
                  <span
                    className={`px-2 py-1 rounded text-xs font-bold flex-shrink-0 ${
                      exercise.difficulty === 'easy'
                        ? 'bg-green-100 text-green-700'
                        : exercise.difficulty === 'medium'
                        ? 'bg-yellow-100 text-yellow-700'
                        : 'bg-red-100 text-red-700'
                    }`}
                  >
                    {exercise.difficulty === 'easy'
                      ? t.easy
                      : exercise.difficulty === 'medium'
                      ? t.medium
                      : t.hard}
                  </span>
                )}
              </div>
            )}
          </div>
          <div className="flex items-center space-x-2 flex-wrap gap-2 flex-shrink-0">
            <button
              onClick={() => router.push(`/dashboard/cheat-code/${languageId}`)}
              className="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/20 text-white rounded-xl hover:bg-white/30 transition text-sm"
            >
              <Icon icon="mdi:arrow-left" className="inline mr-2" />
              {t.back}
            </button>
            <button
              onClick={handleReset}
              className="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/20 text-white rounded-xl hover:bg-white/30 transition text-sm"
            >
              <Icon icon="mdi:refresh" className="inline mr-2" />
              {t.reset}
            </button>
            <button
              onClick={handleRun}
              disabled={isRunning || !code}
              className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Icon icon={isRunning ? 'mdi:loading' : 'mdi:play'} className={isRunning ? 'animate-spin' : ''} />
              <span>{t.run}</span>
            </button>
            <button
              onClick={handleSubmit}
              disabled={isSubmitting || !code || !allPassed}
              className="px-4 py-2 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Icon icon={isSubmitting ? 'mdi:loading' : 'mdi:check'} className={isSubmitting ? 'animate-spin' : ''} />
              <span>{t.submit}</span>
            </button>
          </div>
        </div>

        {/* Monaco Editor Container */}
        <div className="flex-1 relative bg-zinc-950/50 backdrop-blur-sm">
          <div ref={editorContainerRef} className="w-full h-full" />
        </div>

        {/* Output Panel */}
        <div className="bg-white/10 backdrop-blur-md border-t border-white/20">
          <div className="px-4 py-3 border-b border-white/20 flex items-center justify-between">
            <h3 className="text-sm font-semibold text-white drop-shadow-sm flex items-center">
              <Icon icon="mdi:terminal" className="mr-2" />
              {t.output}
            </h3>
            <button
              onClick={() => setOutput('')}
              className="text-xs text-white/60 hover:text-white transition"
            >
              <Icon icon="mdi:trash" className="inline mr-1" />
              {t.clear}
            </button>
          </div>
          <div className="p-4 font-mono text-sm min-h-[120px] max-h-[200px] overflow-y-auto">
            <div className="text-white/80 whitespace-pre-wrap">
              {output || <span className="text-white/50 italic">{t.resultsWillAppear}</span>}
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
