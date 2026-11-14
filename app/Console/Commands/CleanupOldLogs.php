<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:cleanup-old-logs {--days=90 : Number of days to keep}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old habit logs and journal entries';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up records older than {$days} days ({$cutoffDate->toDateString()})...");

        // Note: Only cleanup if explicitly requested, keep data by default
        if (!$this->confirm('This will permanently delete old data. Continue?', false)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        // Count records before deletion
        $habitLogsCount = DB::table('habit_logs')
            ->where('date', '<', $cutoffDate)
            ->count();

        // Delete old habit logs (keeping data structure intact)
        $deleted = DB::table('habit_logs')
            ->where('date', '<', $cutoffDate)
            ->delete();

        $this->info("Deleted {$deleted} habit log(s).");

        $this->info('Cleanup completed successfully!');

        return Command::SUCCESS;
    }
}
