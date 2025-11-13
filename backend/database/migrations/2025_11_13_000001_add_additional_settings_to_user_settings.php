<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add additional settings columns to user_settings table
     */
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            // Notification Settings
            $table->boolean('push_notifications')->default(true)
                ->comment('プッシュ通知を有効にする')->after('notification_enabled');
            $table->boolean('daily_reminders')->default(true)
                ->comment('デイリーリマインダーを有効にする')->after('push_notifications');
            $table->boolean('goal_reminders')->default(false)
                ->comment('ゴールリマインダーを有効にする')->after('daily_reminders');

            // Focus Mode Settings
            $table->boolean('block_notifications')->default(true)
                ->comment('集中モード中は通知をブロック')->after('auto_start_break');
            $table->boolean('background_sound')->default(false)
                ->comment('集中モード中にBGMを再生')->after('block_notifications');

            // Pomodoro duration setting
            $table->integer('pomodoro_duration')->default(25)
                ->comment('ポモドーロタイマーの長さ（分）')->after('default_focus_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn([
                'push_notifications',
                'daily_reminders',
                'goal_reminders',
                'block_notifications',
                'background_sound',
                'pomodoro_duration',
            ]);
        });
    }
};
