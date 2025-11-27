<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send task reminders (chạy mỗi 15 phút)
        // Gửi reminder cho task sắp đến giờ bắt đầu
        $schedule->command('tasks:send-reminders')
                 ->everyFifteenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Send deadline reminders (chạy mỗi giờ)
        // Gửi reminder cho task sắp đến deadline
        $schedule->command('tasks:send-deadline-reminders')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Send incomplete task reminders (chạy mỗi 6 giờ)
        // Gửi reminder cho task quá hạn hoặc lâu chưa làm
        $schedule->command('tasks:send-incomplete-reminders')
                 ->everySixHours()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Send scheduled notifications (chạy mỗi phút)
        // Gửi các notification đã được lên lịch
        $schedule->command('notifications:send-scheduled')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Check abandoned tasks (chạy mỗi 5 phút)
        // Kiểm tra task bị bỏ dở
        $schedule->command('tasks:check-abandoned')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Cleanup old data (chạy mỗi ngày lúc 2 giờ sáng)
        // Dọn dẹp dữ liệu cũ (notifications đã đọc > 30 ngày, abandonment > 90 ngày)
        $schedule->command('cleanup:old-data')
                 ->dailyAt('02:00')
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

