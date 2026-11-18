<?php

namespace Tests\Unit;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Services\HabitAnalyzer;
use App\Services\StreakCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HabitAnalyzerTest extends TestCase
{
    use RefreshDatabase;

    private HabitAnalyzer $analyzer;

    protected function setUp(): void
    {
        parent::setUp();
        $streakCalculator = new StreakCalculator;
        $this->analyzer = new HabitAnalyzer($streakCalculator);
    }

    public function test_it_calculates_habit_analytics()
    {
        $habit = Habit::factory()->daily()->create();

        // Complete 20 out of last 30 days
        for ($i = 0; $i < 20; $i++) {
            HabitLog::create([
                'habit_id' => $habit->id,
                'date' => Carbon::today()->subDays($i),
                'completed' => true,
            ]);
        }

        $analytics = $this->analyzer->getAnalytics($habit, 30);

        $this->assertEquals(30, $analytics['expected_days']);
        $this->assertEquals(20, $analytics['completed_days']);
        $this->assertEquals(10, $analytics['missed_days']);
        $this->assertEquals(30, $analytics['period_days']);
    }

    public function test_it_identifies_best_performing_habits()
    {
        $user = User::factory()->create();

        $goodHabit = Habit::factory()->create(['user_id' => $user->id]);
        $badHabit = Habit::factory()->create(['user_id' => $user->id]);

        // Good habit: 90% completion
        for ($i = 0; $i < 27; $i++) {
            HabitLog::create([
                'habit_id' => $goodHabit->id,
                'date' => Carbon::today()->subDays($i),
                'completed' => true,
            ]);
        }

        // Bad habit: 30% completion
        for ($i = 0; $i < 9; $i++) {
            HabitLog::create([
                'habit_id' => $badHabit->id,
                'date' => Carbon::today()->subDays($i),
                'completed' => true,
            ]);
        }

        $habits = collect([$goodHabit, $badHabit]);
        $bestPerformers = $this->analyzer->getBestPerformers($habits, 1);

        $this->assertCount(1, $bestPerformers);
        $this->assertTrue($bestPerformers->contains($goodHabit));
    }

    public function test_completion_window_includes_today_once_and_ignores_unscheduled_days(): void
    {
        Carbon::setTestNow('2026-06-18');
        try {
            $habit = Habit::factory()->weekly([4])->create();
            foreach (['2026-06-11', '2026-06-17', '2026-06-18', '2026-06-19'] as $date) {
                $habit->logs()->create(['date' => $date, 'completed' => true]);
            }
            $summary = $this->analyzer->getAnalytics($habit, 7);
            $this->assertSame(1, $summary['expected_days']);
            $this->assertSame(1, $summary['completed_days']);
            $this->assertSame(0, $summary['missed_days']);
            $this->assertEquals(100, $summary['completion_rate']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_completion_rate_uses_one_log_query_instead_of_one_per_day(): void
    {
        $habit = Habit::factory()->daily()->create();
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->assertEquals(0, $habit->getCompletionRate(30));
        $this->assertCount(1, DB::getQueryLog());
        DB::disableQueryLog();
    }
}
