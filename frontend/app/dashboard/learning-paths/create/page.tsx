'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { learningPathService, CreateLearningPathData } from '@/lib/services/learningPathService';

interface MindMapNode {
  id: string;
  type: 'root' | 'milestone' | 'task';
  title: string;
  description: string;
  estimatedHours: number;
  estimatedMinutes?: number;
  color: string;
  position: { x: number; y: number };
  parentId?: string;
  priority?: number;
  subtasks?: { title: string; completed?: boolean }[];
  milestoneId?: number;
}

export default function LearningPathCreatorPage() {
  const router = useRouter();
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [goalType, setGoalType] = useState<'career' | 'skill' | 'certification' | 'hobby'>('skill');
  const [startDate, setStartDate] = useState<string>('');
  const [endDate, setEndDate] = useState<string>('');
  const [nodes, setNodes] = useState<MindMapNode[]>([]);
  const [selectedNodeId, setSelectedNodeId] = useState<string | null>(null);
  const [isDragging, setIsDragging] = useState(false);
  const [dragOffset, setDragOffset] = useState({ x: 0, y: 0 });
  const [currentZoom, setCurrentZoom] = useState(1);
  const [gridVisible, setGridVisible] = useState(false);
  const [saving, setSaving] = useState(false);
  const [isLeftSidebarCollapsed, setIsLeftSidebarCollapsed] = useState(false);
  const [isRightSidebarCollapsed, setIsRightSidebarCollapsed] = useState(false);
  const canvasRef = useRef<HTMLDivElement>(null);
  const svgRef = useRef<SVGSVGElement>(null);
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

  useEffect(() => {
    if (nodes.length === 0) {
      const rootNode: MindMapNode = {
        id: 'root',
        type: 'root',
        title: t.learningPathCreator,
        description: '',
        estimatedHours: 0,
        color: '#0FA968',
        position: { x: 50, y: 50 },
      };
      setNodes([rootNode]);
      setSelectedNodeId('root');
    }
  }, []);

  useEffect(() => {
    updateConnections();
  }, [nodes]);

  const updateConnections = useCallback(() => {
    if (!svgRef.current || !canvasRef.current) return;

    const svg = svgRef.current;
    svg.innerHTML = '';

    const rootNode = nodes.find((n) => n.type === 'root');
    if (!rootNode) return;

    const canvasRect = canvasRef.current.getBoundingClientRect();

    const drawLine = (fromId: string, toId: string, stroke = 'rgba(255, 255, 255, 0.3)') => {
      const fromEl = document.querySelector(`[data-node-id="${fromId}"]`) as HTMLElement;
      const toEl = document.querySelector(`[data-node-id="${toId}"]`) as HTMLElement;
      if (!fromEl || !toEl) return;
      const fromRect = fromEl.getBoundingClientRect();
      const toRect = toEl.getBoundingClientRect();
      const x1 = fromRect.left - canvasRect.left + fromRect.width / 2;
      const y1 = fromRect.top - canvasRect.top + fromRect.height / 2;
      const x2 = toRect.left - canvasRect.left + toRect.width / 2;
      const y2 = toRect.top - canvasRect.top + toRect.height / 2;
      const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
      line.setAttribute('x1', String(x1));
      line.setAttribute('y1', String(y1));
      line.setAttribute('x2', String(x2));
      line.setAttribute('y2', String(y2));
      line.setAttribute('stroke', stroke);
      line.setAttribute('stroke-width', '2');
      svg.appendChild(line);
    };

    // Root → Milestone
    nodes.filter((n) => n.type === 'milestone').forEach((m) => drawLine(rootNode.id, m.id));
    // Milestone → Task (task.parentId === milestone.id)
    nodes.filter((n) => n.type === 'task' && n.parentId).forEach((task) => {
      drawLine(task.parentId!, task.id, 'rgba(255, 184, 0, 0.5)');
    });
  }, [nodes]);

  const handleNodeMouseDown = (e: React.MouseEvent, nodeId: string) => {
    if ((e.target as HTMLElement).closest('button')) return;
    e.preventDefault();
    setIsDragging(true);
    setSelectedNodeId(nodeId);
    const node = nodes.find((n) => n.id === nodeId);
    if (!node || !canvasRef.current) return;

    const nodeElement = e.currentTarget as HTMLElement;
    const nodeRect = nodeElement.getBoundingClientRect();
    const canvasRect = canvasRef.current.getBoundingClientRect();
    
    const nodeCenterX = nodeRect.left + nodeRect.width / 2;
    const nodeCenterY = nodeRect.top + nodeRect.height / 2;
    
    setDragOffset({
      x: e.clientX - nodeCenterX,
      y: e.clientY - nodeCenterY,
    });
  };

  const handleMouseMove = useCallback(
    (e: MouseEvent) => {
      if (!isDragging || !selectedNodeId || !canvasRef.current) return;

      e.preventDefault();
      const canvasRect = canvasRef.current.getBoundingClientRect();
      
      // Calculate new node center position relative to canvas
      const nodeCenterX = e.clientX - dragOffset.x;
      const nodeCenterY = e.clientY - dragOffset.y;
      
      const x = ((nodeCenterX - canvasRect.left) / canvasRect.width) * 100;
      const y = ((nodeCenterY - canvasRect.top) / canvasRect.height) * 100;

      setNodes((prev) =>
        prev.map((node) =>
          node.id === selectedNodeId
            ? {
                ...node,
                position: {
                  x: Math.max(0, Math.min(100, x)),
                  y: Math.max(0, Math.min(100, y)),
                },
              }
            : node
        )
      );
    },
    [isDragging, selectedNodeId, dragOffset]
  );

  const handleMouseUp = useCallback(() => {
    if (isDragging) {
      setIsDragging(false);
    }
  }, [isDragging]);

  useEffect(() => {
    if (isDragging) {
      document.addEventListener('mousemove', handleMouseMove);
      document.addEventListener('mouseup', handleMouseUp);
      return () => {
        document.removeEventListener('mousemove', handleMouseMove);
        document.removeEventListener('mouseup', handleMouseUp);
      };
    }
  }, [isDragging, handleMouseMove, handleMouseUp]);

  const addMilestone = () => {
    const newNodeId = `milestone-${Date.now()}`;
    const newNode: MindMapNode = {
      id: newNodeId,
      type: 'milestone',
      title: t.milestone,
      description: '',
      estimatedHours: 0,
      color: '#1F6FEB',
      position: { x: 50, y: 50 },
    };
    setNodes((prev) => [...prev, newNode]);
    setSelectedNodeId(newNodeId);
  };

  const addSubtaskToSelectedTask = () => {
    if (!selectedNodeId || !selectedNode || selectedNode.type !== 'task') return;
    const subs = selectedNode.subtasks ?? [];
    updateNodeProperties(selectedNodeId, {
      subtasks: [...subs, { title: '', completed: false }],
    });
  };

  const addTask = () => {
    if (!selectedNodeId || !selectedNode || selectedNode.type !== 'milestone') {
      alert(t.selectMilestoneFirst);
      return;
    }
    const newNodeId = `task-${Date.now()}`;
    const milestonePos = selectedNode.position;
    const siblingTasks = nodes.filter((n) => n.type === 'task' && n.parentId === selectedNodeId);
    const offsetY = siblingTasks.length * 8;
    const newNode: MindMapNode = {
      id: newNodeId,
      type: 'task',
      title: t.task,
      description: '',
      estimatedHours: 0,
      estimatedMinutes: 30,
      color: '#FFB800',
      position: {
        x: Math.min(milestonePos.x + 15, 85),
        y: Math.min(milestonePos.y + 10 + offsetY, 85),
      },
      parentId: selectedNodeId,
      priority: 3,
      subtasks: [],
    };
    setNodes((prev) => [...prev, newNode]);
    setSelectedNodeId(newNodeId);
  };

  const deleteSelected = () => {
    if (!selectedNodeId || selectedNodeId === 'root') {
      alert(t.delete + ' ' + t.milestone + '?');
      return;
    }
    if (confirm(t.delete + '?')) {
      const removeIds = new Set([selectedNodeId]);
      const sel = nodes.find((n) => n.id === selectedNodeId);
      if (sel?.type === 'milestone') {
        nodes.filter((n) => n.type === 'task' && n.parentId === selectedNodeId).forEach((t) => removeIds.add(t.id));
      }
      setNodes((prev) => prev.filter((n) => !removeIds.has(n.id)));
      setSelectedNodeId('root');
    }
  };

  const updateNodeProperties = (nodeId: string, updates: Partial<MindMapNode>) => {
    setNodes((prev) => prev.map((node) => (node.id === nodeId ? { ...node, ...updates } : node)));
  };

  const zoomIn = () => {
    setCurrentZoom((prev) => Math.min(prev + 0.1, 2));
  };

  const zoomOut = () => {
    setCurrentZoom((prev) => Math.max(prev - 0.1, 0.5));
  };

  const resetZoom = () => {
    setCurrentZoom(1);
  };

  const centerView = () => {
    const rootNode = nodes.find((n) => n.type === 'root');
    if (!rootNode || !canvasRef.current) return;

    const canvasRect = canvasRef.current.getBoundingClientRect();
    const offsetX = 50 - rootNode.position.x;
    const offsetY = 50 - rootNode.position.y;

    setNodes((prev) =>
      prev.map((node) => ({
        ...node,
        position: {
          x: Math.max(0, Math.min(100, node.position.x + offsetX)),
          y: Math.max(0, Math.min(100, node.position.y + offsetY)),
        },
      }))
    );
  };

  const toggleGrid = () => {
    setGridVisible((prev) => !prev);
  };

  const savePath = async () => {
    const pathTitle = (nodes.find((n) => n.type === 'root')?.title ?? '').trim();
    if (!pathTitle) {
      alert(t.pathTitle + ' ' + t.required);
      return;
    }

    setSaving(true);
    try {
      const milestones = nodes
        .filter((n) => n.type === 'milestone')
        .map((milestone, index) => ({
          title: milestone.title,
          description: milestone.description,
          sort_order: index,
          estimated_hours: milestone.estimatedHours || 0,
          position_x: milestone.position.x,
          position_y: milestone.position.y,
          tasks: nodes
            .filter((n) => n.type === 'task' && n.parentId === milestone.id)
            .map((task, taskIndex) => ({
              title: task.title,
              description: task.description,
              estimated_minutes: task.estimatedMinutes ?? 30,
              priority: task.priority ?? 3,
              subtasks: (task.subtasks ?? []).map((st) => ({ title: st.title })),
            })),
        }));

      const totalHours = nodes.reduce((sum, n) => sum + (n.estimatedHours || 0), 0);
      const totalTaskMinutes = nodes
        .filter((n) => n.type === 'task')
        .reduce((sum, n) => sum + (n.estimatedMinutes ?? 30), 0);
      const rootNode = nodes.find((n) => n.type === 'root');
      const pathData: CreateLearningPathData = {
        title: pathTitle,
        description: rootNode?.description?.trim() || undefined,
        goal_type: goalType,
        target_start_date: startDate || undefined,
        target_end_date: endDate || undefined,
        estimated_hours_total: totalHours + Math.ceil(totalTaskMinutes / 60),
        color: rootNode?.color || '#0FA968',
        milestones,
      };

      const response = await learningPathService.createLearningPath(pathData);
      if (response.success && response.data) {
        alert(t.save + ' ' + t.success);
        router.push(`/dashboard/learning-paths/${response.data.id}`);
      }
    } catch (error) {
      console.error('Failed to save learning path:', error);
      alert(t.error + ': ' + (error as Error).message);
    } finally {
      setSaving(false);
    }
  };

  const exportPath = () => {
    const pathTitle = nodes.find((n) => n.type === 'root')?.title ?? '';
    const data = {
      title: pathTitle,
      goalType,
      startDate,
      endDate,
      nodes: nodes.map((node) => ({
        id: node.id,
        type: node.type,
        title: node.title,
        description: node.description,
        estimatedHours: node.estimatedHours,
        estimatedMinutes: node.estimatedMinutes,
        color: node.color,
        position: node.position,
        parentId: node.parentId,
        priority: node.priority,
        subtasks: node.subtasks,
      })),
      exportedAt: new Date().toISOString(),
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${pathTitle.replace(/\s+/g, '_')}_${Date.now()}.json`;
    a.click();
    URL.revokeObjectURL(url);
  };

  const importPath = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';
    input.onchange = (e) => {
      const file = (e.target as HTMLInputElement).files?.[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
          try {
            const data = JSON.parse(event.target?.result as string);
            setGoalType(data.goalType || 'skill');
            setStartDate(data.startDate || '');
            setEndDate(data.endDate || '');
            if (data.nodes && Array.isArray(data.nodes)) {
              const normalized = data.nodes.map((n: MindMapNode) => ({
                ...n,
                estimatedMinutes: n.estimatedMinutes ?? (n.type === 'task' ? 30 : undefined),
                priority: n.priority ?? (n.type === 'task' ? 3 : undefined),
                subtasks: n.subtasks ?? (n.type === 'task' ? [] : undefined),
              }));
              const root = normalized.find((n: MindMapNode) => n.type === 'root');
              if (root && data.title) root.title = data.title;
              setNodes(normalized);
            }
            alert(t.import + ' ' + t.success);
          } catch (error) {
            alert(t.error + ': ' + (error as Error).message);
          }
        };
        reader.readAsText(file);
      }
    };
    input.click();
  };

  const selectedNode = nodes.find((n) => n.id === selectedNodeId);
  const totalMilestones = nodes.filter((n) => n.type === 'milestone').length;
  const totalTasks = nodes.filter((n) => n.type === 'task').length;

  /** Format total minutes as "Xh Ym" or "Xm" */
  const formatTimeTotal = (totalMinutes: number) => {
    if (totalMinutes < 60) return `${totalMinutes}m`;
    const h = Math.floor(totalMinutes / 60);
    const m = totalMinutes % 60;
    return m > 0 ? `${h}h ${m}m` : `${h}h`;
  };

  /** Total minutes per milestone (milestone hours + all its tasks' minutes) */
  const getMilestoneTotalMinutes = (milestoneId: string) => {
    const milestone = nodes.find((n) => n.id === milestoneId && n.type === 'milestone');
    if (!milestone) return 0;
    const taskMinutes = nodes
      .filter((n) => n.type === 'task' && n.parentId === milestoneId)
      .reduce((sum, t) => sum + (t.estimatedMinutes ?? 30), 0);
    return (milestone.estimatedHours || 0) * 60 + taskMinutes;
  };

  /** Total path: all milestones' time + all tasks' time (tasks already counted in milestone) */
  const totalPathMinutes =
    nodes
      .filter((n) => n.type === 'milestone')
      .reduce((sum, m) => sum + getMilestoneTotalMinutes(m.id), 0);
  const totalHours = nodes.reduce((sum, n) => sum + (n.estimatedHours || 0), 0);

  return (
    <div className="flex flex-col h-[calc(100vh-85px)] relative z-10 w-full">
      {/* Header */}
      <header className="bg-white/10 backdrop-blur-md shadow-xl border-b border-white/20 relative z-10">
        <div className="w-full px-4 sm:px-6 lg:px-8 py-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center">
              <div className="flex flex-col">
                <h1 className="text-xl font-bold text-white drop-shadow-lg">{t.learningPathCreator}</h1>
                <p className="text-xs text-white/70">{t.createLearningPath}</p>
              </div>
            </div>
            <div className="flex items-center space-x-3">
              <button
                onClick={() => setIsLeftSidebarCollapsed(!isLeftSidebarCollapsed)}
                className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
                title={isLeftSidebarCollapsed ? t.show : t.hide}
              >
                <Icon icon={isLeftSidebarCollapsed ? 'mdi:chevron-right' : 'mdi:chevron-left'} className="text-base" />
              </button>
              <button
                onClick={importPath}
                className="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/20 text-white rounded-xl hover:bg-white/30 transition"
              >
                <Icon icon="mdi:upload" className="inline mr-2" />
                {t.import}
              </button>
              <button
                onClick={exportPath}
                className="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/20 text-white rounded-xl hover:bg-white/30 transition"
              >
                <Icon icon="mdi:download" className="inline mr-2" />
                {t.export}
              </button>
              <button
                onClick={savePath}
                disabled={saving}
                className="px-4 py-2 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold flex items-center space-x-2 disabled:opacity-50"
              >
                <Icon icon={saving ? 'mdi:loading' : 'mdi:content-save'} className={saving ? 'animate-spin' : ''} />
                <span>{t.save}</span>
              </button>
              <button
                onClick={() => setIsRightSidebarCollapsed(!isRightSidebarCollapsed)}
                className="p-2.5 text-white hover:bg-white/20 rounded-xl transition backdrop-blur-sm border border-white/20"
                title={isRightSidebarCollapsed ? t.show : t.hide}
              >
                <Icon icon={isRightSidebarCollapsed ? 'mdi:chevron-left' : 'mdi:chevron-right'} className="text-base" />
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <div className="flex flex-1 overflow-hidden relative z-10">
        {/* Left Sidebar: Tools & Properties */}
        <aside
          className={`${
            isLeftSidebarCollapsed ? 'w-0 -ml-80 opacity-0' : 'w-80'
          } bg-white/10 backdrop-blur-md border-r border-white/20 shadow-xl overflow-y-auto transition-all duration-300 ease-in-out flex-shrink-0`}
        >
        <div className="p-4 space-y-6">
          {/* Tools */}
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
            <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.tools}</h2>
            <div className="space-y-2">
              <button
                onClick={addMilestone}
                className="w-full flex items-center space-x-3 px-4 py-3 bg-[#0FA968] hover:bg-[#0B8C57] text-white rounded-xl transition shadow-lg hover:shadow-xl font-semibold"
              >
                <Icon icon="mdi:plus" />
                <span>{t.addMilestone}</span>
              </button>
              <button
                onClick={addTask}
                className="w-full flex items-center space-x-3 px-4 py-3 bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 rounded-xl transition border border-white/20"
              >
                <Icon icon="mdi:tasks" />
                <span>{t.addTask}</span>
              </button>
              <button
                onClick={addSubtaskToSelectedTask}
                disabled={!selectedNode || selectedNode.type !== 'task'}
                className="w-full flex items-center space-x-3 px-4 py-3 bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 rounded-xl transition border border-white/20 disabled:opacity-50 disabled:cursor-not-allowed"
                title={selectedNode?.type === 'task' ? t.addSubtask : t.selectMilestoneFirst}
              >
                <Icon icon="mdi:format-list-checks" />
                <span>{t.addSubtask}</span>
              </button>
              <button
                onClick={() => alert(t.connect + ' (Coming soon)')}
                className="w-full flex items-center space-x-3 px-4 py-3 bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 rounded-xl transition border border-white/20"
              >
                <Icon icon="mdi:link" />
                <span>{t.connect}</span>
              </button>
              <button
                onClick={deleteSelected}
                className="w-full flex items-center space-x-3 px-4 py-3 bg-red-500/20 backdrop-blur-sm text-white hover:bg-red-500/30 rounded-xl transition border border-red-400/30"
              >
                <Icon icon="mdi:trash" />
                <span>{t.delete}</span>
              </button>
            </div>
          </div>

          {/* Selected Node Properties */}
          {selectedNode && (
            <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
              <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.properties}</h2>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-white/80 mb-2">{t.nodeTitle}</label>
                  <input
                    type="text"
                    value={selectedNode.title}
                    onChange={(e) => updateNodeProperties(selectedNodeId!, { title: e.target.value })}
                    className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                    placeholder={t.nodeTitle}
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-white/80 mb-2">{t.nodeDescription}</label>
                  <textarea
                    value={selectedNode.description}
                    onChange={(e) => updateNodeProperties(selectedNodeId!, { description: e.target.value })}
                    rows={3}
                    className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#0FA968] resize-none"
                    placeholder={t.nodeDescription}
                  />
                </div>
                {selectedNode.type === 'task' ? (
                  <>
                    <div>
                      <label className="block text-sm font-medium text-white/80 mb-2">{t.estimatedMinutes}</label>
                      <input
                        type="number"
                        min="0"
                        value={selectedNode.estimatedMinutes ?? 30}
                        onChange={(e) => updateNodeProperties(selectedNodeId!, { estimatedMinutes: parseInt(e.target.value) || 30 })}
                        className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                        placeholder="30"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-white/80 mb-2">{t.priority}</label>
                      <select
                        value={selectedNode.priority ?? 3}
                        onChange={(e) => updateNodeProperties(selectedNodeId!, { priority: parseInt(e.target.value) })}
                        className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                        style={{ color: 'white' }}
                        title={t.priority}
                        aria-label={t.priority}
                      >
                        <option value={1} style={{ backgroundColor: '#1a1a1a' }}>1 - {t.lowPriority}</option>
                        <option value={2} style={{ backgroundColor: '#1a1a1a' }}>2</option>
                        <option value={3} style={{ backgroundColor: '#1a1a1a' }}>3 - {t.mediumPriority}</option>
                        <option value={4} style={{ backgroundColor: '#1a1a1a' }}>4</option>
                        <option value={5} style={{ backgroundColor: '#1a1a1a' }}>5 - {t.highPriority}</option>
                      </select>
                    </div>
                    <div>
                      <div className="flex items-center justify-between mb-2">
                        <label className="block text-sm font-medium text-white/80">{t.subtask}</label>
                        <button
                          type="button"
                          onClick={addSubtaskToSelectedTask}
                          className="text-xs px-2 py-1 bg-[#0FA968]/30 text-white rounded-lg hover:bg-[#0FA968]/50"
                        >
                          + {t.addSubtask}
                        </button>
                      </div>
                      <ul className="space-y-2 max-h-40 overflow-y-auto list-none">
                        {(selectedNode.subtasks ?? []).length === 0 ? (
                          <li className="text-xs text-white/50 py-2">{t.noSubtasksYet}</li>
                        ) : (
                          (selectedNode.subtasks ?? []).map((st, idx) => (
                            <li key={idx} className="flex items-center gap-2">
                              <button
                                type="button"
                                onClick={() => {
                                  const subs = [...(selectedNode.subtasks ?? [])];
                                  subs[idx] = { ...st, completed: !st.completed };
                                  updateNodeProperties(selectedNodeId!, { subtasks: subs });
                                }}
                                className="flex-shrink-0 p-0.5 rounded text-white/70 hover:text-[#0FA968] focus:outline-none focus:ring-1 focus:ring-[#0FA968]"
                                title={st.completed ? t.completed : t.active}
                                aria-label={st.completed ? t.completed : t.active}
                              >
                                <Icon icon={st.completed ? 'mdi:checkbox-marked' : 'mdi:checkbox-blank-outline'} className="text-lg" />
                              </button>
                              <input
                                type="text"
                                value={st.title}
                                onChange={(e) => {
                                  const subs = [...(selectedNode.subtasks ?? [])];
                                  subs[idx] = { ...st, title: e.target.value };
                                  updateNodeProperties(selectedNodeId!, { subtasks: subs });
                                }}
                                className={`flex-1 px-3 py-1.5 bg-white/10 border border-white/20 rounded-lg text-white text-sm placeholder-white/40 focus:outline-none focus:ring-1 focus:ring-[#0FA968] ${st.completed ? 'line-through opacity-70' : ''}`}
                                placeholder={t.subtask}
                              />
                              <button
                                type="button"
                                onClick={() => {
                                  const subs = (selectedNode.subtasks ?? []).filter((_, i) => i !== idx);
                                  updateNodeProperties(selectedNodeId!, { subtasks: subs });
                                }}
                                className="p-1 text-white/60 hover:text-red-400 flex-shrink-0"
                                aria-label={t.delete}
                              >
                                <Icon icon="mdi:close" className="text-sm" />
                              </button>
                            </li>
                          ))
                        )}
                      </ul>
                    </div>
                  </>
                ) : (
                  <div>
                    <label className="block text-sm font-medium text-white/80 mb-2">{t.estimatedHours}</label>
                    <input
                      type="number"
                      min="0"
                      value={selectedNode.estimatedHours || 0}
                      onChange={(e) => updateNodeProperties(selectedNodeId!, { estimatedHours: parseInt(e.target.value) || 0 })}
                      className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                      placeholder="0"
                    />
                  </div>
                )}
                <div>
                  <label className="block text-sm font-medium text-white/80 mb-2">{t.color}</label>
                  <div className="flex items-center space-x-2">
                    <input
                      type="color"
                      value={selectedNode.color}
                      onChange={(e) => updateNodeProperties(selectedNodeId!, { color: e.target.value })}
                      className="w-16 h-10 rounded-lg border border-white/20 cursor-pointer"
                      title={t.color}
                      aria-label={t.color}
                    />
                    <input
                      type="text"
                      value={selectedNode.color}
                      onChange={(e) => updateNodeProperties(selectedNodeId!, { color: e.target.value })}
                      className="flex-1 px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                      placeholder="#0FA968"
                    />
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Path Settings */}
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
            <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.pathSettings}</h2>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-white/80 mb-2">{t.goal}</label>
                <select
                  value={goalType}
                  onChange={(e) => setGoalType(e.target.value as any)}
                  className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                  style={{ color: 'white' }}
                  title={t.goal}
                  aria-label={t.goal}
                >
                  <option value="career" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.career}</option>
                  <option value="skill" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.skill}</option>
                  <option value="certification" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.certification}</option>
                  <option value="hobby" style={{ backgroundColor: '#1a1a1a', color: 'white' }}>{t.hobby}</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-white/80 mb-2">{t.startDate}</label>
                <input
                  type="date"
                  value={startDate}
                  onChange={(e) => setStartDate(e.target.value)}
                  className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                  title={t.startDate}
                  aria-label={t.startDate}
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-white/80 mb-2">{t.endDate}</label>
                <input
                  type="date"
                  value={endDate}
                  onChange={(e) => setEndDate(e.target.value)}
                  className="w-full px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#0FA968]"
                  title={t.endDate}
                  aria-label={t.endDate}
                />
              </div>
            </div>
          </div>
        </div>
      </aside>

      {/* Main Canvas Area */}
      <main className="flex-1 relative overflow-hidden flex flex-col">
        {/* Toolbar */}
        <div className="bg-white/10 backdrop-blur-md border-b border-white/20 px-4 py-3 flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <button
              onClick={zoomIn}
              className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
              title={t.zoomIn}
            >
              <Icon icon="mdi:magnify-plus" />
            </button>
            <button
              onClick={zoomOut}
              className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
              title={t.zoomOut}
            >
              <Icon icon="mdi:magnify-minus" />
            </button>
            <button
              onClick={resetZoom}
              className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
              title={t.resetZoom}
            >
              <Icon icon="mdi:fit-to-screen" />
            </button>
            <div className="w-px h-6 bg-white/20 mx-2"></div>
            <button
              onClick={centerView}
              className="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition"
              title={t.centerView}
            >
              <Icon icon="mdi:crosshairs" />
            </button>
            <button
              onClick={toggleGrid}
              className={`p-2 rounded-lg transition ${gridVisible ? 'text-white bg-white/20' : 'text-white/70 hover:text-white hover:bg-white/10'}`}
              title={t.grid}
            >
              <Icon icon="mdi:grid" />
            </button>
          </div>
          <div className="flex items-center space-x-2 text-sm text-white/70">
            <span>{Math.round(currentZoom * 100)}%</span>
            <span className="text-white/40">•</span>
            <span>
              {nodes.length} {t.nodes}
            </span>
          </div>
        </div>

        {/* Mind Map Canvas */}
        <div
          ref={canvasRef}
          className="flex-1 relative overflow-hidden"
          style={{
            background: gridVisible
              ? 'linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px)'
              : 'rgba(0, 0, 0, 0.2)',
            backgroundSize: '50px 50px',
            transform: `scale(${currentZoom})`,
            transformOrigin: 'top left',
          }}
        >
          {/* SVG for connections */}
          <svg
            ref={svgRef}
            style={{
              position: 'absolute',
              top: 0,
              left: 0,
              width: '100%',
              height: '100%',
              pointerEvents: 'none',
              zIndex: 1,
            }}
          />

          {/* Nodes Container */}
          <div style={{ position: 'relative', width: '100%', height: '100%', zIndex: 2 }}>
            {nodes.map((node) => (
              <div
                key={node.id}
                data-node-id={node.id}
                onMouseDown={(e) => handleNodeMouseDown(e, node.id)}
                onClick={(e) => {
                  if (!isDragging) {
                    setSelectedNodeId(node.id);
                  }
                }}
                className={`mindmap-node absolute min-w-[200px] p-4 rounded-2xl transition ${
                  isDragging && selectedNodeId === node.id ? 'cursor-grabbing' : 'cursor-move'
                } ${node.type === 'root' ? 'root' : ''} ${selectedNodeId === node.id ? 'selected' : ''}`}
                style={{
                  left: `${node.position.x}%`,
                  top: `${node.position.y}%`,
                  transform: 'translate(-50%, -50%)',
                  background: node.type === 'root' 
                    ? `linear-gradient(135deg, ${node.color}30, ${node.color}50)`
                    : `rgba(255, 255, 255, 0.1)`,
                  border: `2px solid ${selectedNodeId === node.id ? node.color : 'rgba(255, 255, 255, 0.2)'}`,
                  backdropFilter: 'blur(10px)',
                }}
              >
                <div className="flex items-center justify-between mb-2">
                  <h3 className={`font-bold text-white ${node.type === 'root' ? 'text-lg' : 'text-base'}`}>{node.title}</h3>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      setSelectedNodeId(node.id);
                    }}
                    className="text-white/60 hover:text-white"
                    title={t.edit}
                  >
                    <Icon icon="mdi:pencil" className="text-sm" />
                  </button>
                </div>
                {node.description && <p className="text-white/70 text-sm mb-2">{node.description}</p>}
                <div className="flex items-center space-x-2 text-xs text-white/60">
                  {node.type === 'task' ? (
                    <>
                      <span>
                        <Icon icon="mdi:clock" className="inline mr-1" />
                        {node.estimatedMinutes ?? 30}m
                      </span>
                      {node.priority != null && (
                        <span>
                          <Icon icon="mdi:flag" className="inline mr-1" />
                          P{node.priority}
                        </span>
                      )}
                      {(node.subtasks?.length ?? 0) > 0 && (
                        <span>
                          <Icon icon="mdi:format-list-checks" className="inline mr-1" />
                          {node.subtasks!.length} {t.subtask}
                        </span>
                      )}
                    </>
                  ) : (
                    <>
                      <span>
                        <Icon icon="mdi:clock" className="inline mr-1" />
                        {node.estimatedHours || 0}h
                      </span>
                      {node.type === 'milestone' && (
                        <>
                          <span>
                            <Icon icon="mdi:clock-outline" className="inline mr-1" />
                            {formatTimeTotal(getMilestoneTotalMinutes(node.id))}
                          </span>
                          <span>
                            <Icon icon="mdi:tasks" className="inline mr-1" />
                            {nodes.filter((n) => n.type === 'task' && n.parentId === node.id).length} {t.tasks}
                          </span>
                        </>
                      )}
                    </>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      </main>

      {/* Right Sidebar: Node List & Statistics */}
      <aside
        className={`${
          isRightSidebarCollapsed ? 'w-0 -mr-80 opacity-0 pointer-events-none' : 'w-80'
        } bg-white/10 backdrop-blur-md border-l border-white/20 shadow-xl overflow-y-auto transition-all duration-300 ease-in-out flex-shrink-0`}
      >
        <div className="p-4 space-y-6">
          {/* Node List */}
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
            <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.nodeList}</h2>
            <div className="space-y-2">
              {nodes.map((node) => (
                <div
                  key={node.id}
                  onClick={() => setSelectedNodeId(node.id)}
                  className={`p-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 cursor-pointer transition ${
                    selectedNodeId === node.id ? 'bg-white/20 border-[#0FA968]' : 'hover:bg-white/20'
                  }`}
                >
                  <div className="flex items-center justify-between gap-2">
                    <div className="flex items-center space-x-2 min-w-0">
                      <div
                        className="w-3 h-3 rounded-full flex-shrink-0"
                        style={{ backgroundColor: node.color }}
                      />
                      <span className="text-white font-medium text-sm truncate">{node.title}</span>
                    </div>
                    <div className="flex items-center gap-1.5 flex-shrink-0 text-xs text-white/60">
                      {node.type === 'task' ? (
                        <span>{node.estimatedMinutes ?? 30}m</span>
                      ) : node.type === 'milestone' ? (
                        <span>{formatTimeTotal(getMilestoneTotalMinutes(node.id))}</span>
                      ) : null}
                      <Icon icon="mdi:chevron-right" className="text-white/40 text-xs" />
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Statistics */}
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
            <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.statistics}</h2>
            <div className="space-y-3">
              <div className="flex items-center justify-between text-sm text-white/80">
                <span>{t.totalMilestones}:</span>
                <span className="font-semibold">{totalMilestones}</span>
              </div>
              <div className="flex items-center justify-between text-sm text-white/80">
                <span>{t.totalTasks}:</span>
                <span className="font-semibold">{totalTasks}</span>
              </div>
              <div className="flex items-center justify-between text-sm text-white/80">
                <span>{t.estimatedTime}:</span>
                <span className="font-semibold">
                  {formatTimeTotal(totalPathMinutes)}
                </span>
              </div>
              <div className="flex items-center justify-between text-sm text-white/80">
                <span>{t.progress}:</span>
                <span className="font-semibold text-[#0FA968]">0%</span>
              </div>
            </div>
          </div>

          {/* Quick Actions */}
          <div className="bg-white/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 shadow-xl">
            <h2 className="text-lg font-bold text-white mb-4 drop-shadow-md">{t.quickActionsTitle}</h2>
            <div className="space-y-2">
              <button
                onClick={() => alert(t.aiGenerate + ' (Coming soon)')}
                className="w-full flex items-center space-x-2 px-4 py-2.5 bg-purple-500/20 backdrop-blur-sm text-white hover:bg-purple-500/30 rounded-xl transition border border-purple-500/30"
              >
                <Icon icon="mdi:robot" />
                <span>{t.aiGenerate}</span>
              </button>
              <button
                onClick={() => alert(t.duplicate + ' (Coming soon)')}
                className="w-full flex items-center space-x-2 px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 rounded-xl transition border border-white/20"
              >
                <Icon icon="mdi:content-copy" />
                <span>{t.duplicate}</span>
              </button>
              <button
                onClick={() => alert(t.preview + ' (Coming soon)')}
                className="w-full flex items-center space-x-2 px-4 py-2.5 bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 rounded-xl transition border border-white/20"
              >
                <Icon icon="mdi:eye" />
                <span>{t.preview}</span>
              </button>
            </div>
          </div>
        </div>
      </aside>
      </div>
    </div>
  );
}
