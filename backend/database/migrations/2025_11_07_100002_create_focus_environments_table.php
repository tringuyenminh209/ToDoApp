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
        Schema::create('focus_environments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('focus_session_id')->nullable()->constrained('focus_sessions')->onDelete('cascade');

            // Environment checklist
            $table->boolean('quiet_space')->default(false);
            $table->boolean('phone_silent')->default(false);
            $table->boolean('materials_ready')->default(false);
            $table->boolean('water_coffee_ready')->default(false);
            $table->boolean('comfortable_position')->default(false);
            $table->boolean('notifications_off')->default(false);
            $table->json('apps_closed')->nullable()->comment('List of apps/tabs closed');

            // Status
            $table->boolean('all_checks_passed')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('task_id');
            $table->index('user_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('focus_environments');
    }
};

