<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * チートコード言語テーブル作成
     * Cheat Code Languages: プログラミング言語のマスターデータ
     */
    public function up(): void
    {
        Schema::create('cheat_code_languages', function (Blueprint $table) {
            $table->id();

            // 言語情報
            $table->string('name', 100)->unique()->comment('言語名（php, java, python）');
            $table->string('display_name', 100)->comment('表示名（PHP, Java, Python）');
            $table->string('slug', 100)->unique()->comment('URL slug');

            // 表示設定
            $table->string('icon', 255)->nullable()->comment('アイコンURL');
            $table->string('color', 20)->default('#000000')->comment('色（HEX）');

            // メタデータ
            $table->text('description')->nullable()->comment('説明');
            $table->integer('popularity')->default(0)->comment('人気度（0-100）');
            $table->string('category', 50)->default('programming')->comment('カテゴリ（programming, markup, database）');

            // 統計情報
            $table->integer('sections_count')->default(0)->comment('セクション数');
            $table->integer('examples_count')->default(0)->comment('コード例数');
            $table->integer('exercises_count')->default(0)->comment('演習問題数');

            // 表示制御
            $table->boolean('is_active')->default(true)->comment('表示フラグ');
            $table->integer('sort_order')->default(0)->comment('並び順');

            $table->timestamps();

            // Indexes
            $table->index('category');
            $table->index('is_active');
            $table->index('popularity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheat_code_languages');
    }
};

