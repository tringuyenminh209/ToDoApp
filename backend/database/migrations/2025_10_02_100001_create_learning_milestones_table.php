<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイルストーンテーブル作成
     * Learning Pathを複数のMilestoneに分割
     */
    public function up(): void
    {
        Schema::create('learning_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')
                  ->constrained('learning_paths')
                  ->onDelete('cascade');

            // Milestone情報
            $table->string('title', 255)->comment('マイルストーンタイトル');
            $table->text('description')->nullable()->comment('詳細説明');
            $table->integer('sort_order')->default(0)->comment('並び順');

            // 期間
            $table->date('target_start_date')->nullable()->comment('開始予定日');
            $table->date('target_end_date')->nullable()->comment('完了予定日');
            $table->timestamp('completed_at')->nullable()->comment('完了日時');

            // 進捗
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped'])
                  ->default('pending')
                  ->comment('ステータス');
            $table->decimal('progress_percentage', 5, 2)
                  ->default(0.00)
                  ->comment('進捗率');

            // 見積もり
            $table->integer('estimated_hours')->nullable()->comment('見積もり時間（時間）');
            $table->integer('actual_hours')->default(0)->comment('実際の時間（時間）');

            // 成果物・評価
            $table->json('deliverables')->nullable()->comment('成果物リスト');
            $table->tinyInteger('self_assessment')->nullable()->comment('自己評価（1-5）');
            $table->text('notes')->nullable()->comment('メモ');

            $table->timestamps();

            // Indexes
            $table->index(['learning_path_id', 'sort_order'], 'idx_path_order');
            $table->index(['learning_path_id', 'status'], 'idx_path_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_milestones');
    }
};

