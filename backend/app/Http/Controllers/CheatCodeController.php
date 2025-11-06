<?php

namespace App\Http\Controllers;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Http\Request;

class CheatCodeController extends Controller
{
    /**
     * Get all cheat code languages
     * すべてのチートコード言語を取得
     */
    public function getLanguages(Request $request)
    {
        $query = CheatCodeLanguage::where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'sort_order');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $languages = $query->get()->map(function ($language) {
            return [
                'id' => $language->id,
                'name' => $language->name,
                'displayName' => $language->display_name,
                'icon' => $language->icon,
                'color' => $language->color,
                'description' => $language->description,
                'popularity' => $language->popularity,
                'category' => $language->category,
                'sectionsCount' => $language->sections_count,
                'examplesCount' => $language->examples_count,
                'exercisesCount' => $language->exercises_count,
                'createdAt' => $language->created_at?->toISOString(),
                'updatedAt' => $language->updated_at?->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $languages,
            'message' => 'Languages retrieved successfully'
        ]);
    }

    /**
     * Get a single language by ID or slug
     * 言語IDまたはslugで言語を取得
     */
    public function getLanguage($identifier)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                    ->orWhere('slug', $identifier);
            })
            ->firstOrFail();

        $languageData = [
            'id' => $language->id,
            'name' => $language->name,
            'displayName' => $language->display_name,
            'icon' => $language->icon,
            'color' => $language->color,
            'description' => $language->description,
            'popularity' => $language->popularity,
            'category' => $language->category,
            'sectionsCount' => $language->sections_count,
            'examplesCount' => $language->examples_count,
            'exercisesCount' => $language->exercises_count,
            'createdAt' => $language->created_at?->toISOString(),
            'updatedAt' => $language->updated_at?->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $languageData,
            'message' => 'Language retrieved successfully'
        ]);
    }

    /**
     * Get sections for a language
     * 言語のセクションを取得
     */
    public function getSections(Request $request, $languageId)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $query = CheatCodeSection::where('language_id', $language->id)
            ->where('is_published', true)
            ->with(['examples' => function ($query) {
                $query->where('is_published', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order');

        $sections = $query->get()->map(function ($section) {
            return [
                'id' => $section->id,
                'languageId' => $section->language_id,
                'title' => $section->title,
                'description' => $section->description,
                'sortOrder' => $section->sort_order,
                'examples' => $section->examples->map(function ($example) {
                    return [
                        'id' => $example->id,
                        'sectionId' => $example->section_id,
                        'title' => $example->title,
                        'code' => $example->code,
                        'description' => $example->description,
                        'output' => $example->output,
                        'tags' => $example->tags,
                        'difficulty' => $example->difficulty,
                        'sortOrder' => $example->sort_order,
                        'createdAt' => $example->created_at?->toISOString(),
                        'updatedAt' => $example->updated_at?->toISOString(),
                    ];
                }),
                'createdAt' => $section->created_at?->toISOString(),
                'updatedAt' => $section->updated_at?->toISOString(),
            ];
        });

        $languageData = [
            'id' => $language->id,
            'name' => $language->name,
            'displayName' => $language->display_name,
            'icon' => $language->icon,
            'color' => $language->color,
            'description' => $language->description,
            'popularity' => $language->popularity,
            'category' => $language->category,
            'sectionsCount' => $language->sections_count,
            'examplesCount' => $language->examples_count,
            'exercisesCount' => $language->exercises_count,
            'createdAt' => $language->created_at?->toISOString(),
            'updatedAt' => $language->updated_at?->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'language' => $languageData,
                'sections' => $sections
            ],
            'message' => 'Sections retrieved successfully'
        ]);
    }

    /**
     * Get a single section with examples
     * セクションと例を取得
     */
    public function getSection($languageId, $sectionId)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $section = CheatCodeSection::where('language_id', $language->id)
            ->where('is_published', true)
            ->where(function ($query) use ($sectionId) {
                $query->where('id', $sectionId)
                    ->orWhere('slug', $sectionId);
            })
            ->with(['examples' => function ($query) {
                $query->where('is_published', true)
                    ->orderBy('sort_order');
            }])
            ->firstOrFail();

        $sectionData = [
            'id' => $section->id,
            'languageId' => $section->language_id,
            'title' => $section->title,
            'description' => $section->description,
            'sortOrder' => $section->sort_order,
            'examples' => $section->examples->map(function ($example) {
                return [
                    'id' => $example->id,
                    'sectionId' => $example->section_id,
                    'title' => $example->title,
                    'code' => $example->code,
                    'description' => $example->description,
                    'output' => $example->output,
                    'tags' => $example->tags,
                    'difficulty' => $example->difficulty,
                    'sortOrder' => $example->sort_order,
                    'createdAt' => $example->created_at?->toISOString(),
                    'updatedAt' => $example->updated_at?->toISOString(),
                ];
            }),
            'createdAt' => $section->created_at?->toISOString(),
            'updatedAt' => $section->updated_at?->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $sectionData,
            'message' => 'Section retrieved successfully'
        ]);
    }

    /**
     * Get code examples for a section
     * セクションのコード例を取得
     */
    public function getExamples(Request $request, $languageId, $sectionId)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $section = CheatCodeSection::where('language_id', $language->id)
            ->where(function ($query) use ($sectionId) {
                $query->where('id', $sectionId)
                    ->orWhere('slug', $sectionId);
            })
            ->firstOrFail();

        $query = CodeExample::where('section_id', $section->id)
            ->where('is_published', true);

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $query->orderBy('sort_order');

        $examples = $query->get()->map(function ($example) {
            return [
                'id' => $example->id,
                'sectionId' => $example->section_id,
                'title' => $example->title,
                'code' => $example->code,
                'description' => $example->description,
                'output' => $example->output,
                'tags' => $example->tags,
                'difficulty' => $example->difficulty,
                'sortOrder' => $example->sort_order,
                'createdAt' => $example->created_at?->toISOString(),
                'updatedAt' => $example->updated_at?->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $examples,
            'message' => 'Examples retrieved successfully'
        ]);
    }

    /**
     * Get a single code example
     * コード例を取得
     */
    public function getExample($languageId, $sectionId, $exampleId)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $section = CheatCodeSection::where('language_id', $language->id)
            ->where(function ($query) use ($sectionId) {
                $query->where('id', $sectionId)
                    ->orWhere('slug', $sectionId);
            })
            ->firstOrFail();

        $example = CodeExample::where('section_id', $section->id)
            ->where(function ($query) use ($exampleId) {
                $query->where('id', $exampleId)
                    ->orWhere('slug', $exampleId);
            })
            ->where('is_published', true)
            ->firstOrFail();

        // Increment view count
        $example->increment('views_count');

        $exampleData = [
            'id' => $example->id,
            'sectionId' => $example->section_id,
            'title' => $example->title,
            'code' => $example->code,
            'description' => $example->description,
            'output' => $example->output,
            'tags' => $example->tags,
            'difficulty' => $example->difficulty,
            'sortOrder' => $example->sort_order,
            'createdAt' => $example->created_at?->toISOString(),
            'updatedAt' => $example->updated_at?->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $exampleData,
            'message' => 'Example retrieved successfully'
        ]);
    }

    /**
     * Get categories of languages
     * 言語のカテゴリを取得
     */
    public function getCategories()
    {
        $categories = CheatCodeLanguage::where('is_active', true)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }
}

