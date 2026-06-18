<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->app['auth']->logout();

        $this->get('/goals')->assertRedirect('/login');
    }

    public function test_goals_index_is_rendered(): void
    {
        $this->get('/goals')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Goals/Index'));
    }

    public function test_a_goal_can_be_created(): void
    {
        $response = $this->post('/goals', [
            'title' => 'Courir un 10 km',
            'description' => 'Préparer une course',
            'category' => 'Santé',
            'target_date' => '2026-12-31',
            'status' => 'active',
        ]);

        $goal = Goal::firstOrFail();
        $response->assertRedirect("/goals/{$goal->id}");

        $this->assertDatabaseHas('goals', [
            'title' => 'Courir un 10 km',
            'status' => 'active',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_a_goal_requires_a_title(): void
    {
        $this->post('/goals', ['status' => 'active'])
            ->assertSessionHasErrors('title');
    }

    public function test_a_subtask_can_be_added_and_toggled(): void
    {
        $goal = $this->user->goals()->create(['title' => 'Lire 12 livres', 'status' => 'active']);

        $this->post("/goals/{$goal->id}/tasks", ['title' => 'Choisir un livre']);

        $task = $goal->tasks()->firstOrFail();
        $this->assertFalse($task->done);

        $this->patch("/tasks/{$task->id}", ['done' => true]);

        $this->assertTrue($task->fresh()->done);
    }

    public function test_a_goal_can_be_deleted(): void
    {
        $goal = $this->user->goals()->create(['title' => 'Temporaire', 'status' => 'active']);

        $this->delete("/goals/{$goal->id}")
            ->assertRedirect('/goals');

        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }

    public function test_a_user_cannot_view_another_users_goal(): void
    {
        $other = User::factory()->create();
        $goal = $other->goals()->create(['title' => 'Privé', 'status' => 'active']);

        $this->get("/goals/{$goal->id}")->assertForbidden();
    }
}
