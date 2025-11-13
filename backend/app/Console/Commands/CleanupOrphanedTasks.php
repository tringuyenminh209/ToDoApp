<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOrphanedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:cleanup-orphaned {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up tasks that belong to deleted learning milestones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Searching for orphaned tasks...');

        // Find tasks with learning_milestone_id that doesn't exist anymore
        $orphanedTasks = DB::table('tasks as t')
            ->leftJoin('learning_milestones as lm', 't.learning_milestone_id', '=', 'lm.id')
            ->whereNotNull('t.learning_milestone_id')
            ->whereNull('lm.id')
            ->select('t.id', 't.title', 't.learning_milestone_id', 't.created_at')
            ->get();

        if ($orphanedTasks->isEmpty()) {
            $this->info('✅ No orphaned tasks found!');
            return 0;
        }

        $this->warn("Found {$orphanedTasks->count()} orphaned tasks:");

        // Display the orphaned tasks
        $this->table(
            ['ID', 'Title', 'Milestone ID (deleted)', 'Created At'],
            $orphanedTasks->map(function ($task) {
                return [
                    $task->id,
                    $task->title,
                    $task->learning_milestone_id,
                    $task->created_at,
                ];
            })
        );

        if ($this->option('dry-run')) {
            $this->info('🔍 Dry run mode: No tasks were deleted.');
            $this->info('Run without --dry-run to actually delete these tasks.');
            return 0;
        }

        // Ask for confirmation
        if (!$this->confirm('Do you want to delete these orphaned tasks?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Delete the orphaned tasks
        $taskIds = $orphanedTasks->pluck('id')->toArray();
        $deleted = Task::whereIn('id', $taskIds)->delete();

        $this->info("✅ Successfully deleted {$deleted} orphaned tasks.");

        return 0;
    }
}
