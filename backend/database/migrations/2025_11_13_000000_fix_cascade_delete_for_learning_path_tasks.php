<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix cascade delete for tasks when learning milestone is deleted.
     * Change from onDelete('set null') to onDelete('cascade')
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['learning_milestone_id']);

            // Recreate it with cascade delete
            $table->foreign('learning_milestone_id')
                ->references('id')
                ->on('learning_milestones')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Drop the cascade constraint
            $table->dropForeign(['learning_milestone_id']);

            // Restore the original set null constraint
            $table->foreign('learning_milestone_id')
                ->references('id')
                ->on('learning_milestones')
                ->onDelete('set null');
        });
    }
};
