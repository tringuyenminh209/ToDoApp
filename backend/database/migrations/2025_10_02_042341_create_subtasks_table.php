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
        Schema::create('subtasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            // Subtask Info
            $table->string('title', 255)->comment('サブタスクタイトル');
            $table->boolean('is_completed')->default(false)
                ->comment('完了済み');
            $table->integer('estimated_minutes')->nullable()
                ->comment('予想時間（分）');

            // Ordering
            $table->integer('sort_order')->default(0)
                ->comment('並び順');

            $table->timestamps();

            // Indexes
            $table->index(['task_id', 'sort_order']);
            $table->index(['task_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtasks');
    }
};
