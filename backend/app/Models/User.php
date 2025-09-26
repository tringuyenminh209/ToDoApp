<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'locale',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ユーザーのプロジェクト
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * ユーザーのタスク
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * ユーザーのセッション
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * ユーザーのAIサマリー
     */
    public function aiSummaries()
    {
        return $this->hasMany(AISummary::class);
    }

    /**
     * ユーザーのプッシュトークン
     */
    public function pushTokens()
    {
        return $this->hasMany(PushToken::class);
    }

    /**
     * 今日のタスクを取得
     */
    public function todayTasks()
    {
        return $this->tasks()->whereDate('created_at', today());
    }

    /**
     * 完了したタスクを取得
     */
    public function completedTasks()
    {
        return $this->tasks()->where('status', 'completed');
    }

    /**
     * 今日のセッションを取得
     */
    public function todaySessions()
    {
        return $this->sessions()->whereDate('start_at', today());
    }
}
