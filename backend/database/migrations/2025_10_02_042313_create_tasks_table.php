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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Foreign Keys
            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->onDelete('set null')
                ->comment('プロジェクトID');

            $table->foreignId('learning_milestone_id')
                ->nullable()
                ->constrained('learning_milestones')
                ->onDelete('set null')
                ->comment('学習マイルストーンID（Learning Path機能）');

            // Task Information
            $table->string('title', 255)->comment('タスクタイトル');
            $table->enum('category', ['study', 'work', 'personal', 'other'])
                ->default('other')
                ->comment('タスクカテゴリー（学習/仕事/個人/その他）');
            $table->text('description')->nullable()->comment('詳細説明');

            // Priority & Energy
            $table->tinyInteger('priority')->default(3)
                ->comment('優先度（1-5、5が最高）');
            $table->enum('energy_level', ['low', 'medium', 'high'])
                ->default('medium')
                ->comment('必要なエネルギーレベル');

            // Time Estimation
            $table->integer('estimated_minutes')->nullable()
                ->comment('予想時間（分）');
            $table->timestamp('deadline')->nullable()
                ->comment('締め切り');

            // Status
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])
                ->default('pending')
                ->comment('タスクステータス');

            // AI Features
            $table->boolean('ai_breakdown_enabled')->default(false)
                ->comment('AIによる分解済み');

            $table->timestamps();

            // Indexes for Performance
            $table->index(['user_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index('learning_milestone_id');
            $table->index('deadline');
            $table->index('priority');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
