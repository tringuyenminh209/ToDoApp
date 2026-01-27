<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

/**
 * Xóa toàn bộ bản dịch đã seed trước đó rồi seed lại từ JSON.
 * Chỉ xóa các bản ghi thuộc model được seed bởi: Knowledge, Course, Exercise, CheatCode.
 *
 * php artisan db:seed --class=RefreshTranslationsSeeder
 */
class RefreshTranslationsSeeder extends Seeder
{
    /** Các model có translations được seed từ JSON (translatable_type trong DB) */
    private const SEEDED_MODELS = [
        \App\Models\KnowledgeCategory::class,
        \App\Models\KnowledgeItem::class,
        \App\Models\LearningPathTemplate::class,
        \App\Models\LearningMilestoneTemplate::class,
        \App\Models\TaskTemplate::class,
        \App\Models\Exercise::class,
        \App\Models\CheatCodeLanguage::class,
        \App\Models\CheatCodeSection::class,
        \App\Models\CodeExample::class,
    ];

    public function run(): void
    {
        $this->command->info('🗑️  Đang xóa bản dịch đã seed...');

        $deleted = Translation::whereIn('translatable_type', self::SEEDED_MODELS)->delete();

        $this->command->info("   Đã xóa {$deleted} bản ghi trong bảng translations.");

        $this->command->info('🌐 Đang seed lại bản dịch từ JSON...');
        $this->call([
            KnowledgeTranslationSeeder::class,
            CourseTranslationSeeder::class,
            ExerciseTranslationSeeder::class,
            CheatCodeTranslationSeeder::class,
        ]);

        $this->command->info('✅ Xong: đã xóa và seed lại bản dịch.');
    }
}
