<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DailyHabitReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('app:send-habit-reminders')]
#[Description('Envoie un rappel par email des habitudes non cochées du jour à chaque utilisateur concerné.')]
class SendHabitReminders extends Command
{
    public function handle(): int
    {
        $today = Carbon::today();
        $notified = 0;

        User::query()
            ->with(['habits.logs' => fn ($q) => $q->whereDate('date', $today)])
            ->chunk(100, function ($users) use ($today, &$notified) {
                foreach ($users as $user) {
                    $pending = $user->habits
                        ->filter(fn ($habit) => $habit->isDueOn($today) && $habit->logs->isEmpty())
                        ->pluck('name')
                        ->values()
                        ->all();

                    if ($pending !== []) {
                        $user->notify(new DailyHabitReminder($pending));
                        $notified++;
                    }
                }
            });

        $this->info("Rappels envoyés à {$notified} utilisateur(s).");

        return self::SUCCESS;
    }
}
