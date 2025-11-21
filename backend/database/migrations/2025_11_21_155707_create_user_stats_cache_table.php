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
        Schema::create('user_stats_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Task Statistics
            $table->integer('total_tasks')->default(0);
            $table->integer('completed_tasks')->default(0);
            $table->integer('pending_tasks')->default(0);
            $table->integer('in_progress_tasks')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);

            // Focus Session Statistics
            $table->integer('total_focus_time')->default(0)->comment('Total lifetime focus minutes');
            $table->integer('total_focus_sessions')->default(0);
            $table->integer('average_session_duration')->default(0);

            // Streak Statistics
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);

            // Cache metadata
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique('user_id');
            $table->index('last_calculated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stats_cache');
    }
};
