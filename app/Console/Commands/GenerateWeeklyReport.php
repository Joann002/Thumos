<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:generate-weekly-report {user?}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a weekly progress report for users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->argument('user');
        $users = $userId ? User::where('id', $userId)->get() : User::all();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        foreach ($users as $user) {
            $this->info("Generating report for {$user->name}...");

            // Goals progress
            $completedGoals = $user->goals()
                ->where('status', 'completed')
                ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->count();

            // Habits completion
            $habitsCompleted = $user->habits()
                ->withCount([
                    'logs' => function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('completed', true)
                            ->whereBetween('date', [$startOfWeek, $endOfWeek]);
                    }
                ])
                ->get()
                ->sum('logs_count');

            // Journal entries
            $journalEntries = $user->journalEntries()
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->count();

            $this->table(
                ['Métrique', 'Valeur'],
                [
                    ['Objectifs complétés', $completedGoals],
                    ['Habitudes réalisées', $habitsCompleted],
                    ['Entrées de journal', $journalEntries],
                ]
            );
        }

        $this->info('Reports generated successfully!');

        return Command::SUCCESS;
    }
}
