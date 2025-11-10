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
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->model = config('services.openai.model', 'gpt-5');
        $this->fallbackModel = config('services.openai.fallback_model', 'gpt-4o-mini');
        $this->enableFallback = config('services.openai.enable_fallback', true);
        $this->maxTokens = config('services.openai.max_tokens', 1000);
        $this->temperature = config('services.openai.temperature', 0.7);
        $this->timeout = config('services.openai.timeout', 30); // Sử dụng config từ services.php
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
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])->timeout($this->timeout)->post($this->baseUrl . '/chat/completions', [
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
                        'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
                        'temperature' => $options['temperature'] ?? $this->temperature,
                    ]);

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

        $prompt = "以下のメッセージを分析して、タスク作成の意図があるか判断してください。
タスク作成の意図がある場合は、タスク情報を抽出してJSONで返してください。
意図がない場合は、null を返してください。

メッセージ: {$message}

タスク作成の意図がある場合のJSON形式:
{
  \"has_task_intent\": true,
  \"task\": {
    \"title\": \"タスクのタイトル\",
    \"description\": \"タスクの説明（オプション）\",
    \"estimated_minutes\": 推定時間（分）,
    \"priority\": \"high/medium/low\",
    \"scheduled_time\": \"YYYY-MM-DD HH:MM:SS\" (オプション、開始時刻が指定されている場合),
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

キーワード例:
- タスクを追加、タスク作成、〜したい、〜をやる、勉強する、学習する
- 「15分」「30分」などの時間指定
- 「ちいさく」「分割」「サブタスク」などの分割指示
- 「17時30分」「午後5時半」「17:30」などの開始時刻指定
- 「朝9時から」「13時スタート」などの時刻表現

注意:
- 質問や雑談は「has_task_intent\": false にしてください
- scheduled_timeは今日の日付に時刻を組み合わせてください (例: 今日が2025-11-10で「17時30分」なら「2025-11-10 17:30:00」)
- 時刻指定がない場合は scheduled_time を省略してください";

        try {
            // Parse task intent timeout: ngắn hơn general timeout (10s)
            $parseTimeout = min(10, $this->timeout * 0.33); // 33% của general timeout hoặc tối đa 10s
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout((int)$parseTimeout)->post($this->baseUrl . '/chat/completions', [
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
                'max_tokens' => 500,
                'temperature' => 0.3, // Low temperature for consistent parsing
            ]);

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
     * Chat with AI - for general conversation
     *
     * @param array $messages Array of messages in format: [['role' => 'user/assistant', 'content' => 'message']]
     * @param array $options Additional options for the API call
     * @return array Response containing message and metadata
     */
    public function chat(array $messages, array $options = []): array
    {
        if (!$this->apiKey) {
            return [
                'message' => 'AI service is currently unavailable. Please try again later.',
                'error' => true
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

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])->timeout((int)$chatTimeout)->post($this->baseUrl . '/chat/completions', [
                        'model' => $model,
                        'messages' => $apiMessages,
                        'max_tokens' => $options['max_tokens'] ?? 500, // Giảm từ 800 xuống 500 để nhanh hơn
                        'temperature' => $options['temperature'] ?? 0.7,
                        'stream' => false,
                    ]);

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

                        Log::warning('AI Chat: API request failed', [
                            'status' => $response->status(),
                            'error' => $errorMessage,
                            'body' => $errorBody,
                            'attempt' => $attempt,
                            'model' => $model,
                            'base_url' => $this->baseUrl
                        ]);
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
        }

        return [
            'message' => $errorMsg . 'しばらくしてからもう一度お試しください。',
            'error' => true,
            'debug_info' => [
                'api_key_configured' => !empty($this->apiKey),
                'models_tried' => $models,
                'base_url' => $this->baseUrl
            ]
        ];
    }
}
