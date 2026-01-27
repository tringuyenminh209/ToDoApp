<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

/**
 * Resolves roadmap/course title and description from course translation JSON files.
 *
 * Hai luồng dùng chung, không xung đột:
 * 1) Clone (LearningPathTemplateController): getTemplateTranslation / getMilestoneTranslation / getTaskTranslation
 *    → dịch vô luôn, ghi title/description vào DB theo locale lúc clone.
 * 2) Show/API (LearningPathController): getTemplateTranslation + applyPathDetailTranslations
 *    → ghi đè response theo locale hiện tại (X-Locale), không ghi DB. Path cũ (DB còn ja) vẫn hiển thị đúng khi đổi ngôn ngữ.
 *
 * Nguồn dịch duy nhất: backend/database/translations/courses/{courseKey}_{vi|en}.json
 */
class CourseTranslationService
{
    protected static array $templateTitleToCourseKey = [
        'Docker実践マスターコース' => 'docker_basic',
        'Docker基礎' => 'docker_basic',
        'PHP基礎演習' => 'php_basic',
        'PHP基礎' => 'php_basic',
        'Java基礎演習' => 'java_basic',
        'Java基礎' => 'java_basic',
        'SQL/データベース基礎コース' => 'sql_basic',
        'SQL基礎' => 'sql_basic',
        'HTML基礎演習' => 'html_basic',
        'HTML基礎' => 'html_basic',
        'JavaScript基礎演習' => 'javascript_basic',
        'JavaScript基礎' => 'javascript_basic',
        'TypeScript完全コース' => 'typescript_basic',
        'TypeScript基礎' => 'typescript_basic',
        'React.js完全コース' => 'react_basic',
        'React基礎' => 'react_basic',
        'Python基礎コース' => 'python_basic',
        'Python基礎' => 'python_basic',
        'Laravel基礎演習' => 'laravel_basic',
        'Laravel基礎' => 'laravel_basic',
        'Javaプログラミング設計演習' => 'java_design',
        'Java設計' => 'java_design',
        'Go言語基礎コース' => 'go_basic',
        'Go基礎' => 'go_basic',
        'Git/GitHub完全コース' => 'git_basic',
        'Git基礎' => 'git_basic',
    ];

    /**
     * Path title がすでに vi/en で保存されている場合に、courseKey を解決するための逆引き。
     * Clone 時に translated title で path が作られると applyPathDetailTranslations で使う。
     */
    protected static array $translatedTitleToCourseKey = [
        'Bài tập cơ bản Java' => 'java_basic',
        'Java Basic Course' => 'java_basic',
        'Khóa học Thiết kế Lập trình Java' => 'java_design',
        'Java Programming Design Course' => 'java_design',
        'Khóa học Go Cơ bản' => 'go_basic',
        'Go Basic Course' => 'go_basic',
        'Go Language Basic Course' => 'go_basic',
        'Khóa học Docker Master' => 'docker_basic',
        'Docker Master Course' => 'docker_basic',
        'Khóa học Git/GitHub Hoàn chỉnh' => 'git_basic',
        'Git/GitHub Complete Course' => 'git_basic',
        'Khóa học HTML Cơ bản' => 'html_basic',
        'HTML Basic Course' => 'html_basic',
        'Khóa học Laravel Cơ bản' => 'laravel_basic',
        'Laravel Basic Course' => 'laravel_basic',
        'Khóa học PHP Cơ bản' => 'php_basic',
        'PHP Basic Course' => 'php_basic',
        'Khóa học Python Cơ bản' => 'python_basic',
        'Python Basic Course' => 'python_basic',
        'Khóa học React.js Hoàn chỉnh' => 'react_basic',
        'React.js Complete Course' => 'react_basic',
        'Khóa học SQL/Cơ sở Dữ liệu Cơ bản' => 'sql_basic',
        'SQL/Database Fundamentals Course' => 'sql_basic',
        'Khóa học TypeScript Hoàn chỉnh' => 'typescript_basic',
        'TypeScript Complete Course' => 'typescript_basic',
        'Khóa học JavaScript Cơ bản' => 'javascript_basic',
        'JavaScript Basic Course' => 'javascript_basic',
    ];

    /**
     * Get translated title and description for a template (roadmap).
     * $pathOrJaTitle: tiếng Nhật (vd "Java基礎演習") hoặc đã dịch (vd "Bài tập cơ bản Java") — đều resolve được courseKey.
     * Returns null if no translation found or locale is ja.
     */
    public static function getTemplateTranslation(string $pathOrJaTitle, ?string $locale = null): ?array
    {
        $locale = $locale ?? App::getLocale();
        if ($locale === 'ja') {
            return null;
        }

        $courseKey = self::$templateTitleToCourseKey[$pathOrJaTitle] ?? self::resolveCourseKey($pathOrJaTitle);
        if (!$courseKey) {
            return null;
        }

        $path = database_path("translations/courses/{$courseKey}_{$locale}.json");
        if (!file_exists($path)) {
            return null;
        }

        $data = json_decode(file_get_contents($path), true);
        $tt = $data['template_translations'] ?? [];
        // Ưu tiên key trùng $pathOrJaTitle (khi là ja), không thì lấy entry đầu tiên (file đã theo locale)
        $translations = $tt[$pathOrJaTitle] ?? null;
        if (!$translations && !empty($tt)) {
            $translations = reset($tt);
        }
        if (!$translations || !is_array($translations)) {
            return null;
        }

        return [
            'title' => $translations['title'] ?? $pathOrJaTitle,
            'description' => $translations['description'] ?? '',
        ];
    }

