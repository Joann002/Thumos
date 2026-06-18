<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_journal_index_is_rendered(): void
    {
        $this->get('/journal')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Journal/Index'));
    }

    public function test_an_entry_can_be_created(): void
    {
        $this->post('/journal', [
            'date' => '2026-06-18',
            'content' => 'Bonne journée de code.',
            'mood' => 'good',
        ])->assertRedirect('/journal');

        $this->assertDatabaseHas('journal_entries', [
            'content' => 'Bonne journée de code.',
            'mood' => 'good',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_content_and_date_are_required(): void
    {
        $this->post('/journal', ['mood' => 'good'])
            ->assertSessionHasErrors(['date', 'content']);
    }

    public function test_invalid_mood_is_rejected(): void
    {
        $this->post('/journal', [
            'date' => '2026-06-18',
            'content' => 'Test',
            'mood' => 'euphoric',
        ])->assertSessionHasErrors('mood');
    }

    public function test_an_entry_can_be_updated(): void
    {
        $entry = $this->user->journalEntries()->create([
            'date' => '2026-06-17',
            'content' => 'Ancien contenu',
            'mood' => 'neutral',
        ]);

        $this->put("/journal/{$entry->id}", [
            'date' => '2026-06-17',
            'content' => 'Nouveau contenu',
            'mood' => 'great',
        ])->assertRedirect('/journal');

        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'content' => 'Nouveau contenu',
            'mood' => 'great',
        ]);
    }

    public function test_an_entry_can_be_deleted(): void
    {
        $entry = $this->user->journalEntries()->create(['date' => '2026-06-18', 'content' => 'À supprimer']);

        $this->delete("/journal/{$entry->id}")->assertRedirect('/journal');

        $this->assertDatabaseMissing('journal_entries', ['id' => $entry->id]);
    }

    public function test_a_user_cannot_edit_another_users_entry(): void
    {
        $other = User::factory()->create();
        $entry = $other->journalEntries()->create(['date' => '2026-06-18', 'content' => 'Privé']);

        $this->get("/journal/{$entry->id}/edit")->assertForbidden();
    }
}
