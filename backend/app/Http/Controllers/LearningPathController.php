<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class LearningPathController extends Controller
{
    /**
     * Get all learning paths for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            $query = LearningPath::where('user_id', $user->id)
                ->with(['milestones', 'knowledgeItems', 'studySchedules']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by goal type
            if ($request->has('goal_type')) {
                $query->where('goal_type', $request->goal_type);
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $paths = $query->get();

            return response()->json([
                'success' => true,
                'data' => $paths
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching learning paths: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning paths の取得に失敗しました'
            ], 500);
        }
    }

    /**
     * Get a specific learning path
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            $path = LearningPath::where('user_id', $user->id)
                ->with([
                    'milestones.tasks.subtasks',
                    'milestones.tasks.knowledgeItems' => function($query) {
                        $query->orderBy('item_type')->orderBy('created_at');
                    },
                    'knowledgeItems',
                    'studySchedules'
                ])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $path
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の取得に失敗しました'
            ], 404);
        }
    }

    /**
     * Create a new learning path
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'goal_type' => ['required', Rule::in(['career', 'skill', 'certification', 'hobby'])],
                'target_start_date' => 'nullable|date',
                'target_end_date' => 'nullable|date|after_or_equal:target_start_date',
                'estimated_hours_total' => 'nullable|integer|min:0',
                'tags' => 'nullable|array',
                'color' => 'nullable|string|max:20',
                'icon' => 'nullable|string|max:50',
            ]);

            $user = Auth::user();

            $path = LearningPath::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'goal_type' => $validated['goal_type'],
                'target_start_date' => $validated['target_start_date'] ?? null,
                'target_end_date' => $validated['target_end_date'] ?? null,
                'estimated_hours_total' => $validated['estimated_hours_total'] ?? null,
                'tags' => $validated['tags'] ?? null,
                'color' => $validated['color'] ?? null,
                'icon' => $validated['icon'] ?? null,
                'status' => 'active',
                'progress_percentage' => 0,
                'is_ai_generated' => false,
            ]);

            // Auto-create Knowledge Category for this roadmap
            // Always create a new category for each roadmap
            try {
                \App\Models\KnowledgeCategory::create([
                    'user_id' => $user->id,
                    'name' => $validated['title'],
                    'description' => 'ロードマップ: ' . $validated['title'],
                    'parent_id' => null,
                    'sort_order' => 0,
                    'color' => $validated['color'] ?? null,
                    'icon' => $validated['icon'] ?? null,
                ]);
            } catch (\Exception $e) {
                // Log but don't fail the roadmap creation if category creation fails
                Log::warning('Failed to create knowledge category for roadmap: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => $path,
                'message' => 'Learning path を作成しました'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の作成に失敗しました'
            ], 500);
        }
    }

    /**
     * Update a learning path
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $path = LearningPath::where('user_id', $user->id)->findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'goal_type' => ['sometimes', Rule::in(['career', 'skill', 'certification', 'hobby'])],
                'target_start_date' => 'nullable|date',
                'target_end_date' => 'nullable|date',
                'status' => ['sometimes', Rule::in(['active', 'paused', 'completed', 'abandoned'])],
                'estimated_hours_total' => 'nullable|integer|min:0',
                'tags' => 'nullable|array',
                'color' => 'nullable|string|max:20',
                'icon' => 'nullable|string|max:50',
            ]);

            $path->update($validated);

            return response()->json([
                'success' => true,
                'data' => $path,
                'message' => 'Learning path を更新しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の更新に失敗しました'
            ], 500);
        }
    }

    /**
     * Delete a learning path
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            $path = LearningPath::where('user_id', $user->id)->findOrFail($id);
            $path->delete();

            return response()->json([
                'success' => true,
                'message' => 'Learning path を削除しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の削除に失敗しました'
            ], 500);
        }
    }

    /**
     * Mark a learning path as completed
     */
    public function complete($id)
    {
        try {
            $user = Auth::user();

            $path = LearningPath::where('user_id', $user->id)->findOrFail($id);

            $path->update([
                'status' => 'completed',
                'progress_percentage' => 100
            ]);

            return response()->json([
                'success' => true,
                'data' => $path,
                'message' => 'Learning path を完了しました'
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing learning path: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Learning path の完了に失敗しました'
            ], 500);
        }
    }

    /**
     * Get learning path statistics
     */
    public function stats()
    {
        try {
            $user = Auth::user();

            $stats = [
                'total' => LearningPath::where('user_id', $user->id)->count(),
                'active' => LearningPath::where('user_id', $user->id)->where('status', 'active')->count(),
                'completed' => LearningPath::where('user_id', $user->id)->where('status', 'completed')->count(),
                'paused' => LearningPath::where('user_id', $user->id)->where('status', 'paused')->count(),
                'average_progress' => LearningPath::where('user_id', $user->id)->avg('progress_percentage') ?? 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching learning path stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '統計の取得に失敗しました'
            ], 500);
        }
    }
}

