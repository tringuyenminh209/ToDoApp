<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIService
{
    private $apiKey;
    private $baseUrl;
    private $model;
    private $fallbackModel;
    private $enableFallback;
    private $maxTokens;
    private $temperature;
    private $timeout;

    public function __construct()
    {
        // Try to get API key from config first, then from env, then from .env file directly
        $this->apiKey = config('services.openai.api_key') ?: env('OPENAI_API_KEY') ?: $this->readEnvFile('OPENAI_API_KEY');
        $this->baseUrl = config('services.openai.base_url') ?: env('OPENAI_BASE_URL') ?: $this->readEnvFile('OPENAI_BASE_URL', 'https://api.openai.com/v1');
        $this->model = config('services.openai.model') ?: env('OPENAI_MODEL') ?: $this->readEnvFile('OPENAI_MODEL', 'gpt-5');
        $this->fallbackModel = config('services.openai.fallback_model') ?: env('OPENAI_FALLBACK_MODEL') ?: $this->readEnvFile('OPENAI_FALLBACK_MODEL', 'gpt-4o-mini');
        $this->enableFallback = config('services.openai.enable_fallback') !== null ? config('services.openai.enable_fallback') : (env('OPENAI_ENABLE_FALLBACK') !== null ? env('OPENAI_ENABLE_FALLBACK') : ($this->readEnvFile('OPENAI_ENABLE_FALLBACK') ?: true));
        $this->maxTokens = config('services.openai.max_tokens') ?: env('OPENAI_MAX_TOKENS') ?: (int)($this->readEnvFile('OPENAI_MAX_TOKENS') ?: 1000);
        $this->temperature = config('services.openai.temperature') ?: env('OPENAI_TEMPERATURE') ?: (float)($this->readEnvFile('OPENAI_TEMPERATURE') ?: 0.7);
        $this->timeout = config('services.openai.timeout') ?: env('OPENAI_TIMEOUT') ?: (int)($this->readEnvFile('OPENAI_TIMEOUT') ?: 30);
    }

    /**
     * Read value from .env file directly
     */
    private function readEnvFile(string $key, ?string $default = null): ?string
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return $default;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE format
            if (strpos($line, '=') !== false) {
                [$envKey, $value] = explode('=', $line, 2);
                $envKey = trim($envKey);
                $value = trim($value);

                // Remove quotes if present
                $value = trim($value, '"\'');

                if ($envKey === $key) {
                    return $value ?: $default;
                }
            }
        }

        return $default;
    }

    /**
     * Break down task into subtasks
     */
    public function breakdownTask(string $taskTitle, string $taskDescription, string $complexity = 'medium'): array
    {
        $prompt = $this->buildBreakdownPrompt($taskTitle, $taskDescription, $complexity);

        return $this->callOpenAI($prompt, [
            'max_tokens' => 1500,
            'temperature' => 0.3, // Lower temperature for more consistent breakdowns
        ]);
    }

    /**
     * Generate daily suggestions based on user activity
     */
    public function generateDailySuggestions(array $recentTasks, array $completedTasks, array $userPreferences = []): array
    {
        $prompt = $this->buildSuggestionsPrompt($recentTasks, $completedTasks, $userPreferences);

        return $this->callOpenAI($prompt, [
            'max_tokens' => 1200,
            'temperature' => 0.8, // Higher temperature for creative suggestions
        ]);
    }

    /**
     * Generate daily summary
     */
    public function generateDailySummary(array $tasks, array $sessions, string $date, array $metrics = []): array
    {
        $prompt = $this->buildSummaryPrompt($tasks, $sessions, $date, $metrics);

        return $this->callOpenAI($prompt, [
            'max_tokens' => 1000,
            'temperature' => 0.6,
        ]);
    }

    /**
     * Generate productivity insights
     */
    public function generateProductivityInsights(array $weeklyData, array $trends = []): array
    {
        $prompt = $this->buildInsightsPrompt($weeklyData, $trends);

        return $this->callOpenAI($prompt, [
            'max_tokens' => 800,
            'temperature' => 0.5,
        ]);
    }

    /**
     * Generate learning recommendations
     */
    public function generateLearningRecommendations(array $completedTasks, array $learningPaths = []): array
    {
        $prompt = $this->buildLearningPrompt($completedTasks, $learningPaths);

        return $this->callOpenAI($prompt, [
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ]);
    }

    /**
     * Analyze focus session patterns
     */
    public function analyzeFocusPatterns(array $sessions, array $productivityData = []): array
    {
        $prompt = $this->buildFocusAnalysisPrompt($sessions, $productivityData);

        return $this->callOpenAI($prompt, [
            'max_tokens' => 800,
            'temperature' => 0.4,
        ]);
    }

    /**
     * Generate motivational messages
     */
    public function generateMotivationalMessage(string $mood, array $achievements = [], array $goals = []): string
    {
        $prompt = $this->buildMotivationalPrompt($mood, $achievements, $goals);

        $response = $this->callOpenAI($prompt, [
            'max_tokens' => 200,
            'temperature' => 0.9,
        ]);

        return $response['message'] ?? 'Keep up the great work!';
    }

    /**
     * Call OpenAI API with retry logic and fallback model support
     */
    private function callOpenAI(string $prompt, array $options = []): array
    {
        if (!$this->apiKey) {
            return $this->getFallbackResponse($prompt);
        }

        $maxRetries = 3;
        $retryDelay = 1; // seconds
        $models = [$this->model];

        // Add fallback model if enabled
        if ($this->enableFallback && $this->fallbackModel !== $this->model) {
            $models[] = $this->fallbackModel;
        }

        foreach ($models as $model) {
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    // Determine which parameter to use based on model
                    // Newer models (gpt-5, o1, etc.) use max_completion_tokens instead of max_tokens
                    $useMaxCompletionTokens = in_array($model, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);
                    $maxTokensValue = $options['max_tokens'] ?? $this->maxTokens;

                    $requestBody = [
                        'model' => $model,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You are a helpful productivity assistant. Always respond in Japanese and return valid JSON.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ],
                        'temperature' => $options['temperature'] ?? $this->temperature,
                    ];

                    // Use appropriate parameter based on model
                    if ($useMaxCompletionTokens) {
                        $requestBody['max_completion_tokens'] = $maxTokensValue;
                    } else {
                        $requestBody['max_tokens'] = $maxTokensValue;
                    }

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])->timeout($this->timeout)->post($this->baseUrl . '/chat/completions', $requestBody);

                    if ($response->successful()) {
                        $data = $response->json();
                        $content = $data['choices'][0]['message']['content'] ?? '';

                        // Parse JSON response
                        $parsedContent = json_decode($content, true);

                        if (json_last_error() === JSON_ERROR_NONE) {
                            Log::info('AI Service: Success with model', ['model' => $model]);
                            return $parsedContent;
                        }

                        // If JSON parsing fails, try to extract JSON from response
                        $jsonMatch = [];
                        if (preg_match('/\{.*\}/s', $content, $jsonMatch)) {
                            $parsedContent = json_decode($jsonMatch[0], true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                Log::info('AI Service: Success with model (extracted JSON)', ['model' => $model]);
                                return $parsedContent;
                            }
                        }

                        Log::warning('AI Service: Invalid JSON response', [
                            'content' => $content,
                            'attempt' => $attempt,
                            'model' => $model
                        ]);
                    } else {
                        Log::warning('AI Service: API request failed', [
                            'status' => $response->status(),
                            'body' => $response->body(),
                            'attempt' => $attempt,
                            'model' => $model
                        ]);
                    }

                } catch (\Exception $e) {
                    Log::error('AI Service: API call failed', [
                        'error' => $e->getMessage(),
                        'attempt' => $attempt,
                        'model' => $model
                    ]);
                }

                // Wait before retry
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2; // Exponential backoff
                }
            }
        }

        // All models and retries failed, return fallback
        Log::warning('AI Service: All models failed, using fallback response');
        return $this->getFallbackResponse($prompt);
    }

    /**
     * Get fallback response when AI is unavailable
     */
    private function getFallbackResponse(string $prompt): array
    {
        // Cache fallback responses to avoid repeated processing
        $cacheKey = 'ai_fallback_' . md5($prompt);

        return Cache::remember($cacheKey, 3600, function () use ($prompt) {
            if (str_contains($prompt, 'breakdown')) {
                return $this->getFallbackBreakdown();
            } elseif (str_contains($prompt, 'suggestions')) {
                return $this->getFallbackSuggestions();
            } elseif (str_contains($prompt, 'summary')) {
                return $this->getFallbackSummary();
            } elseif (str_contains($prompt, 'insights')) {
                return $this->getFallbackInsights();
            }

            return ['message' => 'AI service is temporarily unavailable'];
        });
    }

    /**
     * Build breakdown prompt
     */
    private function buildBreakdownPrompt(string $title, string $description, string $complexity): string
    {
        $complexityMap = [
            'simple' => '3-5個の簡単なサブタスク',
            'medium' => '5-8個の中程度のサブタスク',
            'complex' => '8-12個の詳細なサブタスク'
        ];

        return "以下のタスクを{$complexityMap[$complexity]}に分割してください：

タスク: {$title}
説明: {$description}

各サブタスクには以下を含めてください：
- title: サブタスクのタイトル
- estimated_minutes: 推定時間（分）

JSON形式で返してください：
[
  {
    \"title\": \"サブタスク1\",
    \"estimated_minutes\": 30
  }
]";
    }

    /**
     * Build suggestions prompt
     */
    private function buildSuggestionsPrompt(array $recentTasks, array $completedTasks, array $userPreferences): string
    {
        $taskTitles = collect($recentTasks)->pluck('title')->join(', ');
        $completedTitles = collect($completedTasks)->pluck('title')->join(', ');

        return "ユーザーの最近の活動に基づいて、今日のタスク提案をしてください：

最近のタスク: {$taskTitles}
完了したタスク: {$completedTitles}

以下の形式で3-5個の提案をしてください：
[
  {
    \"title\": \"提案タイトル\",
    \"description\": \"提案の説明\",
    \"priority\": \"high|medium|low\",
    \"estimated_time\": \"推定時間\"
  }
]";
    }

    /**
     * Build summary prompt
     */
    private function buildSummaryPrompt(array $tasks, array $sessions, string $date, array $metrics): string
    {
        $completedTasks = collect($tasks)->where('status', 'completed');
        $totalFocusTime = collect($sessions)->sum('actual_minutes');

        return "{$date}の活動を分析して、日次サマリーを生成してください：

完了したタスク: {$completedTasks->count()}個
総フォーカス時間: {$totalFocusTime}分
セッション数: " . count($sessions) . "回

以下の形式でサマリーを返してください：
{
  \"achievements\": [\"達成事項1\", \"達成事項2\"],
  \"insights\": [\"洞察1\", \"洞察2\"],
  \"recommendations\": [\"推奨事項1\", \"推奨事項2\"],
  \"mood\": \"good|average|poor\",
  \"productivity_score\": 85
}";
    }

    /**
     * Build insights prompt
     */
    private function buildInsightsPrompt(array $weeklyData, array $trends): string
    {
        return "週間データを分析して、生産性の洞察を提供してください：

週間データ: " . json_encode($weeklyData) . "
トレンド: " . json_encode($trends) . "

以下の形式で洞察を返してください：
{
  \"key_insights\": [\"洞察1\", \"洞察2\"],
  \"improvement_areas\": [\"改善点1\", \"改善点2\"],
  \"recommendations\": [\"推奨事項1\", \"推奨事項2\"],
  \"strengths\": [\"強み1\", \"強み2\"]
}";
    }

    /**
     * Build learning prompt
     */
    private function buildLearningPrompt(array $completedTasks, array $learningPaths): string
    {
        $taskTitles = collect($completedTasks)->pluck('title')->join(', ');

        return "完了したタスクに基づいて、学習推奨事項を提供してください：

完了したタスク: {$taskTitles}
学習パス: " . json_encode($learningPaths) . "

以下の形式で推奨事項を返してください：
[
  {
    \"skill\": \"スキル名\",
    \"recommendation\": \"推奨事項\",
    \"priority\": \"high|medium|low\",
    \"estimated_time\": \"推定時間\"
  }
]";
    }

    /**
     * Build focus analysis prompt
     */
    private function buildFocusAnalysisPrompt(array $sessions, array $productivityData): string
    {
        return "フォーカスセッションデータを分析して、パターンを特定してください：

セッションデータ: " . json_encode($sessions) . "
生産性データ: " . json_encode($productivityData) . "

以下の形式で分析結果を返してください：
{
  \"optimal_times\": [\"最適な時間帯1\", \"最適な時間帯2\"],
  \"session_patterns\": [\"パターン1\", \"パターン2\"],
  \"efficiency_tips\": [\"効率化のコツ1\", \"効率化のコツ2\"],
  \"recommendations\": [\"推奨事項1\", \"推奨事項2\"]
}";
    }

    /**
     * Build motivational prompt
     */
    private function buildMotivationalPrompt(string $mood, array $achievements, array $goals): string
    {
        $achievementText = implode(', ', $achievements);
        $goalText = implode(', ', $goals);

        return "ユーザーの気分と成果に基づいて、励ましのメッセージを生成してください：

現在の気分: {$mood}
今日の成果: {$achievementText}
目標: {$goalText}

短くて励ましになるメッセージを1つ返してください。";
    }

    /**
     * Fallback responses
     */
    private function getFallbackBreakdown(): array
    {
        return [
            [
                'title' => 'タスクの準備',
                'estimated_minutes' => 15
            ],
            [
                'title' => 'メイン作業',
                'estimated_minutes' => 45
            ],
            [
                'title' => '確認と整理',
                'estimated_minutes' => 15
            ]
        ];
    }

    private function getFallbackSuggestions(): array
    {
        return [
            [
                'title' => '重要なタスクを完了する',
                'description' => '今日の最重要タスクに取り組んでください',
                'priority' => 'high',
                'estimated_time' => '60分'
            ],
            [
                'title' => '短い休憩を取る',
                'description' => '集中力を維持するために休憩を取りましょう',
                'priority' => 'medium',
                'estimated_time' => '10分'
            ]
        ];
    }

    private function getFallbackSummary(): array
    {
        return [
            'achievements' => ['タスクに取り組みました', '集中力を維持しました'],
            'insights' => ['継続的な取り組みが重要です', '適度な休憩が効果的です'],
            'recommendations' => ['明日も同じペースで続けましょう', '目標を明確に設定しましょう'],
            'mood' => 'good',
            'productivity_score' => 75
        ];
    }

    private function getFallbackInsights(): array
    {
        return [
            'key_insights' => ['継続的な取り組みが成果につながります', '適切な休憩が重要です'],
            'improvement_areas' => ['時間管理の最適化', '集中力の維持'],
            'recommendations' => ['ポモドーロテクニックの活用', '目標の明確化'],
            'strengths' => ['継続性', '集中力']
        ];
    }

    /**
     * Get AI service status
     */
    public function getStatus(): array
    {
        return [
            'available' => !empty($this->apiKey),
            'model' => $this->model,
            'fallback_model' => $this->fallbackModel,
            'enable_fallback' => $this->enableFallback,
            'base_url' => $this->baseUrl,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];
    }

    /**
     * Test AI connection
     */
    public function testConnection(): bool
    {
        try {
            // Test connection timeout: ngắn hơn general timeout (10s)
            $testTimeout = min(10, $this->timeout * 0.33); // 33% của general timeout hoặc tối đa 10s
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout((int)$testTimeout)->get($this->baseUrl . '/models');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('AI Service: Connection test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Parse task creation intent from user message
     *
     * @param string $message User message to analyze
     * @return array|null Task data if task creation intent detected, null otherwise
     */
    public function parseTaskIntent(string $message): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        $prompt = "以下のメッセージを分析して、**明確なタスク作成の意図があるか**判断してください。
タスク作成の意図がある場合は、タスク情報を抽出してJSONで返してください。
意図がない場合は、必ず false を返してください。

メッセージ: {$message}

タスク作成の意図がある場合のJSON形式:
{
  \"has_task_intent\": true,
  \"task\": {
    \"title\": \"タスクのタイトル\",
    \"description\": \"タスクの説明（オプション）\",
    \"estimated_minutes\": 推定時間（分）,
    \"priority\": \"high/medium/low\",
    \"deadline\": \"YYYY-MM-DD\" (オプション、期限が指定されている場合のみ),
    \"scheduled_time\": \"HH:MM:SS\" (オプション、開始時刻が指定されている場合。時刻のみ、例: \"14:30:00\"),
    \"tags\": [\"タグ1\", \"タグ2\"],
    \"subtasks\": [
      {
        \"title\": \"サブタスク1\",
        \"estimated_minutes\": 時間（分）
      }
    ]
  }
}

タスク作成の意図がない場合:
{
  \"has_task_intent\": false
}

**明確にタスク作成の意図があるキーワード:**
- 「タスクを追加」「タスクを作る」「タスク作成」
- 「〜したい」+時間指定 (例: 「英語を30分勉強したい」)
- 「〜をやる」+具体的な行動 (例: 「レポートを書く」)
- 「〜を始める」「〜を完成させる」

**タスク作成の意図がないもの (必ず false を返す):**
- 質問: 「どうすればいいですか？」「何をすべき？」「天気は？」
- 雑談: 「こんにちは」「ありがとう」「疲れた」「おやすみ」
- 相談: 「どう思いますか？」「アドバイスください」
- 感想: 「楽しい」「嬉しい」「大変だ」
- 確認: 「本当ですか？」「そうなんですか？」
- 一般的な会話: 「はい」「いいえ」「わかりました」
- **情報確認**: 「lịch học thứ 3を確認」「スケジュールを見せて」「予定を教えて」「時間割を確認」

**重要な判断基準:**
1. 具体的な行動が明示されているか？
2. その行動を実行する意図が明確か？
3. 単なる質問や相談ではないか？

**例:**
❌ \"今日は何をすべきですか？\" → {\"has_task_intent\": false} (質問)
❌ \"疲れました\" → {\"has_task_intent\": false} (感想)
❌ \"ありがとう\" → {\"has_task_intent\": false} (雑談)
❌ \"タスクが多すぎる\" → {\"has_task_intent\": false} (相談)
❌ \"Kiểm tra lịch học thứ 3\" → {\"has_task_intent\": false} (スケジュール確認の質問)
❌ \"今日の予定を教えて\" → {\"has_task_intent\": false} (情報確認)
❌ \"スケジュールを見せて\" → {\"has_task_intent\": false} (情報確認)
✅ \"英語を30分勉強する\" → {\"has_task_intent\": true} (明確な行動)
✅ \"レポートを書くタスクを作成\" → {\"has_task_intent\": true} (明確な意図)
✅ \"Tạo task học tiếng anh 30 phút\" → {\"has_task_intent\": true} (明確なタスク作成)

注意:
- deadlineはユーザーが明示的に期限を指定した場合のみ含めてください
  例: 「明日まで」「来週の金曜日まで」「10月30日まで」など
- deadlineが指定されていない場合は、フィールドを省略してください（バックエンドで自動的に今日の日付が設定されます）
- scheduled_timeは時刻のみ（HH:MM:SSまたはHH:MM形式）で指定してください。例: \"14:30:00\" または \"14:30\"
- 時刻指定がない場合は scheduled_time を省略してください
- 疑わしい場合は false を返してください";

        try {
            // Parse task intent timeout: ngắn hơn general timeout (10s)
            $parseTimeout = min(10, $this->timeout * 0.33); // 33% của general timeout hoặc tối đa 10s

            // Determine which parameter to use based on model
            $useMaxCompletionTokens = in_array($this->fallbackModel, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

            $requestBody = [
                'model' => $this->fallbackModel, // Use faster model for parsing
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a task parser assistant. Analyze user messages and extract task information. Always return valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3, // Low temperature for consistent parsing
            ];

            // Use appropriate parameter based on model
            if ($useMaxCompletionTokens) {
                $requestBody['max_completion_tokens'] = 500;
            } else {
                $requestBody['max_tokens'] = 500;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout((int)$parseTimeout)->post($this->baseUrl . '/chat/completions', $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                // Parse JSON response
                $parsedContent = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    if (!empty($parsedContent['has_task_intent']) && $parsedContent['has_task_intent'] === true) {
                        Log::info('Task intent detected', ['task' => $parsedContent['task']]);
                        return $parsedContent['task'];
                    }
                }

                // Try to extract JSON from response
                $jsonMatch = [];
                if (preg_match('/\{.*\}/s', $content, $jsonMatch)) {
                    $parsedContent = json_decode($jsonMatch[0], true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($parsedContent['has_task_intent'])) {
                        if ($parsedContent['has_task_intent'] === true) {
                            return $parsedContent['task'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Task intent parsing failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Parse timetable class creation intent from user message
     * Similar to parseTaskIntent() but for timetable classes
     *
     * @param string $message User message
     * @return array|null Timetable class data if intent detected, null otherwise
     */
    public function parseTimetableIntent(string $message): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        $prompt = "以下のメッセージを分析して、**明確な授業登録の意図があるか**判断してください。
授業登録の意図がある場合は、授業情報を抽出してJSONで返してください。
意図がない場合は、必ず false を返してください。

メッセージ: {$message}

授業登録の意図がある場合のJSON形式:
{
  \"has_timetable_intent\": true,
  \"timetable_class\": {
    \"name\": \"授業名\",
    \"day\": \"monday/tuesday/wednesday/thursday/friday/saturday/sunday\",
    \"start_time\": \"HH:MM\",
    \"end_time\": \"HH:MM\",
    \"period\": 1-10 (オプション、指定されていない場合は時間から計算),
    \"room\": \"教室名（オプション）\",
    \"instructor\": \"教員名（オプション）\",
    \"description\": \"説明（オプション）\"
  }
}

授業登録の意図がない場合:
{
  \"has_timetable_intent\": false
}

**明確に授業登録の意図があるキーワード:**
- 「授業を追加」「授業を登録」「クラスを追加」「時間割に追加」「授業を入れる」
- 「〜の授業がある」+時間指定 (例: 「月曜日に数学の授業がある」)
- 「〜のクラスを追加」(例: 「Calculusのクラスを追加」)
- ベトナム語: 「thêm lớp」「đăng ký lớp」「thêm lịch học」「thêm môn」
- 英語: \"add class\", \"register class\", \"add to timetable\"

**授業登録の意図がないもの (必ず false を返す):**
- 質問: 「今日の授業は何ですか？」「スケジュールを見せて」
- 確認: 「授業の時間を確認」「時間割を教えて」
- 雑談: 「授業が大変」「先生が厳しい」

**日本語の曜日 → 英語マッピング:**
- 月曜日 → monday
- 火曜日 → tuesday
- 水曜日 → wednesday
- 木曜日 → thursday
- 金曜日 → friday
- 土曜日 → saturday
- 日曜日 → sunday

**ベトナム語の曜日 → 英語マッピング:**
- thứ 2 → monday
- thứ 3 → tuesday
- thứ 4 → wednesday
- thứ 5 → thursday
- thứ 6 → friday
- thứ 7 → saturday
- chủ nhật → sunday

**時間フォーマット:**
- 日本語: 「9時」→ \"09:00\", 「10時半」→ \"10:30\", \"9時15分\" → \"09:15\"
- ベトナム語: \"9h\" → \"09:00\", \"9h30\" → \"09:30\"
- 英語: \"9am\" → \"09:00\", \"2:30pm\" → \"14:30\"
- 24時間制: \"14:00\" → \"14:00\"

**例:**
❌ \"今日の授業は何ですか？\" → {\"has_timetable_intent\": false} (質問)
❌ \"月曜日のスケジュールを見せて\" → {\"has_timetable_intent\": false} (確認)
❌ \"授業が多すぎる\" → {\"has_timetable_intent\": false} (雑談)
✅ \"月曜日の9時から10時までCalculusの授業を追加\" → {\"has_timetable_intent\": true, \"timetable_class\": {\"name\": \"Calculus\", \"day\": \"monday\", \"start_time\": \"09:00\", \"end_time\": \"10:00\"}}
✅ \"木曜日の9時から10時まで日本語の授業を追加してください\" → {\"has_timetable_intent\": true, \"timetable_class\": {\"name\": \"日本語\", \"day\": \"thursday\", \"start_time\": \"09:00\", \"end_time\": \"10:00\"}}
✅ \"Thêm lớp Calculus thứ 2 lúc 9h\" → {\"has_timetable_intent\": true, \"timetable_class\": {\"name\": \"Calculus\", \"day\": \"monday\", \"start_time\": \"09:00\"}}
✅ \"火曜日に英語の授業を入れて、10時から11時半まで\" → {\"has_timetable_intent\": true, \"timetable_class\": {\"name\": \"英語\", \"day\": \"tuesday\", \"start_time\": \"10:00\", \"end_time\": \"11:30\"}}

注意:
- start_time と end_time は必須です (HH:MM 形式)
- period は指定されていない場合は省略してください (バックエンドで計算)
- day は必ず英語 (monday-sunday) で返してください
- 疑わしい場合は false を返してください";

        try {
            $parseTimeout = min(10, $this->timeout * 0.33);

            $useMaxCompletionTokens = in_array($this->fallbackModel, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

            $requestBody = [
                'model' => $this->fallbackModel,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a timetable parser assistant. Analyze user messages and extract timetable class information. Always return valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
            ];

            if ($useMaxCompletionTokens) {
                $requestBody['max_completion_tokens'] = 500;
            } else {
                $requestBody['max_tokens'] = 500;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout((int)$parseTimeout)->post($this->baseUrl . '/chat/completions', $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                // Parse JSON response
                $parsedContent = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    if (!empty($parsedContent['has_timetable_intent']) && $parsedContent['has_timetable_intent'] === true) {
                        Log::info('Timetable intent detected', ['class' => $parsedContent['timetable_class']]);
                        return $parsedContent['timetable_class'];
                    }
                }

                // Try to extract JSON from response
                $jsonMatch = [];
                if (preg_match('/\{.*\}/s', $content, $jsonMatch)) {
                    $parsedContent = json_decode($jsonMatch[0], true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($parsedContent['has_timetable_intent'])) {
                        if ($parsedContent['has_timetable_intent'] === true) {
                            return $parsedContent['timetable_class'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Timetable intent parsing failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Chat with AI - for general conversation
     *
     * @param array $messages Array of messages in format: [['role' => 'user/assistant', 'content' => 'message']]
     * @param array $options Additional options for the API call
     * @return array Response containing message and metadata
     */
    public function chat(array $messages, array $options = []): array
    {
        if (!$this->apiKey) {
            Log::warning('AI Chat: API key not configured', [
                'api_key_set' => !empty($this->apiKey),
                'api_key_preview' => !empty($this->apiKey) ? substr($this->apiKey, 0, 7) . '...' : 'Not set',
                'config_value' => config('services.openai.api_key') ? 'Set' : 'Not set'
            ]);
            return [
                'message' => 'AI service is currently unavailable. Please try again later.',
                'error' => true,
                'debug_info' => [
                    'api_key_configured' => !empty($this->apiKey),
                    'config_check' => config('services.openai.api_key') ? 'Set' : 'Not set'
                ]
            ];
        }

        $maxRetries = 2; // Giảm từ 3 xuống 2 để nhanh hơn
        $retryDelay = 0.5; // Giảm delay từ 1s xuống 0.5s
        $models = [$this->model];

        if ($this->enableFallback && $this->fallbackModel !== $this->model) {
            $models[] = $this->fallbackModel;
        }

        foreach ($models as $model) {
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    // Prepare messages array
                    $apiMessages = [
                        [
                            'role' => 'system',
                            'content' => $options['system_prompt'] ?? 'You are a helpful productivity assistant. Always respond in Japanese in a friendly and encouraging manner.'
                        ]
                    ];

                    // Add conversation history
                    foreach ($messages as $msg) {
                        $apiMessages[] = [
                            'role' => $msg['role'],
                            'content' => $msg['content']
                        ];
                    }

                    // Chat timeout: sử dụng config nhưng có thể override bằng options
                    $chatTimeout = $options['timeout'] ?? ($this->timeout * 0.5); // Chat timeout = 50% của general timeout (15s nếu timeout=30s)

                    // Determine which parameter to use based on model
                    // Newer models (gpt-5, o1, etc.) use max_completion_tokens instead of max_tokens
                    $useMaxCompletionTokens = in_array($model, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

                    // Set appropriate max_tokens based on model and use case
                    // For GPT-5 and o1 models, use higher token limits due to longer context and more detailed responses
                    if ($useMaxCompletionTokens) {
                        $maxTokensValue = $options['max_tokens'] ?? 16000; // Higher limit for GPT-5 and o1 models (increased from 8000)
                    } else {
                        $maxTokensValue = $options['max_tokens'] ?? 2000; // Standard limit for other models
                    }

                    $requestBody = [
                        'model' => $model,
                        'messages' => $apiMessages,
                        'stream' => false,
                    ];

                    // Temperature support varies by model
                    // GPT-5 and o1 series only support temperature=1 (default)
                    $noTemperatureModels = ['gpt-5', 'o1', 'o1-preview', 'o1-mini'];
                    if (!in_array($model, $noTemperatureModels)) {
                        $requestBody['temperature'] = $options['temperature'] ?? 0.7;
                    }

                    // Use appropriate parameter based on model
                    if ($useMaxCompletionTokens) {
                        $requestBody['max_completion_tokens'] = $maxTokensValue;
                    } else {
                        $requestBody['max_tokens'] = $maxTokensValue;
                    }

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])->timeout((int)$chatTimeout)->post($this->baseUrl . '/chat/completions', $requestBody);

                    if ($response->successful()) {
                        $data = $response->json();
                        $content = $data['choices'][0]['message']['content'] ?? '';

                        Log::info('AI Chat: Success', [
                            'model' => $model,
                            'tokens' => $data['usage']['total_tokens'] ?? 0
                        ]);

                        return [
                            'message' => $content,
                            'model' => $model,
                            'tokens' => $data['usage']['total_tokens'] ?? 0,
                            'finish_reason' => $data['choices'][0]['finish_reason'] ?? 'stop',
                            'error' => false
                        ];
                    } else {
                        $errorBody = $response->json();
                        $errorMessage = $errorBody['error']['message'] ?? $response->body();
                        $errorCode = $errorBody['error']['code'] ?? null;
                        $errorType = $errorBody['error']['type'] ?? null;

                        Log::warning('AI Chat: API request failed', [
                            'status' => $response->status(),
                            'error' => $errorMessage,
                            'error_code' => $errorCode,
                            'error_type' => $errorType,
                            'body' => $errorBody,
                            'attempt' => $attempt,
                            'model' => $model,
                            'base_url' => $this->baseUrl
                        ]);

                        // If quota exceeded, don't retry
                        if ($response->status() === 429 && ($errorType === 'insufficient_quota' || $errorCode === 'insufficient_quota')) {
                            Log::error('AI Chat: Quota exceeded, stopping retries', [
                                'model' => $model,
                                'error' => $errorMessage
                            ]);
                            break; // Stop retrying this model
                        }
                    }

                } catch (\Exception $e) {
                    Log::error('AI Chat: API call failed', [
                        'error' => $e->getMessage(),
                        'attempt' => $attempt,
                        'model' => $model
                    ]);
                }

                if ($attempt < $maxRetries) {
                    usleep((int)($retryDelay * 1000000)); // Convert seconds to microseconds
                    $retryDelay *= 2;
                }
            }
        }

        // All attempts failed
        Log::warning('AI Chat: All models failed', [
            'models_tried' => $models,
            'api_key_set' => !empty($this->apiKey),
            'api_key_preview' => !empty($this->apiKey) ? substr($this->apiKey, 0, 7) . '...' : 'Not set',
            'base_url' => $this->baseUrl
        ]);

        // Return more specific error message
        $errorMsg = '申し訳ございません。現在AIサービスに接続できません。';
        if (empty($this->apiKey)) {
            $errorMsg = 'AIサービスが設定されていません。管理者にお問い合わせください。';
        } else {
            // Check if it's a quota issue (this would be set if we detected quota error)
            // For now, we'll use a generic message, but could be enhanced to detect quota errors
            $errorMsg = '申し訳ございません。AIサービスの利用制限に達したか、一時的に利用できません。しばらくしてからもう一度お試しください。';
        }

        return [
            'message' => $errorMsg,
            'error' => true,
            'debug_info' => [
                'api_key_configured' => !empty($this->apiKey),
                'models_tried' => $models,
                'base_url' => $this->baseUrl
            ]
        ];
    }

    /**
     * Chat with AI with user context (tasks, timetable, calendar)
     * This allows AI to give context-aware suggestions
     *
     * @param array $messages Array of messages in format: [['role' => 'user/assistant', 'content' => 'message']]
     * @param array $userContext User context including tasks, timetable, calendar
     * @param array $options Additional options for the API call
     * @return array Response containing message, task_suggestion, and metadata
     */
    public function chatWithUserContext(array $messages, array $userContext, array $options = []): array
    {
        if (!$this->apiKey) {
            return [
                'message' => 'AI service is currently unavailable. Please try again later.',
                'error' => true,
                'task_suggestion' => null
            ];
        }

        // Build context-aware system prompt
        $systemPrompt = $this->buildContextAwareSystemPrompt($userContext);

        // Merge system prompt into options
        $contextOptions = array_merge($options, [
            'system_prompt' => $systemPrompt
        ]);

        // Call regular chat with context-aware system prompt
        $response = $this->chat($messages, $contextOptions);

        // Try to parse task_suggestion from response if exists
        $taskSuggestion = null;
        if (!empty($response['message']) && is_string($response['message'])) {
            try {
                // Try to extract JSON code block first (```json ... ```)
                $jsonMatch = [];
                if (preg_match('/```json\s*(\{[\s\S]*?\})\s*```/i', $response['message'], $jsonMatch)) {
                    $parsed = json_decode($jsonMatch[1], true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($parsed['task_suggestion'])) {
                        $taskSuggestion = $parsed['task_suggestion'];
                        // Replace message with clean message without JSON block
                        $response['message'] = $parsed['message'] ?? trim(preg_replace('/```json[\s\S]*```/i', '', $response['message']));
                    }
                }
                // If no code block, try to find JSON object at the end of message
                elseif (preg_match('/\n\s*(\{[^{}]*"task_suggestion"[^{}]*\{[^{}]*\}[^{}]*\})\s*$/s', $response['message'], $jsonMatch)) {
                    $parsed = json_decode($jsonMatch[1], true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($parsed['task_suggestion'])) {
                        $taskSuggestion = $parsed['task_suggestion'];
                        // Replace message with clean message without JSON
                        $response['message'] = $parsed['message'] ?? trim(str_replace($jsonMatch[1], '', $response['message']));
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail - just continue without task suggestion
                Log::debug('Failed to parse task suggestion from AI response', [
                    'error' => $e->getMessage(),
                    'response_preview' => substr($response['message'], 0, 200)
                ]);
            }
        }

        $response['task_suggestion'] = $taskSuggestion;

        return $response;
    }

    /**
     * Build context-aware system prompt
     *
     * @param array $context User context (tasks, timetable, calendar)
     * @return string System prompt with context
     */
    private function buildContextAwareSystemPrompt(array $context): string
    {
        $tasks = $context['tasks'] ?? [];
        $timetable = $context['timetable'] ?? [];

        $tasksInfo = $this->formatTasksInfo($tasks);
        $scheduleInfo = $this->formatScheduleInfo($timetable);
        $freeTimeAnalysis = $this->analyzeFreeTime($timetable, $tasks);
        $deadlineWarnings = $this->analyzeDeadlines($tasks);

        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i');

        return "あなたは親切で有能な生産性アシスタントです。日本語で応答してください。

現在: {$today} {$currentTime}

{$tasksInfo}
{$scheduleInfo}
{$freeTimeAnalysis}
{$deadlineWarnings}

【重要な指示】

1. **通常の会話**: JSON形式を使わず、普通のテキストで返答してください。
   例: 「モチベーションを上げる方法を教えてください」→ 親切にアドバイスする

2. **スケジュール/時間割の質問**: ユーザーがスケジュールや時間割について聞いた場合:
   - 上記のスケジュール情報を参照して答えてください
   - 「Kiểm tra lịch học thứ 3」「今日の予定は？」などの質問に対応
   - タスク作成せず、スケジュール情報を表示してください
   - 例: 「火曜日の授業は以下の通りです: [授業リスト]」

3. **タスク提案時のみ**: メッセージの最後にJSON形式を追加
```json
{
  \"message\": \"提案メッセージ\",
  \"task_suggestion\": {
    \"title\": \"タスク名\",
    \"description\": \"説明\",
    \"estimated_minutes\": 60,
    \"priority\": \"high/medium/low\",
    \"scheduled_time\": \"14:00:00\",
    \"reason\": \"提案理由\"
  }
}
```

3. **Proactive提案**: 期限が近い、空き時間がある場合は積極的に提案する

4. **会話トーン**: 親しみやすく、具体的で実行可能なアドバイスを提供

scheduled_timeは時刻のみ（HH:MM:SSまたはHH:MM形式）で指定してください。例: \"14:30:00\" または \"14:30\"";
    }

    /**
     * Format tasks information for AI context
     *
     * @param array $tasks User's tasks
     * @return string Formatted tasks info
     */
    private function formatTasksInfo(array $tasks): string
    {
        if (empty($tasks)) {
            return "## 現在のタスク\n現在、進行中のタスクはありません。";
        }

        $pendingTasks = array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'pending');
        $inProgressTasks = array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'in_progress');

        $info = "## 現在のタスク\n";
        $info .= "合計: " . count($tasks) . "個\n";
        $info .= "保留中: " . count($pendingTasks) . "個\n";
        $info .= "進行中: " . count($inProgressTasks) . "個\n\n";

        $info .= "### タスクリスト:\n";
        $taskCount = 0;
        foreach ($tasks as $task) {
            if ($taskCount >= 10) { // Limit to 10 tasks to avoid token limit
                $info .= "... 他" . (count($tasks) - 10) . "個のタスク\n";
                break;
            }

            $title = $task['title'] ?? 'No title';
            $status = $task['status'] ?? 'pending';
            $priority = $task['priority'] ?? 3;
            $deadline = $task['deadline'] ?? null;
            $scheduledTime = $task['scheduled_time'] ?? null;

            $info .= "- [{$status}] {$title}";
            if ($priority >= 4) {
                $info .= " (優先度: 高)";
            }
            if ($deadline) {
                $info .= " [期限: {$deadline}]";
            }
            if ($scheduledTime) {
                $info .= " [予定: {$scheduledTime}]";
            }
            $info .= "\n";

            $taskCount++;
        }

        return $info;
    }

    /**
     * Format schedule information for AI context
     *
     * @param array $timetable User's timetable/calendar
     * @return string Formatted schedule info
     */
    private function formatScheduleInfo(array $timetable): string
    {
        if (empty($timetable)) {
            return "## スケジュール\n今週のスケジュールはありません。";
        }

        $info = "## 週間スケジュール\n\n";

        // Map English day names to Japanese
        $dayNameMap = [
            'monday' => '月曜日',
            'tuesday' => '火曜日',
            'wednesday' => '水曜日',
            'thursday' => '木曜日',
            'friday' => '金曜日',
            'saturday' => '土曜日',
            'sunday' => '日曜日',
        ];

        // If timetable is grouped by day (new format)
        $hasSchedule = false;
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            if (isset($timetable[$day]) && !empty($timetable[$day])) {
                $hasSchedule = true;
                $dayJp = $dayNameMap[$day] ?? $day;
                $info .= "**{$dayJp}:**\n";
                foreach ($timetable[$day] as $class) {
                    $time = $class['time'] ?? '';
                    $title = $class['title'] ?? $class['class_name'] ?? 'No title';
                    $info .= "  - {$time}: {$title}\n";
                }
                $info .= "\n";
            }
        }

        if (!$hasSchedule) {
            return "## スケジュール\n今週のスケジュールはありません。";
        }

        return $info;
    }

    /**
     * Analyze free time slots in schedule
     *
     * @param array $timetable User's timetable
     * @param array $tasks User's tasks
     * @return string Free time analysis
     */
    private function analyzeFreeTime(array $timetable, array $tasks): string
    {
        if (empty($timetable) && empty($tasks)) {
            return "## 空き時間分析\n一日中自由な時間があります。タスクを計画的に配置できます。";
        }

        $analysis = "## 空き時間分析\n";

        // Parse schedule items to get busy time slots
        $busySlots = [];

        // Add timetable classes to busy slots
        if (!empty($timetable)) {
            if (isset($timetable['classes']) && is_array($timetable['classes'])) {
                foreach ($timetable['classes'] as $class) {
                    $time = $class['time'] ?? $class['start_time'] ?? '';
                    if ($time) {
                        $busySlots[] = $time;
                    }
                }
            } else {
                foreach ($timetable as $item) {
                    if (is_array($item)) {
                        $time = $item['time'] ?? $item['start_time'] ?? '';
                        if ($time) {
                            $busySlots[] = $time;
                        }
                    }
                }
            }
        }

        // Add scheduled tasks to busy slots
        foreach ($tasks as $task) {
            if (!empty($task['scheduled_time'])) {
                $scheduledTime = $task['scheduled_time'];
                // scheduled_time is now TIME type (HH:MM:SS or HH:MM)
                // Extract HH:MM portion
                try {
                    // If it's HH:MM:SS, take first 5 chars; if HH:MM, use as is
                    $timeParts = explode(':', $scheduledTime);
                    if (count($timeParts) >= 2) {
                        $busySlots[] = sprintf('%02d:%02d', (int)$timeParts[0], (int)$timeParts[1]);
                    }
                } catch (\Exception $e) {
                    // Skip invalid times
                }
            }
        }

        if (empty($busySlots)) {
            $analysis .= "- 現在、予定されている授業やタスクはありません\n";
            $analysis .= "- 一日を自由に使えます\n";
        } else {
            $analysis .= "- 予定がある時間帯: " . count($busySlots) . "個\n";
            $analysis .= "- 空き時間を活用してタスクを進めましょう\n";

            // Suggest optimal times for tasks
            $currentHour = (int)now()->format('H');
            if ($currentHour < 12) {
                $analysis .= "- 💡 午前中は集中力が高い時間帯です。重要なタスクに最適です\n";
            } elseif ($currentHour < 18) {
                $analysis .= "- 💡 午後は作業を進めるのに良い時間です\n";
            } else {
                $analysis .= "- 💡 夕方以降は軽めのタスクや復習に適しています\n";
            }
        }

        return $analysis;
    }

    /**
     * Analyze task deadlines and provide warnings
     *
     * @param array $tasks User's tasks
     * @return string Deadline warnings
     */
    private function analyzeDeadlines(array $tasks): string
    {
        if (empty($tasks)) {
            return "";
        }

        $warnings = [];
        $urgentTasks = [];
        $overdueTasks = [];
        $now = now();

        foreach ($tasks as $task) {
            $status = $task['status'] ?? 'pending';

            // Skip completed or cancelled tasks
            if (in_array($status, ['completed', 'cancelled'])) {
                continue;
            }

            $deadline = $task['deadline'] ?? null;

            if ($deadline) {
                try {
                    $deadlineDate = new \DateTime($deadline);
                    $hoursUntilDeadline = $now->diffInHours($deadlineDate, false);

                    if ($hoursUntilDeadline < 0) {
                        // Overdue
                        $overdueTasks[] = $task;
                    } elseif ($hoursUntilDeadline <= 24) {
                        // Due within 24 hours
                        $urgentTasks[] = $task;
                    }
                } catch (\Exception $e) {
                    // Skip invalid dates
                }
            }
        }

        if (empty($overdueTasks) && empty($urgentTasks)) {
            return "";
        }

        $analysis = "## ⚠️ 期限警告\n";

        if (!empty($overdueTasks)) {
            $analysis .= "### 🔴 期限切れタスク (" . count($overdueTasks) . "個)\n";
            foreach ($overdueTasks as $task) {
                $title = $task['title'] ?? 'No title';
                $deadline = $task['deadline'] ?? '';
                $analysis .= "- **{$title}** (期限: {$deadline})\n";
            }
            $analysis .= "\n";
        }

        if (!empty($urgentTasks)) {
            $analysis .= "### 🟡 緊急タスク - 24時間以内 (" . count($urgentTasks) . "個)\n";
            foreach ($urgentTasks as $task) {
                $title = $task['title'] ?? 'No title';
                $deadline = $task['deadline'] ?? '';
                try {
                    $deadlineDate = new \DateTime($deadline);
                    $hoursLeft = $now->diffInHours($deadlineDate);
                    $analysis .= "- **{$title}** (残り: 約{$hoursLeft}時間)\n";
                } catch (\Exception $e) {
                    $analysis .= "- **{$title}** (期限: {$deadline})\n";
                }
            }
            $analysis .= "\n";
        }

        $analysis .= "💡 これらのタスクを優先的に進めることをお勧めします。\n";

        return $analysis;
    }
}
