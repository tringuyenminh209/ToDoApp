<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('learning_path_templates', function (Blueprint $table) {
            $table->id();

            // Template Information
            $table->string('title', 255)->comment('テンプレートタイトル');
            $table->text('description')->nullable()->comment('説明');

            // Categorization
            $table->enum('category', ['programming', 'design', 'business', 'language', 'data_science', 'other'])
                  ->default('other')
                  ->comment('カテゴリー');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])
                  ->default('beginner')
                  ->comment('難易度');

            // Estimation
            $table->integer('estimated_hours_total')->nullable()->comment('総学習時間見積もり（時間）');

            // Metadata
            $table->json('tags')->nullable()->comment('タグ配列');
            $table->string('icon', 50)->nullable()->comment('アイコン');
            $table->string('color', 7)->default('#0FA968')->comment('色（HEX）');

            // Popularity
            $table->boolean('is_featured')->default(false)->comment('おすすめテンプレート');
            $table->integer('usage_count')->default(0)->comment('使用回数');

            $table->timestamps();

            // Indexes
            $table->index('category');
            $table->index('difficulty');
            $table->index(['is_featured', 'usage_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_path_templates');
    }
};

