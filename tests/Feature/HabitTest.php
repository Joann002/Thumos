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

    /** @test */
    public function user_can_create_daily_habit()
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

    /** @test */
    public function user_can_create_weekly_habit()
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

    /** @test */
    public function user_can_toggle_habit_completion()
    {
        $user = User::factory()->create();
        $habit = Habit::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/habits/{$habit->id}/toggle", [
            'date' => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('habit_logs', [
            'habit_id' => $habit->id,
            'date' => Carbon::today()->toDateString(),
            'completed' => true,
        ]);
    }

    /** @test */
    public function user_cannot_access_others_habits()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $habit = Habit::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete("/habits/{$habit->id}");

        $response->assertForbidden();
    }
}
