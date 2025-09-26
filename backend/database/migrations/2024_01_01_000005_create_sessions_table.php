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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('start_at');
            $table->timestamp('end_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->enum('session_type', ['work', 'break'])->default('work');
            $table->enum('outcome', ['completed', 'skipped', 'interrupted'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_at']);
            $table->index(['user_id', 'start_at'], 'idx_user_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
