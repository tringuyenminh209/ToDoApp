<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\Exercise;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ExerciseTranslationSeeder extends Seeder
{
    /**
     * Seed translations for Exercises
     *
     * Đọc từ file JSON và tạo translations cho:
     * - Exercise: title, description, question
     *
     * ※ 実行前に php artisan migrate で cheat_code_languages / exercises が存在すること。
     */
    public function run(): void
    {
        $this->command->info('🌐 Bắt đầu seed bản dịch Exercises...');

        if (!Schema::hasTable('cheat_code_languages') || !Schema::hasTable('exercises')) {
            $this->command->warn('⚠️  Bỏ qua ExerciseTranslationSeeder: thiếu bảng cheat_code_languages hoặc exercises.');
            $this->command->warn('   Chạy: php artisan migrate --force');
            return;
        }

        // Danh sách các languages có exercises
        $languages = [
            'php' => 'PHP',
            'java' => 'Java',
            'javascript' => 'JavaScript',
            'python' => 'Python',
            'go' => 'Go',
            'cpp' => 'C++',
            'kotlin' => 'Kotlin',
            'bash' => 'Bash',
            'mysql' => 'MySQL',
        ];

        foreach ($languages as $langKey => $langName) {
            $this->seedExercisesForLanguage($langKey);
        }

        $this->command->info('✅ Đã seed bản dịch Exercises thành công!');
    }

    /**
     * Seed translations cho exercises của một language
     */
    private function seedExercisesForLanguage(string $langKey): void
    {
        $enPath = database_path("translations/exercises/{$langKey}_exercises_en.json");
        $viPath = database_path("translations/exercises/{$langKey}_exercises_vi.json");

        if (!File::exists($enPath) || !File::exists($viPath)) {
            $this->command->warn("⚠️  Không tìm thấy file translations cho exercises: {$langKey}");
            return;
        }

        $enTranslations = json_decode(File::get($enPath), true);
        $viTranslations = json_decode(File::get($viPath), true);

        if (!$enTranslations || !$viTranslations) {
            $this->command->error("❌ Lỗi đọc file JSON translations cho: {$langKey}");
            return;
        }

        // Get language (DB接続・テーブルエラー時はメッセージを出してスキップ)
        try {
            $language = CheatCodeLanguage::where('name', $langKey)->first();
        } catch (Throwable $e) {
            $this->command->error("❌ Lỗi DB khi lấy language '{$langKey}': " . $e->getMessage());
            $this->command->warn('   Kiểm tra: php artisan migrate, .env (DB_*), quyền MySQL.');
            return;
        }
        if (!$language) {
            $this->command->warn("⚠️  Không tìm thấy language: {$langKey}");
            return;
        }

        $this->command->info("  📚 Đang dịch exercises cho: {$langKey}");

        $exercises = $enTranslations['exercises'] ?? [];
        $seeded = 0;
        $notFound = 0;

        foreach ($exercises as $jaTitle => $translations) {
            // Tìm exercise theo title (Japanese) và language_id
            $exercise = Exercise::where('language_id', $language->id)
                ->where('title', $jaTitle)
                ->first();

            // Nếu không tìm thấy, thử tìm theo title đã dịch
            if (!$exercise && isset($translations['title'])) {
                $exercise = Exercise::where('language_id', $language->id)
                    ->where('title', $translations['title'])
                    ->first();
            }

            // Nếu vẫn không tìm thấy, thử tìm bằng cách so sánh không phân biệt hoa thường
            if (!$exercise && isset($translations['title'])) {
                $exercise = Exercise::where('language_id', $language->id)
                    ->whereRaw('LOWER(title) = ?', [strtolower($translations['title'])])
                    ->first();
            }

            if (!$exercise) {
                $notFound++;
                continue;
            }

            // Chuẩn bị translations
            $exerciseTranslations = [];

            // Title translation
            if (isset($translations['title'])) {
                // Nếu title trong DB đã là tiếng Anh, chỉ cần dịch sang tiếng Việt
                if ($exercise->title === $translations['title']) {
                    $viTitle = $viTranslations['exercises'][$jaTitle]['title'] ?? null;
                    if ($viTitle) {
                        $exerciseTranslations['title'] = [
                            'en' => $exercise->title,
                            'vi' => $viTitle,
                        ];
                    }
                } else {
                    // Title cần dịch cả 2 ngôn ngữ
                    $exerciseTranslations['title'] = [
                        'en' => $translations['title'],
                        'vi' => $viTranslations['exercises'][$jaTitle]['title'] ?? null,
                    ];
                }
            }

            // Description translation
            if (isset($translations['description']) && !empty($translations['description'])) {
                $exerciseTranslations['description'] = [
                    'en' => $translations['description'],
                    'vi' => $viTranslations['exercises'][$jaTitle]['description'] ?? null,
                ];
            }

            // Question translation
            if (isset($translations['question']) && !empty($translations['question'])) {
                $exerciseTranslations['question'] = [
                    'en' => $translations['question'],
                    'vi' => $viTranslations['exercises'][$jaTitle]['question'] ?? null,
                ];
            }

            // Set translations
            if (!empty($exerciseTranslations)) {
                $exercise->setTranslations($exerciseTranslations);
                $seeded++;
            }
        }

        $this->command->line("    ✓ Đã dịch {$seeded} exercises cho {$langKey}");
        if ($notFound > 0) {
            $this->command->warn("    ⚠️  Không tìm thấy {$notFound} exercises");
        }
    }
}
