<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HabitTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_habits_index_is_rendered(): void
    {
        $this->get('/habits')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Habits/Index'));
    }

    public function test_a_habit_can_be_created(): void
    {
        $this->post('/habits', [
            'name' => 'Méditer',
            'frequency' => 'daily',
            'color' => '#16a34a',
        ])->assertRedirect('/habits');

        $this->assertDatabaseHas('habits', [
            'name' => 'Méditer',
            'frequency' => 'daily',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_a_weekly_habit_stores_days_of_week(): void
    {
        $this->post('/habits', [
            'name' => 'Sport',
            'frequency' => 'weekly',
            'days_of_week' => [1, 3, 5],
        ])->assertRedirect('/habits');

        $habit = $this->user->habits()->firstOrFail();

        $this->assertSame([1, 3, 5], $habit->days_of_week);
    }

    public function test_toggling_marks_and_unmarks_today(): void
    {
        $habit = $this->user->habits()->create(['name' => 'Lire', 'frequency' => 'daily']);

        $this->post("/habits/{$habit->id}/toggle");
        $this->assertSame(1, $habit->logs()->whereDate('date', Carbon::today())->count());

        $this->post("/habits/{$habit->id}/toggle");
        $this->assertSame(0, $habit->logs()->whereDate('date', Carbon::today())->count());
    }

    public function test_a_habit_can_be_deleted(): void
    {
        $habit = $this->user->habits()->create(['name' => 'Temporaire', 'frequency' => 'daily']);

        $this->delete("/habits/{$habit->id}")->assertRedirect('/habits');

        $this->assertDatabaseMissing('habits', ['id' => $habit->id]);
    }

    public function test_a_user_cannot_toggle_another_users_habit(): void
    {
        $other = User::factory()->create();
        $habit = $other->habits()->create(['name' => 'Privé', 'frequency' => 'daily']);

        $this->post("/habits/{$habit->id}/toggle")->assertForbidden();
    }
}
