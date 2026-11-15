<?php

namespace Tests\Unit;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Services\HabitAnalyzer;
use App\Services\StreakCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HabitAnalyzerTest extends TestCase
{
    use RefreshDatabase;

    private HabitAnalyzer $analyzer;

    protected function setUp(): void
    {
        parent::setUp();
        $streakCalculator = new StreakCalculator();
        $this->analyzer = new HabitAnalyzer($streakCalculator);
    }

    /** @test */
    public function it_calculates_habit_analytics()
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

    /** @test */
    public function it_identifies_best_performing_habits()
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
}
