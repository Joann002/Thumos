<?php

namespace Tests\Unit;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Services\StreakCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StreakCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private StreakCalculator $calculator;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new StreakCalculator();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_calculates_zero_streak_for_habit_with_no_logs()
    {
        $habit = Habit::factory()->create([
            'user_id' => $this->user->id,
            'frequency' => 'daily',
        ]);

        $streak = $this->calculator->current($habit);

        $this->assertEquals(0, $streak);
    }

    /** @test */
    public function it_calculates_current_streak_for_consecutive_days()
    {
        $habit = Habit::factory()->create([
            'user_id' => $this->user->id,
            'frequency' => 'daily',
        ]);

        // Create logs for last 5 days
        for ($i = 4; $i >= 0; $i--) {
            HabitLog::create([
                'habit_id' => $habit->id,
                'date' => Carbon::today()->subDays($i),
                'completed' => true,
            ]);
        }

        $streak = $this->calculator->current($habit);

        $this->assertEquals(5, $streak);
    }

    /** @test */
    public function it_breaks_streak_on_missed_day()
    {
        $habit = Habit::factory()->create([
            'user_id' => $this->user->id,
            'frequency' => 'daily',
        ]);

        // Day 1-3: completed
        for ($i = 5; $i >= 3; $i--) {
            HabitLog::create([
                'habit_id' => $habit->id,
                'date' => Carbon::today()->subDays($i),
                'completed' => true,
            ]);
        }

        // Day 4: missed (no log)

        // Day 5-6: completed
        HabitLog::create([
            'habit_id' => $habit->id,
            'date' => Carbon::today()->subDays(1),
            'completed' => true,
        ]);

        HabitLog::create([
            'habit_id' => $habit->id,
            'date' => Carbon::today(),
            'completed' => true,
        ]);

        $streak = $this->calculator->current($habit);

        // Should only count last 2 days
        $this->assertEquals(2, $streak);
    }

    /** @test */
    public function it_calculates_longest_streak()
    {
        $habit = Habit::factory()->create([
            'user_id' => $this->user->id,
            'frequency' => 'daily',
        ]);

        // First streak: 10 days
        for ($i = 20; $i >= 11; $i--) {
            HabitLog::create([
                'habit_id' => $habit->id,
                'date' => Carbon::today()->subDays($i),
                'completed' => true,
            ]);
        }

        // Gap

        // Current streak: 5 days
        for ($i = 4; $i >= 0; $i--) {
            HabitLog::create([
                'habit_id' => $habit->id,
                'date' => Carbon::today()->subDays($i),
                'completed' => true,
            ]);
        }

        $longest = $this->calculator->longest($habit);

        $this->assertEquals(10, $longest);
    }

    /** @test */
    public function it_respects_weekly_habit_schedule()
    {
        $habit = Habit::factory()->create([
            'user_id' => $this->user->id,
            'frequency' => 'weekly',
            'days_of_week' => [1, 3, 5], // Mon, Wed, Fri
        ]);

        $monday = Carbon::parse('next monday');
        
        // Complete Monday, Wednesday, Friday
        HabitLog::create([
            'habit_id' => $habit->id,
            'date' => $monday,
            'completed' => true,
        ]);

        HabitLog::create([
            'habit_id' => $habit->id,
            'date' => $monday->copy()->addDays(2),
            'completed' => true,
        ]);

        HabitLog::create([
            'habit_id' => $habit->id,
            'date' => $monday->copy()->addDays(4),
            'completed' => true,
        ]);

        // Tuesday and Thursday are not scheduled, so they shouldn't break the streak
        $streak = $this->calculator->current($habit);

        $this->assertEquals(3, $streak);
    }
}
