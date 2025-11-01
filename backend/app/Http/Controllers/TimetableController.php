<?php

namespace App\Http\Controllers;

use App\Models\TimetableClass;
use App\Models\TimetableStudy;
use App\Models\TimetableClassWeeklyContent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimetableController extends Controller
{
    /**
     * Get complete timetable (classes + studies)
     * GET /api/timetable?year=2025&week=44
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            // Get week parameters
            $year = $request->get('year', now()->year);
            $weekNumber = $request->get('week', now()->weekOfYear);

            $classes = TimetableClass::where('user_id', $user->id)
                ->with(['learningPath', 'weeklyContents' => function($query) use ($year, $weekNumber) {
                    $query->where('year', $year)
                          ->where('week_number', $weekNumber);
                }])
                ->orderBy('day')
                ->orderBy('period')
                ->get();

            // Add weekly content to each class
            $classes = $classes->map(function($class) use ($year, $weekNumber) {
                $weeklyContent = $class->weeklyContents->first();
                $class->weekly_content = $weeklyContent;
                unset($class->weeklyContents); // Remove the collection
                return $class;
            });

            $studies = TimetableStudy::where('user_id', $user->id)
                ->where('status', '!=', 'completed')
                ->with(['timetableClass', 'task'])
                ->orderBy('due_date')
                ->get();

            // Get current and next class
            $now = now();
            $currentClass = $classes->first(fn($class) => $class->isNow());
            $nextClass = $classes->first(fn($class) => $class->isNext());

            return response()->json([
                'success' => true,
                'data' => [
                    'classes' => $classes,
                    'studies' => $studies,
                    'current_class' => $currentClass,
                    'next_class' => $nextClass,
                    'current_time' => $now->format('H:i'),
                    'current_day' => strtolower($now->format('l')),
                    'year' => $year,
                    'week_number' => $weekNumber,
                ],
                'message' => '時間割を取得しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '時間割の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all classes
     * GET /api/timetable/classes
     */
    public function getClasses(Request $request): JsonResponse
    {
        $user = $request->user();

        $classes = TimetableClass::where('user_id', $user->id)
            ->with(['learningPath'])
            ->orderBy('day')
            ->orderBy('period')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $classes,
            'message' => '授業一覧を取得しました'
        ]);
    }

    /**
     * Create a new class
     * POST /api/timetable/classes
     */
    public function createClass(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'room' => 'nullable|string|max:100',
            'instructor' => 'nullable|string|max:255',
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'period' => 'required|integer|min:1|max:10',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'learning_path_id' => 'nullable|exists:learning_paths,id',
        ]);

        try {
            $class = TimetableClass::create([
                'user_id' => $request->user()->id,
                ...$request->only([
                    'name', 'description', 'room', 'instructor',
                    'day', 'period', 'start_time', 'end_time',
                    'color', 'icon', 'notes', 'learning_path_id'
                ])
            ]);

            $class->load('learningPath');

            return response()->json([
                'success' => true,
                'data' => $class,
                'message' => '授業を追加しました'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '授業の追加に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a class
     * PUT /api/timetable/classes/{id}
     */
    public function updateClass(Request $request, string $id): JsonResponse
    {
        $class = TimetableClass::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'day' => 'sometimes|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'period' => 'sometimes|integer|min:1|max:10',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
        ]);

        try {
            $class->update($request->only([
                'name', 'description', 'room', 'instructor',
                'day', 'period', 'start_time', 'end_time',
                'color', 'icon', 'notes', 'learning_path_id'
            ]));

            $class->load('learningPath');

            return response()->json([
                'success' => true,
                'data' => $class,
                'message' => '授業を更新しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '授業の更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a class
     * DELETE /api/timetable/classes/{id}
     */
    public function deleteClass(Request $request, string $id): JsonResponse
    {
        $class = TimetableClass::where('user_id', $request->user()->id)->findOrFail($id);

        try {
            $class->delete();

            return response()->json([
                'success' => true,
                'message' => '授業を削除しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '授業の削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all studies (homework/review)
     * GET /api/timetable/studies
     */
    public function getStudies(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = TimetableStudy::where('user_id', $user->id)
            ->with(['timetableClass', 'task']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('overdue')) {
            $query->overdue();
        }

        if ($request->has('due_soon')) {
            $query->dueSoon($request->get('due_soon', 3));
        }

        $studies = $query->orderBy('due_date')->get();

        return response()->json([
            'success' => true,
            'data' => $studies,
            'message' => '宿題・復習一覧を取得しました'
        ]);
    }

    /**
     * Create a new study
     * POST /api/timetable/studies
     */
    public function createStudy(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:homework,review,exam,project',
            'subject' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'required|integer|min:1|max:5',
            'timetable_class_id' => 'nullable|exists:timetable_classes,id',
            'task_id' => 'nullable|exists:tasks,id',
        ]);

        try {
            $study = TimetableStudy::create([
                'user_id' => $request->user()->id,
                ...$request->only([
                    'title', 'description', 'type', 'subject',
                    'due_date', 'priority', 'timetable_class_id', 'task_id'
                ])
            ]);

            $study->load(['timetableClass', 'task']);

            return response()->json([
                'success' => true,
                'data' => $study,
                'message' => '宿題・復習を追加しました'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '宿題・復習の追加に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle study completion
     * PUT /api/timetable/studies/{id}/toggle
     */
    public function toggleStudy(Request $request, string $id): JsonResponse
    {
        $study = TimetableStudy::where('user_id', $request->user()->id)->findOrFail($id);

        try {
            $study->toggleStatus();
            $study->load(['timetableClass', 'task']);

            return response()->json([
                'success' => true,
                'data' => $study,
                'message' => 'ステータスを更新しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ステータスの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a study
     * DELETE /api/timetable/studies/{id}
     */
    public function deleteStudy(Request $request, string $id): JsonResponse
    {
        $study = TimetableStudy::where('user_id', $request->user()->id)->findOrFail($id);

        try {
            $study->delete();

            return response()->json([
                'success' => true,
                'message' => '宿題・復習を削除しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '宿題・復習の削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get weekly content for a class
     * GET /api/timetable/classes/{id}/weekly-content?year=2025&week=44
     */
    public function getWeeklyContent(Request $request, string $id): JsonResponse
    {
        $class = TimetableClass::where('user_id', $request->user()->id)->findOrFail($id);

        $year = $request->get('year', now()->year);
        $weekNumber = $request->get('week', now()->weekOfYear);

        $weeklyContent = $class->getWeeklyContent($year, $weekNumber);

        return response()->json([
            'success' => true,
            'data' => $weeklyContent,
            'message' => '週別内容を取得しました'
        ]);
    }

    /**
     * Update or create weekly content for a class
     * POST /api/timetable/classes/{id}/weekly-content
     */
    public function updateWeeklyContent(Request $request, string $id): JsonResponse
    {
        $class = TimetableClass::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'year' => 'required|integer',
            'week_number' => 'required|integer|min:1|max:53',
            'week_start_date' => 'required|date',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'homework' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed,cancelled',
        ]);

        try {
            $weeklyContent = TimetableClassWeeklyContent::updateOrCreate(
                [
                    'timetable_class_id' => $class->id,
                    'year' => $request->year,
                    'week_number' => $request->week_number,
                ],
                [
                    'week_start_date' => $request->week_start_date,
                    'title' => $request->title,
                    'content' => $request->content,
                    'homework' => $request->homework,
                    'notes' => $request->notes,
                    'status' => $request->get('status', 'scheduled'),
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $weeklyContent,
                'message' => '週別内容を更新しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '週別内容の更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete weekly content
     * DELETE /api/timetable/weekly-content/{id}
     */
    public function deleteWeeklyContent(Request $request, string $id): JsonResponse
    {
        $weeklyContent = TimetableClassWeeklyContent::whereHas('timetableClass', function($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        try {
            $weeklyContent->delete();

            return response()->json([
                'success' => true,
                'message' => '週別内容を削除しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '週別内容の削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

