<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create study_schedules table
 * スケジュール学習テーブルを作成
 *
 * Purpose:
 * - Enforce discipline by requiring users to set specific study times
 * - Support weekly recurring study schedule
 * - Enable reminders for upcoming study sessions
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('study_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained()->onDelete('cascade');

            // Study time settings
            $table->time('study_time'); // e.g., "19:30:00"
            $table->tinyInteger('day_of_week'); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $table->integer('duration_minutes')->default(60); // Default 1 hour

            // Status
            $table->boolean('is_active')->default(true); // Can temporarily disable a schedule

            // Reminder settings
            $table->integer('reminder_before_minutes')->default(30); // Remind 30 mins before
            $table->boolean('reminder_enabled')->default(true);

            // Tracking
            $table->integer('completed_sessions')->default(0); // Number of times user studied
            $table->integer('missed_sessions')->default(0); // Number of times user missed
            $table->date('last_studied_at')->nullable(); // Last time user studied on this schedule

            $table->timestamps();

            // Indexes
            $table->index(['learning_path_id', 'day_of_week']);
            $table->index(['learning_path_id', 'is_active']);

            // Unique constraint: one schedule per learning path per day/time
            $table->unique(['learning_path_id', 'day_of_week', 'study_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_schedules');
    }
};
