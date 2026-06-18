<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\Habit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-06-18');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_calendar_renders_current_month_by_default(): void
    {
        $this->get('/calendar')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Calendar/Index')
                ->where('label', 'juin 2026')
                ->has('days'));
    }

    public function test_calendar_exposes_completions_and_deadlines(): void
    {
        $habit = Habit::create(['name' => 'Lire', 'frequency' => 'daily']);
        $habit->logs()->create(['date' => '2026-06-18', 'completed' => true]);

        Goal::create([
            'title' => 'Rendre le projet',
            'status' => 'active',
            'target_date' => '2026-06-18',
        ]);

        $this->get('/calendar?year=2026&month=6')
            ->assertOk()
            ->assertInertia(function ($page) {
                $days = collect($page->toArray()['props']['days']);
                $today = $days->firstWhere('date', '2026-06-18');

                $this->assertSame(1, $today['completed_count']);
                $this->assertCount(1, $today['goals']);

                return $page;
            });
    }

    public function test_navigation_targets_previous_and_next_months(): void
    {
        $this->get('/calendar?year=2026&month=6')
            ->assertInertia(fn ($page) => $page
                ->where('prev', ['year' => 2026, 'month' => 5])
                ->where('next', ['year' => 2026, 'month' => 7]));
    }
}
