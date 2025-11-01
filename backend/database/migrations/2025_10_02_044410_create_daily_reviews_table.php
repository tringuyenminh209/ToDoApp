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
        Schema::create('daily_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Review Date
            $table->date('date')->comment('レビュー日');

            // Mood
            $table->enum('mood', ['excellent', 'good', 'average', 'poor', 'terrible'])
                ->nullable()
                ->comment('気分');

            // Daily Stats
            $table->integer('tasks_completed')->default(0)
                ->comment('完了したタスク数');
            $table->integer('focus_time_minutes')->default(0)
                ->comment('合計集中時間（分）');
            $table->tinyInteger('productivity_score')->nullable()
                ->comment('生産性スコア（1-10）');

            // Individual Scores
            $table->tinyInteger('focus_time_score')->nullable()
                ->comment('集中時間スコア（1-10）');
            $table->tinyInteger('task_completion_score')->nullable()
                ->comment('タスク完了スコア（1-10）');
            $table->tinyInteger('goal_achievement_score')->nullable()
                ->comment('目標達成スコア（1-10）');
            $table->tinyInteger('work_life_balance_score')->nullable()
                ->comment('ワークライフバランススコア（1-10）');

            // Reflection (JSON arrays)
            $table->json('achievements')->nullable()
                ->comment('達成事項（JSON配列）');
            $table->text('gratitude_note')->nullable()
                ->comment('感謝のメモ');
            $table->json('gratitude')->nullable()
                ->comment('感謝（JSON配列）');
            $table->text('challenges_faced')->nullable()
                ->comment('直面した課題');
            $table->json('challenges')->nullable()
                ->comment('課題（JSON配列）');
            $table->json('lessons_learned')->nullable()
                ->comment('学んだこと（JSON配列）');
            $table->text('tomorrow_goals')->nullable()
                ->comment('明日の目標');
            $table->text('notes')->nullable()
                ->comment('メモ');

            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reviews');
    }
};
