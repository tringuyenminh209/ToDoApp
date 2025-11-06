<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ユーザー演習問題進捗テーブル作成
     * User Exercise Progress: ユーザーの演習問題進捗状況
     */
    public function up(): void
    {
        Schema::create('user_exercise_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ユーザーID');
            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->onDelete('cascade')
                ->comment('演習問題ID');

            // 進捗情報
            $table->boolean('is_completed')->default(false)->comment('完了フラグ');
            $table->integer('best_score')->default(0)->comment('最高スコア');
            $table->integer('attempts_count')->default(0)->comment('試行回数');

            // 日時
            $table->timestamp('last_attempted_at')->nullable()->comment('最終試行日時');
            $table->timestamp('completed_at')->nullable()->comment('完了日時');

            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'exercise_id'], 'idx_user_exercise');
            $table->index(['user_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exercise_progress');
    }
};

