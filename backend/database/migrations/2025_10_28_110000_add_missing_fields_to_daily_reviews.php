<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_reviews', function (Blueprint $table) {
            // Add mood enum
            $table->enum('mood', ['excellent', 'good', 'average', 'poor', 'terrible'])
                ->nullable()
                ->after('date')
                ->comment('気分');

            // Change productivity_score to support 1-10 (was 1-5)
            $table->tinyInteger('productivity_score')->nullable()->change();

            // Add individual score fields
            $table->tinyInteger('focus_time_score')->nullable()
                ->after('productivity_score')
                ->comment('集中時間スコア（1-10）');

            $table->tinyInteger('task_completion_score')->nullable()
                ->after('focus_time_score')
                ->comment('タスク完了スコア（1-10）');

            $table->tinyInteger('goal_achievement_score')->nullable()
                ->after('task_completion_score')
                ->comment('目標達成スコア（1-10）');

            $table->tinyInteger('work_life_balance_score')->nullable()
                ->after('goal_achievement_score')
                ->comment('ワークライフバランススコア（1-10）');

            // Add JSON arrays for structured data
            $table->json('achievements')->nullable()
                ->after('work_life_balance_score')
                ->comment('達成事項（JSON配列）');

            $table->json('challenges')->nullable()
                ->after('challenges_faced')
                ->comment('課題（JSON配列）');

            $table->json('lessons_learned')->nullable()
                ->after('challenges')
                ->comment('学んだこと（JSON配列）');

            $table->json('gratitude')->nullable()
                ->after('gratitude_note')
                ->comment('感謝（JSON配列）');

            // Add notes field
            $table->text('notes')->nullable()
                ->after('tomorrow_goals')
                ->comment('メモ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'mood',
                'focus_time_score',
                'task_completion_score',
                'goal_achievement_score',
                'work_life_balance_score',
                'achievements',
                'challenges',
                'lessons_learned',
                'gratitude',
                'notes'
            ]);
        });
    }
};
