<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng translations đa ngôn ngữ (Polymorphic)
     * Hỗ trợ: ja (Japanese), en (English), vi (Vietnamese)
     * 
     * Sử dụng cho các bảng:
     * - cheat_code_languages, cheat_code_sections, code_examples
     * - knowledge_categories, knowledge_items
     * - learning_path_templates, learning_milestone_templates, task_templates
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation
            $table->string('translatable_type', 255)
                  ->comment('Model class (App\\Models\\CheatCodeSection)');
            $table->unsignedBigInteger('translatable_id')
                  ->comment('ID của record gốc');

            // Ngôn ngữ
            $table->string('locale', 5)
                  ->comment('Mã ngôn ngữ: ja, en, vi');

            // Field và giá trị
            $table->string('field', 100)
                  ->comment('Tên field cần dịch (title, description, content)');
            $table->longText('value')
                  ->nullable()
                  ->comment('Nội dung đã dịch');

            $table->timestamps();

            // Indexes
            $table->index(['translatable_type', 'translatable_id'], 'idx_translatable');
            $table->index('locale', 'idx_locale');

            // Unique constraint - đảm bảo không trùng lặp
            $table->unique(
                ['translatable_type', 'translatable_id', 'locale', 'field'],
                'unique_translation'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
