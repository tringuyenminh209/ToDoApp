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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Notification Type
            $table->enum('type', [
                'reminder',
                'achievement',
                'motivational',
                'system'
            ])->comment('通知タイプ');

            // Content
            $table->string('title', 255)->comment('タイトル');
            $table->text('message')->comment('内容');
            $table->json('data')->nullable()
                ->comment('追加データ（JSON）');

            // Status
            $table->boolean('is_read')->default(false)
                ->comment('既読');

            // Scheduling
            $table->timestamp('scheduled_at')->nullable()
                ->comment('送信予定時刻');
            $table->timestamp('sent_at')->nullable()
                ->comment('送信済み時刻');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_read']);
            $table->index('scheduled_at');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
