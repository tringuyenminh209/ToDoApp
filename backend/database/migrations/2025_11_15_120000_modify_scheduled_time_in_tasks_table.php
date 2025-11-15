<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Modify scheduled_time column in tasks table
 * Change from timestamp to time (only hours and minutes)
 *
 * Purpose:
 * - Store only the time (HH:MM:SS) instead of full datetime
 * - Align with study_schedules which also uses time type
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert existing timestamp data to time format
        DB::statement("
            UPDATE tasks
            SET scheduled_time = TIME(scheduled_time)
            WHERE scheduled_time IS NOT NULL
        ");

        // Then modify the column type
        Schema::table('tasks', function (Blueprint $table) {
            $table->time('scheduled_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('scheduled_time')->nullable()->change();
        });
    }
};
