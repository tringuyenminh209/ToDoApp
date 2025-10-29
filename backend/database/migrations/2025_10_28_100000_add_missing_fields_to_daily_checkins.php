<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_checkins', function (Blueprint $table) {
            // Add mood enum (in addition to mood_score for backwards compatibility)
            $table->enum('mood', ['excellent', 'good', 'average', 'poor', 'terrible'])
                ->nullable()
                ->after('date')
                ->comment('気分（enum形式）');

            // Add sleep and stress tracking
            $table->decimal('sleep_hours', 4, 2)->nullable()
                ->after('mood')
                ->comment('睡眠時間');

            $table->enum('stress_level', ['low', 'medium', 'high'])
                ->nullable()
                ->after('sleep_hours')
                ->comment('ストレスレベル');

            // Add priorities and goals as JSON
            $table->json('priorities')->nullable()
                ->after('schedule_note')
                ->comment('優先事項（JSON配列）');

            $table->json('goals')->nullable()
                ->after('priorities')
                ->comment('目標（JSON配列）');

            // Add notes field (keep schedule_note for backwards compatibility)
            $table->text('notes')->nullable()
                ->after('goals')
                ->comment('メモ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_checkins', function (Blueprint $table) {
            $table->dropColumn([
                'mood',
                'sleep_hours',
                'stress_level',
                'priorities',
                'goals',
                'notes'
            ]);
        });
    }
};
