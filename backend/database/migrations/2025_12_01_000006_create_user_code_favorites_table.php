<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ユーザーコードお気に入りテーブル作成
     * User Code Favorites: ユーザーがお気に入りに登録したコード例
     */
    public function up(): void
    {
        Schema::create('user_code_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('ユーザーID');
            $table->foreignId('code_example_id')
                ->constrained('code_examples')
                ->onDelete('cascade')
                ->comment('コード例ID');

            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'code_example_id'], 'idx_user_code_example');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_code_favorites');
    }
};

