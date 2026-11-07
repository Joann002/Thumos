<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class DateHelper
{
    /**
     * Format a date in a human-readable French format
     */
    public static function formatFrench(Carbon $date): string
    {
        $months = [
            1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
            5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
            9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
        ];

        return $date->day . ' ' . $months[$date->month] . ' ' . $date->year;
    }

    /**
     * Get relative time in French (e.g., "il y a 2 jours")
     */
    public static function relativeTimeFrench(Carbon $date): string
    {
        $now = Carbon::now();
        $diff = $now->diffInDays($date);

        if ($date->isToday()) {
            return "aujourd'hui";
        }

        if ($date->isYesterday()) {
            return 'hier';
        }

        if ($date->isTomorrow()) {
            return 'demain';
        }

        if ($diff < 7 && $date->isPast()) {
            return "il y a {$diff} jour" . ($diff > 1 ? 's' : '');
        }

        if ($diff < 7 && $date->isFuture()) {
            return "dans {$diff} jour" . ($diff > 1 ? 's' : '');
        }

        return self::formatFrench($date);
    }

    /**
     * Get days remaining until a date
     */
    public static function daysUntil(Carbon $date): int
    {
        return Carbon::today()->diffInDays($date, false);
    }

    /**
     * Check if date is within the next N days
     */
    public static function isWithinDays(Carbon $date, int $days): bool
    {
        $future = Carbon::today()->addDays($days);
        return $date->between(Carbon::today(), $future);
    }

    /**
     * Get week number of the year
     */
    public static function weekNumber(Carbon $date): int
    {
        return $date->weekOfYear;
    }

    /**
     * Get French day name
     */
    public static function dayNameFrench(Carbon $date): string
    {
        $days = [
            1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi',
            5 => 'vendredi', 6 => 'samedi', 7 => 'dimanche',
        ];

        return $days[$date->dayOfWeekIso];
    }
}
