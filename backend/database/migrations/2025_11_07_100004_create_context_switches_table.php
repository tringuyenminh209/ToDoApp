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
        Schema::create('context_switches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // From task
            $table->foreignId('from_task_id')->nullable()->constrained('tasks')->onDelete('set null');
            $table->string('from_category')->nullable();
            $table->integer('from_focus_difficulty')->nullable();

            // To task
            $table->foreignId('to_task_id')->constrained('tasks')->onDelete('cascade');
            $table->string('to_category')->nullable();
            $table->integer('to_focus_difficulty')->nullable();

            // Context switch details
            $table->boolean('is_significant_switch')->default(false)->comment('Different category or focus level');
            $table->integer('estimated_cost_minutes')->default(23)->comment('Estimated recovery time');
            $table->boolean('user_proceeded')->default(false)->comment('Did user proceed despite warning');
            $table->text('user_note')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index(['user_id', 'created_at']);
            $table->index('is_significant_switch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('context_switches');
    }
};

