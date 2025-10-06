<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 知識アイテムテーブル作成
     * Note, Code, Exercise, Resource Link, Attachmentを保存
     */
    public function up(): void
    {
        Schema::create('knowledge_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->constrained('knowledge_categories')
                ->onDelete('cascade');

            // Item情報
            $table->string('title', 500)->comment('タイトル');
            $table->enum('item_type', ['note', 'code_snippet', 'resource_link', 'exercise', 'attachment'])
                  ->default('note')
                  ->comment('アイテムタイプ');

            // コンテンツ
            $table->longText('content')->nullable()->comment('本文（Markdown）');
            $table->string('code_language', 50)->nullable()->comment('コード言語（code_snippetの場合）');
            $table->string('url', 2048)->nullable()->comment('リンクURL（resource_linkの場合）');

            // 練習問題用（exercise）
            $table->text('question')->nullable()->comment('問題文');
            $table->text('answer')->nullable()->comment('解答');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable()->comment('難易度');

            // 添付ファイル
            $table->string('attachment_path', 500)->nullable()->comment('ファイルパス');
            $table->string('attachment_mime', 100)->nullable()->comment('MIMEタイプ');
            $table->integer('attachment_size')->nullable()->comment('ファイルサイズ（bytes）');

            // タグ・関連
            $table->json('tags')->nullable()->comment('タグ配列');
            $table->foreignId('learning_path_id')
                  ->nullable()
                  ->constrained('learning_paths')
                  ->onDelete('set null')
                  ->comment('関連Learning Path');
            $table->foreignId('source_task_id')
                  ->nullable()
                  ->constrained('tasks')
                  ->onDelete('set null')
                  ->comment('元Task');

            // Spaced Repetition（間隔反復）
            $table->integer('review_count')->default(0)->comment('復習回数');
            $table->timestamp('last_reviewed_at')->nullable()->comment('最終復習日時');
            $table->date('next_review_date')->nullable()->comment('次回復習予定日');
            $table->tinyInteger('retention_score')->default(3)->comment('記憶定着スコア（1-5）');

            // AI機能
            $table->text('ai_summary')->nullable()->comment('AI生成サマリー');

            // メタデータ
            $table->integer('view_count')->default(0)->comment('閲覧回数');
            $table->boolean('is_favorite')->default(false)->comment('お気に入り');
            $table->boolean('is_archived')->default(false)->comment('アーカイブ済み');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'category_id'], 'idx_user_category');
            $table->index(['user_id', 'item_type'], 'idx_user_type');
            $table->index(['user_id', 'is_favorite'], 'idx_user_favorite');
            $table->index(['user_id', 'next_review_date'], 'idx_next_review');
            $table->index(['user_id', 'created_at'], 'idx_user_created');
            $table->index('learning_path_id');
            $table->index('source_task_id');

            // Full-text search index
            $table->fullText(['title', 'content'], 'idx_fulltext_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_items');
    }
};

