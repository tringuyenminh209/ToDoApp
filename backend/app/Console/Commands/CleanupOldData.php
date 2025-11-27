<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Services\TaskAbandonmentService;
use Illuminate\Console\Command;

class CleanupOldData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cleanup:old-data';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup old notifications and abandonment records';

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
        $this->info('Cleaning up old data...');

        // Delete old read notifications (older than 30 days)
        $deletedNotifications = Notification::where('is_read', true)
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        $this->line("Deleted {$deletedNotifications} old read notifications.");

        // Delete old abandonment records (older than 90 days)
        $deletedAbandonments = $this->abandonmentService->cleanupOldAbandonments();

        $this->line("Deleted {$deletedAbandonments} old abandonment records.");

        $this->info('Cleanup completed successfully.');

        return Command::SUCCESS;
    }
}
