<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
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
        $habit = $this->user->habits()->create(['name' => 'Lire', 'frequency' => 'daily']);
        $habit->logs()->create(['date' => '2026-06-18', 'completed' => true]);

        $this->user->goals()->create([
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

    public function test_invalid_month_and_day_inputs_are_validation_errors(): void
    {
        $this->get('/calendar?month=13&year=0')->assertSessionHasErrors(['month', 'year']);
        $this->get('/calendar/not-a-date')->assertSessionHasErrors('date');
    }

    public function test_day_details_find_cast_dates_and_exclude_other_users(): void
    {
        $habit = $this->user->habits()->create(['name' => 'Lire', 'frequency' => 'daily']);
        $habit->logs()->create(['date' => '2026-06-18', 'completed' => true]);
        $this->user->goals()->create(['title' => 'Mon objectif', 'status' => 'active', 'target_date' => '2026-06-18']);
        $this->user->journalEntries()->create(['date' => '2026-06-18', 'content' => 'Mon journal']);
        User::factory()->create()->journalEntries()->create(['date' => '2026-06-18', 'content' => 'Privé']);
        $this->get('/calendar/2026-06-18')->assertOk()->assertJsonPath('habits.0.completed', true)
            ->assertJsonPath('goals.0.title', 'Mon objectif')->assertJsonPath('journal.content', 'Mon journal');
    }
}
