<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_goal()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/goals', [
            'title' => 'Learn Laravel',
            'description' => 'Master Laravel framework',
            'category' => 'Development',
            'status' => 'in_progress',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('goals', [
            'title' => 'Learn Laravel',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_can_update_own_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/goals/{$goal->id}", [
            'title' => 'Updated Title',
            'description' => $goal->description,
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'title' => 'Updated Title',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function user_cannot_update_others_goal()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put("/goals/{$goal->id}", [
            'title' => 'Hacked Title',
            'description' => $goal->description,
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_delete_own_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/goals/{$goal->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }
}
