<?php

namespace Database\Seeders;

use App\Models\KnowledgeCategory;
use App\Models\KnowledgeItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class KnowledgeTranslationSeeder extends Seeder
{
    /**
     * Seed translations for Knowledge Categories and Items
     * 
     * Đọc từ file JSON và tạo translations cho:
     * - KnowledgeCategory: name, description
     * - KnowledgeItem: title, content, question, answer
     */
    public function run(): void
    {
        $this->command->info('🌐 Bắt đầu seed bản dịch Knowledge...');

        $this->seedKnowledgeCategories();
        $this->seedKnowledgeItems();
        
        $this->command->info('✅ Đã seed bản dịch Knowledge thành công!');
    }

    /**
     * Seed translations cho Knowledge Categories
     */
    private function seedKnowledgeCategories(): void
    {
        $enPath = database_path('translations/knowledge/categories_en.json');
        $viPath = database_path('translations/knowledge/categories_vi.json');

        if (!File::exists($enPath) || !File::exists($viPath)) {
            $this->command->warn('⚠️  Không tìm thấy file translations cho Knowledge Categories');
            return;
        }

        $enTranslations = json_decode(File::get($enPath), true);
        $viTranslations = json_decode(File::get($viPath), true);

        if (!$enTranslations || !$viTranslations) {
            $this->command->error('❌ Lỗi đọc file JSON translations');
            return;
        }

        $categories = $enTranslations['categories'] ?? [];
        $seeded = 0;
        $notFound = 0;

        foreach ($categories as $jaName => $translations) {
            // Tìm category theo tên tiếng Nhật
            $category = KnowledgeCategory::where('name', $jaName)->first();

            if (!$category) {
                $notFound++;
                continue;
            }

            // Set translations
            $category->setTranslations([
                'name' => [
                    'en' => $translations['name'] ?? null,
                    'vi' => $viTranslations['categories'][$jaName]['name'] ?? null,
                ],
                'description' => [
                    'en' => $translations['description'] ?? null,
                    'vi' => $viTranslations['categories'][$jaName]['description'] ?? null,
                ],
            ]);

            $seeded++;
        }

        $this->command->info("  ✓ Đã dịch {$seeded} Knowledge Categories");
        
        if ($notFound > 0) {
            $this->command->warn("  ⚠️  Không tìm thấy {$notFound} categories trong database");
        }
    }

    /**
     * Seed translations cho Knowledge Items
     */
    private function seedKnowledgeItems(): void
    {
        $enPath = database_path('translations/knowledge/items_en.json');
        $viPath = database_path('translations/knowledge/items_vi.json');

        if (!File::exists($enPath) || !File::exists($viPath)) {
            $this->command->warn('⚠️  Không tìm thấy file translations cho Knowledge Items');
            return;
        }

        $enTranslations = json_decode(File::get($enPath), true);
        $viTranslations = json_decode(File::get($viPath), true);

        if (!$enTranslations || !$viTranslations) {
            $this->command->error('❌ Lỗi đọc file JSON translations');
            return;
        }

        $items = $enTranslations['items'] ?? [];
        $seeded = 0;
        $notFound = 0;

        foreach ($items as $jaTitle => $translations) {
            $item = null;

            // Thử tìm theo title tiếng Nhật trước
            $item = KnowledgeItem::where('title', $jaTitle)->first();

            // Nếu không tìm thấy, thử tìm theo title đã dịch (tiếng Anh)
            if (!$item && isset($translations['title'])) {
                $item = KnowledgeItem::where('title', $translations['title'])->first();
            }

            // Nếu vẫn không tìm thấy, có thể title đã là tiếng Anh trong DB
            // Thử tìm bằng cách so sánh không phân biệt hoa thường
            if (!$item && isset($translations['title'])) {
                $item = KnowledgeItem::whereRaw('LOWER(title) = ?', [strtolower($translations['title'])])->first();
            }

            if (!$item) {
                $notFound++;
                continue;
            }

            // Chuẩn bị translations
            $itemTranslations = [];

            // Title translation - chỉ dịch nếu title trong DB khác với title đã dịch
            if (isset($translations['title'])) {
                // Nếu title trong DB đã là tiếng Anh, chỉ cần dịch sang tiếng Việt
                if ($item->title === $translations['title']) {
                    // Title đã đúng tiếng Anh, chỉ cần thêm tiếng Việt
                    $viTitle = $viTranslations['items'][$jaTitle]['title'] ?? null;
                    if ($viTitle) {
                        $itemTranslations['title'] = [
                            'en' => $item->title, // Giữ nguyên
                            'vi' => $viTitle,
                        ];
                    }
                } else {
                    // Title cần dịch cả 2 ngôn ngữ
                    $itemTranslations['title'] = [
                        'en' => $translations['title'],
                        'vi' => $viTranslations['items'][$jaTitle]['title'] ?? null,
                    ];
                }
            }

            // Content translation (cho note và resource_link)
            if (isset($translations['content']) && !empty($translations['content'])) {
                $itemTranslations['content'] = [
                    'en' => $translations['content'],
                    'vi' => $viTranslations['items'][$jaTitle]['content'] ?? null,
                ];
            }

            // Question translation (cho exercise)
            if (isset($translations['question']) && !empty($translations['question'])) {
                $itemTranslations['question'] = [
                    'en' => $translations['question'],
                    'vi' => $viTranslations['items'][$jaTitle]['question'] ?? null,
                ];
            }

            // Answer không dịch (giữ nguyên code)
            // Chỉ dịch phần giải thích nếu có trong content

            // Set translations
            if (!empty($itemTranslations)) {
                $item->setTranslations($itemTranslations);
                $seeded++;
            }
        }

        $this->command->info("  ✓ Đã dịch {$seeded} Knowledge Items");
        
        if ($notFound > 0) {
            $this->command->warn("  ⚠️  Không tìm thấy {$notFound} items trong database");
        }
    }
}
