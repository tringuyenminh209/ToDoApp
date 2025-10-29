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
        Schema::create('ai_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Summary Type & Date
            $table->enum('summary_type', ['daily', 'weekly', 'monthly'])
                ->comment('サマリータイプ');
            $table->date('date')->comment('サマリー日付');

            // Content & Metrics
            $table->json('content')->comment('AIサマリーコンテンツ');
            $table->json('metrics')->nullable()->comment('メトリクス情報');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'summary_type']);
            $table->index(['user_id', 'date']);
            $table->unique(['user_id', 'summary_type', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_summaries');
    }
};
