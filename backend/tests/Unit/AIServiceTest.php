<?php

namespace Tests\Unit;

use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIServiceTest extends TestCase
{
    use RefreshDatabase;

    private AIService $aiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aiService = new AIService();
    }

    /** @test */
    public function it_can_breakdown_task()
    {
        // Mock OpenAI API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                [
                                    'title' => 'Setup Laravel project',
                                    'estimated_minutes' => 30
                                ],
                                [
                                    'title' => 'Create API endpoints',
                                    'estimated_minutes' => 60
                                ]
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->aiService->breakdownTask(
            'Build Laravel API',
            'Create a REST API with Laravel',
            'medium'
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('estimated_minutes', $result[0]);
    }

    /** @test */
    public function it_returns_fallback_when_api_fails()
    {
        // Mock failed API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([], 500)
        ]);

        $result = $this->aiService->breakdownTask(
            'Test Task',
            'Test Description',
            'simple'
        );

        $this->assertIsArray($result);
        $this->assertCount(3, $result); // Fallback breakdown has 3 items
    }

    /** @test */
    public function it_can_generate_daily_suggestions()
    {
        // Mock OpenAI API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                [
                                    'title' => 'Complete pending tasks',
                                    'description' => 'Finish the tasks from yesterday',
                                    'priority' => 'high',
                                    'estimated_time' => '60分'
                                ]
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $recentTasks = [
            ['title' => 'Task 1', 'status' => 'pending'],
            ['title' => 'Task 2', 'status' => 'in_progress']
        ];

        $completedTasks = [
            ['title' => 'Completed Task 1'],
            ['title' => 'Completed Task 2']
        ];

        $result = $this->aiService->generateDailySuggestions($recentTasks, $completedTasks);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('description', $result[0]);
    }

    /** @test */
    public function it_can_generate_daily_summary()
    {
        // Mock OpenAI API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'achievements' => ['Completed 3 tasks'],
                                'insights' => ['Morning was most productive'],
                                'recommendations' => ['Continue morning routine'],
                                'mood' => 'good',
                                'productivity_score' => 85
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $tasks = [
            ['title' => 'Task 1', 'status' => 'completed'],
            ['title' => 'Task 2', 'status' => 'completed']
        ];

        $sessions = [
            ['actual_minutes' => 25, 'session_type' => 'work'],
            ['actual_minutes' => 5, 'session_type' => 'break']
        ];

        $result = $this->aiService->generateDailySummary($tasks, $sessions, '2025-01-02');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('achievements', $result);
        $this->assertArrayHasKey('insights', $result);
        $this->assertArrayHasKey('recommendations', $result);
        $this->assertArrayHasKey('mood', $result);
        $this->assertArrayHasKey('productivity_score', $result);
    }

    /** @test */
    public function it_can_generate_motivational_message()
    {
        // Mock OpenAI API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Keep up the great work! You are doing amazing!'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->aiService->generateMotivationalMessage(
            'good',
            ['Completed task 1', 'Completed task 2'],
            ['Finish project', 'Learn new skill']
        );

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function it_returns_service_status()
    {
        $status = $this->aiService->getStatus();

        $this->assertIsArray($status);
        $this->assertArrayHasKey('available', $status);
        $this->assertArrayHasKey('model', $status);
        $this->assertArrayHasKey('base_url', $status);
        $this->assertArrayHasKey('max_tokens', $status);
        $this->assertArrayHasKey('temperature', $status);
    }

    /** @test */
    public function it_can_test_connection()
    {
        // Mock successful connection test
        Http::fake([
            'api.openai.com/v1/models' => Http::response([], 200)
        ]);

        $result = $this->aiService->testConnection();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_handles_connection_test_failure()
    {
        // Mock failed connection test
        Http::fake([
            'api.openai.com/v1/models' => Http::response([], 500)
        ]);

        $result = $this->aiService->testConnection();

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_generate_productivity_insights()
    {
        // Mock OpenAI API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'key_insights' => ['Consistent morning routine'],
                                'improvement_areas' => ['Time management'],
                                'recommendations' => ['Use Pomodoro technique'],
                                'strengths' => ['Focus', 'Persistence']
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $weeklyData = [
            'monday' => ['tasks_completed' => 3, 'focus_time' => 120],
            'tuesday' => ['tasks_completed' => 4, 'focus_time' => 150]
        ];

        $trends = ['productivity_increasing' => true];

        $result = $this->aiService->generateProductivityInsights($weeklyData, $trends);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('key_insights', $result);
        $this->assertArrayHasKey('improvement_areas', $result);
        $this->assertArrayHasKey('recommendations', $result);
        $this->assertArrayHasKey('strengths', $result);
    }

    /** @test */
    public function it_can_generate_learning_recommendations()
    {
        // Mock OpenAI API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                [
                                    'skill' => 'Laravel',
                                    'recommendation' => 'Learn advanced Eloquent relationships',
                                    'priority' => 'high',
                                    'estimated_time' => '2週間'
                                ]
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $completedTasks = [
            ['title' => 'Basic Laravel setup', 'tags' => ['Laravel', 'PHP']],
            ['title' => 'Simple API endpoint', 'tags' => ['API', 'Laravel']]
        ];

        $learningPaths = [
            ['name' => 'Laravel Developer', 'progress' => 30]
        ];

        $result = $this->aiService->generateLearningRecommendations($completedTasks, $learningPaths);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('skill', $result[0]);
        $this->assertArrayHasKey('recommendation', $result[0]);
        $this->assertArrayHasKey('priority', $result[0]);
        $this->assertArrayHasKey('estimated_time', $result[0]);
    }

    /** @test */
    public function it_can_analyze_focus_patterns()
    {
        // Mock OpenAI API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'optimal_times' => ['9:00-11:00', '14:00-16:00'],
                                'session_patterns' => ['25-minute work, 5-minute break'],
                                'efficiency_tips' => ['Eliminate distractions', 'Set clear goals'],
                                'recommendations' => ['Use Pomodoro technique', 'Take regular breaks']
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $sessions = [
            ['started_at' => '09:00', 'duration_minutes' => 25, 'efficiency_score' => 85],
            ['started_at' => '14:00', 'duration_minutes' => 25, 'efficiency_score' => 90]
        ];

        $productivityData = [
            'morning_score' => 85,
            'afternoon_score' => 90
        ];

        $result = $this->aiService->analyzeFocusPatterns($sessions, $productivityData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('optimal_times', $result);
        $this->assertArrayHasKey('session_patterns', $result);
        $this->assertArrayHasKey('efficiency_tips', $result);
        $this->assertArrayHasKey('recommendations', $result);
    }
}
