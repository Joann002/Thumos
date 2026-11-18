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

    public function test_search_and_mood_filters_remain_scoped_to_the_current_user(): void
    {
        $this->user->journalEntries()->create(['date' => '2026-06-18', 'content' => 'Lecture agréable', 'mood' => 'good']);
        $this->user->journalEntries()->create(['date' => '2026-06-18', 'content' => 'Autre activité', 'mood' => 'bad']);
        User::factory()->create()->journalEntries()->create(['date' => '2026-06-18', 'content' => 'Lecture privée', 'mood' => 'good']);
        $this->get('/journal?q=Lecture&mood=good')->assertInertia(fn ($page) => $page
            ->has('entries.data', 1)->where('entries.data.0.content', 'Lecture agréable')
            ->where('filters.q', 'Lecture')->where('entries.total', 1));
    }

    public function test_journal_paginates_and_keeps_filters_in_navigation(): void
    {
        for ($i = 0; $i < 16; $i++) {
            $this->user->journalEntries()->create(['date' => '2026-06-18', 'content' => 'Lecture '.$i, 'mood' => 'good']);
        }
        $this->get('/journal?q=Lecture')->assertInertia(fn ($page) => $page->has('entries.data', 15)
            ->where('entries.total', 16)->where('entries.last_page', 2)
            ->where('entries.next_page_url', fn ($url) => str_contains($url, 'q=Lecture')));
    }

    public function test_journal_rejects_oversized_content_and_non_calendar_dates(): void
    {
        $this->post('/journal', ['date' => 'tomorrow', 'content' => str_repeat('x', 5001), 'mood' => 'excellent'])
            ->assertSessionHasErrors(['date', 'content', 'mood']);
        $this->assertDatabaseCount('journal_entries', 0);
    }
}
