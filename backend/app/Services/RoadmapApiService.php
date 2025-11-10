<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * RoadmapApiService
 * External API integration cho IT learning roadmaps
 * 外部API統合サービス - IT学習ロードマップ用
 */
class RoadmapApiService
{
    private $cacheTimeout = 3600; // 1 hour

    /**
     * Fetch roadmap from Microsoft Learn Catalog API
     * Microsoft LearnカタログAPIからロードマップを取得
     */
    public function fetchMicrosoftLearnRoadmap(string $topic = 'developer'): array
    {
        try {
            $cacheKey = "microsoft_learn_roadmap_{$topic}";

            return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($topic) {
                // Microsoft Learn Catalog API endpoint
                $url = "https://learn.microsoft.com/api/catalog/";

                $response = Http::timeout(30)->get($url, [
                    'locale' => 'ja-jp',
                    'topic' => $topic,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->convertMicrosoftLearnToRoadmap($data);
                }

                Log::warning('Microsoft Learn API failed', [
                    'status' => $response->status(),
                    'topic' => $topic
                ]);

                return [];
            });
        } catch (\Exception $e) {
            Log::error('Microsoft Learn API error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate roadmap using AI (OpenAI)
     * AIを使用してロードマップを生成
     */
    public function generateRoadmapWithAI(string $topic, string $level = 'beginner'): array
    {
        try {
            $aiService = app(AIService::class);

            $prompt = "以下のトピックの学習ロードマップをJSON形式で作成してください：

トピック: {$topic}
レベル: {$level}

**重要**: 各タスクには必ず以下の要素を含めてください：
1. **subtasks**: 少なくとも3つのサブタスク（基礎学習、実践、復習など）
2. **knowledge_items**: 少なくとも2つのナレッジアイテム（note、resource_link、code_snippetなど）

以下の形式で返してください：
{
  \"title\": \"ロードマップタイトル\",
  \"description\": \"説明\",
  \"estimated_hours\": 100,
  \"milestones\": [
    {
      \"title\": \"マイルストーン1\",
      \"description\": \"説明\",
      \"sort_order\": 1,
      \"estimated_hours\": 20,
      \"tasks\": [
        {
          \"title\": \"タスク1\",
          \"description\": \"詳細な説明\",
          \"estimated_minutes\": 120,
          \"priority\": 3,
          \"subtasks\": [
            {
              \"title\": \"サブタスク1: 基礎を学習\",
              \"description\": \"基本概念と理論を理解する\",
              \"estimated_minutes\": 40,
              \"sort_order\": 1
            },
            {
              \"title\": \"サブタスク2: 実践\",
              \"description\": \"実際にコードを書いて練習する\",
              \"estimated_minutes\": 40,
              \"sort_order\": 2
            },
            {
              \"title\": \"サブタスク3: 復習\",
              \"description\": \"学習内容を確認し、理解を深める\",
              \"estimated_minutes\": 40,
              \"sort_order\": 3
            }
          ],
          \"knowledge_items\": [
            {
              \"type\": \"note\",
              \"title\": \"学習メモ\",
              \"content\": \"重要なポイントや注意事項\",
              \"sort_order\": 1
            },
            {
              \"type\": \"resource_link\",
              \"title\": \"公式ドキュメント\",
              \"url\": \"https://example.com/docs\",
              \"description\": \"公式リソースへのリンク\",
              \"sort_order\": 2
            }
          ]
        }
      ]
    }
  ]
}

**注意**:
- 各タスクには必ずsubtasksとknowledge_itemsを含めること
- subtasksは少なくとも3つ、knowledge_itemsは少なくとも2つ作成すること
- JSONのみを返し、説明文は含めないこと";

            $response = $aiService->chat([
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ], [
                'max_tokens' => 2000,
                'temperature' => 0.7,
            ]);

            if (!empty($response['error'])) {
                Log::warning('AI roadmap generation failed', [
                    'topic' => $topic,
                    'message' => $response['message']
                ]);
                return [];
            }

            $content = $response['message'] ?? '';

            // Extract JSON from response
            $jsonMatch = [];
            if (preg_match('/\{.*\}/s', $content, $jsonMatch)) {
                $roadmapData = json_decode($jsonMatch[0], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $roadmapData;
                }
            }

            Log::warning('Failed to parse AI roadmap response', [
                'topic' => $topic,
                'content' => substr($content, 0, 200)
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('AI roadmap generation error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get popular IT roadmaps (curated list)
     * 人気のITロードマップを取得（キュレーションリスト）
     */
    public function getPopularRoadmaps(): array
    {
        return [
            [
                'id' => 'frontend',
                'title' => 'Frontend Developer Roadmap',
                'description' => 'モダンなフロントエンド開発者のロードマップ',
                'category' => 'programming',
                'difficulty' => 'beginner',
                'estimated_hours' => 200,
                'source' => 'roadmap.sh',
                'url' => 'https://roadmap.sh/frontend',
            ],
            [
                'id' => 'backend',
                'title' => 'Backend Developer Roadmap',
                'description' => 'バックエンド開発者の包括的なロードマップ',
                'category' => 'programming',
                'difficulty' => 'intermediate',
                'estimated_hours' => 300,
                'source' => 'roadmap.sh',
                'url' => 'https://roadmap.sh/backend',
            ],
            [
                'id' => 'devops',
                'title' => 'DevOps Roadmap',
                'description' => 'DevOpsエンジニアになるためのロードマップ',
                'category' => 'programming',
                'difficulty' => 'intermediate',
                'estimated_hours' => 250,
                'source' => 'roadmap.sh',
                'url' => 'https://roadmap.sh/devops',
            ],
            [
                'id' => 'fullstack',
                'title' => 'Full Stack Developer Roadmap',
                'description' => 'フルスタック開発者のロードマップ',
                'category' => 'programming',
                'difficulty' => 'advanced',
                'estimated_hours' => 500,
                'source' => 'roadmap.sh',
                'url' => 'https://roadmap.sh/full-stack',
            ],
            [
                'id' => 'android',
                'title' => 'Android Developer Roadmap',
                'description' => 'Androidアプリ開発者のロードマップ',
                'category' => 'programming',
                'difficulty' => 'intermediate',
                'estimated_hours' => 300,
                'source' => 'roadmap.sh',
                'url' => 'https://roadmap.sh/android',
            ],
            [
                'id' => 'java',
                'title' => 'Java Developer Roadmap',
                'description' => 'Java開発者のロードマップ',
                'category' => 'programming',
                'difficulty' => 'intermediate',
                'estimated_hours' => 400,
                'source' => 'roadmap.sh',
                'url' => 'https://roadmap.sh/java',
            ],
        ];
    }

    /**
     * Convert Microsoft Learn data to roadmap format
     */
    private function convertMicrosoftLearnToRoadmap(array $data): array
    {
        // Convert Microsoft Learn catalog format to our roadmap format
        // This is a simplified conversion - adjust based on actual API response
        return [
            'title' => $data['title'] ?? 'Microsoft Learn Course',
            'description' => $data['description'] ?? '',
            'category' => 'programming',
            'difficulty' => $this->mapDifficulty($data['level'] ?? 'beginner'),
            'estimated_hours' => $data['duration'] ?? 0,
            'source' => 'microsoft_learn',
            'url' => $data['url'] ?? '',
            'milestones' => $this->extractMilestones($data),
        ];
    }

    /**
     * Map Microsoft Learn difficulty to our format
     */
    private function mapDifficulty(string $level): string
    {
        $mapping = [
            'beginner' => 'beginner',
            'intermediate' => 'intermediate',
            'advanced' => 'advanced',
        ];

        return $mapping[strtolower($level)] ?? 'beginner';
    }

    /**
     * Extract milestones from Microsoft Learn data
     */
    private function extractMilestones(array $data): array
    {
        // Extract modules/units as milestones
        // Adjust based on actual API structure
        $milestones = [];

        if (isset($data['modules']) && is_array($data['modules'])) {
            foreach ($data['modules'] as $index => $module) {
                $milestones[] = [
                    'title' => $module['title'] ?? "Module " . ($index + 1),
                    'description' => $module['description'] ?? '',
                    'sort_order' => $index + 1,
                    'estimated_hours' => $module['duration'] ?? 0,
                    'tasks' => $this->extractTasks($module),
                ];
            }
        }

        return $milestones;
    }

    /**
     * Extract tasks from module data
     */
    private function extractTasks(array $module): array
    {
        $tasks = [];

        if (isset($module['units']) && is_array($module['units'])) {
            foreach ($module['units'] as $index => $unit) {
                $tasks[] = [
                    'title' => $unit['title'] ?? "Unit " . ($index + 1),
                    'description' => $unit['description'] ?? '',
                    'estimated_minutes' => ($unit['duration'] ?? 0) * 60,
                    'priority' => 3,
                    'knowledge_items' => [
                        [
                            'type' => 'resource_link',
                            'title' => $unit['title'] ?? '',
                            'url' => $unit['url'] ?? '',
                            'description' => $unit['description'] ?? '',
                        ]
                    ],
                ];
            }
        }

        return $tasks;
    }
}

