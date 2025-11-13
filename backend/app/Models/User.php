<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

Class User extends Authenticatable{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'language',
        'timezone',
        'avatar_url',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function focusSessions(): HasMany
    {
        return $this->hasMany(FocusSession::class);
    }

    public function aiSuggestions(): HasMany
    {
        return $this->hasMany(AISuggestion::class);
    }

    public function aiInteractions(): HasMany
    {
        return $this->hasMany(AIInteraction::class);
    }

    public function dailyCheckins(): HasMany
    {
        return $this->hasMany(DailyCheckin::class);
    }

    public function dailyReviews(): HasMany
    {
        return $this->hasMany(DailyReview::class);
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function userSettings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    // Alias for convenience
    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function userStats(): HasOne
    {
        return $this->hasOne(UserStats::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(PerformanceMetric::class);
    }

    public function learningPaths(): HasMany
    {
        return $this->hasMany(LearningPath::class);
    }

    public function knowledgeCategories(): HasMany
    {
        return $this->hasMany(KnowledgeCategory::class);
    }

    public function knowledgeItems(): HasMany
    {
        return $this->hasMany(KnowledgeItem::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByTimezone($query, $timezone)
    {
        return $query->where('timezone', $timezone);
    }

    public function scopeWithAvatar($query)
    {
        return $query->whereNotNull('avatar_url');
    }

    public function scopeWithoutAvatar($query)
    {
        return $query->whereNull('avatar_url');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeActive($query)
    {
        return $query->whereHas('tasks', function($q) {
            $q->where('status', 'in_progress');
        });
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->name ?: 'User #' . $this->id;
    }

    public function getAvatarUrlAttribute($value)
    {
        return $value ?: 'https://via.placeholder.com/100';
    }

    public function getLanguageDisplayAttribute()
    {
        return match($this->language) {
            'vi' => 'Tiếng Việt',
            'en' => 'English',
            'ja' => '日本語',
            default => 'Unknown',
        };
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        return $initials ?: 'U';
    }

    public function getTimezoneAttribute($value)
    {
        return $value ?: 'UTC';
    }

    // Helper methods
    public function isVerified()
    {
        return !is_null($this->email_verified_at);
    }

    public function isUnverified()
    {
        return is_null($this->email_verified_at);
    }

    public function hasAvatar()
    {
        return !is_null($this->avatar_url);
    }

    public function getTotalTasksCount()
    {
        return $this->tasks()->count();
    }

    public function getCompletedTasksCount()
    {
        return $this->tasks()->where('status', 'completed')->count();
    }

    public function getActiveTasksCount()
    {
        return $this->tasks()->where('status', 'in_progress')->count();
    }

    public function getPendingTasksCount()
    {
        return $this->tasks()->where('status', 'pending')->count();
    }

    public function getTotalProjectsCount()
    {
        return $this->projects()->count();
    }

    public function getActiveProjectsCount()
    {
        return $this->projects()->where('status', 'active')->count();
    }

    public function getTotalLearningPathsCount()
    {
        return $this->learningPaths()->count();
    }

    public function getActiveLearningPathsCount()
    {
        return $this->learningPaths()->where('status', 'active')->count();
    }

    public function getTotalKnowledgeItemsCount()
    {
        return $this->knowledgeItems()->count();
    }

    public function getTotalFocusSessionsCount()
    {
        return $this->focusSessions()->count();
    }

    public function getTotalFocusTimeMinutes()
    {
        return $this->focusSessions()->sum('duration_minutes');
    }

    public function getTotalFocusTimeHours()
    {
        return round($this->getTotalFocusTimeMinutes() / 60, 1);
    }

    public function getProductivityScore()
    {
        $completedTasks = $this->getCompletedTasksCount();
        $totalTasks = $this->getTotalTasksCount();

        if ($totalTasks === 0) return 0;

        return round(($completedTasks / $totalTasks) * 100, 1);
    }

    public function getActivitySummary()
    {
        return [
            'tasks' => [
                'total' => $this->getTotalTasksCount(),
                'completed' => $this->getCompletedTasksCount(),
                'active' => $this->getActiveTasksCount(),
                'pending' => $this->getPendingTasksCount(),
            ],
            'projects' => [
                'total' => $this->getTotalProjectsCount(),
                'active' => $this->getActiveProjectsCount(),
            ],
            'learning_paths' => [
                'total' => $this->getTotalLearningPathsCount(),
                'active' => $this->getActiveLearningPathsCount(),
            ],
            'knowledge_items' => $this->getTotalKnowledgeItemsCount(),
            'focus_sessions' => $this->getTotalFocusSessionsCount(),
            'focus_time_hours' => $this->getTotalFocusTimeHours(),
            'productivity_score' => $this->getProductivityScore(),
        ];
    }

    public function getRecentActivity($days = 7)
    {
        return [
            'tasks_created' => $this->tasks()->where('created_at', '>=', now()->subDays($days))->count(),
            'tasks_completed' => $this->tasks()->where('status', 'completed')
                ->where('updated_at', '>=', now()->subDays($days))->count(),
            'focus_sessions' => $this->focusSessions()->where('created_at', '>=', now()->subDays($days))->count(),
            'daily_checkins' => $this->dailyCheckins()->where('created_at', '>=', now()->subDays($days))->count(),
        ];
    }

    public function updateLanguage($language)
    {
        $this->update(['language' => $language]);
    }

    public function updateTimezone($timezone)
    {
        $this->update(['timezone' => $timezone]);
    }

    public function updateAvatar($avatarUrl)
    {
        $this->update(['avatar_url' => $avatarUrl]);
    }

    public function markEmailAsVerified()
    {
        $this->update(['email_verified_at' => now()]);
    }
}