    /**
     * Translate knowledge_items array for a task using course file.
     * Returns new array with title/content translated when available.
     */
    public static function translateKnowledgeItemsForCreate(
        array $knowledgeItems,
        string $templateJaTitle,
        string $taskJaTitle,
        ?string $locale = null
    ): array {
        $locale = $locale ?? App::getLocale();
        if (empty($knowledgeItems) || !in_array($locale, ['en', 'vi'])) {
            return $knowledgeItems;
        }

        $courseKey = self::$templateTitleToCourseKey[$templateJaTitle] ?? null;
        if (!$courseKey) {
            return $knowledgeItems;
        }

        $path = database_path("translations/courses/{$courseKey}_{$locale}.json");
        if (!file_exists($path)) {
            return $knowledgeItems;
        }

        $data = json_decode(file_get_contents($path), true);
        $tasksTranslations = $data['tasks'] ?? [];
        $taskTrans = null;
        foreach ($tasksTranslations as $key => $t) {
            if ($key === $taskJaTitle || ($t['title'] ?? null) === $taskJaTitle) {
                $taskTrans = $t;
                break;
            }
        }

        if (!$taskTrans || empty($taskTrans['knowledge_items'])) {
            return $knowledgeItems;
        }

        $kiTrans = $taskTrans['knowledge_items'];
        $result = [];
        foreach ($knowledgeItems as $item) {
            $jaTitle = $item['title'] ?? null;
            $out = $item;
            if ($jaTitle && isset($kiTrans[$jaTitle])) {
                if (!empty($kiTrans[$jaTitle]['title'])) {
                    $out['title'] = $kiTrans[$jaTitle]['title'];
                }
                if (isset($kiTrans[$jaTitle]['content'])) {
                    $out['content'] = $kiTrans[$jaTitle]['content'];
                }
                if (isset($kiTrans[$jaTitle]['question'])) {
                    $out['question'] = $kiTrans[$jaTitle]['question'];
                }
            }
            $result[] = $out;
        }
        return $result;
    }

    /**
     * Translate subtasks array for a task using course file.
     */
    public static function translateSubtasksForCreate(
        array $subtasks,
        string $templateJaTitle,
        string $taskJaTitle,
        ?string $locale = null
    ): array {
        $locale = $locale ?? App::getLocale();
        if (empty($subtasks) || !in_array($locale, ['en', 'vi'])) {
            return $subtasks;
        }

        $courseKey = self::$templateTitleToCourseKey[$templateJaTitle] ?? null;
        if (!$courseKey) {
            return $subtasks;
        }

        $path = database_path("translations/courses/{$courseKey}_{$locale}.json");
        if (!file_exists($path)) {
            return $subtasks;
        }

        $data = json_decode(file_get_contents($path), true);
        $tasksTranslations = $data['tasks'] ?? [];
        $taskTrans = null;
        foreach ($tasksTranslations as $key => $t) {
            if ($key === $taskJaTitle || ($t['title'] ?? null) === $taskJaTitle) {
                $taskTrans = $t;
                break;
            }
        }

        if (!$taskTrans || empty($taskTrans['subtasks'])) {
            return $subtasks;
        }

        $subTrans = $taskTrans['subtasks'];
        $result = [];
        foreach ($subtasks as $s) {
            $jaTitle = $s['title'] ?? null;
            $out = $s;
            if ($jaTitle && isset($subTrans[$jaTitle])) {
                $out['title'] = $subTrans[$jaTitle];
            }
            $result[] = $out;
        }
        return $result;
    }

    public static function getCourseKeyFromTemplateTitle(string $jaTitle): ?string
    {
        return self::$templateTitleToCourseKey[$jaTitle] ?? null;
    }

    /**
     * Resolve courseKey from path title (Japanese or already translated vi/en).
     */
    public static function resolveCourseKey(string $pathTitle): ?string
    {
        return self::$templateTitleToCourseKey[$pathTitle] ?? self::$translatedTitleToCourseKey[$pathTitle] ?? null;
    }

