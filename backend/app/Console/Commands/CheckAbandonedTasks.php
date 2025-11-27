<?php

namespace App\Console\Commands;

use App\Services\TaskAbandonmentService;
use Illuminate\Console\Command;

class CheckAbandonedTasks extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tasks:check-abandoned';

    /**
     * The console command description.
     */
    protected $description = 'Check for abandoned tasks and mark them';

    protected TaskAbandonmentService $abandonmentService;

    public function __construct(TaskAbandonmentService $abandonmentService)
    {
        parent::__construct();
        $this->abandonmentService = $abandonmentService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for abandoned tasks...');

        $abandonedTasks = $this->abandonmentService->checkAbandonedTasks();

        if (empty($abandonedTasks)) {
            $this->info('No abandoned tasks found.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . count($abandonedTasks) . ' abandoned tasks:');

        foreach ($abandonedTasks as $task) {
            $this->line("- Task #{$task['task_id']}: {$task['task_title']} (User: {$task['user_id']})");
        }

        return Command::SUCCESS;
    }
}
