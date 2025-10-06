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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Action Info
            $table->string('action', 100)
                ->comment('アクション（例: task.created, session.completed）');

            // Resource Info
            $table->string('resource_type', 50)->nullable()
                ->comment('リソースタイプ（例: Task, Session）');
            $table->bigInteger('resource_id')->nullable()
                ->comment('リソースID');

            // Request Info
            $table->string('ip_address', 45)->nullable()
                ->comment('IPアドレス');
            $table->text('user_agent')->nullable()
                ->comment('ユーザーエージェント');

            // Additional Data
            $table->json('metadata')->nullable()
                ->comment('追加メタデータ（JSON）');

            $table->timestamp('created_at');

            // Indexes
            $table->index(['user_id', 'action']);
            $table->index(['resource_type', 'resource_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
