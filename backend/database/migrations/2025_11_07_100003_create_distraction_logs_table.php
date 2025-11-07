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
        Schema::create('distraction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('focus_session_id')->nullable()->constrained('focus_sessions')->onDelete('cascade');

            // Distraction details
            $table->enum('distraction_type', [
                'phone',
                'social_media',
                'noise',
                'person',
                'thoughts',
                'hunger_thirst',
                'fatigue',
                'other'
            ])->default('other');

            $table->integer('duration_seconds')->nullable()->comment('How long the distraction lasted');
            $table->text('notes')->nullable();

            // Timing
            $table->timestamp('occurred_at')->useCurrent();
            $table->time('time_of_day')->nullable()->comment('What time of day did distraction occur');

            $table->timestamps();

            // Indexes
            $table->index('task_id');
            $table->index('user_id');
            $table->index('distraction_type');
            $table->index(['user_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distraction_logs');
    }
};

