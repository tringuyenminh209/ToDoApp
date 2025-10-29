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
        Schema::create('focus_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            // Session Type
            $table->enum('session_type', ['work', 'break', 'long_break'])
                ->default('work')
                ->comment('セッションタイプ：作業、短い休憩、長い休憩');

            // Duration
            $table->integer('duration_minutes')
                ->comment('予定時間（分）');
            $table->integer('actual_minutes')->nullable()
                ->comment('実際の時間（分）');

            // Timestamps
            $table->timestamp('started_at')->comment('開始時刻');
            $table->timestamp('ended_at')->nullable()->comment('終了時刻');

            // Status & Quality
            $table->enum('status', ['active', 'completed', 'paused', 'cancelled'])
                ->default('active')
                ->comment('セッションステータス');
            $table->text('notes')->nullable()->comment('メモ');
            $table->tinyInteger('quality_score')->nullable()
                ->comment('品質スコア（1-5）');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'started_at']);
            $table->index('task_id');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('focus_sessions');
    }
};
