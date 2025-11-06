<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ユーザー演習問題提出テーブル作成
     * User Exercise Submissions: ユーザーのコード提出履歴
     */
    public function up(): void
    {
        Schema::create('user_exercise_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ユーザーID');
            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->onDelete('cascade')
                ->comment('演習問題ID');

            // 提出コード
            $table->text('code')->comment('提出コード');
            $table->string('language', 50)->comment('言語');

            // 実行結果
            $table->enum('status', ['pending', 'running', 'success', 'failed', 'error', 'timeout'])
                ->default('pending')
                ->comment('ステータス');
            $table->integer('passed_test_cases')->default(0)->comment('通過テストケース数');
            $table->integer('total_test_cases')->default(0)->comment('総テストケース数');
            $table->integer('score')->default(0)->comment('スコア');

            // パフォーマンス
            $table->integer('execution_time')->nullable()->comment('実行時間（ミリ秒）');
            $table->integer('memory_used')->nullable()->comment('使用メモリ（KB）');

            // エラー情報
            $table->text('error_message')->nullable()->comment('エラーメッセージ');
            $table->json('test_results')->nullable()->comment('テスト結果詳細');

            // 提出日時
            $table->timestamp('submitted_at')->useCurrent()->comment('提出日時');

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('exercise_id');
            $table->index('status');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exercise_submissions');
    }
};

