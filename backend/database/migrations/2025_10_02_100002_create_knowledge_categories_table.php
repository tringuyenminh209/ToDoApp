<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 知識カテゴリテーブル作成
     * 階層構造でカテゴリを管理（parent_id使用）
     */
    public function up(): void
    {
        Schema::create('knowledge_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('knowledge_categories')
                  ->onDelete('cascade')
                  ->comment('親カテゴリID（階層構造）');

            // カテゴリ情報
            $table->string('name', 255)->comment('カテゴリ名');
            $table->text('description')->nullable()->comment('説明');
            $table->integer('sort_order')->default(0)->comment('並び順');

            // 見た目
            $table->string('color', 7)->default('#0FA968')->comment('色（HEX）');
            $table->string('icon', 50)->nullable()->comment('アイコン名');

            // 統計
            $table->integer('item_count')->default(0)->comment('アイテム数');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'parent_id'], 'idx_user_parent');
            $table->index(['parent_id', 'sort_order'], 'idx_parent_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_categories');
    }
};

