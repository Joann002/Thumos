<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_is_rendered_for_authenticated_users(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Dashboard')->has('stats'));
    }

    public function test_dashboard_aggregates_today_stats(): void
    {
        $user = User::factory()->create();
        $habit = $user->habits()->create(['name' => 'Lire', 'frequency' => 'daily']);
        $habit->logs()->create(['date' => '2026-06-18', 'completed' => true]);
        $user->goals()->create(['title' => 'Objectif actif', 'status' => 'active']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertInertia(function ($page) {
                $stats = $page->toArray()['props']['stats'];

                $this->assertSame(100, $stats['completion']);
                $this->assertSame(1, $stats['done_today']);
                $this->assertSame(1, $stats['active_goals']);

                return $page;
            });
    }

    public function test_dashboard_does_not_count_incomplete_or_past_logs_as_done_today(): void
    {
        $user = User::factory()->create();
        $habit = $user->habits()->create(['name' => 'Lire', 'frequency' => 'daily']);
        $habit->logs()->create(['date' => '2026-06-17', 'completed' => true]);
        $habit->logs()->create(['date' => '2026-06-18', 'completed' => false]);
        $this->actingAs($user)->get('/dashboard')->assertInertia(fn ($page) => $page
            ->where('stats.done_today', 0)->where('todayHabits.0.done_today', false)->where('stats.best_streak', 1));
    }
}
