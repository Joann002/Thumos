<?php

namespace Tests\Feature;

use App\Models\Habit;
use App\Services\StreakCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StreakCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private StreakCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new StreakCalculator();
        Carbon::setTestNow('2026-06-18'); // a Thursday
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function dailyHabitWithDates(array $dates): Habit
    {
        $habit = Habit::create(['name' => 'Test', 'frequency' => 'daily']);

        foreach ($dates as $date) {
            $habit->logs()->create(['date' => $date, 'completed' => true]);
        }

        return $habit->fresh('logs');
    }

    public function test_no_logs_means_zero(): void
    {
        $habit = Habit::create(['name' => 'Vide', 'frequency' => 'daily']);

        $this->assertSame(0, $this->calculator->current($habit));
        $this->assertSame(0, $this->calculator->longest($habit));
    }

    public function test_three_consecutive_days_including_today(): void
    {
        $habit = $this->dailyHabitWithDates(['2026-06-16', '2026-06-17', '2026-06-18']);

        $this->assertSame(3, $this->calculator->current($habit));
    }

    public function test_today_not_done_yet_does_not_break_streak(): void
    {
        // Today (18th) missing, but the run up to yesterday stands.
        $habit = $this->dailyHabitWithDates(['2026-06-15', '2026-06-16', '2026-06-17']);

        $this->assertSame(3, $this->calculator->current($habit));
    }

    public function test_a_gap_before_yesterday_breaks_the_streak(): void
    {
        // 17th missing => only the 18th counts.
        $habit = $this->dailyHabitWithDates(['2026-06-14', '2026-06-15', '2026-06-18']);

        $this->assertSame(1, $this->calculator->current($habit));
    }

    public function test_longest_streak_finds_the_best_run(): void
    {
        $habit = $this->dailyHabitWithDates([
            '2026-06-01', '2026-06-02', '2026-06-03', '2026-06-04', // run of 4
            '2026-06-10', '2026-06-11', // run of 2
        ]);

        $this->assertSame(4, $this->calculator->longest($habit));
    }

    public function test_weekly_habit_counts_only_scheduled_days(): void
    {
        // Scheduled Mon/Wed/Fri. Today is Thursday 18th (not scheduled).
        $habit = Habit::create([
            'name' => 'Sport',
            'frequency' => 'weekly',
            'days_of_week' => [1, 3, 5],
        ]);

        // Fri 12, Mon 15, Wed 17 completed -> 3 consecutive scheduled days.
        foreach (['2026-06-12', '2026-06-15', '2026-06-17'] as $date) {
            $habit->logs()->create(['date' => $date, 'completed' => true]);
        }

        $this->assertSame(3, $this->calculator->current($habit->fresh('logs')));
    }

    public function test_weekly_habit_missed_scheduled_day_breaks_streak(): void
    {
        // Scheduled Mon/Wed/Fri; Wed 17 missed, today Thu not scheduled.
        $habit = Habit::create([
            'name' => 'Sport',
            'frequency' => 'weekly',
            'days_of_week' => [1, 3, 5],
        ]);

        foreach (['2026-06-12', '2026-06-15'] as $date) {
            $habit->logs()->create(['date' => $date, 'completed' => true]);
        }

        // Most recent scheduled day (Wed 17) was missed -> streak broken.
        $this->assertSame(0, $this->calculator->current($habit->fresh('logs')));
    }
}
