<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class JournalEntryController extends Controller
{
    private const MOODS = ['great', 'good', 'neutral', 'bad', 'awful'];

    public function index(Request $request): Response
    {
        $entries = $request->user()->journalEntries()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Journal/Index', [
            'entries' => $entries,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Journal/Create', [
            'today' => Carbon::today()->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->journalEntries()->create($this->validateEntry($request));

        return redirect()->route('journal.index')
            ->with('success', 'Entrée ajoutée.');
    }

    public function edit(JournalEntry $entry): Response
    {
        $this->authorizeOwner($entry);

        return Inertia::render('Journal/Edit', [
            'entry' => $entry,
        ]);
    }

    public function update(Request $request, JournalEntry $entry): RedirectResponse
    {
        $this->authorizeOwner($entry);

        $entry->update($this->validateEntry($request));

        return redirect()->route('journal.index')
            ->with('success', 'Entrée mise à jour.');
    }

    public function destroy(JournalEntry $entry): RedirectResponse
    {
        $this->authorizeOwner($entry);

        $entry->delete();

        return redirect()->route('journal.index')
            ->with('success', 'Entrée supprimée.');
    }

    private function authorizeOwner(JournalEntry $entry): void
    {
        abort_if($entry->user_id !== Auth::id(), 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'date' => ['required', 'date'],
            'content' => ['required', 'string'],
            'mood' => ['nullable', 'in:'.implode(',', self::MOODS)],
        ]);
    }
}
