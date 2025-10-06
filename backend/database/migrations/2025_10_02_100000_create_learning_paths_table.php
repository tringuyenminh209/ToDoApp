<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 学習ロードマップテーブル作成
     * Learning Path: ユーザーの長期学習目標を管理
     */
    public function up(): void
    {
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Path情報
            $table->string('title', 255)->comment('学習目標タイトル');
            $table->text('description')->nullable()->comment('詳細説明');
            $table->enum('goal_type', ['career', 'skill', 'certification', 'hobby'])
                  ->default('skill')
                  ->comment('目標タイプ');

            // 期間
            $table->date('target_start_date')->nullable()->comment('開始予定日');
            $table->date('target_end_date')->nullable()->comment('完了目標日');

            // 進捗
            $table->enum('status', ['active', 'paused', 'completed', 'abandoned'])
                  ->default('active')
                  ->comment('ステータス');
            $table->decimal('progress_percentage', 5, 2)
                  ->default(0.00)
                  ->comment('進捗率（0-100）');

            // AI生成フラグ
            $table->boolean('is_ai_generated')->default(false)->comment('AI生成ロードマップ');
            $table->text('ai_prompt')->nullable()->comment('AI生成時のプロンプト');

            // 時間管理
            $table->integer('estimated_hours_total')->nullable()->comment('総学習時間見積もり（時間）');
            $table->integer('actual_hours_total')->default(0)->comment('実際の学習時間（時間）');

            // タグ・カテゴリ
            $table->json('tags')->nullable()->comment('タグ配列');
            $table->string('color', 7)->default('#0FA968')->comment('色（HEX）');
            $table->string('icon', 50)->nullable()->comment('アイコン名');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['user_id', 'created_at'], 'idx_user_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_paths');
    }
};

