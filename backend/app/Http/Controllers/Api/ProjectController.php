<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * プロジェクト一覧を取得
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::where('user_id', auth()->id())
            ->withCount(['tasks', 'activeTasks']);

        // アーカイブ済みのフィルタリング
        if ($request->has('archived')) {
            $query->where('is_archived', $request->archived);
        }

        // 検索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * 新しいプロジェクトを作成
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => '入力データに誤りがあります',
                    'fields' => $validator->errors()
                ]
            ], 422);
        }

        $project = Project::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#0FA968',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'プロジェクトが作成されました',
            'data' => $project
        ], 201);
    }

    /**
     * 特定のプロジェクトを取得
     */
    public function show(Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このプロジェクトにアクセスする権限がありません'
                ]
            ], 403);
        }

        $project->load(['tasks.subtasks', 'activeTasks']);

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    /**
     * プロジェクトを更新
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このプロジェクトを更新する権限がありません'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_archived' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => '入力データに誤りがあります',
                    'fields' => $validator->errors()
                ]
            ], 422);
        }

        $project->update($request->only([
            'name', 'description', 'color', 'is_archived'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'プロジェクトが更新されました',
            'data' => $project
        ]);
    }

    /**
     * プロジェクトを削除
     */
    public function destroy(Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このプロジェクトを削除する権限がありません'
                ]
            ], 403);
        }

        // プロジェクトに関連するタスクがある場合は削除を拒否
        if ($project->tasks()->count() > 0) {
            return response()->json([
                'error' => [
                    'code' => 'CONFLICT',
                    'message' => 'タスクが存在するプロジェクトは削除できません。まずタスクを削除または移動してください。'
                ]
            ], 409);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'プロジェクトが削除されました'
        ]);
    }

    /**
     * プロジェクトをアーカイブ
     */
    public function archive(Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このプロジェクトをアーカイブする権限がありません'
                ]
            ], 403);
        }

        $project->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'プロジェクトがアーカイブされました',
            'data' => $project
        ]);
    }

    /**
     * プロジェクトを復元
     */
    public function restore(Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'このプロジェクトを復元する権限がありません'
                ]
            ], 403);
        }

        $project->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'message' => 'プロジェクトが復元されました',
            'data' => $project
        ]);
    }
}
