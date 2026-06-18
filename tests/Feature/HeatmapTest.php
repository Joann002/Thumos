<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HeatmapTest extends TestCase
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

    public function test_heatmap_is_rendered_with_a_full_grid(): void
    {
        $this->get('/heatmap')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Heatmap/Index')
                ->where('weeks', fn ($weeks) => count($weeks) === 53 && count($weeks[0]) === 7)
                ->has('rangeLabel'));
    }

    public function test_heatmap_counts_completions_per_day(): void
    {
        $habitA = $this->user->habits()->create(['name' => 'A', 'frequency' => 'daily']);
        $habitB = $this->user->habits()->create(['name' => 'B', 'frequency' => 'daily']);

        $habitA->logs()->create(['date' => '2026-06-18', 'completed' => true]);
        $habitB->logs()->create(['date' => '2026-06-18', 'completed' => true]);

        $this->get('/heatmap')
            ->assertOk()
            ->assertInertia(function ($page) {
                $props = $page->toArray()['props'];

                $this->assertSame(2, $props['totalCompletions']);

                $cells = collect($props['weeks'])->flatten(1);
                $today = $cells->firstWhere('date', '2026-06-18');

                $this->assertSame(2, $today['count']);
                $this->assertSame(4, $today['level']); // 2/2 habits => max level

                return $page;
            });
    }
}
