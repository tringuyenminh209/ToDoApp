<?php

namespace App\Http\Controllers;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use App\Models\ExerciseTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExerciseController extends Controller
{
    /**
     * Get all exercises for a language
     * 言語の練習問題一覧を取得
     */
    public function getExercises(Request $request, $languageId)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $query = Exercise::where('language_id', $language->id)
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
                    ->orWhere('question', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'sort_order');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $exercises = $query->get()->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'languageId' => $exercise->language_id,
                'title' => $exercise->title,
                'slug' => $exercise->slug,
                'description' => $exercise->description,
                'difficulty' => $exercise->difficulty,
                'points' => $exercise->points,
                'tags' => $exercise->tags,
                'timeLimit' => $exercise->time_limit,
                'submissionsCount' => $exercise->submissions_count,
                'successCount' => $exercise->success_count,
                'successRate' => $exercise->success_rate,
                'sortOrder' => $exercise->sort_order,
                'createdAt' => $exercise->created_at?->toISOString(),
                'updatedAt' => $exercise->updated_at?->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'language' => [
                    'id' => $language->id,
                    'name' => $language->name,
                    'displayName' => $language->display_name,
                ],
                'exercises' => $exercises
            ],
            'message' => 'Exercises retrieved successfully'
        ]);
    }

    /**
     * Get a single exercise with test cases
     * 練習問題の詳細を取得
     */
    public function getExercise($languageId, $exerciseId)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $exercise = Exercise::where('language_id', $language->id)
            ->where(function ($query) use ($exerciseId) {
                $query->where('id', $exerciseId)
                    ->orWhere('slug', $exerciseId);
            })
            ->where('is_published', true)
            ->with(['testCases' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->firstOrFail();

        // Only show sample test cases (is_sample = true)
        $sampleTestCases = $exercise->testCases->filter(function ($testCase) {
            return $testCase->is_sample && !$testCase->is_hidden;
        })->map(function ($testCase) {
            return [
                'id' => $testCase->id,
                'input' => $testCase->input,
                'expectedOutput' => $testCase->expected_output,
                'description' => $testCase->description,
                'sortOrder' => $testCase->sort_order,
            ];
        })->values();

        $exerciseData = [
            'id' => $exercise->id,
            'languageId' => $exercise->language_id,
            'title' => $exercise->title,
            'slug' => $exercise->slug,
            'description' => $exercise->description,
            'question' => $exercise->question,
            'starterCode' => $exercise->starter_code,
            'hints' => $exercise->hints,
            'difficulty' => $exercise->difficulty,
            'points' => $exercise->points,
            'tags' => $exercise->tags,
            'timeLimit' => $exercise->time_limit,
            'submissionsCount' => $exercise->submissions_count,
            'successCount' => $exercise->success_count,
            'successRate' => $exercise->success_rate,
            'testCases' => $sampleTestCases,
            'totalTestCases' => $exercise->testCases->count(),
            'createdAt' => $exercise->created_at?->toISOString(),
            'updatedAt' => $exercise->updated_at?->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $exerciseData,
            'message' => 'Exercise retrieved successfully'
        ]);
    }

    /**
     * Get exercise solution (only after successful submission)
     * 解答を取得（正解後のみ）
     */
    public function getSolution($languageId, $exerciseId)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $exercise = Exercise::where('language_id', $language->id)
            ->where(function ($query) use ($exerciseId) {
                $query->where('id', $exerciseId)
                    ->orWhere('slug', $exerciseId);
            })
            ->where('is_published', true)
            ->firstOrFail();

        // TODO: Add authentication and check if user has solved this exercise

        return response()->json([
            'success' => true,
            'data' => [
                'solution' => $exercise->solution,
            ],
            'message' => 'Solution retrieved successfully'
        ]);
    }

    /**
     * Submit and validate solution
     * 解答を提出して検証
     */
    public function submitSolution(Request $request, $languageId, $exerciseId)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $exercise = Exercise::where('language_id', $language->id)
            ->where(function ($query) use ($exerciseId) {
                $query->where('id', $exerciseId)
                    ->orWhere('slug', $exerciseId);
            })
            ->where('is_published', true)
            ->with('testCases')
            ->firstOrFail();

        $code = $request->input('code');

        // Run code against all test cases
        $results = $this->runTestCases($exercise, $code, $language->name);

        // Check if all test cases passed
        $allPassed = collect($results)->every(fn($result) => $result['passed']);
        $passedCount = collect($results)->filter(fn($result) => $result['passed'])->count();
        $totalCount = count($results);

        // Update exercise statistics
        $exercise->increment('submissions_count');
        if ($allPassed) {
            $exercise->increment('success_count');
            $exercise->update([
                'success_rate' => ($exercise->success_count / $exercise->submissions_count) * 100
            ]);
        }

        // Prepare response with only sample test results visible
        $visibleResults = collect($results)->map(function ($result) {
            if (!$result['is_sample']) {
                // Hide actual output for hidden test cases, only show pass/fail
                return [
                    'description' => $result['description'],
                    'passed' => $result['passed'],
                    'is_sample' => $result['is_sample'],
                ];
            }
            return $result;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'allPassed' => $allPassed,
                'passedCount' => $passedCount,
                'totalCount' => $totalCount,
                'results' => $visibleResults,
                'points' => $allPassed ? $exercise->points : 0,
            ],
            'message' => $allPassed ? 'All test cases passed!' : 'Some test cases failed'
        ]);
    }

    /**
     * Run code against test cases
     * テストケースに対してコードを実行
     */
    private function runTestCases(Exercise $exercise, string $code, string $languageName): array
    {
        $results = [];

        foreach ($exercise->testCases as $testCase) {
            try {
                $output = $this->executeCode($code, $testCase->input, $languageName);
                $passed = trim($output) === trim($testCase->expected_output);

                $results[] = [
                    'description' => $testCase->description,
                    'input' => $testCase->input,
                    'expectedOutput' => $testCase->expected_output,
                    'actualOutput' => $output,
                    'passed' => $passed,
                    'is_sample' => $testCase->is_sample,
                    'error' => null,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'description' => $testCase->description,
                    'input' => $testCase->input,
                    'expectedOutput' => $testCase->expected_output,
                    'actualOutput' => null,
                    'passed' => false,
                    'is_sample' => $testCase->is_sample,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Execute code safely
     * コードを安全に実行
     */
    private function executeCode(string $code, string $input, string $languageName): string
    {
        // Create temporary workspace for code execution
        $filename = 'exercise_' . Str::random(10);
        $workDir = "/tmp/{$filename}";
        $createdPaths = [];

        try {
            if (!is_dir($workDir) && !mkdir($workDir, 0700, true) && !is_dir($workDir)) {
                throw new \Exception('Failed to create execution directory');
            }
            $createdPaths[] = $workDir;

            switch (strtolower($languageName)) {
                case 'bash':
                    $filepath = "{$workDir}/main.sh";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('bash ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception('Execution error: ' . $result->errorOutput());
                    }

                    return $result->output();

                case 'python':
                    $filepath = "{$workDir}/main.py";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('python3 ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception('Execution error: ' . $result->errorOutput());
                    }

                    return $result->output();

                case 'php':
                    $filepath = "{$workDir}/main.php";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('php ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception('Execution error: ' . $result->errorOutput());
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
                        throw new \Exception('Execution error: ' . $result->errorOutput());
                    }

                    return $result->output();

                case 'go':
                    $filepath = "{$workDir}/main.go";
                    file_put_contents($filepath, $code);
                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('go run ' . escapeshellarg($filepath));

                    if ($result->failed()) {
                        throw new \Exception('Execution error: ' . $result->errorOutput());
                    }

                    return $result->output();

                case 'java':
                    $filepath = "{$workDir}/Main.java";
                    file_put_contents($filepath, $code);
                    $compile = Process::timeout(5)
                        ->run('javac ' . escapeshellarg($filepath));

                    if ($compile->failed()) {
                        throw new \Exception('Compilation error: ' . $compile->errorOutput());
                    }

                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('java -cp ' . escapeshellarg($workDir) . ' Main');

                    if ($result->failed()) {
                        throw new \Exception('Execution error: ' . $result->errorOutput());
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
                        throw new \Exception('Compilation error: ' . $compile->errorOutput());
                    }

                    $result = Process::timeout(5)
                        ->input($input)
                        ->run(escapeshellarg($binaryPath));

                    if ($result->failed()) {
                        throw new \Exception('Execution error: ' . $result->errorOutput());
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
                        throw new \Exception('Compilation error: ' . $compile->errorOutput());
                    }

                    $result = Process::timeout(5)
                        ->input($input)
                        ->run('java -jar ' . escapeshellarg($jarPath));

                    if ($result->failed()) {
                        throw new \Exception('Execution error: ' . $result->errorOutput());
                    }

                    return $result->output();

                default:
                    throw new \Exception("Language {$languageName} is not supported for execution");
            }
        } finally {
            // Always clean up temp files
            if (is_dir($workDir)) {
                $files = array_diff(scandir($workDir), ['.', '..']);
                foreach ($files as $file) {
                    @unlink($workDir . '/' . $file);
                }
                @rmdir($workDir);
            }
        }
    }

    /**
     * Get exercise statistics
     * 練習問題の統計を取得
     */
    public function getStatistics($languageId, $exerciseId)
    {
        $language = CheatCodeLanguage::where('is_active', true)
            ->where(function ($query) use ($languageId) {
                $query->where('id', $languageId)
                    ->orWhere('slug', $languageId);
            })
            ->firstOrFail();

        $exercise = Exercise::where('language_id', $language->id)
            ->where(function ($query) use ($exerciseId) {
                $query->where('id', $exerciseId)
                    ->orWhere('slug', $exerciseId);
            })
            ->where('is_published', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'submissionsCount' => $exercise->submissions_count,
                'successCount' => $exercise->success_count,
                'successRate' => $exercise->success_rate,
                'difficulty' => $exercise->difficulty,
                'points' => $exercise->points,
            ],
            'message' => 'Statistics retrieved successfully'
        ]);
    }
}
