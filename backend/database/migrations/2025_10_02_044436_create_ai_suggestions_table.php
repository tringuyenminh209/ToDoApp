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
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Suggestion Type
            $table->enum('type', [
                'task_breakdown',
                'daily_plan',
                'smart_schedule',
                'motivational'
            ])->comment('提案タイプ');

            // Content (JSON)
            $table->json('content')
                ->comment('提案内容（JSON形式）');

            // Source Task (optional)
            $table->foreignId('source_task_id')->nullable()
                ->constrained('tasks')
                ->onDelete('set null');

            // Feedback
            $table->boolean('is_accepted')->default(false)
                ->comment('ユーザーが承認済み');
            $table->tinyInteger('feedback_score')->nullable()
                ->comment('評価スコア（1-5）');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'type']);
            $table->index('source_task_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_suggestions');
    }
};
