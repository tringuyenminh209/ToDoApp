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
        Schema::create('daily_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Check-in Date
            $table->date('date')->comment('チェックイン日');

            // Morning State
            $table->enum('energy_level', ['low', 'medium', 'high'])
                ->comment('朝のエネルギーレベル');
            $table->tinyInteger('mood_score')
                ->comment('気分スコア（1-5）');

            // Planning
            $table->text('schedule_note')->nullable()
                ->comment('今日のスケジュールメモ');
            $table->boolean('ai_suggestions_generated')->default(false)
                ->comment('AI提案生成済み');

            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_checkins');
    }
};
