<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * tasksテーブルにLearning Milestone関連付けを追加
     * TaskとLearning Pathを統合
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('learning_milestone_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('learning_milestones')
                  ->onDelete('set null')
                  ->comment('Learning Milestoneとの関連');

            // Index追加
            $table->index('learning_milestone_id', 'idx_milestone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['learning_milestone_id']);
            $table->dropIndex('idx_milestone');
            $table->dropColumn('learning_milestone_id');
        });
    }
};

