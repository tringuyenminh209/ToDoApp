<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIInteraction successful()
 */
	class AIInteraction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Task|null $sourceTask
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AISuggestion accepted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AISuggestion byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AISuggestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AISuggestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AISuggestion query()
 */
	class AISuggestion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AISummary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AISummary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AISummary query()
 */
	class AISummary extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $action アクション（例: task.created, session.completed）
 * @property string|null $resource_type リソースタイプ（例: Task, Session）
 * @property int|null $resource_id リソースID
 * @property string|null $ip_address IPアドレス
 * @property string|null $user_agent ユーザーエージェント
 * @property array<array-key, mixed>|null $metadata 追加メタデータ（JSON）
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog byAction($action)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog byResource($type, $id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $last_message_at
 * @property int $message_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation archived()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatConversation whereUserId($value)
 */
	class ChatConversation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $conversation_id
 * @property int $user_id
 * @property string $role
 * @property string $content
 * @property array<array-key, mixed>|null $metadata
 * @property int|null $token_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ChatConversation $conversation
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage assistantMessages()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage userMessages()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereTokenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatMessage whereUserId($value)
 */
	class ChatMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name 言語名（php, java, python）
 * @property string $display_name 表示名（PHP, Java, Python）
 * @property string $slug URL slug
 * @property string|null $icon アイコンURL
 * @property string $color 色（HEX）
 * @property string|null $description 説明
 * @property int $popularity 人気度（0-100）
 * @property string $category カテゴリ（programming, markup, database）
 * @property-read int|null $sections_count セクション数
 * @property int $examples_count コード例数
 * @property-read int|null $exercises_count 演習問題数
 * @property bool $is_active 表示フラグ
 * @property int $sort_order 並び順
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CodeExample> $codeExamples
 * @property-read int|null $code_examples_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exercise> $exercises
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CheatCodeSection> $sections
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage byCategory($category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereExamplesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereExercisesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage wherePopularity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereSectionsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeLanguage whereUpdatedAt($value)
 */
	class CheatCodeLanguage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $language_id
 * @property string $title タイトル（Getting Started, Variables）
 * @property string $slug URL slug
 * @property string|null $description 説明
 * @property string|null $icon アイコン
 * @property-read int|null $examples_count コード例数
 * @property int $sort_order 並び順
 * @property bool $is_published 公開フラグ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CodeExample> $examples
 * @property-read \App\Models\CheatCodeLanguage $language
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereExamplesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatCodeSection whereUpdatedAt($value)
 */
	class CheatCodeSection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $section_id
 * @property int $language_id
 * @property string $title タイトル（hello.php）
 * @property string $slug URL slug
 * @property string $code ソースコード
 * @property string|null $description 説明
 * @property string|null $output 実行結果
 * @property string $difficulty 難易度
 * @property array<array-key, mixed>|null $tags タグ配列
 * @property int $views_count 閲覧回数
 * @property int $favorites_count お気に入り数
 * @property int $sort_order 並び順
 * @property bool $is_published 公開フラグ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CheatCodeLanguage $language
 * @property-read \App\Models\CheatCodeSection $section
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample byDifficulty($difficulty)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample popular()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereFavoritesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereOutput($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CodeExample whereViewsCount($value)
 */
	class CodeExample extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $from_task_id
 * @property string|null $from_category
 * @property int|null $from_focus_difficulty
 * @property int $to_task_id
 * @property string|null $to_category
 * @property int|null $to_focus_difficulty
 * @property bool $is_significant_switch Different category or focus level
 * @property int $estimated_cost_minutes Estimated recovery time
 * @property bool $user_proceeded Did user proceed despite warning
 * @property string|null $user_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Task|null $fromTask
 * @property-read \App\Models\Task $toTask
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch recent($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch significant()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch userProceeded()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereEstimatedCostMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereFromCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereFromFocusDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereFromTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereIsSignificantSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereToCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereToFocusDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereToTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereUserNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContextSwitch whereUserProceeded($value)
 */
	class ContextSwitch extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date チェックイン日
 * @property string $energy_level 朝のエネルギーレベル
 * @property int $mood_score 気分スコア（1-5）
 * @property string|null $mood 気分（enum形式）
 * @property numeric|null $sleep_hours 睡眠時間
 * @property string|null $stress_level ストレスレベル
 * @property string|null $schedule_note 今日のスケジュールメモ
 * @property array<array-key, mixed>|null $priorities 優先事項（JSON配列）
 * @property array<array-key, mixed>|null $goals 目標（JSON配列）
 * @property string|null $notes メモ
 * @property bool $ai_suggestions_generated AI提案生成済み
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereAiSuggestionsGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereEnergyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereGoals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereMood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereMoodScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin wherePriorities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereScheduleNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereSleepHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereStressLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyCheckin whereUserId($value)
 */
	class DailyCheckin extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date レビュー日
 * @property string|null $mood 気分
 * @property int $tasks_completed 完了したタスク数
 * @property int $focus_time_minutes 合計集中時間（分）
 * @property int|null $productivity_score 生産性スコア（1-10）
 * @property int|null $focus_time_score 集中時間スコア（1-10）
 * @property int|null $task_completion_score タスク完了スコア（1-10）
 * @property int|null $goal_achievement_score 目標達成スコア（1-10）
 * @property int|null $work_life_balance_score ワークライフバランススコア（1-10）
 * @property array<array-key, mixed>|null $achievements 達成事項（JSON配列）
 * @property string|null $gratitude_note 感謝のメモ
 * @property array<array-key, mixed>|null $gratitude 感謝（JSON配列）
 * @property string|null $challenges_faced 直面した課題
 * @property array<array-key, mixed>|null $challenges 課題（JSON配列）
 * @property array<array-key, mixed>|null $lessons_learned 学んだこと（JSON配列）
 * @property string|null $tomorrow_goals 明日の目標
 * @property string|null $notes メモ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereAchievements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereChallenges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereChallengesFaced($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereFocusTimeMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereFocusTimeScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereGoalAchievementScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereGratitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereGratitudeNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereLessonsLearned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereMood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereProductivityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereTaskCompletionScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereTasksCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereTomorrowGoals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyReview whereWorkLifeBalanceScore($value)
 */
	class DailyReview extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property int|null $focus_session_id
 * @property string $distraction_type
 * @property int|null $duration_seconds How long the distraction lasted
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $occurred_at
 * @property string|null $time_of_day What time of day did distraction occur
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FocusSession|null $focusSession
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog byTask($taskId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog byTimeOfDay($hour)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog recent($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereDistractionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereFocusSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereOccurredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereTimeOfDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DistractionLog whereUserId($value)
 */
	class DistractionLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $language_id
 * @property string $title タイトル
 * @property string $slug URL slug
 * @property string $description 説明
 * @property string $question 問題文
 * @property string|null $starter_code スターターコード
 * @property string|null $solution 解答（非表示）
 * @property array<array-key, mixed>|null $hints ヒント配列
 * @property string $difficulty 難易度
 * @property int $points ポイント
 * @property array<array-key, mixed>|null $tags タグ配列
 * @property int $time_limit 時間制限（分）
 * @property int $submissions_count 提出回数
 * @property int $success_count 成功回数
 * @property numeric $success_rate 成功率（%）
 * @property bool $is_published 公開フラグ
 * @property int $sort_order 並び順
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CheatCodeLanguage $language
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExerciseTestCase> $testCases
 * @property-read int|null $test_cases_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise byDifficulty($difficulty)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereHints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereSolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereStarterCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereSubmissionsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereSuccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereSuccessRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereTimeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereUpdatedAt($value)
 */
	class Exercise extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $exercise_id
 * @property string $input 入力
 * @property string $expected_output 期待される出力
 * @property string|null $description 説明
 * @property bool $is_sample サンプル表示フラグ
 * @property bool $is_hidden 非表示フラグ
 * @property int $sort_order 並び順
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Exercise $exercise
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereExpectedOutput($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereInput($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereIsSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseTestCase whereUpdatedAt($value)
 */
	class ExerciseTestCase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property int|null $focus_session_id
 * @property bool $quiet_space
 * @property bool $phone_silent
 * @property bool $materials_ready
 * @property bool $water_coffee_ready
 * @property bool $comfortable_position
 * @property bool $notifications_off
 * @property array<array-key, mixed>|null $apps_closed List of apps/tabs closed
 * @property bool $all_checks_passed
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FocusSession|null $focusSession
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment allPassed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment byTask($taskId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment recent($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereAllChecksPassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereAppsClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereComfortablePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereFocusSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereMaterialsReady($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereNotificationsOff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment wherePhoneSilent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereQuietSpace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusEnvironment whereWaterCoffeeReady($value)
 */
	class FocusEnvironment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $task_id
 * @property string $session_type セッションタイプ：作業、短い休憩、長い休憩
 * @property int $duration_minutes 予定時間（分）
 * @property int|null $actual_minutes 実際の時間（分）
 * @property \Illuminate\Support\Carbon $started_at 開始時刻
 * @property \Illuminate\Support\Carbon|null $ended_at 終了時刻
 * @property string $status セッションステータス
 * @property string|null $notes メモ
 * @property int|null $quality_score 品質スコア（1-5）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $duration_formatted
 * @property-read mixed $efficiency
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession break()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereActualMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereQualityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereSessionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FocusSession work()
 */
	class FocusSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $name カテゴリ名
 * @property string|null $description 説明
 * @property int $sort_order 並び順
 * @property string $color 色（HEX）
 * @property string|null $icon アイコン名
 * @property int $item_count アイテム数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, KnowledgeCategory> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KnowledgeItem> $knowledgeItems
 * @property-read int|null $knowledge_items_count
 * @property-read KnowledgeCategory|null $parent
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory rootCategories()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereItemCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereUserId($value)
 */
	class KnowledgeCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $title タイトル
 * @property string $item_type アイテムタイプ
 * @property string|null $content 本文（Markdown）
 * @property string|null $code_language コード言語（code_snippetの場合）
 * @property string|null $url リンクURL（resource_linkの場合）
 * @property string|null $question 問題文
 * @property string|null $answer 解答
 * @property string|null $difficulty 難易度
 * @property string|null $attachment_path ファイルパス
 * @property string|null $attachment_mime MIMEタイプ
 * @property int|null $attachment_size ファイルサイズ（bytes）
 * @property array<array-key, mixed>|null $tags タグ配列
 * @property int|null $learning_path_id
 * @property int|null $source_task_id
 * @property int $review_count 復習回数
 * @property \Illuminate\Support\Carbon|null $last_reviewed_at 最終復習日時
 * @property \Illuminate\Support\Carbon|null $next_review_date 次回復習予定日
 * @property int $retention_score 記憶定着スコア（1-5）
 * @property string|null $ai_summary AI生成サマリー
 * @property int $view_count 閲覧回数
 * @property bool $is_favorite お気に入り
 * @property bool $is_archived アーカイブ済み
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KnowledgeCategory $category
 * @property-read \App\Models\LearningPath|null $learningPath
 * @property-read \App\Models\Task|null $sourceTask
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem archived()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem attachments()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem codeSnippets()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem dueForReview()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem exercises()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem favorites()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem notes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem resourceLinks()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereAiSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereAttachmentMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereAttachmentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereAttachmentSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereCodeLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereIsFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereLastReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereLearningPathId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereNextReviewDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereRetentionScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereReviewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereSourceTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeItem whereViewCount($value)
 */
	class KnowledgeItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $learning_path_id
 * @property string $title マイルストーンタイトル
 * @property string|null $description 詳細説明
 * @property int $sort_order 並び順
 * @property \Illuminate\Support\Carbon|null $target_start_date 開始予定日
 * @property \Illuminate\Support\Carbon|null $target_end_date 完了予定日
 * @property \Illuminate\Support\Carbon|null $completed_at 完了日時
 * @property string $status ステータス
 * @property numeric $progress_percentage 進捗率
 * @property int|null $estimated_hours 見積もり時間（時間）
 * @property int $actual_hours 実際の時間（時間）
 * @property array<array-key, mixed>|null $deliverables 成果物リスト
 * @property int|null $self_assessment 自己評価（1-5）
 * @property string|null $notes メモ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $completion_rate
 * @property-read mixed $days_for_completion
 * @property-read mixed $estimated_duration
 * @property-read mixed $is_due_soon
 * @property-read mixed $is_overdue
 * @property-read mixed $status_display
 * @property-read mixed $time_utilization
 * @property-read \App\Models\LearningPath $learningPath
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone highPriority()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone upcoming($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereActualHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereDeliverables($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereEstimatedHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereLearningPathId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereProgressPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereSelfAssessment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereTargetEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereTargetStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestone whereUpdatedAt($value)
 */
	class LearningMilestone extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $template_id
 * @property string $title マイルストーンタイトル
 * @property string|null $description 説明
 * @property int $sort_order 並び順
 * @property int|null $estimated_hours 見積もり時間（時間）
 * @property array<array-key, mixed>|null $deliverables 成果物リスト
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskTemplate> $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\LearningPathTemplate $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereDeliverables($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereEstimatedHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningMilestoneTemplate whereUpdatedAt($value)
 */
	class LearningMilestoneTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $title 学習目標タイトル
 * @property string|null $description 詳細説明
 * @property string $goal_type 目標タイプ
 * @property \Illuminate\Support\Carbon|null $target_start_date 開始予定日
 * @property \Illuminate\Support\Carbon|null $target_end_date 完了目標日
 * @property string $status ステータス
 * @property numeric $progress_percentage 進捗率（0-100）
 * @property bool $is_ai_generated AI生成ロードマップ
 * @property string|null $ai_prompt AI生成時のプロンプト
 * @property int|null $estimated_hours_total 総学習時間見積もり（時間）
 * @property int $actual_hours_total 実際の学習時間（時間）
 * @property array<array-key, mixed>|null $tags タグ配列
 * @property string $color 色（HEX）
 * @property string|null $icon アイコン名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $completed_milestones
 * @property-read mixed $days_remaining
 * @property-read mixed $estimated_duration_days
 * @property-read mixed $goal_type_display
 * @property-read mixed $is_overdue
 * @property-read mixed $status_display
 * @property-read mixed $time_utilization
 * @property-read mixed $total_milestones
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KnowledgeItem> $knowledgeItems
 * @property-read int|null $knowledge_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LearningMilestone> $milestones
 * @property-read int|null $milestones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudySchedule> $studySchedules
 * @property-read int|null $study_schedules_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath abandoned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath aiGenerated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath byGoalType($goalType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath paused()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereActualHoursTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereAiPrompt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereEstimatedHoursTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereGoalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereIsAiGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereProgressPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereTargetEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereTargetStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPath whereUserId($value)
 */
	class LearningPath extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title テンプレートタイトル
 * @property string|null $description 説明
 * @property string $category カテゴリー
 * @property string $difficulty 難易度
 * @property int|null $estimated_hours_total 総学習時間見積もり（時間）
 * @property array<array-key, mixed>|null $tags タグ配列
 * @property string|null $icon アイコン
 * @property string $color 色（HEX）
 * @property bool $is_featured おすすめテンプレート
 * @property int $usage_count 使用回数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LearningMilestoneTemplate> $milestones
 * @property-read int|null $milestones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate byCategory($category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate byDifficulty($difficulty)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate popular($limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereEstimatedHoursTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningPathTemplate whereUsageCount($value)
 */
	class LearningPathTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type 通知タイプ
 * @property string $title タイトル
 * @property string $message 内容
 * @property array<array-key, mixed>|null $data 追加データ（JSON）
 * @property bool $is_read 既読
 * @property \Illuminate\Support\Carbon|null $scheduled_at 送信予定時刻
 * @property \Illuminate\Support\Carbon|null $sent_at 送信済み時刻
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $action_data
 * @property-read mixed $formatted_time_ago
 * @property-read mixed $status
 * @property-read mixed $type_display
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification achievements()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification motivational()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification read()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification recent($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification reminders()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification scheduled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification sent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification system()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification unread()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $metric_date メトリクス日
 * @property string $metric_type 指標タイプ
 * @property numeric $metric_value メトリクス値
 * @property string|null $trend_direction トレンド
 * @property string|null $notes メモ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_value
 * @property-read mixed $metric_type_display
 * @property-read mixed $trend_direction_display
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric dailyCompletion()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric focusTime()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric forDate($date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric forDateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric moodTrend()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric negativeTrend()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric positiveTrend()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric recent($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric stableTrend()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric streakMaintenance()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereMetricDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereMetricType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereMetricValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereTrendDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PerformanceMetric whereUserId($value)
 */
	class PerformanceMetric extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $name_en プロジェクト名（英語）
 * @property string $name_ja プロジェクト名（日本語）
 * @property string|null $description_en 説明（英語）
 * @property string|null $description_ja 説明（日本語）
 * @property string $status ステータス
 * @property \Illuminate\Support\Carbon|null $start_date 開始日
 * @property \Illuminate\Support\Carbon|null $end_date 終了日
 * @property numeric $progress_percentage 進捗率
 * @property string $color カラーコード
 * @property bool $is_active アクティブかどうか
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $completion_rate
 * @property-read mixed $days_remaining
 * @property-read mixed $is_overdue
 * @property-read mixed $status_display
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project byStatus($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDescriptionJa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereNameJa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereProgressPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUserId($value)
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * StudySchedule Model
 * スケジュール学習モデル
 * 
 * Purpose:
 * - Enforce study discipline with scheduled learning times
 * - Track completion and missed sessions
 * - Support reminders for upcoming study sessions
 *
 * @property int $id
 * @property int $learning_path_id
 * @property string $study_time
 * @property int $day_of_week
 * @property int $duration_minutes
 * @property bool $is_active
 * @property int $reminder_before_minutes
 * @property bool $reminder_enabled
 * @property int $completed_sessions
 * @property int $missed_sessions
 * @property \Illuminate\Support\Carbon|null $last_studied_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $completion_rate
 * @property-read int $consistency_score
 * @property-read string $day_name
 * @property-read string $day_name_japanese
 * @property-read \Carbon\Carbon $reminder_time
 * @property-read string $study_time_formatted
 * @property-read \App\Models\LearningPath $learningPath
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule forDay(int $dayOfWeek)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule forToday()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule upcoming()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereCompletedSessions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereLastStudiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereLearningPathId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereMissedSessions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereReminderBeforeMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereReminderEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereStudyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudySchedule whereUpdatedAt($value)
 */
	class StudySchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $task_id
 * @property string $title サブタスクタイトル
 * @property bool $is_completed 完了済み
 * @property int|null $estimated_minutes 予想時間（分）
 * @property int $sort_order 並び順
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $estimated_hours
 * @property-read mixed $estimated_time_formatted
 * @property-read \App\Models\Task $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask byTask($taskId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask whereEstimatedMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask withEstimatedTime()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subtask withoutEstimatedTime()
 */
	class Subtask extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name タグ名
 * @property string $color 色（HEX形式）
 * @property string|null $icon アイコン名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $display_name
 * @property-read mixed $is_popular
 * @property-read mixed $usage_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag byColor($color)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag popular()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag recentlyUsed($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag withIcon()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag withoutIcon()
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $project_id
 * @property int|null $learning_milestone_id
 * @property string $title タスクタイトル
 * @property string $category タスクカテゴリー（学習/仕事/個人/その他）
 * @property string|null $description 詳細説明
 * @property int $priority 優先度（1-5、5が最高）
 * @property string $energy_level 必要なエネルギーレベル
 * @property int|null $estimated_minutes 予想時間（分）
 * @property \Illuminate\Support\Carbon|null $deadline 締め切り
 * @property string|null $scheduled_time
 * @property string $status タスクステータス
 * @property bool $is_abandoned 放棄されたタスク
 * @property bool $ai_breakdown_enabled AIによる分解済み
 * @property bool $requires_deep_focus Deep Work Modeが必要
 * @property bool $allow_interruptions 割り込みを許可するか
 * @property int $focus_difficulty 集中難易度（1-5: shallow to ultra-deep focus）
 * @property int|null $warmup_minutes タスク前の準備時間（分）
 * @property int|null $cooldown_minutes タスク後の振り返り時間（分）
 * @property int|null $recovery_minutes タスク完了後の休息時間（分）
 * @property \Illuminate\Support\Carbon|null $last_focus_at 最後にこのタスクに集中した時刻
 * @property \Illuminate\Support\Carbon|null $last_active_at 最後にアクティブだった時刻（heartbeat更新）
 * @property int $total_focus_minutes 集中に費やした合計時間（分）
 * @property int $distraction_count 記録された気が散った回数
 * @property int $abandonment_count 放棄された回数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskAbandonment> $abandonments
 * @property-read int|null $abandonments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContextSwitch> $contextSwitchesFrom
 * @property-read int|null $context_switches_from_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContextSwitch> $contextSwitchesTo
 * @property-read int|null $context_switches_to_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DistractionLog> $distractionLogs
 * @property-read int|null $distraction_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FocusEnvironment> $focusEnvironments
 * @property-read int|null $focus_environments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FocusSession> $focusSessions
 * @property-read int|null $focus_sessions_count
 * @property-read mixed $completion_percentage
 * @property-read mixed $days_until_deadline
 * @property-read mixed $energy_level_display
 * @property-read mixed $estimated_hours
 * @property-read mixed $estimated_time_formatted
 * @property-read mixed $is_due_soon
 * @property-read mixed $is_overdue
 * @property-read mixed $learning_path_id
 * @property-read mixed $priority_display
 * @property-read mixed $remaining_minutes
 * @property-read mixed $status_display
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KnowledgeItem> $knowledgeItems
 * @property-read int|null $knowledge_items_count
 * @property-read \App\Models\LearningMilestone|null $learningMilestone
 * @property-read \App\Models\Project|null $project
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subtask> $subtasks
 * @property-read int|null $subtasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task aiBreakdownEnabled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task byEnergyLevel($energyLevel)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task byMilestone($milestoneId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task byPriority($priority)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task byProject($projectId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task byStatus($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task dueSoon($days = 3)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task highEnergy()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task highPriority()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task lowEnergy()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task lowPriority()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAbandonmentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAiBreakdownEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAllowInterruptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCooldownMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDistractionCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereEnergyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereEstimatedMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereFocusDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereIsAbandoned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereLastActiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereLastFocusAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereLearningMilestoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRecoveryMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRequiresDeepFocus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereScheduledTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTotalFocusMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereWarmupMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withDeadline()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withEstimatedTime()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withoutEstimatedTime()
 */
	class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $task_id
 * @property int|null $focus_session_id
 * @property \Illuminate\Support\Carbon $started_at タスク開始時刻
 * @property \Illuminate\Support\Carbon $last_active_at 最後のアクティブ時刻
 * @property \Illuminate\Support\Carbon $abandoned_at 放棄検出時刻
 * @property int $duration_minutes 作業時間（分）
 * @property string $abandonment_type 放棄タイプ
 * @property int|null $inactivity_minutes 非アクティブ期間（分）
 * @property bool $auto_detected 自動検出されたかどうか
 * @property string|null $reason 放棄理由（ユーザー入力）
 * @property bool $resumed 後で再開したか
 * @property \Illuminate\Support\Carbon|null $resumed_at 再開時刻
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FocusSession|null $focusSession
 * @property-read mixed $formatted_duration
 * @property-read mixed $formatted_inactivity
 * @property-read mixed $resume_time
 * @property-read mixed $type_display
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment autoDetected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment byTask($taskId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment manual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment notResumed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment recent($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment resumed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereAbandonedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereAbandonmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereAutoDetected($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereFocusSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereInactivityMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereLastActiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereResumed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereResumedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskAbandonment whereUserId($value)
 */
	class TaskAbandonment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $task_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tag $tag
 * @property-read \App\Models\Task $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereUpdatedAt($value)
 */
	class TaskTag extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $milestone_template_id
 * @property string $title タスクタイトル
 * @property string|null $description 説明
 * @property int $sort_order 並び順
 * @property int|null $estimated_minutes 見積もり時間（分）
 * @property int $priority 優先度（1-5）
 * @property array<array-key, mixed>|null $resources リソース（リンク、動画など）
 * @property array<array-key, mixed>|null $subtasks サブタスクのリスト
 * @property array<array-key, mixed>|null $knowledge_items 学習コンテンツ（ノート、コード例、リンク、演習）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LearningMilestoneTemplate $milestoneTemplate
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereEstimatedMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereKnowledgeItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereMilestoneTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereResources($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereSubtasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTemplate whereUpdatedAt($value)
 */
	class TaskTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $name 授業名
 * @property string|null $description 詳細説明
 * @property string|null $room 教室
 * @property string|null $instructor 講師名
 * @property string $day 曜日
 * @property int $period 時限（1-10）
 * @property string $start_time 開始時刻
 * @property string $end_time 終了時刻
 * @property string $color 色（HEX）
 * @property string|null $icon アイコン名
 * @property string|null $notes メモ
 * @property int|null $learning_path_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $day_display
 * @property-read \App\Models\LearningPath|null $learningPath
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TimetableStudy> $studies
 * @property-read int|null $studies_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TimetableClassWeeklyContent> $weeklyContents
 * @property-read int|null $weekly_contents_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass byDay($day)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass byPeriod($period)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereInstructor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereLearningPathId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClass whereUserId($value)
 */
	class TimetableClass extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $timetable_class_id
 * @property int $year 年
 * @property int $week_number 週番号（1-53）
 * @property \Illuminate\Support\Carbon $week_start_date 週の開始日（月曜日）
 * @property string|null $title 週別タイトル
 * @property string|null $content 週別内容・トピック
 * @property string|null $homework 宿題
 * @property string|null $notes 週別メモ
 * @property string $status ステータス
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TimetableClass $timetableClass
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent byClass($classId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent byWeek($year, $weekNumber)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent byWeekStartDate($weekStartDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereHomework($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereTimetableClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereWeekNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereWeekStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableClassWeeklyContent whereYear($value)
 */
	class TimetableClassWeeklyContent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $timetable_class_id
 * @property string $title タイトル
 * @property string|null $description 詳細説明
 * @property string $type タイプ
 * @property string|null $subject 科目名
 * @property \Illuminate\Support\Carbon|null $due_date 提出期限
 * @property int $priority 優先度（1-5）
 * @property string $status ステータス
 * @property \Illuminate\Support\Carbon|null $completed_at 完了日時
 * @property int|null $task_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $days_until_due
 * @property-read mixed $is_overdue
 * @property-read mixed $status_display
 * @property-read mixed $type_display
 * @property-read \App\Models\Task|null $task
 * @property-read \App\Models\TimetableClass|null $timetableClass
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy byPriority($priority)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy dueSoon($days = 3)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereTimetableClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimetableStudy whereUserId($value)
 */
	class TimetableStudy extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name ユーザー名
 * @property string $email メールアドレス
 * @property string|null $fcm_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at メール確認日時
 * @property string $password パスワード（ハッシュ化）
 * @property string $language UI言語
 * @property string $timezone タイムゾーン
 * @property string|null $avatar_url アバター画像URL
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActivityLog> $activityLogs
 * @property-read int|null $activity_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AIInteraction> $aiInteractions
 * @property-read int|null $ai_interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AISuggestion> $aiSuggestions
 * @property-read int|null $ai_suggestions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DailyCheckin> $dailyCheckins
 * @property-read int|null $daily_checkins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DailyReview> $dailyReviews
 * @property-read int|null $daily_reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FocusSession> $focusSessions
 * @property-read int|null $focus_sessions_count
 * @property-read mixed $display_name
 * @property-read mixed $initials
 * @property-read mixed $language_display
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KnowledgeCategory> $knowledgeCategories
 * @property-read int|null $knowledge_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KnowledgeItem> $knowledgeItems
 * @property-read int|null $knowledge_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LearningPath> $learningPaths
 * @property-read int|null $learning_paths_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PerformanceMetric> $performanceMetrics
 * @property-read int|null $performance_metrics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \App\Models\UserSetting|null $settings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\UserProfile|null $userProfile
 * @property-read \App\Models\UserSetting|null $userSettings
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byLanguage($language)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byTimezone($timezone)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User recent($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User unverified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFcmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withAvatar()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutAvatar()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $goal_type ユーザーの主な目標
 * @property string|null $preferred_time 好みの作業時間帯
 * @property bool $notification_enabled
 * @property bool $onboarding_completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $goal_type_display
 * @property-read mixed $notification_status
 * @property-read mixed $onboarding_status
 * @property-read mixed $preferred_time_display
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile byGoalType($goalType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile completedOnboarding()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile pendingOnboarding()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereGoalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereOnboardingCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile wherePreferredTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile withNotifications()
 */
	class UserProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $theme
 * @property int $default_focus_minutes デフォルトの集中時間（分）
 * @property int $pomodoro_duration ポモドーロタイマーの長さ（分）
 * @property int $break_minutes 短い休憩時間（分）
 * @property int $long_break_minutes 長い休憩時間（分）
 * @property bool $auto_start_break 休憩を自動的に開始
 * @property bool $block_notifications 集中モード中は通知をブロック
 * @property bool $background_sound 集中モード中にBGMを再生
 * @property int $daily_target_tasks 1日の目標タスク数
 * @property bool $notification_enabled
 * @property bool $push_notifications プッシュ通知を有効にする
 * @property bool $daily_reminders デイリーリマインダーを有効にする
 * @property bool $goal_reminders ゴールリマインダーを有効にする
 * @property array<array-key, mixed>|null $reminder_times リマインダー時刻（JSON配列）
 * @property string $language
 * @property string $timezone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereAutoStartBreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereBackgroundSound($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereBlockNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereBreakMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereDailyReminders($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereDailyTargetTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereDefaultFocusMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereGoalReminders($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereLongBreakMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting wherePomodoroDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting wherePushNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereReminderTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereUserId($value)
 */
	class UserSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $total_tasks
 * @property int $completed_tasks
 * @property int $pending_tasks
 * @property int $in_progress_tasks
 * @property numeric $completion_rate
 * @property int $total_focus_time Total lifetime focus minutes
 * @property int $total_focus_sessions
 * @property int $average_session_duration
 * @property int $current_streak
 * @property int $longest_streak
 * @property \Illuminate\Support\Carbon|null $last_calculated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereAverageSessionDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereCompletedTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereCompletionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereCurrentStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereInProgressTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereLastCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereLongestStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache wherePendingTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereTotalFocusSessions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereTotalFocusTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereTotalTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStatsCache whereUserId($value)
 */
	class UserStatsCache extends \Eloquent {}
}

