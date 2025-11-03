<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LearningPath;
use Illuminate\Database\Seeder;

class TestUserLearningPathsSeeder extends Seeder
{
    /**
     * Create sample learning paths for test user
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            $this->command->error('Test user not found!');
            return;
        }

        // Learning Path 1: Java Learning
        LearningPath::create([
            'user_id' => $user->id,
            'title' => 'Java学習ロードマップ',
            'description' => 'Javaの基礎から応用まで、6ヶ月で習得する',
            'goal_type' => 'skill',
            'status' => 'active',
            'progress_percentage' => 30,
            'estimated_hours_total' => 240,
            'actual_hours_total' => 72,
            'tags' => ['java', 'backend', 'programming'],
            'color' => '#ED8B00',
            'icon' => '☕',
        ]);

        // Learning Path 2: React Development
        LearningPath::create([
            'user_id' => $user->id,
            'title' => 'React開発マスター',
            'description' => 'Reactでモダンなフロントエンド開発を学ぶ',
            'goal_type' => 'skill',
            'status' => 'active',
            'progress_percentage' => 50,
            'estimated_hours_total' => 180,
            'actual_hours_total' => 90,
            'tags' => ['react', 'frontend', 'javascript'],
            'color' => '#61DAFB',
            'icon' => '⚛️',
        ]);

        // Learning Path 3: Python Data Science (Completed)
        LearningPath::create([
            'user_id' => $user->id,
            'title' => 'Python データサイエンス',
            'description' => 'Pythonでデータ分析と機械学習の基礎',
            'goal_type' => 'skill',
            'status' => 'completed',
            'progress_percentage' => 100,
            'estimated_hours_total' => 200,
            'actual_hours_total' => 215,
            'tags' => ['python', 'data-science', 'ml'],
            'color' => '#3776AB',
            'icon' => '🐍',
        ]);

        $this->command->info('Created 3 learning paths for test user');
    }
}

