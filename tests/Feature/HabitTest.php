<?php

namespace Tests\Feature;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HabitTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_daily_habit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/habits', [
            'name' => 'Morning Meditation',
            'frequency' => 'daily',
            'color' => '#8B5CF6',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('habits', [
            'name' => 'Morning Meditation',
            'user_id' => $user->id,
            'frequency' => 'daily',
        ]);
    }

    public function test_user_can_create_weekly_habit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/habits', [
            'name' => 'Gym Workout',
            'frequency' => 'weekly',
            'days_of_week' => [1, 3, 5],
            'color' => '#EF4444',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('habits', [
            'name' => 'Gym Workout',
            'frequency' => 'weekly',
        ]);
    }

    public function test_user_can_toggle_habit_completion()
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/habits/{$habit->id}/toggle", [
            'date' => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertTrue($habit->logs()->whereDate('date', Carbon::today())
            ->where('completed', true)->exists());
    }

    public function test_user_cannot_access_others_habits()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $habit = Habit::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete("/habits/{$habit->id}");

        $response->assertForbidden();
    }

    public function test_weekly_habits_require_distinct_valid_days_and_hex_colours(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post('/habits', ['name' => 'Weekly', 'frequency' => 'weekly', 'days_of_week' => []])
            ->assertSessionHasErrors('days_of_week');
        $this->post('/habits', ['name' => 'Weekly', 'frequency' => 'weekly', 'days_of_week' => [1, '1'], 'color' => 'red'])
            ->assertSessionHasErrors(['days_of_week.0', 'color']);
    }

    public function test_numeric_weekdays_are_normalized_and_daily_days_are_cleared(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/habits', ['name' => 'Sport', 'frequency' => 'weekly', 'days_of_week' => ['1', '3']])
            ->assertSessionHasNoErrors();
        $habit = $user->habits()->firstOrFail();
        $this->assertSame([1, 3], $habit->days_of_week);
        $this->put('/habits/'.$habit->id, ['name' => 'Sport', 'frequency' => 'daily', 'days_of_week' => [1, 3]])
            ->assertSessionHasNoErrors();
        $this->assertNull($habit->fresh()->days_of_week);
    }

    public function test_an_incomplete_log_does_not_mark_the_habit_done(): void
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->daily()->create(['user_id' => $user->id]);
        $habit->logs()->create(['date' => Carbon::today(), 'completed' => false]);
        $this->actingAs($user)->get('/habits')->assertInertia(fn ($page) => $page->where('habits.0.done_today', false));
        $this->post('/habits/'.$habit->id.'/toggle')->assertSessionHasNoErrors();
        $this->assertSame(1, $habit->logs()->count());
        $this->assertTrue($habit->logs()->first()->completed);
    }

    public function test_toggling_future_or_unscheduled_days_is_rejected(): void
    {
        Carbon::setTestNow('2026-06-18');
        try {
            $user = User::factory()->create();
            $habit = Habit::factory()->weekly([1])->create(['user_id' => $user->id]);
            $this->actingAs($user)->post('/habits/'.$habit->id.'/toggle', ['date' => '2026-06-19'])
                ->assertSessionHasErrors('date');
            $this->post('/habits/'.$habit->id.'/toggle')->assertSessionHasErrors('date');
            $this->assertSame(0, $habit->logs()->count());
        } finally {
            Carbon::setTestNow();
        }
    }
}
