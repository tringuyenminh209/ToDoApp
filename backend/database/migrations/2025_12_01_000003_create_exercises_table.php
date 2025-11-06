<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 演習問題テーブル作成
     * Exercises: プログラミング演習問題
     */
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')
                ->constrained('cheat_code_languages')
                ->onDelete('cascade')
                ->comment('言語ID');

            // 問題情報
            $table->string('title', 200)->comment('タイトル');
            $table->string('slug', 200)->comment('URL slug');
            $table->text('description')->comment('説明');
            $table->text('question')->comment('問題文');

            // コード
            $table->text('starter_code')->nullable()->comment('スターターコード');
            $table->text('solution')->nullable()->comment('解答（非表示）');

            // メタデータ
            $table->json('hints')->nullable()->comment('ヒント配列');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium')->comment('難易度');
            $table->integer('points')->default(10)->comment('ポイント');
            $table->json('tags')->nullable()->comment('タグ配列');

            // 制限
            $table->integer('time_limit')->default(60)->comment('時間制限（分）');

            // 統計情報
            $table->integer('submissions_count')->default(0)->comment('提出回数');
            $table->integer('success_count')->default(0)->comment('成功回数');
            $table->decimal('success_rate', 5, 2)->default(0.00)->comment('成功率（%）');

            // 公開設定
            $table->boolean('is_published')->default(true)->comment('公開フラグ');
            $table->integer('sort_order')->default(0)->comment('並び順');

            $table->timestamps();

            // Indexes
            $table->unique(['language_id', 'slug'], 'idx_language_slug');
            $table->index('language_id');
            $table->index('difficulty');
            $table->index('success_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};

