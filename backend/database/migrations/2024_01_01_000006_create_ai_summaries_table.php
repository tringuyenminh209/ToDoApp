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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('summary_date');
            $table->json('highlights')->nullable();
            $table->json('blockers')->nullable();
            $table->json('plan')->nullable();
            $table->text('insights')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'summary_date'], 'unique_user_date');
            $table->index(['user_id', 'summary_date']);
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
