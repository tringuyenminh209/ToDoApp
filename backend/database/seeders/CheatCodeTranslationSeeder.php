<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Seeder để import translations từ JSON files
 * 
 * Sử dụng:
 * php artisan db:seed --class=CheatCodeTranslationSeeder
 */
class CheatCodeTranslationSeeder extends Seeder
{
    /**
     * Đường dẫn đến folder translations
     */
    protected string $translationsPath;

    public function __construct()
    {
        $this->translationsPath = database_path('translations/cheat_codes');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting cheat code translations import...');

        // Supported programming languages (all languages with translation files)
        $languages = [
            'php', 'java', 'javascript', 'python',
            'bash', 'cpp', 'css3', 'docker', 'go', 'html',
            'kotlin', 'laravel', 'mysql', 'yaml'
        ];

        // Import translations for each locale
        foreach (['en', 'vi'] as $locale) {
            foreach ($languages as $lang) {
                $this->importTranslationsForLanguage($lang, $locale);
            }
        }

        $this->command->info('✅ Cheat code translations imported successfully!');
    }

    /**
     * Import translations for a specific language and locale
     */
    protected function importTranslationsForLanguage(string $language, string $locale): void
    {
        $file = "{$this->translationsPath}/{$language}_{$locale}.json";
        
        if (!File::exists($file)) {
            $this->command->warn("Translation file not found: {$file}");
            return;
        }

        $translations = json_decode(File::get($file), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Invalid JSON in: {$file}");
            return;
        }

        $this->command->info("Importing {$language} ({$locale}) translations...");

        // Import language translations
        $this->importLanguageTranslations($translations, $locale);
        
        // Import section translations
        $this->importSectionTranslations($translations, $locale);
        
        // Import example translations
        $this->importExampleTranslations($translations, $locale);
    }

    /**
     * Import language translations
     */
    protected function importLanguageTranslations(array $translations, string $locale): void
    {
        if (!isset($translations['language'])) {
            return;
        }

        foreach ($translations['language'] as $languageName => $fields) {
            $language = CheatCodeLanguage::where('name', $languageName)->first();
            
            if (!$language) {
                $this->command->warn("Language not found: {$languageName}");
                continue;
            }

            foreach ($fields as $field => $value) {
                $language->setTranslation($field, $locale, $value);
            }

            $this->command->line("  ✓ Language: {$languageName}");
        }
    }

    /**
     * Import section translations
     */
    protected function importSectionTranslations(array $translations, string $locale): void
    {
        if (!isset($translations['sections'])) {
            return;
        }

        foreach ($translations['sections'] as $sectionSlug => $fields) {
            $section = CheatCodeSection::where('slug', $sectionSlug)->first();
            
            if (!$section) {
                $this->command->warn("Section not found: {$sectionSlug}");
                continue;
            }

            foreach ($fields as $field => $value) {
                $section->setTranslation($field, $locale, $value);
            }

            $this->command->line("  ✓ Section: {$sectionSlug}");
        }
    }

    /**
     * Import example translations
     */
    protected function importExampleTranslations(array $translations, string $locale): void
    {
        if (!isset($translations['examples'])) {
            return;
        }

        $importedCount = 0;

        foreach ($translations['examples'] as $sectionSlug => $examples) {
            $section = CheatCodeSection::where('slug', $sectionSlug)->first();
            
            if (!$section) {
                continue;
            }

            foreach ($examples as $exampleTitle => $fields) {
                // Tìm example bằng title gốc (tiếng Nhật) hoặc slug
                $example = CodeExample::where('section_id', $section->id)
                    ->where(function ($query) use ($exampleTitle) {
                        $query->where('title', $exampleTitle)
                              ->orWhere('slug', \Illuminate\Support\Str::slug($exampleTitle));
                    })
                    ->first();
                
                if (!$example) {
                    // Thử tìm bằng cách so sánh không phân biệt chữ hoa/thường
                    $example = CodeExample::where('section_id', $section->id)
                        ->whereRaw('LOWER(title) = ?', [strtolower($exampleTitle)])
                        ->first();
                }

                if (!$example) {
                    $this->command->warn("    Example not found: {$exampleTitle} in section {$sectionSlug}");
                    continue;
                }

                foreach ($fields as $field => $value) {
                    $example->setTranslation($field, $locale, $value);
                }

                $importedCount++;
            }
        }

        $this->command->line("  ✓ Imported {$importedCount} examples for {$locale}");
    }
}
