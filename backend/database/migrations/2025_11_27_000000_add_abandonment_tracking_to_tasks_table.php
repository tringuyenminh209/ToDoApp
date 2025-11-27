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
        Schema::table('tasks', function (Blueprint $table) {
            // Track when user last interacted with this task
            $table->timestamp('last_active_at')->nullable()
                ->after('last_focus_at')
                ->comment('最後にアクティブだった時刻（heartbeat更新）');

            // Flag if task was abandoned
            $table->boolean('is_abandoned')->default(false)
                ->after('status')
                ->comment('放棄されたタスク');

            // Count how many times task was abandoned
            $table->integer('abandonment_count')->default(0)
                ->after('distraction_count')
                ->comment('放棄された回数');

            // Add index for finding abandoned tasks
            $table->index(['status', 'last_active_at']);
            $table->index(['user_id', 'is_abandoned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status', 'last_active_at']);
            $table->dropIndex(['user_id', 'is_abandoned']);
            $table->dropColumn(['last_active_at', 'is_abandoned', 'abandonment_count']);
        });
    }
};
