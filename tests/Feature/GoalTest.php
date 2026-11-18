<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_goal()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/goals', [
            'title' => 'Learn Laravel',
            'description' => 'Master Laravel framework',
            'category' => 'Development',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('goals', [
            'title' => 'Learn Laravel',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_update_own_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/goals/{$goal->id}", [
            'title' => 'Updated Title',
            'description' => $goal->description,
            'status' => 'done',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'title' => 'Updated Title',
            'status' => 'done',
        ]);
    }

    public function test_user_cannot_update_others_goal()
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

    public function test_user_can_delete_own_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/goals/{$goal->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }

    public function test_goals_use_the_same_statuses_and_limits_as_the_form(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        foreach (['active', 'paused', 'done'] as $status) {
            $this->post('/goals', ['title' => 'Objectif', 'status' => $status])->assertSessionHasNoErrors();
        }
        $this->post('/goals', ['title' => 'Invalid', 'status' => 'unknown', 'description' => str_repeat('x', 2001)])
            ->assertSessionHasErrors(['status', 'description']);
        $this->assertSame(3, $user->goals()->count());
    }

    public function test_goal_updates_cannot_transfer_ownership(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $owner->id]);
        $this->actingAs($owner)->put('/goals/'.$goal->id, [
            'title' => 'Updated', 'status' => 'active', 'user_id' => $other->id,
        ])->assertSessionHasNoErrors();
        $this->assertSame($owner->id, $goal->fresh()->user_id);
    }
}
