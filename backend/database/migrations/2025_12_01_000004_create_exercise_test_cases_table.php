<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 演習問題テストケーステーブル作成
     * Exercise Test Cases: テストケース
     */
    public function up(): void
    {
        Schema::create('exercise_test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->onDelete('cascade')
                ->comment('演習問題ID');

            // テストケース情報
            $table->text('input')->comment('入力');
            $table->text('expected_output')->comment('期待される出力');
            $table->string('description', 255)->nullable()->comment('説明');

            // 表示設定
            $table->boolean('is_sample')->default(false)->comment('サンプル表示フラグ');
            $table->boolean('is_hidden')->default(false)->comment('非表示フラグ');
            $table->integer('sort_order')->default(0)->comment('並び順');

            $table->timestamps();

            // Indexes
            $table->index('exercise_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_test_cases');
    }
};

