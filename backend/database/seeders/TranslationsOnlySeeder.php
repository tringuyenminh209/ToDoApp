<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Chạy chỉ các seeder translation – không tạo/xóa course, user, cheatcode…
 * Dùng khi muốn cập nhật bản dịch từ JSON mà không ảnh hưởng dữ liệu.
 *
 * php artisan db:seed --class=TranslationsOnlySeeder
 */
class TranslationsOnlySeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KnowledgeTranslationSeeder::class,
            CourseTranslationSeeder::class,
            ExerciseTranslationSeeder::class,
            CheatCodeTranslationSeeder::class,
        ]);
    }
}
