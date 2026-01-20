<?php

namespace App\Http\Controllers;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

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

    /**
     * Run a code example
     * コード例を実行
     */
    public function runExample(Request $request, $languageId, $sectionId, $exampleId)
    {
        $request->validate([
            'input' => 'nullable|string',
        ]);

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

        try {
            $output = $this->executeCode($example->code ?? '', $request->input('input', ''), $language->name);

            return response()->json([
                'success' => true,
                'data' => [
                    'output' => $output,
                ],
                'message' => 'Execution completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Execution error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run a custom code example
     * カスタムコードを実行
     */
    public function runCustomExample(Request $request, $languageId, $sectionId, $exampleId)
    {
        $request->validate([
            'code' => 'required|string|max:20000',
            'input' => 'nullable|string',
        ]);

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

        CodeExample::where('section_id', $section->id)
            ->where(function ($query) use ($exampleId) {
                $query->where('id', $exampleId)
                    ->orWhere('slug', $exampleId);
            })
            ->where('is_published', true)
            ->firstOrFail();

        try {
            $output = $this->executeCode($request->input('code'), $request->input('input', ''), $language->name);

            return response()->json([
                'success' => true,
                'data' => [
                    'output' => $output,
                ],
                'message' => 'Execution completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Execution error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute code safely
     * コードを安全に実行
     */
    private function executeCode(string $code, string $input, string $languageName): string
    {
        $filename = 'example_' . Str::random(10);
        $workDir = "/tmp/{$filename}";

        try {
            if (!is_dir($workDir) && !mkdir($workDir, 0700, true) && !is_dir($workDir)) {
                throw new \Exception('Failed to create execution directory');
            }

            switch (strtolower($languageName)) {
                case 'bash':
                    $filepath = "{$workDir}/main.sh";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('bash ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception($result->errorOutput());
                    }

                    return $result->output();

                case 'python':
                    $filepath = "{$workDir}/main.py";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('python3 ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception($result->errorOutput());
                    }

                    return $result->output();

                case 'php':
                    $filepath = "{$workDir}/main.php";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('php ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception($result->errorOutput());
                    }

                    return $result->output();

                case 'javascript':
                case 'js':
                    $filepath = "{$workDir}/main.js";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('node ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception($result->errorOutput());
                    }

                    return $result->output();

                case 'go':
                    $filepath = "{$workDir}/main.go";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('go run ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception($result->errorOutput());
                    }

                    return $result->output();

                case 'java':
                    $filepath = "{$workDir}/Main.java";
                    file_put_contents($filepath, $code);
                    $compile = Process::timeout(5)
                        ->run('javac ' . escapeshellarg($filepath));

                    if ($compile->failed()) {
                        throw new \Exception($compile->errorOutput());
                    }

                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('java -cp ' . escapeshellarg($workDir) . ' Main');

                    if ($result->failed()) {
                        throw new \Exception($result->errorOutput());
                    }

                    return $result->output();

                case 'cpp':
                case 'c++':
                    $sourcePath = "{$workDir}/main.cpp";
                    $binaryPath = "{$workDir}/main";
                    file_put_contents($sourcePath, $code);
                    $compile = Process::timeout(5)->run(
                        'g++ ' . escapeshellarg($sourcePath) . ' -std=c++17 -O2 -o ' . escapeshellarg($binaryPath)
                    );

                    if ($compile->failed()) {
                        throw new \Exception($compile->errorOutput());
                    }

                    $result = Process::timeout(5)
                        ->input($input)
                        ->run(escapeshellarg($binaryPath));

                    if ($result->failed()) {
                        throw new \Exception($result->errorOutput());
                    }

                    return $result->output();

                case 'kotlin':
                    $filepath = "{$workDir}/Main.kt";
                    $jarPath = "{$workDir}/main.jar";
                    file_put_contents($filepath, $code);
                    $compile = Process::timeout(5)->run(
                        'kotlinc ' . escapeshellarg($filepath) . ' -include-runtime -d ' . escapeshellarg($jarPath)
                    );

                    if ($compile->failed()) {
                        throw new \Exception($compile->errorOutput());
                    }

                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('java -jar ' . escapeshellarg($jarPath));

                    if ($result->failed()) {
                        throw new \Exception($result->errorOutput());
                    }

                    return $result->output();

                default:
                    throw new \Exception("Language {$languageName} is not supported for execution");
            }
        } finally {
            if (is_dir($workDir)) {
                $files = array_diff(scandir($workDir), ['.', '..']);
                foreach ($files as $file) {
                    @unlink($workDir . '/' . $file);
                }
                @rmdir($workDir);
            }
        }
    }
}

