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
        Schema::create('ai_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Interaction Type
            $table->enum('interaction_type', [
                'breakdown',
                'suggestion',
                'coach',
                'reschedule'
            ])->comment('AI連携タイプ');

            // Request & Response Data
            $table->json('input_data')
                ->comment('入力データ（JSON）');
            $table->json('response_data')
                ->comment('レスポンスデータ（JSON）');

            // Performance Tracking
            $table->integer('processing_time_ms')->nullable()
                ->comment('処理時間（ミリ秒）');
            $table->boolean('success')->default(true)
                ->comment('連携成功');

            $table->timestamp('created_at');

            // Indexes
            $table->index(['user_id', 'interaction_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_interactions');
    }
};
