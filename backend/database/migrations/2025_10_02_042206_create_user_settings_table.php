<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ユーザー設定テーブル作成
     */
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Appearance
            $table->enum('theme', ['light', 'dark', 'auto'])->default('auto');

            // Pomodoro Settings
            $table->integer('default_focus_minutes')->default(25)
                ->comment('デフォルトの集中時間（分）');
            $table->integer('break_minutes')->default(5)
                ->comment('短い休憩時間（分）');
            $table->integer('long_break_minutes')->default(15)
                ->comment('長い休憩時間（分）');
            $table->boolean('auto_start_break')->default(false)
                ->comment('休憩を自動的に開始');

            // Daily Goals
            $table->integer('daily_target_tasks')->default(3)
                ->comment('1日の目標タスク数');

            // Notifications
            $table->boolean('notification_enabled')->default(true);
            $table->json('reminder_times')->nullable()
                ->comment('リマインダー時刻（JSON配列）');

            // Localization
            $table->enum('language', ['vi', 'en', 'ja'])->default('vi');
            $table->string('timezone', 50)->default('UTC');

            $table->timestamps();

            // Indexes
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
