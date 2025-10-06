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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Metric Info
            $table->date('metric_date')->comment('メトリクス日');
            $table->enum('metric_type', [
                'daily_completion',
                'focus_time',
                'mood_trend',
                'streak_maintenance'
            ])->comment('指標タイプ');

            // Metric Value
            $table->decimal('metric_value', 10, 4)
                ->comment('メトリクス値');

            // Trend Analysis
            $table->enum('trend_direction', ['up', 'down', 'stable'])->nullable()
                ->comment('トレンド');
            $table->text('notes')->nullable()
                ->comment('メモ');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'metric_type', 'metric_date']);
            $table->index('metric_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};
