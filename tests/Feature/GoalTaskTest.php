<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_reordering_requires_a_complete_distinct_list_of_owned_tasks(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $user->id]);
        $one = $goal->tasks()->create(['title' => 'One', 'order' => 1]);
        $two = $goal->tasks()->create(['title' => 'Two', 'order' => 2]);
        $foreign = Goal::factory()->create()->tasks()->create(['title' => 'Private', 'order' => 1]);
        $this->actingAs($user)->post('/goals/'.$goal->id.'/tasks/reorder', ['ids' => [$one->id, $foreign->id]])
            ->assertSessionHasErrors('ids.1');
        $this->assertSame(1, $one->fresh()->order);
        $this->assertSame(2, $two->fresh()->order);
        $this->post('/goals/'.$goal->id.'/tasks/reorder', ['ids' => [$one->id, $one->id]])
            ->assertSessionHasErrors('ids.0');
        $this->post('/goals/'.$goal->id.'/tasks/reorder', ['ids' => [$one->id]])
            ->assertSessionHasErrors('ids');
        $this->post('/goals/'.$goal->id.'/tasks/reorder', ['ids' => [$two->id, $one->id]])
            ->assertSessionHasNoErrors();
        $this->assertSame(1, $two->fresh()->order);
        $this->assertSame(2, $one->fresh()->order);
    }

    public function test_another_users_tasks_cannot_be_updated_or_deleted(): void
    {
        $task = Goal::factory()->create()->tasks()->create(['title' => 'Private', 'order' => 1]);
        $this->actingAs(User::factory()->create())->patch('/tasks/'.$task->id, ['done' => true])->assertForbidden();
        $this->delete('/tasks/'.$task->id)->assertForbidden();
        $this->assertDatabaseHas('goal_tasks', ['id' => $task->id, 'done' => false]);
    }
}
