<?php

namespace Database\Seeders;

use App\Models\LearningPathTemplate;
use App\Models\LearningMilestoneTemplate;
use App\Models\TaskTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CourseTranslationSeeder extends Seeder
{
    /**
     * Seed translations for Courses (LearningPathTemplate, Milestones, Tasks)
     * 
     * Đọc từ file JSON và tạo translations cho:
     * - LearningPathTemplate: title, description
     * - LearningMilestoneTemplate: title, description, deliverables
     * - TaskTemplate: title, description
     */
    public function run(): void
    {
        $this->command->info('🌐 Bắt đầu seed bản dịch Courses...');

        // Danh sách các courses cần dịch
        $courses = [
            'php_basic' => 'PHP基礎演習',
            'java_basic' => 'Java基礎演習',
            'sql_basic' => 'SQL/データベース基礎コース',
            'html_basic' => 'HTML基礎演習',
            'javascript_basic' => 'JavaScript基礎演習',
            'typescript_basic' => 'TypeScript完全コース',
            'react_basic' => 'React.js完全コース',
            'python_basic' => 'Python基礎コース',
            'laravel_basic' => 'Laravel基礎演習',
            'java_design' => 'Javaプログラミング設計演習',
            'go_basic' => 'Go言語基礎コース',
            'git_basic' => 'Git/GitHub完全コース',
            'docker_basic' => 'Docker実践マスターコース',
        ];

        foreach ($courses as $courseKey => $courseTitle) {
            $this->seedCourse($courseKey, $courseTitle);
        }

        $this->command->info('✅ Đã seed bản dịch Courses thành công!');
    }

    /**
     * Seed translations cho một course
     */
    private function seedCourse(string $courseKey, string $courseTitle): void
    {
        $enPath = database_path("translations/courses/{$courseKey}_en.json");
        $viPath = database_path("translations/courses/{$courseKey}_vi.json");

        if (!File::exists($enPath) || !File::exists($viPath)) {
            $this->command->warn("⚠️  Không tìm thấy file translations cho course: {$courseKey}");
            return;
        }

        $enTranslations = json_decode(File::get($enPath), true);
        $viTranslations = json_decode(File::get($viPath), true);

        if (!$enTranslations || !$viTranslations) {
            $this->command->error("❌ Lỗi đọc file JSON translations cho: {$courseKey}");
            return;
        }

        $this->command->info("  📚 Đang dịch course: {$courseKey}");

        // Seed template translations
        $this->seedTemplate($courseTitle, $enTranslations, $viTranslations);

        // Seed milestones translations
        $this->seedMilestones($courseTitle, $enTranslations, $viTranslations);

        // Seed tasks translations
        $this->seedTasks($enTranslations, $viTranslations);
    }

    /**
     * Seed translations cho template
     */
    private function seedTemplate(string $courseTitle, array $enTranslations, array $viTranslations): void
    {
        $template = LearningPathTemplate::where('title', $courseTitle)->first();

        if (!$template) {
            $this->command->warn("  ⚠️  Không tìm thấy template: {$courseTitle}");
            return;
        }

        $templateTranslations = $enTranslations['template_translations'][$courseTitle] ?? null;
        if (!$templateTranslations) {
            return;
        }

        $template->setTranslations([
            'title' => [
                'en' => $templateTranslations['title'] ?? null,
                'vi' => $viTranslations['template_translations'][$courseTitle]['title'] ?? null,
            ],
            'description' => [
                'en' => $templateTranslations['description'] ?? null,
                'vi' => $viTranslations['template_translations'][$courseTitle]['description'] ?? null,
            ],
        ]);

        $this->command->line("    ✓ Template: {$courseTitle}");
    }

    /**
     * Seed translations cho milestones
     */
    private function seedMilestones(string $courseTitle, array $enTranslations, array $viTranslations): void
    {
        $template = LearningPathTemplate::where('title', $courseTitle)->first();
        if (!$template) {
            return;
        }

        $milestones = $enTranslations['milestones'] ?? [];
        $seeded = 0;
        $notFound = 0;

        foreach ($milestones as $jaTitle => $translations) {
            $milestone = LearningMilestoneTemplate::where('template_id', $template->id)
                ->where('title', $jaTitle)
                ->first();

            if (!$milestone) {
                $notFound++;
                continue;
            }

            // Set translations
            $milestone->setTranslations([
                'title' => [
                    'en' => $translations['title'] ?? null,
                    'vi' => $viTranslations['milestones'][$jaTitle]['title'] ?? null,
                ],
                'description' => [
                    'en' => $translations['description'] ?? null,
                    'vi' => $viTranslations['milestones'][$jaTitle]['description'] ?? null,
                ],
            ]);

            // Deliverables không có trong translatable fields, nhưng có thể lưu trong JSON
            // Nếu cần dịch deliverables, có thể thêm vào translatable fields

            $seeded++;
        }

        $this->command->line("    ✓ Đã dịch {$seeded} milestones");
        if ($notFound > 0) {
            $this->command->warn("    ⚠️  Không tìm thấy {$notFound} milestones");
        }
    }

    /**
     * Seed translations cho tasks
     */
    private function seedTasks(array $enTranslations, array $viTranslations): void
    {
        $tasks = $enTranslations['tasks'] ?? [];
        $seeded = 0;
        $notFound = 0;

        foreach ($tasks as $jaTitle => $translations) {
            // Tìm task theo title (có thể là tiếng Nhật hoặc tiếng Anh)
            $task = TaskTemplate::where('title', $jaTitle)->first();

            // Nếu không tìm thấy, thử tìm theo title đã dịch
            if (!$task && isset($translations['title'])) {
                $task = TaskTemplate::where('title', $translations['title'])->first();
            }

            // Nếu vẫn không tìm thấy, thử tìm bằng cách so sánh không phân biệt hoa thường
            if (!$task && isset($translations['title'])) {
                $task = TaskTemplate::whereRaw('LOWER(title) = ?', [strtolower($translations['title'])])->first();
            }

            if (!$task) {
                $notFound++;
                continue;
            }

            // Chuẩn bị translations
            $taskTranslations = [];

            // Title translation
            if (isset($translations['title'])) {
                // Nếu title trong DB đã là tiếng Anh, chỉ cần dịch sang tiếng Việt
                if ($task->title === $translations['title']) {
                    $viTitle = $viTranslations['tasks'][$jaTitle]['title'] ?? null;
                    if ($viTitle) {
                        $taskTranslations['title'] = [
                            'en' => $task->title,
                            'vi' => $viTitle,
                        ];
                    }
                } else {
                    // Title cần dịch cả 2 ngôn ngữ
                    $taskTranslations['title'] = [
                        'en' => $translations['title'],
                        'vi' => $viTranslations['tasks'][$jaTitle]['title'] ?? null,
                    ];
                }
            }

            // Description translation
            if (isset($translations['description']) && !empty($translations['description'])) {
                $taskTranslations['description'] = [
                    'en' => $translations['description'],
                    'vi' => $viTranslations['tasks'][$jaTitle]['description'] ?? null,
                ];
            }

            // Set translations
            if (!empty($taskTranslations)) {
                $task->setTranslations($taskTranslations);
                $seeded++;
            }
        }

        $this->command->line("    ✓ Đã dịch {$seeded} tasks");
        if ($notFound > 0) {
            $this->command->warn("    ⚠️  Không tìm thấy {$notFound} tasks");
        }
    }
}
