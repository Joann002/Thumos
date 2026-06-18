<?php

namespace Tests\Feature;

use App\Models\JournalEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

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
        $entry = JournalEntry::create([
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
        $entry = JournalEntry::create(['date' => '2026-06-18', 'content' => 'À supprimer']);

        $this->delete("/journal/{$entry->id}")->assertRedirect('/journal');

        $this->assertDatabaseMissing('journal_entries', ['id' => $entry->id]);
    }
}
