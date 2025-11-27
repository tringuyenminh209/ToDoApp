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
        Schema::create('task_abandonments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');
            $table->foreignId('focus_session_id')
                ->nullable()
                ->constrained('focus_sessions')
                ->onDelete('set null')
                ->comment('関連するフォーカスセッション');

            // Timing
            $table->timestamp('started_at')
                ->comment('タスク開始時刻');
            $table->timestamp('last_active_at')
                ->comment('最後のアクティブ時刻');
            $table->timestamp('abandoned_at')
                ->comment('放棄検出時刻');
            $table->integer('duration_minutes')
                ->comment('作業時間（分）');

            // Abandonment details
            $table->enum('abandonment_type', [
                'app_switched',      // User switched to another app
                'long_inactivity',   // Long period of inactivity
                'manual',            // User manually abandoned
                'deadline_passed',   // Deadline passed while working
            ])->default('long_inactivity')
                ->comment('放棄タイプ');

            $table->integer('inactivity_minutes')
                ->nullable()
                ->comment('非アクティブ期間（分）');

            $table->boolean('auto_detected')->default(true)
                ->comment('自動検出されたかどうか');

            $table->text('reason')->nullable()
                ->comment('放棄理由（ユーザー入力）');

            // Did user come back?
            $table->boolean('resumed')->default(false)
                ->comment('後で再開したか');
            $table->timestamp('resumed_at')->nullable()
                ->comment('再開時刻');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'abandoned_at']);
            $table->index(['task_id', 'abandoned_at']);
            $table->index('abandonment_type');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_abandonments');
    }
};
