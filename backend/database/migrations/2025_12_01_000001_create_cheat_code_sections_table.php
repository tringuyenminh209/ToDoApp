<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * チートコードセクションテーブル作成
     * Cheat Code Sections: 言語ごとのセクション（Getting Started, Variables等）
     */
    public function up(): void
    {
        Schema::create('cheat_code_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')
                ->constrained('cheat_code_languages')
                ->onDelete('cascade')
                ->comment('言語ID');

            // セクション情報
            $table->string('title', 200)->comment('タイトル（Getting Started, Variables）');
            $table->string('slug', 200)->comment('URL slug');
            $table->text('description')->nullable()->comment('説明');
            $table->string('icon', 255)->nullable()->comment('アイコン');

            // 統計情報
            $table->integer('examples_count')->default(0)->comment('コード例数');
            $table->integer('sort_order')->default(0)->comment('並び順');

            // 公開設定
            $table->boolean('is_published')->default(true)->comment('公開フラグ');

            $table->timestamps();

            // Indexes
            $table->unique(['language_id', 'slug'], 'idx_language_slug');
            $table->index('language_id');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheat_code_sections');
    }
};

