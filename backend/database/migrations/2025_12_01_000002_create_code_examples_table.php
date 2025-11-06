<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * コード例テーブル作成
     * Code Examples: コードサンプル
     */
    public function up(): void
    {
        Schema::create('code_examples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('cheat_code_sections')
                ->onDelete('cascade')
                ->comment('セクションID');
            $table->foreignId('language_id')
                ->constrained('cheat_code_languages')
                ->onDelete('cascade')
                ->comment('言語ID（非正規化）');

            // コード例情報
            $table->string('title', 200)->comment('タイトル（hello.php）');
            $table->string('slug', 200)->comment('URL slug');
            $table->text('code')->comment('ソースコード');
            $table->text('description')->nullable()->comment('説明');
            $table->text('output')->nullable()->comment('実行結果');

            // メタデータ
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy')->comment('難易度');
            $table->json('tags')->nullable()->comment('タグ配列');

            // 統計情報
            $table->integer('views_count')->default(0)->comment('閲覧回数');
            $table->integer('favorites_count')->default(0)->comment('お気に入り数');
            $table->integer('sort_order')->default(0)->comment('並び順');

            // 公開設定
            $table->boolean('is_published')->default(true)->comment('公開フラグ');

            $table->timestamps();

            // Indexes
            $table->index('section_id');
            $table->index('language_id');
            $table->index('difficulty');
            $table->index('views_count');

            // Full-text search index
            $table->fullText(['title', 'description', 'code'], 'idx_fulltext_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_examples');
    }
};

