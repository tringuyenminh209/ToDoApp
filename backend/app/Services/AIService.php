<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
    private $isLocalProvider;

    private $cacheTtl;

    public function __construct()
    {
        // Try to get API key from config first, then from env, then from .env file directly
        $this->apiKey = config('services.openai.api_key') ?: env('OPENAI_API_KEY') ?: $this->readEnvFile('OPENAI_API_KEY');
        $this->baseUrl = config('services.openai.base_url') ?: env('OPENAI_BASE_URL') ?: $this->readEnvFile('OPENAI_BASE_URL', 'https://api.openai.com/v1');
        $this->model = config('services.openai.model') ?: env('OPENAI_MODEL') ?: $this->readEnvFile('OPENAI_MODEL', 'qwen2.5:3b');
        $this->fallbackModel = config('services.openai.fallback_model') ?: env('OPENAI_FALLBACK_MODEL') ?: $this->readEnvFile('OPENAI_FALLBACK_MODEL', 'qwen2.5:3b');
        $this->enableFallback = config('services.openai.enable_fallback') !== null ? config('services.openai.enable_fallback') : (env('OPENAI_ENABLE_FALLBACK') !== null ? env('OPENAI_ENABLE_FALLBACK') : ($this->readEnvFile('OPENAI_ENABLE_FALLBACK') ?: true));
        $this->maxTokens = config('services.openai.max_tokens') ?: env('OPENAI_MAX_TOKENS') ?: (int)($this->readEnvFile('OPENAI_MAX_TOKENS') ?: 500);
        $this->temperature = config('services.openai.temperature') ?: env('OPENAI_TEMPERATURE') ?: (float)($this->readEnvFile('OPENAI_TEMPERATURE') ?: 0.5);
        $this->timeout = config('services.openai.timeout') ?: env('OPENAI_TIMEOUT') ?: (int)($this->readEnvFile('OPENAI_TIMEOUT') ?: 120);
        $this->cacheTtl = config('services.openai.cache_ttl') ?: env('OPENAI_CACHE_TTL') ?: 3600;
        $this->isLocalProvider = $this->isLocalOpenAICompatibleProvider();

        if ($this->isLocalProvider) {
            // Local provider最適化: fallback無効、タイムアウト延長
            $this->enableFallback = false;
            $this->fallbackModel = $this->model;
            // Local providerの場合、最小タイムアウトを120秒に
            $this->timeout = max(120, $this->timeout);
            // トークン数を制限してレスポンス速度を向上
            $this->maxTokens = min(500, $this->maxTokens);
        }
    }

    public function getContextChatTimeout(int $default = 12): int
    {
        if ($this->isLocalProvider) {
            return max(120, (int)$this->timeout);
        }

        return max($default, (int)($this->timeout * 0.5));
    }

    public function isLocalProvider(): bool
    {
        return $this->isLocalProvider;
    }

    /**
     * Add keep_alive parameter to request body for local providers
     * This keeps the model in memory to reduce load_duration
     */
    private function addKeepAlive(array $requestBody): array
    {
        if ($this->isLocalProvider && !isset($requestBody['keep_alive'])) {
            $requestBody['keep_alive'] = '30m'; // Keep model in memory for 30 minutes
        }
        return $requestBody;
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
     * Detect if base URL points to a local OpenAI-compatible provider (Ollama, etc.)
     */
    private function isLocalOpenAICompatibleProvider(): bool
    {
        $baseUrl = strtolower($this->baseUrl ?? '');

        return str_contains($baseUrl, 'ollama')
            || str_contains($baseUrl, 'localhost:11434')
            || str_contains($baseUrl, '127.0.0.1:11434')
            || str_contains($baseUrl, 'host.docker.internal:11434');
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

        $maxRetries = 2;
        $retryDelay = 0.5; // seconds
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

                    // Local provider用: keep_aliveを追加
                    $requestBody = $this->addKeepAlive($requestBody);

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

            if ($response->successful()) {
                return true;
            }

            if ($this->isLocalProvider) {
                $fallbackResponse = Http::timeout((int)$testTimeout)->get('http://ollama:11434/api/tags');
                return $fallbackResponse->successful();
            }

            return false;
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
    public function parseQuickIntents(string $message, array $conversationHistory = []): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        // Local provider用キャッシュ: 類似メッセージの結果を再利用
        $cacheKey = 'ai_intent_' . md5($message);
        if ($this->isLocalProvider) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                Log::info('parseQuickIntents: Using cached result');
                return $cached;
            }
        }

        $today = now()->format('Y-m-d');
        $dayOfWeek = now()->locale('ja')->isoFormat('dddd');
        $messageLength = mb_strlen($message);

        // Local provider用: 短いメッセージの場合、ローカルパターンマッチングを先に試行
        if ($this->isLocalProvider && $messageLength < 50) {
            $localResult = $this->tryLocalPatternMatching($message);
            if ($localResult !== null) {
                Log::info('parseQuickIntents: Using local pattern matching');
                Cache::put($cacheKey, $localResult, 300); // 5分キャッシュ
                return $localResult;
            }
        }

        $contextText = '';
        if (!empty($conversationHistory) && $messageLength < 30) {
            $recentHistory = array_slice($conversationHistory, -2); // 2つに削減
            $contextText = "\n履歴:\n";
            foreach ($recentHistory as $msg) {
                $contextText .= ($msg['role'] === 'user' ? 'U' : 'A') . ": " . mb_substr($msg['content'], 0, 50) . "\n";
            }
        }

        // Local provider用: 簡略化プロンプト
        $prompt = $this->isLocalProvider
            ? $this->buildSimplifiedIntentPrompt($message, $today, $contextText)
            : $this->buildFullIntentPrompt($message, $today, $dayOfWeek, $contextText);

        try {
            // Local provider用: より長いタイムアウト
            $parseTimeout = $this->isLocalProvider
                ? min(60, max(30, (int)($this->timeout * 0.5)))
                : min(12, (int)($this->timeout * 0.4));

            $modelToUse = $this->model;
            $useMaxCompletionTokens = in_array($modelToUse, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

            $requestBody = [
                'model' => $modelToUse,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'JSON parser. Return only valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.1, // より低いtemperatureで安定性向上
            ];

            // Local provider用: keep_aliveを追加してモデルをメモリに保持
            $requestBody = $this->addKeepAlive($requestBody);

            // Local provider用: トークン数を削減
            $maxTokens = $this->isLocalProvider ? 300 : 700;
            if ($useMaxCompletionTokens) {
                $requestBody['max_completion_tokens'] = $maxTokens;
            } else {
                $requestBody['max_tokens'] = $maxTokens;
            }

            if (str_contains($modelToUse, 'gpt-4') || str_contains($modelToUse, 'gpt-5')) {
                $requestBody['response_format'] = ['type' => 'json_object'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout($parseTimeout)->post($this->baseUrl . '/chat/completions', $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                $parsedContent = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $result = [
                        'task' => $parsedContent['task'] ?? null,
                        'timetable' => $parsedContent['timetable'] ?? null,
                        'knowledge_query' => $parsedContent['knowledge_query'] ?? null,
                        'has_knowledge_creation' => !empty($parsedContent['has_knowledge_creation']),
                    ];

                    // 結果をキャッシュ
                    if ($this->isLocalProvider) {
                        Cache::put($cacheKey, $result, 300);
                    }

                    return $result;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Quick intent parsing failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Local pattern matching for common intents (no AI call needed)
     * ローカルパターンマッチング: AI呼び出し不要な意図を検出
     */
    private function tryLocalPatternMatching(string $message): ?array
    {
        $normalized = mb_strtolower(trim($message));

        // 挨拶や単純な質問はスキップ
        if (preg_match('/^(hi|hello|こんにちは|おはよう|ありがとう|ok|はい|いいえ)[!！。.\s]*$/u', $normalized)) {
            return ['task' => null, 'timetable' => null, 'knowledge_query' => null, 'has_knowledge_creation' => false];
        }

        // スケジュール確認パターン
        if (preg_match('/(今日|明日|スケジュール|予定|時間割|lịch|schedule).*?(確認|見せて|教えて|check|show)/iu', $normalized)) {
            return ['task' => null, 'timetable' => null, 'knowledge_query' => null, 'has_knowledge_creation' => false];
        }

        // 時間・日付の質問パターン
        if (preg_match('/(今何時|何日|何曜日|time|date)/iu', $normalized)) {
            return ['task' => null, 'timetable' => null, 'knowledge_query' => null, 'has_knowledge_creation' => false];
        }

        return null; // パターンにマッチしない場合はAI呼び出し
    }

    /**
     * Build simplified intent prompt for local providers
     * Local provider用: 簡略化プロンプト
     */
    private function buildSimplifiedIntentPrompt(string $message, string $today, string $contextText): string
    {
        return "Analyze: \"{$message}\"
Date: {$today}{$contextText}

Return JSON only:
{\"task\":null,\"timetable\":null,\"knowledge_query\":null,\"has_knowledge_creation\":false}

If task intent: {\"task\":{\"title\":\"...\",\"priority\":\"medium\"}}
If timetable: {\"timetable\":{\"name\":\"...\",\"day\":\"monday\",\"start_time\":\"09:00\",\"end_time\":\"10:00\"}}
If search: {\"knowledge_query\":{\"keywords\":[\"...\"]}}";
    }

    /**
     * Build full intent prompt for cloud providers
     * Cloud provider用: 完全プロンプト
     */
    private function buildFullIntentPrompt(string $message, string $today, string $dayOfWeek, string $contextText): string
    {
        return "以下のメッセージを分析し、必要ならタスク・時間割・Knowledge検索の情報を抽出してください。
意図がないものは null を返してください。Knowledge作成の意図があるかも判定してください。

**今日の日付**: {$today} ({$dayOfWeek})
{$contextText}
メッセージ: {$message}

出力は必ずJSONのみ:
{
  \"task\": null or {
    \"title\": \"タスクのタイトル\",
    \"description\": \"タスクの説明（オプション）\",
    \"estimated_minutes\": 30,
    \"priority\": \"high/medium/low\",
    \"deadline\": \"YYYY-MM-DD\" (オプション),
    \"scheduled_time\": \"HH:MM\" (オプション),
    \"tags\": [\"タグ1\"],
    \"subtasks\": [{\"title\": \"サブタスク\", \"estimated_minutes\": 15}]
  },
  \"timetable\": null or {
    \"name\": \"授業名\",
    \"day\": \"monday/tuesday/wednesday/thursday/friday/saturday/sunday\",
    \"start_time\": \"HH:MM\",
    \"end_time\": \"HH:MM\",
    \"period\": 1,
    \"room\": \"教室名\",
    \"instructor\": \"教員名\",
    \"description\": \"説明\",
    \"color\": \"#6366f1\",
    \"icon\": \"📚\"
  },
  \"knowledge_query\": null or {
    \"item_type\": \"any/note/code_snippet/exercise/resource_link/attachment\",
    \"keywords\": [\"keyword1\", \"keyword2\"],
    \"learning_path_id\": null,
    \"category_id\": null
  },
  \"has_knowledge_creation\": true/false
}

**重要**:
- 意図がないものは必ず null
- 予定確認や雑談は task/timetable にしない
- 時間指定が無い場合は scheduled_time を省略
- keywords は短い単語配列
- JSON以外の文字を出力しない";
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

        $today = now()->format('Y-m-d');
        $dayOfWeek = now()->locale('ja')->isoFormat('dddd');

        $prompt = "以下のメッセージを分析して、**明確なタスク作成の意図があるか**判断してください。
タスク作成の意図がある場合は、タスク情報を抽出してJSONで返してください。
意図がない場合は、必ず false を返してください。

**今日の日付**: {$today} ({$dayOfWeek})

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
- **授業/クラス登録** (これは時間割登録なのでタスクではない): 「授業を追加」「クラスを追加」「時間割に追加」「授業を登録」「lớp học」「thêm lớp」「đăng ký lớp」「add class」「register class」

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
❌ \"木曜日の9時から10時まで日本語の授業を追加してください\" → {\"has_task_intent\": false} (授業登録、タスクではない)
❌ \"月曜日にCalculusの授業を登録\" → {\"has_task_intent\": false} (クラス登録、タスクではない)
❌ \"Thêm lớp Programming thứ 2\" → {\"has_task_intent\": false} (lớp học、タスクではない)
❌ \"Add English class on Tuesday\" → {\"has_task_intent\": false} (クラス登録、タスクではない)
✅ \"英語を30分勉強する\" → {\"has_task_intent\": true} (明確な行動)
✅ \"レポートを書くタスクを作成\" → {\"has_task_intent\": true} (明確な意図)
✅ \"Tạo task học tiếng anh 30 phút\" → {\"has_task_intent\": true} (明確なタスク作成)

注意:
- **deadline**: ユーザーが期限を指定した場合、今日の日付を基準に計算して YYYY-MM-DD 形式で含めてください
  例:
  - 「明日」 → 今日+1日 (例: 今日が2025-11-20なら2025-11-21)
  - 「明後日」「あさって」 → 今日+2日 (例: 2025-11-22)
  - 「来週の金曜日」 → 次の金曜日の日付
  - 「10月30日」 → 2025-10-30 (年は今年を仮定)
  - 期限指定がない場合は deadline を省略してください（バックエンドで自動的に今日の日付が設定されます）
- **scheduled_time**: 時刻のみ（HH:MM:SSまたはHH:MM形式）で指定してください。例: \"14:30:00\" または \"17:00\"
  - タスク実行時刻が指定されている場合のみ含めてください
  - 時刻指定がない場合は省略してください
- 疑わしい場合は false を返してください";

        try {
            // Parse task intent timeout: ローカルプロバイダー用に延長（30秒）
            $parseTimeout = $this->isLocalProvider ? 30 : min(10, $this->timeout * 0.33);

            // Determine which parameter to use based on model
            $useMaxCompletionTokens = in_array($this->fallbackModel, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

            $requestBody = [
                'model' => $this->model, // Use main model instead of fallback for better accuracy
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

            // Add keep_alive for local providers
            $requestBody = $this->addKeepAlive($requestBody);

            Log::info('parseTaskIntent: Sending request', [
                'model' => $requestBody['model'],
                'message_length' => mb_strlen($message),
                'prompt_length' => mb_strlen($prompt)
            ]);

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

                Log::info('parseTaskIntent: Received response', [
                    'content_length' => mb_strlen($content),
                    'content_preview' => mb_substr($content, 0, 200)
                ]);

                // Parse JSON response
                $parsedContent = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    if (!empty($parsedContent['has_task_intent']) && $parsedContent['has_task_intent'] === true) {
                        Log::info('Task intent detected', ['task' => $parsedContent['task']]);
                        return $parsedContent['task'];
                    } else {
                        Log::info('Task intent not detected', ['parsed_content' => $parsedContent]);
                    }
                } else {
                    Log::warning('parseTaskIntent: JSON decode error', [
                        'error' => json_last_error_msg(),
                        'content' => $content
                    ]);
                }

                // Try to extract JSON from response
                $jsonMatch = [];
                if (preg_match('/\{.*\}/s', $content, $jsonMatch)) {
                    $parsedContent = json_decode($jsonMatch[0], true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($parsedContent['has_task_intent'])) {
                        if ($parsedContent['has_task_intent'] === true) {
                            Log::info('Task intent detected (extracted from text)', ['task' => $parsedContent['task']]);
                            return $parsedContent['task'];
                        }
                    }
                } else {
                    Log::warning('parseTaskIntent: No JSON found in response', ['content' => $content]);
                }
            } else {
                Log::error('parseTaskIntent: API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
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
    public function parseTimetableIntent(string $message, array $conversationHistory = []): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        // Only use conversation history for SHORT/UNCLEAR messages
        // If message has clear timetable keywords, parse independently
        $hasTimeKeywords = preg_match('/(月曜|火曜|水曜|木曜|金曜|土曜|日曜|monday|tuesday|wednesday|thursday|friday|saturday|sunday)/iu', $message);
        $hasClassKeywords = preg_match('/(クラス|授業|class|lecture)/iu', $message);
        $messageLength = mb_strlen($message);

        // Use history ONLY if message is short (<15 chars) AND doesn't have clear keywords
        $useHistory = !empty($conversationHistory) && $messageLength < 15 && !($hasTimeKeywords && $hasClassKeywords);

        // Build conversation context if needed
        $contextText = '';
        if ($useHistory) {
            \Log::info('parseTimetableIntent: Using conversation history (short/unclear message)', [
                'message_count' => count($conversationHistory),
                'current_message' => $message,
                'message_length' => $messageLength
            ]);
            $contextText = "\n会話履歴:\n";
            // Get last 3 messages for context
            $recentHistory = array_slice($conversationHistory, -3);
            foreach ($recentHistory as $msg) {
                $role = $msg['role'] === 'user' ? 'ユーザー' : 'アシスタント';
                $contextText .= "{$role}: {$msg['content']}\n";
            }
            $contextText .= "\n";
            \Log::info('parseTimetableIntent: Context text', ['context' => $contextText]);
        } else {
            \Log::info('parseTimetableIntent: Parsing message independently (has clear keywords)', [
                'current_message' => $message,
                'message_length' => $messageLength,
                'has_time_keywords' => $hasTimeKeywords,
                'has_class_keywords' => $hasClassKeywords
            ]);
        }

        $prompt = "以下のメッセージ（と会話履歴）を分析して、**授業登録の意図があるか**判断してください。
授業登録の意図がある場合は、授業情報を抽出してJSONで返してください。
意図がない場合は、必ず false を返してください。
{$contextText}
現在のメッセージ: {$message}

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
- **確認・承認の返事** (会話履歴で授業登録について話していた場合): 「オケー追加して」「はい追加」「OK」「追加して」「いいです」「得ない」「được」
- ベトナム語: 「thêm lớp」「đăng ký lớp」「thêm lịch học」「thêm môn」「được, thêm đi」
- 英語: \"add class\", \"register class\", \"add to timetable\", \"ok add it\", \"yes add\"

**重要:** 会話履歴があり、直前のメッセージでアシスタントが授業登録を提案していた場合、確認の返事（「オケー」「はい」「OK」「追加して」など）は授業登録の意図とみなしてください。その場合、履歴から授業情報を抽出してください。

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

                \Log::info('parseTimetableIntent: AI response received', ['response' => $content]);

                // Parse JSON response
                $parsedContent = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    \Log::info('parseTimetableIntent: JSON parsed successfully', [
                        'has_intent' => $parsedContent['has_timetable_intent'] ?? false,
                        'parsed_data' => $parsedContent
                    ]);

                    if (!empty($parsedContent['has_timetable_intent']) && $parsedContent['has_timetable_intent'] === true) {
                        Log::info('Timetable intent detected', ['class' => $parsedContent['timetable_class']]);
                        return $parsedContent['timetable_class'];
                    } else {
                        \Log::info('parseTimetableIntent: No timetable intent in parsed response');
                    }
                } else {
                    \Log::warning('parseTimetableIntent: JSON parse error', ['error' => json_last_error_msg()]);
                }

                // Try to extract JSON from response
                $jsonMatch = [];
                if (preg_match('/\{.*\}/s', $content, $jsonMatch)) {
                    $parsedContent = json_decode($jsonMatch[0], true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($parsedContent['has_timetable_intent'])) {
                        if ($parsedContent['has_timetable_intent'] === true) {
                            \Log::info('parseTimetableIntent: Timetable intent found via regex extraction');
                            return $parsedContent['timetable_class'];
                        }
                    }
                }
            } else {
                \Log::error('parseTimetableIntent: API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Timetable intent parsing failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Parse knowledge query intent from user message
     * Detects when user is asking about their knowledge items
     *
     * @param string $message User message
     * @param array $conversationHistory Optional conversation context
     * @return array|null Knowledge query data if intent detected, null otherwise
     */
    public function parseKnowledgeQueryIntent(string $message, array $conversationHistory = []): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        // Build context if history provided
        $contextText = '';
        if (!empty($conversationHistory)) {
            $contextText = "\n会話履歴:\n";
            $recentHistory = array_slice($conversationHistory, -3);
            foreach ($recentHistory as $msg) {
                $role = $msg['role'] === 'user' ? 'ユーザー' : 'アシスタント';
                $contextText .= "{$role}: {$msg['content']}\n";
            }
            $contextText .= "\n";
        }

        $prompt = "以下のメッセージを分析して、**knowledge items（学習メモ・コード・演習問題）を検索する意図があるか**判断してください。
{$contextText}
現在のメッセージ: {$message}

Knowledge検索の意図がある場合のJSON形式:
{
  \"has_knowledge_query\": true,
  \"query\": {
    \"keywords\": [\"java\", \"list\"],
    \"item_type\": \"code_snippet|note|exercise|resource_link|attachment|any\",
    \"learning_path_id\": null,
    \"category_id\": null
  }
}

Knowledge検索の意図がない場合:
{
  \"has_knowledge_query\": false
}

**明確にKnowledge検索の意図があるキーワード:**
- 「〜について教えて」「〜を見せて」「〜を探して」
- 「〜のメモ」「〜のコード」「〜の演習問題」
- 「Java list ntn?」「Cách làm bubble sort?」
- 「Review lại exercises về sorting」
- 「〜を復習したい」「〜を確認したい」
- 「前に書いた〜」「保存した〜」

**Knowledge検索の意図がないもの (必ず false を返す):**
- 質問: 「〜とは何ですか？」「〜の方法を教えて」(新しい知識を求める質問)
- タスク作成: 「タスクを作成」「〜をやる」
- 雑談: 「こんにちは」「ありがとう」
- 一般的な会話

**重要な判断基準:**
1. ユーザーが**既存のknowledge items**を参照しようとしているか？
2. 新しい情報を求めるのではなく、**保存済みの情報**を探しているか？
3. 具体的なトピック・キーワードがあるか？

**例:**
❌ \"Javaのリストとは何ですか？\" → {\"has_knowledge_query\": false} (新しい知識を求める質問)
❌ \"タスクを作成\" → {\"has_knowledge_query\": false} (タスク作成)
✅ \"Java listのメモを見せて\" → {\"has_knowledge_query\": true, \"query\": {\"keywords\": [\"java\", \"list\"], \"item_type\": \"note\"}}
✅ \"binary searchのコード\" → {\"has_knowledge_query\": true, \"query\": {\"keywords\": [\"binary\", \"search\"], \"item_type\": \"code_snippet\"}}
✅ \"sortingの演習問題をreview\" → {\"has_knowledge_query\": true, \"query\": {\"keywords\": [\"sorting\"], \"item_type\": \"exercise\"}}

注意:
- keywords: 検索に使用するキーワードの配列
- item_type: 指定がない場合は \"any\" を返す
- learning_path_id, category_id: メッセージから明示的に指定されている場合のみ含める
- 疑わしい場合は false を返してください";

        try {
            $parseTimeout = min(10, $this->timeout * 0.33);
            $useMaxCompletionTokens = in_array($this->fallbackModel, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

            $requestBody = [
                'model' => $this->fallbackModel,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a knowledge query parser assistant. Analyze user messages and extract knowledge search intent. Always return valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
            ];

            // Local provider用: keep_aliveを追加
            $requestBody = $this->addKeepAlive($requestBody);

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

                Log::info('parseKnowledgeQueryIntent: AI response received', ['response' => $content]);

                // Parse JSON response
                $parsedContent = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    if (!empty($parsedContent['has_knowledge_query']) && $parsedContent['has_knowledge_query'] === true) {
                        Log::info('Knowledge query intent detected', ['query' => $parsedContent['query']]);
                        return $parsedContent['query'];
                    }
                }

                // Try to extract JSON from response
                $jsonMatch = [];
                if (preg_match('/\{.*\}/s', $content, $jsonMatch)) {
                    $parsedContent = json_decode($jsonMatch[0], true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($parsedContent['has_knowledge_query'])) {
                        if ($parsedContent['has_knowledge_query'] === true) {
                            return $parsedContent['query'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Knowledge query intent parsing failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Parse knowledge creation intent from user message
     * Detects when user wants to CREATE categories and knowledge items
     *
     * @param string $message User message
     * @param array $conversationHistory Optional conversation context
     * @param User $user The user (to check existing categories)
     * @return array|null Creation data if intent detected, null otherwise
     */
    public function parseKnowledgeCreationIntent(string $message, array $conversationHistory = [], $user = null): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        // Build context - include user's existing categories
        $existingCategories = [];
        if ($user) {
            $existingCategories = \App\Models\KnowledgeCategory::where('user_id', $user->id)
                ->select('id', 'name', 'parent_id', 'description')
                ->get()
                ->toArray();
        }

        $contextText = '';
        if (!empty($conversationHistory)) {
            $contextText = "\n会話履歴:\n";
            $recentHistory = array_slice($conversationHistory, -3);
            foreach ($recentHistory as $msg) {
                $role = $msg['role'] === 'user' ? 'ユーザー' : 'アシスタント';
                $contextText .= "{$role}: {$msg['content']}\n";
            }
        }

        // Build existing categories context
        $categoriesContext = '';
        if (!empty($existingCategories)) {
            $categoriesContext = "\n\n既存のフォルダ/カテゴリ:\n";
            foreach ($existingCategories as $cat) {
                $parentInfo = $cat['parent_id'] ? " (親: {$cat['parent_id']})" : '';
                $categoriesContext .= "- [{$cat['id']}] {$cat['name']}{$parentInfo}\n";
            }
        }

        $systemPrompt = "あなたは知識管理アシスタントです。
ユーザーのメッセージを分析して、knowledge folderやknowledge itemを作成する意図があるかを判断してください。

## 判定基準:
- 「作成」「追加」「保存」「記録」「フォルダ」「ノート」などのキーワード
- 具体的な技術/トピック名の言及 (例: JavaScript, Python, React)
- コードスニペット、メモ、演習問題などの言及

## 出力形式 (JSON):
{
    \"has_creation_intent\": true/false,
    \"action\": \"create\" | \"add_to_existing\",
    \"categories\": [
        {
            \"name\": \"カテゴリ名\",
            \"description\": \"説明 (自動生成)\",
            \"color\": \"#hex色コード (適切な色を選択)\",
            \"icon\": \"アイコン名 (技術に合ったもの)\",
            \"parent_id\": null or 既存カテゴリID
        }
    ],
    \"items\": [
        {
            \"title\": \"タイトル\",
            \"item_type\": \"note\" | \"code_snippet\" | \"exercise\" | \"resource_link\" | \"attachment\",
            \"content\": \"内容 (note/code_snippet用)\",
            \"code_language\": \"言語 (code_snippet用)\",
            \"url\": \"URL (resource_link用)\",
            \"question\": \"問題文 (exercise用)\",
            \"answer\": \"解答 (exercise用)\",
            \"difficulty\": \"easy\" | \"medium\" | \"hard\",
            \"tags\": [\"tag1\", \"tag2\"],
            \"category_name\": \"所属カテゴリ名\"
        }
    ],
    \"ai_explanation\": \"実行内容の説明\"
}

## 重要:
- item_typeを正しく判定 (コード→code_snippet, メモ→note, 問題→exercise, リンク→resource_link)
- code_snippetの場合、code_languageを必ず設定
- tagsは関連キーワードから自動抽出
- 既存カテゴリがあれば再利用 (parent_idを設定)
- ユーザーが具体的な内容を提供していない場合、サンプル/テンプレートを生成
{$categoriesContext}";

        $userPrompt = "ユーザーのメッセージ: {$message}{$contextText}";

        try {
            // Use fallback model for faster parsing (like parseKnowledgeQueryIntent)
            $modelToUse = $this->fallbackModel;
            $parseTimeout = 60; // Increased timeout for knowledge creation parsing

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ];

            // Determine if we should use max_completion_tokens (for o1/o3/gpt-5 models)
            $useMaxCompletionTokens = in_array($modelToUse, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

            $requestBody = [
                'model' => $modelToUse,
                'messages' => $messages,
                'temperature' => 0.3, // Lower temperature for structured parsing
            ];

            if ($useMaxCompletionTokens) {
                $requestBody['max_completion_tokens'] = 2000;
            } else {
                $requestBody['max_tokens'] = 2000;
            }

            // Add response format for JSON mode if using gpt-4o or later
            if (str_contains($modelToUse, 'gpt-4') || str_contains($modelToUse, 'gpt-5')) {
                $requestBody['response_format'] = ['type' => 'json_object'];
            }

            // Local provider用: keep_aliveを追加
            $requestBody = $this->addKeepAlive($requestBody);

            Log::info('parseKnowledgeCreationIntent: Sending request', [
                'model' => $modelToUse,
                'message' => $message
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout((int)$parseTimeout)->post($this->baseUrl . '/chat/completions', $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                Log::info('parseKnowledgeCreationIntent: AI response', ['response' => $content]);

                $parsedContent = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    if (!empty($parsedContent['has_creation_intent']) && $parsedContent['has_creation_intent'] === true) {
                        Log::info('Knowledge creation intent detected', ['data' => $parsedContent]);
                        return $parsedContent;
                    }
                }
            } else {
                Log::error('parseKnowledgeCreationIntent: API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('parseKnowledgeCreationIntent: Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
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

        $maxRetries = 1; // Reduce retries for faster response
        $retryDelay = 0.5;
        $models = [$this->model];

        if ($this->enableFallback && $this->fallbackModel !== $this->model) {
            $models[] = $this->fallbackModel;
        }

        foreach ($models as $model) {
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    // Local provider用: システムプロンプトを簡略化（速度向上のため）
                    $systemPrompt = $options['system_prompt'] ?? ($this->isLocalProvider
                        ? '日本語で簡潔に応答。' // 最短のシステムプロンプト
                        : 'You are a helpful productivity assistant. Always respond in Japanese in a friendly and encouraging manner.');

                    // Prepare messages array
                    $apiMessages = [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ]
                    ];

                    // Add conversation history (Local provider用: 制限を強化して速度向上)
                    $historyLimit = $this->isLocalProvider ? 2 : count($messages); // 4→2に削減
                    $limitedMessages = array_slice($messages, -$historyLimit);

                    foreach ($limitedMessages as $msg) {
                        // Local provider用: 長いメッセージを短縮（速度向上のため）
                        $content = $msg['content'];
                        if ($this->isLocalProvider && mb_strlen($content) > 300) { // 500→300に削減
                            $content = mb_substr($content, 0, 300) . '...';
                        }
                        $apiMessages[] = [
                            'role' => $msg['role'],
                            'content' => $content
                        ];
                    }

                    // Chat timeout: Local provider用に長めに設定（タイムアウトを延長）
                    $chatTimeout = $options['timeout'] ?? ($this->isLocalProvider
                        ? max(180, $this->timeout * 1.5) // Local: 最小180秒（3分）に延長
                        : $this->timeout * 0.5); // Cloud: 50%

                    // Determine which parameter to use based on model
                    $useMaxCompletionTokens = in_array($model, ['gpt-5', 'o1', 'o1-preview', 'o1-mini']);

                    // Set appropriate max_tokens based on model and use case
                    if ($useMaxCompletionTokens) {
                        $maxTokensValue = $options['max_tokens'] ?? 16000;
                    } else {
                        // Local provider用: トークン数を大幅に削減して高速化（300→200に削減）
                        $defaultMaxTokens = $this->isLocalProvider ? 200 : 2000;
                        $maxTokensValue = $options['max_tokens'] ?? $defaultMaxTokens;
                    }

                    $requestBody = [
                        'model' => $model,
                        'messages' => $apiMessages,
                        'stream' => false,
                        'keep_alive' => '30m', // モデルをメモリに30分保持（load_duration削減）
                    ];

                    // Temperature support varies by model
                    $noTemperatureModels = ['gpt-5', 'o1', 'o1-preview', 'o1-mini'];
                    if (!in_array($model, $noTemperatureModels)) {
                        // Local provider用: 低いtemperatureで高速化・安定性向上
                        $requestBody['temperature'] = $options['temperature'] ?? ($this->isLocalProvider ? 0.3 : 0.7);
                    }

                    // Use appropriate parameter based on model
                    if ($useMaxCompletionTokens) {
                        $requestBody['max_completion_tokens'] = $maxTokensValue;
                    } else {
                        $requestBody['max_tokens'] = $maxTokensValue;
                    }

                    Log::info('AI Chat: Sending request', [
                        'model' => $model,
                        'timeout' => $chatTimeout,
                        'max_tokens' => $maxTokensValue,
                        'is_local' => $this->isLocalProvider,
                        'message_count' => count($apiMessages)
                    ]);

                    try {
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
                                break;
                            }
                        }
                    } catch (\Illuminate\Http\Client\ConnectionException $e) {
                        Log::error('AI Chat: Connection timeout or failed', [
                            'error' => $e->getMessage(),
                            'timeout' => $chatTimeout,
                            'model' => $model,
                            'base_url' => $this->baseUrl,
                            'attempt' => $attempt
                        ]);
                    } catch (\Exception $e) {
                        Log::error('AI Chat: Request exception', [
                            'error' => $e->getMessage(),
                            'model' => $model,
                            'base_url' => $this->baseUrl,
                            'attempt' => $attempt
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
                    usleep((int)($retryDelay * 1000000));
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
     * Chat with AI using streaming (Server-Sent Events)
     * Yields chunks of response as they are generated
     *
     * @param array $messages Array of messages in format: [['role' => 'user/assistant', 'content' => 'message']]
     * @param array $options Additional options for the API call
     * @return \Generator Yields arrays with 'content', 'done', 'error' keys
     */
    public function chatStream(array $messages, array $options = []): \Generator
    {
        if (!$this->apiKey) {
            yield [
                'content' => 'AI service is currently unavailable. Please try again later.',
                'done' => true,
                'error' => true
            ];
            return;
        }

        $model = $this->model;
        $systemPrompt = $options['system_prompt'] ?? ($this->isLocalProvider
            ? '日本語で簡潔に応答してください。'
            : 'You are a helpful productivity assistant. Always respond in Japanese in a friendly and encouraging manner.');

        // Prepare messages array
        $apiMessages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];

        // Add conversation history (Local provider用: 制限)
        $historyLimit = $this->isLocalProvider ? 4 : count($messages);
        $limitedMessages = array_slice($messages, -$historyLimit);

        foreach ($limitedMessages as $msg) {
            // Local provider用: 長いメッセージを短縮
            $content = $msg['content'];
            if ($this->isLocalProvider && mb_strlen($content) > 500) {
                $content = mb_substr($content, 0, 500) . '...';
            }
            $apiMessages[] = [
                'role' => $msg['role'],
                'content' => $content
            ];
        }

        // Chat timeout: Local provider用に長めに設定
        $chatTimeout = $options['timeout'] ?? ($this->isLocalProvider
            ? max(90, $this->timeout * 0.75)
            : $this->timeout * 0.5);

        // Local provider用: トークン数を大幅に削減して高速化
        $defaultMaxTokens = $this->isLocalProvider ? 300 : 2000;
        $maxTokensValue = $options['max_tokens'] ?? $defaultMaxTokens;

        $requestBody = [
            'model' => $model,
            'messages' => $apiMessages,
            'stream' => true, // ストリーミングを有効化
            'keep_alive' => '30m', // モデルをメモリに30分保持（load_duration削減）
        ];

        // Temperature support varies by model
        $noTemperatureModels = ['gpt-5', 'o1', 'o1-preview', 'o1-mini'];
        if (!in_array($model, $noTemperatureModels)) {
            $requestBody['temperature'] = $options['temperature'] ?? ($this->isLocalProvider ? 0.3 : 0.7);
        }

        $requestBody['max_tokens'] = $maxTokensValue;

        try {
            // Guzzleクライアントを使用してストリーミングリクエストを送信
            $client = new Client([
                'timeout' => (int)$chatTimeout,
                'stream' => true,
            ]);

            $response = $client->post($this->baseUrl . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestBody,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                // エラーレスポンスの読み取り（ストリーミングではない）
                $errorBody = $response->getBody()->getContents();
                $errorData = json_decode($errorBody, true);
                $errorMessage = $errorData['error']['message'] ?? $errorBody ?? 'Unknown error';
                yield [
                    'content' => 'エラー: ' . $errorMessage,
                    'done' => true,
                    'error' => true
                ];
                return;
            }

            // ストリーミングレスポンスを処理
            $body = $response->getBody();
            $fullContent = '';
            $buffer = '';

            // ストリーミングデータを読み取る
            while (!$body->eof()) {
                $chunk = $body->read(8192); // 8KBずつ読み取る
                if ($chunk === false || $chunk === '') {
                    // 少し待ってから再試行（データがまだ来る可能性がある）
                    usleep(100000); // 100ms待機
                    if ($body->eof()) {
                        break;
                    }
                    continue;
                }

                $buffer .= $chunk;
                $lines = explode("\n", $buffer);

                // 最後の行は完全でない可能性があるため、バッファに残す
                $buffer = array_pop($lines) ?? '';

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) {
                        continue;
                    }

                    // Server-Sent Events形式: "data: {...}"
                    if (!str_starts_with($line, 'data: ')) {
                        continue;
                    }

                    $data = substr($line, 6); // "data: " を削除
                    if ($data === '[DONE]') {
                        yield [
                            'content' => '',
                            'done' => true,
                            'error' => false,
                            'full_message' => $fullContent
                        ];
                        return;
                    }

                    $json = json_decode($data, true);
                    if (!$json) {
                        continue;
                    }

                    // Ollama/OpenAI互換形式の処理
                    $content = null;
                    if (isset($json['choices'][0]['delta']['content'])) {
                        $content = $json['choices'][0]['delta']['content'];
                    } elseif (isset($json['message']['content'])) {
                        $content = $json['message']['content'];
                    } elseif (isset($json['choices'][0]['message']['content'])) {
                        // 一部の実装ではmessageに直接contentがある場合がある
                        $content = $json['choices'][0]['message']['content'];
                    }

                    if ($content !== null && $content !== '') {
                        $fullContent .= $content;
                        yield [
                            'content' => $content,
                            'done' => false,
                            'error' => false
                        ];
                    }
                }
            }

            // 残りのバッファを処理
            if (!empty($buffer)) {
                $line = trim($buffer);
                if (str_starts_with($line, 'data: ')) {
                    $data = substr($line, 6);
                    if ($data !== '[DONE]') {
                        $json = json_decode($data, true);
                        if ($json) {
                            $content = $json['choices'][0]['delta']['content']
                                ?? $json['message']['content']
                                ?? $json['choices'][0]['message']['content']
                                ?? null;
                            if ($content) {
                                $fullContent .= $content;
                                yield [
                                    'content' => $content,
                                    'done' => false,
                                    'error' => false
                                ];
                            }
                        }
                    }
                }
            }

            // 最終的な完全なメッセージを返す
            yield [
                'content' => '',
                'done' => true,
                'error' => false,
                'full_message' => $fullContent
            ];

        } catch (RequestException $e) {
            Log::error('AI Chat Stream: Request failed', [
                'error' => $e->getMessage(),
                'model' => $model
            ]);

            yield [
                'content' => '申し訳ございません。AIサービスの接続に失敗しました。',
                'done' => true,
                'error' => true
            ];
        } catch (\Exception $e) {
            Log::error('AI Chat Stream: API call failed', [
                'error' => $e->getMessage(),
                'model' => $model
            ]);

            yield [
                'content' => '申し訳ございません。AIサービスの接続に失敗しました。',
                'done' => true,
                'error' => true
            ];
        }
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
        $knowledgeItems = $context['knowledge_items'] ?? [];

        $tasksInfo = $this->formatTasksInfo($tasks);
        $scheduleInfo = $this->formatScheduleInfo($timetable);
        $freeTimeAnalysis = $this->analyzeFreeTime($timetable, $tasks);
        $deadlineWarnings = $this->analyzeDeadlines($tasks);
        $knowledgeInfo = $this->formatKnowledgeItems($knowledgeItems);

        // NEW: Enhanced context analysis
        $priorityAnalysis = $this->analyzePriorityTasks($tasks);
        $timeGapAnalysis = $this->analyzeTimeGaps($timetable, $tasks);
        $productivityInsights = $this->getProductivityInsights($tasks);

        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i');

        return "あなたは親切で有能な生産性アシスタントです。日本語で応答してください。

現在: {$today} {$currentTime}

{$tasksInfo}
{$scheduleInfo}
{$knowledgeInfo}
{$priorityAnalysis}
{$timeGapAnalysis}
{$productivityInsights}
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
     * Format knowledge items information for AI context
     *
     * @param array $knowledgeItems User's knowledge items (searched results)
     * @return string Formatted knowledge info
     */
    private function formatKnowledgeItems(array $knowledgeItems): string
    {
        if (empty($knowledgeItems)) {
            return "";
        }

        $info = "## 📚 検索された Knowledge Items\n";
        $info .= "ユーザーの質問に関連する以下の保存済みアイテムが見つかりました:\n\n";

        $itemCount = 0;
        foreach ($knowledgeItems as $item) {
            if ($itemCount >= 5) { // Limit to 5 items to avoid token limit
                $info .= "... 他" . (count($knowledgeItems) - 5) . "個のアイテム\n";
                break;
            }

            $title = $item['title'] ?? 'No title';
            $type = $item['type'] ?? 'unknown';
            $category = $item['category'] ?? '';
            $tags = $item['tags'] ?? [];

            // Type emoji mapping
            $typeEmoji = [
                'note' => '📝',
                'code_snippet' => '💻',
                'exercise' => '✏️',
                'resource_link' => '🔗',
                'attachment' => '📎',
            ];
            $emoji = $typeEmoji[$type] ?? '📄';

            $info .= "### {$emoji} {$title}\n";
            $info .= "- **Type**: {$type}\n";

            if ($category) {
                $info .= "- **Category**: {$category}\n";
            }

            if (!empty($tags)) {
                $info .= "- **Tags**: " . implode(', ', array_slice($tags, 0, 5)) . "\n";
            }

            // Add content based on type
            if ($type === 'code_snippet' && !empty($item['content'])) {
                $lang = $item['code_language'] ?? 'plaintext';
                $content = $item['content'];
                $info .= "- **Code** ({$lang}):\n```{$lang}\n{$content}\n```\n";
            } elseif ($type === 'note' && !empty($item['content'])) {
                $info .= "- **Content**: " . substr($item['content'], 0, 300) . "...\n";
            } elseif ($type === 'exercise' && !empty($item['question'])) {
                $info .= "- **Question**: {$item['question']}\n";
                if (!empty($item['answer'])) {
                    $info .= "- **Answer**: " . substr($item['answer'], 0, 200) . "...\n";
                }
            } elseif ($type === 'resource_link' && !empty($item['url'])) {
                $info .= "- **URL**: {$item['url']}\n";
            }

            if (!empty($item['last_reviewed'])) {
                $info .= "- **Last reviewed**: {$item['last_reviewed']}\n";
            }

            $info .= "\n";
            $itemCount++;
        }

        $info .= "\n**重要**: これらはユーザーが以前保存したknowledge itemsです。ユーザーの質問に答える際は、これらの情報を活用してください。\n";

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

    /**
     * Analyze priority tasks
     * Highlights high-priority tasks that need attention
     *
     * @param array $tasks User's tasks
     * @return string Priority analysis
     */
    private function analyzePriorityTasks(array $tasks): string
    {
        if (empty($tasks)) {
            return "";
        }

        // Filter high priority tasks (priority >= 4) that are not completed
        $highPriorityTasks = array_filter($tasks, function($task) {
            $priority = $task['priority'] ?? 3;
            $status = $task['status'] ?? 'pending';
            return $priority >= 4 && !in_array($status, ['completed', 'cancelled']);
        });

        if (empty($highPriorityTasks)) {
            return "";
        }

        $analysis = "## ⭐ 優先タスク\n";
        $analysis .= "現在、" . count($highPriorityTasks) . "個の高優先度タスクがあります:\n\n";

        $count = 0;
        foreach ($highPriorityTasks as $task) {
            if ($count >= 5) { // Limit to 5 tasks
                $remaining = count($highPriorityTasks) - 5;
                $analysis .= "... 他{$remaining}個の高優先度タスク\n";
                break;
            }

            $title = $task['title'] ?? 'No title';
            $priority = $task['priority'] ?? 3;
            $status = $task['status'] ?? 'pending';
            $deadline = $task['deadline'] ?? null;
            $scheduled = $task['scheduled_time'] ?? null;

            $priorityEmoji = $priority >= 5 ? '🔴' : '🟠';
            $analysis .= "{$priorityEmoji} **{$title}**";

            $details = [];
            if ($deadline) {
                try {
                    $deadlineDate = new \DateTime($deadline);
                    $daysLeft = now()->diffInDays($deadlineDate, false);
                    if ($daysLeft < 0) {
                        $details[] = "期限切れ";
                    } elseif ($daysLeft == 0) {
                        $details[] = "今日期限";
                    } elseif ($daysLeft == 1) {
                        $details[] = "明日期限";
                    } else {
                        $details[] = "期限: {$daysLeft}日後";
                    }
                } catch (\Exception $e) {
                    // Skip
                }
            }

            if ($status === 'in_progress') {
                $details[] = "進行中";
            } elseif ($scheduled) {
                $details[] = "予定済み";
            } else {
                $details[] = "未スケジュール";
            }

            if (!empty($details)) {
                $analysis .= " (" . implode(', ', $details) . ")";
            }

            $analysis .= "\n";
            $count++;
        }

        $analysis .= "\n💡 これらの高優先度タスクに注目してください。\n";

        return $analysis;
    }

    /**
     * Analyze time gaps and scheduling opportunities
     * Suggests when to schedule tasks based on available time
     *
     * @param array $timetable User's timetable
     * @param array $tasks User's tasks
     * @return string Time gap analysis
     */
    private function analyzeTimeGaps(array $timetable, array $tasks): string
    {
        if (empty($timetable) && empty($tasks)) {
            return "";
        }

        $analysis = "## 📅 スケジューリング機会\n";

        // Count unscheduled tasks
        $unscheduledTasks = array_filter($tasks, function($task) {
            $status = $task['status'] ?? 'pending';
            $scheduled = $task['scheduled_time'] ?? null;
            return !in_array($status, ['completed', 'cancelled']) && empty($scheduled);
        });

        $unscheduledCount = count($unscheduledTasks);

        if ($unscheduledCount > 0) {
            $analysis .= "- **未スケジュールタスク**: {$unscheduledCount}個\n";

            // Calculate total estimated time needed
            $totalMinutes = 0;
            foreach ($unscheduledTasks as $task) {
                $totalMinutes += $task['estimated_minutes'] ?? 0;
            }

            if ($totalMinutes > 0) {
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $timeStr = $hours > 0 ? "{$hours}時間{$minutes}分" : "{$minutes}分";
                $analysis .= "- **必要な時間**: 約{$timeStr}\n";
            }

            // Analyze current time
            $currentHour = (int)now()->format('H');
            $currentDay = now()->format('l'); // Day name

            // Suggest based on time of day
            if ($currentHour >= 8 && $currentHour < 12) {
                $analysis .= "- 💡 **今がチャンス**: 午前中は集中力が高い時間です。重要なタスクを始めましょう\n";
            } elseif ($currentHour >= 12 && $currentHour < 14) {
                $analysis .= "- 💡 **ランチタイム後**: 軽めのタスクから始めて、徐々にペースを上げましょう\n";
            } elseif ($currentHour >= 14 && $currentHour < 18) {
                $analysis .= "- 💡 **午後の作業時間**: 生産的な時間帯です。タスクを進めましょう\n";
            } elseif ($currentHour >= 18 && $currentHour < 22) {
                $analysis .= "- 💡 **夕方の時間**: 軽めのタスクや復習に適しています\n";
            } else {
                $analysis .= "- 💡 休息も大切です。明日のために計画を立てましょう\n";
            }

            // Count timetable classes today
            $todayClasses = 0;
            if (isset($timetable[$currentDay])) {
                $todayClasses = count($timetable[$currentDay]);
            }

            if ($todayClasses > 0) {
                $analysis .= "- 📚 今日の授業: {$todayClasses}コマ\n";
                $analysis .= "- 授業の合間や終了後に短いタスクを入れると効率的です\n";
            } else {
                $analysis .= "- 📚 今日は授業がありません。計画的にタスクを進められます\n";
            }
        } else {
            $analysis .= "- ✅ すべてのタスクがスケジュール済みです！\n";
            $analysis .= "- 予定通りに進めましょう\n";
        }

        return $analysis;
    }

    /**
     * Get productivity insights
     * Analyzes user's task completion patterns and provides insights
     *
     * @param array $tasks User's tasks
     * @return string Productivity insights
     */
    private function getProductivityInsights(array $tasks): string
    {
        if (empty($tasks)) {
            return "";
        }

        $analysis = "## 📊 生産性インサイト\n";

        // Calculate statistics
        $totalTasks = count($tasks);
        $completedTasks = array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'completed');
        $inProgressTasks = array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'in_progress');
        $pendingTasks = array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'pending');

        $completedCount = count($completedTasks);
        $inProgressCount = count($inProgressTasks);
        $pendingCount = count($pendingTasks);

        // Completion rate
        if ($totalTasks > 0) {
            $completionRate = round(($completedCount / $totalTasks) * 100);
            $analysis .= "- **完了率**: {$completionRate}% ({$completedCount}/{$totalTasks})\n";

            if ($completionRate >= 70) {
                $analysis .= "  - 🎉 素晴らしい進捗です！この調子で続けましょう\n";
            } elseif ($completionRate >= 40) {
                $analysis .= "  - 👍 良いペースです。少しずつ進めていきましょう\n";
            } else {
                $analysis .= "  - 💪 まずは小さなタスクから完了させていきましょう\n";
            }
        }

        // Task distribution
        $analysis .= "- **タスク分布**:\n";
        $analysis .= "  - ✅ 完了: {$completedCount}個\n";
        $analysis .= "  - 🔄 進行中: {$inProgressCount}個\n";
        $analysis .= "  - 📝 保留中: {$pendingCount}個\n";

        // Priority distribution (excluding completed/cancelled)
        $activeTasks = array_filter($tasks, fn($t) => !in_array($t['status'] ?? '', ['completed', 'cancelled']));
        if (!empty($activeTasks)) {
            $highPriority = array_filter($activeTasks, fn($t) => ($t['priority'] ?? 3) >= 4);
            $mediumPriority = array_filter($activeTasks, fn($t) => ($t['priority'] ?? 3) == 3);
            $lowPriority = array_filter($activeTasks, fn($t) => ($t['priority'] ?? 3) <= 2);

            $analysis .= "- **優先度分布** (アクティブタスク):\n";
            $analysis .= "  - 🔴 高: " . count($highPriority) . "個\n";
            $analysis .= "  - 🟡 中: " . count($mediumPriority) . "個\n";
            $analysis .= "  - 🟢 低: " . count($lowPriority) . "個\n";

            if (count($highPriority) > 5) {
                $analysis .= "  - ⚠️ 高優先度タスクが多いです。焦点を絞りましょう\n";
            }
        }

        // Time-based insights
        $tasksWithEstimate = array_filter($tasks, fn($t) => !empty($t['estimated_minutes']));
        if (!empty($tasksWithEstimate)) {
            $totalEstimatedMinutes = array_sum(array_column($tasksWithEstimate, 'estimated_minutes'));
            $hours = floor($totalEstimatedMinutes / 60);
            $minutes = $totalEstimatedMinutes % 60;
            $timeStr = $hours > 0 ? "{$hours}時間{$minutes}分" : "{$minutes}分";

            $analysis .= "- **推定作業時間**: 約{$timeStr}\n";

            // Suggest time management
            if ($totalEstimatedMinutes > 480) { // > 8 hours
                $analysis .= "  - 💡 作業量が多いです。タスクを数日に分散させることをお勧めします\n";
            } elseif ($totalEstimatedMinutes > 240) { // > 4 hours
                $analysis .= "  - 💡 集中力を保つため、休憩を挟みながら進めましょう\n";
            }
        }

        return $analysis;
    }
}
