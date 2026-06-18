<?php

namespace Tests\Feature;

use App\Models\Goal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

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
        ]);
    }

    public function test_a_goal_requires_a_title(): void
    {
        $this->post('/goals', ['status' => 'active'])
            ->assertSessionHasErrors('title');
    }

    public function test_a_subtask_can_be_added_and_toggled(): void
    {
        $goal = Goal::create(['title' => 'Lire 12 livres', 'status' => 'active']);

        $this->post("/goals/{$goal->id}/tasks", ['title' => 'Choisir un livre']);

        $task = $goal->tasks()->firstOrFail();
        $this->assertFalse($task->done);

        $this->patch("/tasks/{$task->id}", ['done' => true]);

        $this->assertTrue($task->fresh()->done);
    }

    public function test_a_goal_can_be_deleted(): void
    {
        $goal = Goal::create(['title' => 'Temporaire', 'status' => 'active']);

        $this->delete("/goals/{$goal->id}")
            ->assertRedirect('/goals');

        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }
}
