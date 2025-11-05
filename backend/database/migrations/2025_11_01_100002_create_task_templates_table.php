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
        Schema::create('task_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_template_id')
                  ->constrained('learning_milestone_templates')
                  ->onDelete('cascade')
                  ->comment('マイルストーンテンプレートID');

            // Task Information
            $table->string('title', 255)->comment('タスクタイトル');
            $table->text('description')->nullable()->comment('説明');
            $table->integer('sort_order')->default(0)->comment('並び順');

            // Estimation
            $table->integer('estimated_minutes')->nullable()->comment('見積もり時間（分）');
            $table->tinyInteger('priority')->default(3)->comment('優先度（1-5）');

            // Resources
            $table->json('resources')->nullable()->comment('リソース（リンク、動画など）');

            // Subtasks
            $table->json('subtasks')->nullable()->comment('サブタスクのリスト');

            // Knowledge Items
            $table->json('knowledge_items')->nullable()->comment('学習コンテンツ（ノート、コード例、リンク、演習）');

            $table->timestamps();

            // Indexes
            $table->index(['milestone_template_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_templates');
    }
};

