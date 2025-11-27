<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:send-scheduled';

    /**
     * The console command description.
     */
    protected $description = 'Send scheduled notifications that are due';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Sending scheduled notifications...');

        // Find notifications that should be sent
        $notifications = Notification::scheduled()->get();

        if ($notifications->isEmpty()) {
            $this->info('No scheduled notifications to send.');
            return Command::SUCCESS;
        }

        $sentCount = 0;

        foreach ($notifications as $notification) {
            try {
                // Mark as sent
                $notification->markAsSent();

                // TODO: Integrate with push notification service (FCM)
                // For now, just mark as sent

                $sentCount++;

                $this->line("Sent: {$notification->title} to user #{$notification->user_id}");

            } catch (\Exception $e) {
                Log::error('Failed to send scheduled notification', [
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Successfully sent {$sentCount} notifications.");

        return Command::SUCCESS;
    }
}