    /**
     * Load decoded course translation file for a path title. Returns null if not vi/en or file missing.
     * pathTitle は日本語・vi・en いずれでも可（translatedTitleToCourseKey で逆引きする）。
     * Keys: milestones[], tasks[]
     */
    public static function loadCourseTranslations(string $pathTitle, ?string $locale = null): ?array
    {
        $locale = $locale ?? App::getLocale();
        if (!in_array($locale, ['vi', 'en'], true)) {
            return null;
        }
        $courseKey = self::resolveCourseKey($pathTitle);
        if (!$courseKey) {
            return null;
        }
        $path = database_path("translations/courses/{$courseKey}_{$locale}.json");
        if (!file_exists($path)) {
            return null;
        }
        return json_decode(file_get_contents($path), true);
    }

    /**
     * Get translated title/description for a milestone. Returns null if not found.
     */
    public static function getMilestoneTranslation(string $pathJaTitle, string $milestoneJaTitle, ?string $locale = null): ?array
    {
        $data = self::loadCourseTranslations($pathJaTitle, $locale);
        if (!$data) {
            return null;
        }
        $m = $data['milestones'][$milestoneJaTitle] ?? null;
        if (!$m) {
            return null;
        }
        return [
            'title' => $m['title'] ?? $milestoneJaTitle,
            'description' => $m['description'] ?? '',
        ];
    }

    /**
     * Get translated title, description, and subtask map for a task. Returns null if not found.
     * subtasks: [ 'Goをインストール' => 'Cài đặt Go', ... ]
     */
    public static function getTaskTranslation(string $pathJaTitle, string $taskJaTitle, ?string $locale = null): ?array
    {
        $data = self::loadCourseTranslations($pathJaTitle, $locale);
        if (!$data) {
            return null;
        }
        $tasks = $data['tasks'] ?? [];
        $t = null;
        foreach ($tasks as $key => $val) {
            if ($key === $taskJaTitle || ($val['title'] ?? null) === $taskJaTitle) {
                $t = is_array($val) ? $val : null;
                if ($t === null) {
                    break;
                }
                if (!isset($t['title'])) {
                    $t['title'] = $taskJaTitle;
                }
                break;
            }
        }
        if (!$t) {
            return null;
        }
        return [
            'title' => $t['title'] ?? $taskJaTitle,
            'description' => $t['description'] ?? '',
            'subtasks' => $t['subtasks'] ?? [],
        ];
    }

    /**
     * Apply milestone/task/subtask translations to learning path detail array (for show response).
     * Modifies pathData in place; returns pathData.
     */
    public static function applyPathDetailTranslations(array $pathData, string $pathJaTitle, ?string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();
        if (!in_array($locale, ['vi', 'en'], true)) {
            return $pathData;
        }
        $milestones = $pathData['milestones'] ?? [];
        foreach ($milestones as &$milestone) {
            $jaTitle = $milestone['title'] ?? '';
            if ($jaTitle === '') {
                continue;
            }
            $trans = self::getMilestoneTranslation($pathJaTitle, $jaTitle, $locale);
            if ($trans) {
                $milestone['title'] = $trans['title'];
                $milestone['description'] = $trans['description'] ?? $milestone['description'] ?? '';
            }
            $tasks = $milestone['tasks'] ?? [];
            foreach ($tasks as &$task) {
                $taskJa = $task['title'] ?? '';
                if ($taskJa === '') {
                    continue;
                }
                $taskTrans = self::getTaskTranslation($pathJaTitle, $taskJa, $locale);
                if ($taskTrans) {
                    $task['title'] = $taskTrans['title'];
                    $task['description'] = $taskTrans['description'] ?? $task['description'] ?? '';
                    $subMap = $taskTrans['subtasks'] ?? [];
                    if (!empty($subMap) && !empty($task['subtasks'])) {
                        foreach ($task['subtasks'] as &$st) {
                            $stTitle = $st['title'] ?? null;
                            if ($stTitle !== null && isset($subMap[$stTitle])) {
                                $st['title'] = $subMap[$stTitle];
                            }
                        }
                    }
                }
            }
        }
        $pathData['milestones'] = $milestones;
        return $pathData;
    }

    /**
     * デフォルトカテゴリ名（テンプレート以外）の vi/en 表示用翻訳。
     * 例: 「プログラミング演習」→ ベトナム語/英語
     */
    protected static array $categoryNameTranslations = [
        'プログラミング演習' => [
            'vi' => ['title' => 'Bài tập lập trình', 'description' => 'Thư mục bài tập lập trình'],
            'en' => ['title' => 'Programming Exercises', 'description' => 'Folder for programming exercises'],
        ],
    ];

    /**
     * Get translated title/description for known category names (e.g. プログラミング演習).
     * Returns null if locale is ja or no translation.
     */
    public static function getCategoryNameTranslation(string $jaName, ?string $locale = null): ?array
    {
        $locale = $locale ?? App::getLocale();
        if ($locale === 'ja') {
            return null;
        }
        $map = self::$categoryNameTranslations[$jaName][$locale] ?? null;
        return $map ? ['title' => $map['title'], 'description' => $map['description']] : null;
    }
}
