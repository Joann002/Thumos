<?php

namespace Tests\Unit;

use App\Models\Goal;
use App\Models\GoalTask;
use App\Models\User;
use App\Services\GoalProgressTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GoalProgressTrackerTest extends TestCase
{
    use RefreshDatabase;

    private GoalProgressTracker $tracker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tracker = new GoalProgressTracker;
    }

    public function test_it_calculates_goal_progress_summary()
    {
        $goal = Goal::factory()->create();

        GoalTask::factory()->create(['goal_id' => $goal->id, 'done' => true]);
        GoalTask::factory()->create(['goal_id' => $goal->id, 'done' => true]);
        GoalTask::factory()->create(['goal_id' => $goal->id, 'done' => false]);
        GoalTask::factory()->create(['goal_id' => $goal->id, 'done' => false]);

        $summary = $this->tracker->getSummary($goal);

        $this->assertEquals(4, $summary['total_tasks']);
        $this->assertEquals(2, $summary['completed_tasks']);
        $this->assertEquals(2, $summary['remaining_tasks']);
        $this->assertEquals(50, $summary['percentage']);
    }

    public function test_it_detects_overdue_goals()
    {
        $user = User::factory()->create();

        $overdueGoal = Goal::factory()->create([
            'user_id' => $user->id,
            'target_date' => Carbon::yesterday(),
            'status' => 'active',
        ]);

        $onTimeGoal = Goal::factory()->create([
            'user_id' => $user->id,
            'target_date' => Carbon::tomorrow(),
            'status' => 'active',
        ]);

        $goals = collect([$overdueGoal, $onTimeGoal]);
        $overdue = $this->tracker->getOverdue($goals);

        $this->assertCount(1, $overdue);
        $this->assertTrue($overdue->contains($overdueGoal));
    }

    public function test_it_finds_goals_due_soon()
    {
        $user = User::factory()->create();

        $dueSoon = Goal::factory()->create([
            'user_id' => $user->id,
            'target_date' => Carbon::now()->addDays(5),
            'status' => 'active',
        ]);

        $notDueSoon = Goal::factory()->create([
            'user_id' => $user->id,
            'target_date' => Carbon::now()->addMonths(2),
            'status' => 'active',
        ]);

        $goals = collect([$dueSoon, $notDueSoon]);
        $upcoming = $this->tracker->getDueSoon($goals);

        $this->assertCount(1, $upcoming);
        $this->assertTrue($upcoming->contains($dueSoon));
    }

    public function test_current_statuses_and_today_deadlines_are_consistent(): void
    {
        Carbon::setTestNow('2026-06-18 15:00:00');
        try {
            $today = Goal::factory()->create(['status' => 'active', 'target_date' => '2026-06-18']);
            $done = Goal::factory()->completed()->create(['target_date' => '2026-06-17']);
            $this->assertFalse($today->isOverdue());
            $this->assertFalse($done->isOverdue());
            $this->assertCount(1, $this->tracker->getDueSoon(collect([$today, $done])));
            $groups = $this->tracker->groupByStatus(collect([$today, $done]));
            $this->assertCount(1, $groups['active']);
            $this->assertCount(1, $groups['done']);
        } finally {
            Carbon::setTestNow();
        }
    }
}
