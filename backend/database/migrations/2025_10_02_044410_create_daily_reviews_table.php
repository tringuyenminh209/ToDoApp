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

            // Daily Stats
            $table->integer('tasks_completed')->default(0)
                ->comment('完了したタスク数');
            $table->integer('focus_time_minutes')->default(0)
                ->comment('合計集中時間（分）');
            $table->tinyInteger('productivity_score')->nullable()
                ->comment('生産性スコア（1-5）');

            // Reflection
            $table->text('gratitude_note')->nullable()
                ->comment('感謝のメモ');
            $table->text('challenges_faced')->nullable()
                ->comment('直面した課題');
            $table->text('tomorrow_goals')->nullable()
                ->comment('明日の目標');

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
