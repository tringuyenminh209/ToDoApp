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
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Date
            $table->date('stat_date')->comment('統計日');

            // Daily Stats
            $table->integer('tasks_completed_today')->default(0)
                ->comment('今日完了したタスク数');
            $table->integer('focus_minutes_today')->default(0)
                ->comment('今日の集中時間（分）');

            // Streaks
            $table->integer('streak_days')->default(0)
                ->comment('連続日数');

            // Scores
            $table->decimal('productivity_score', 3, 2)->nullable()
                ->comment('生産性スコア');
            $table->decimal('mood_average', 3, 2)->nullable()
                ->comment('平均気分');
            $table->enum('energy_avg', ['low', 'medium', 'high'])->nullable()
                ->comment('平均エネルギー');

            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'stat_date']);
            $table->index('stat_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};
