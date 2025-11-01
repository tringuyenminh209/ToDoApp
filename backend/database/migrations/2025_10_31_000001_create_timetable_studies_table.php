<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 宿題・復習テーブル作成
     * Timetable Studies: 宿題・復習・試験管理
     */
    public function up(): void
    {
        Schema::create('timetable_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('timetable_class_id')
                  ->nullable()
                  ->constrained('timetable_classes')
                  ->onDelete('set null')
                  ->comment('関連授業');

            // Study情報
            $table->string('title', 255)->comment('タイトル');
            $table->text('description')->nullable()->comment('詳細説明');
            $table->enum('type', ['homework', 'review', 'exam', 'project'])
                  ->default('homework')
                  ->comment('タイプ');

            // 科目
            $table->string('subject', 255)->nullable()->comment('科目名');

            // 期限・優先度
            $table->date('due_date')->nullable()->comment('提出期限');
            $table->integer('priority')->default(3)->comment('優先度（1-5）');

            // ステータス
            $table->enum('status', ['pending', 'in_progress', 'completed'])
                  ->default('pending')
                  ->comment('ステータス');
            $table->timestamp('completed_at')->nullable()->comment('完了日時');

            // タスク関連
            $table->foreignId('task_id')
                  ->nullable()
                  ->constrained('tasks')
                  ->onDelete('set null')
                  ->comment('関連Task');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['user_id', 'due_date'], 'idx_user_due_date');
            $table->index(['user_id', 'type'], 'idx_user_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_studies');
    }
};

