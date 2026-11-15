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
        // Send daily habit reminders at 7 PM
        $schedule->command('app:send-habit-reminders')
            ->dailyAt('19:00')
            ->withoutOverlapping();

        // Generate weekly reports every Monday at 9 AM
        $schedule->command('app:generate-weekly-report')
            ->weeklyOn(1, '9:00')
            ->withoutOverlapping();

        // Cleanup old logs every month
        $schedule->command('app:cleanup-old-logs --days=90')
            ->monthly()
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
