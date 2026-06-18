<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class JournalEntryController extends Controller
{
    private const MOODS = ['great', 'good', 'neutral', 'bad', 'awful'];

    public function index(): Response
    {
        $entries = JournalEntry::query()
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
        JournalEntry::create($this->validateEntry($request));

        return redirect()->route('journal.index')
            ->with('success', 'Entrée ajoutée.');
    }

    public function edit(JournalEntry $entry): Response
    {
        return Inertia::render('Journal/Edit', [
            'entry' => $entry,
        ]);
    }

    public function update(Request $request, JournalEntry $entry): RedirectResponse
    {
        $entry->update($this->validateEntry($request));

        return redirect()->route('journal.index')
            ->with('success', 'Entrée mise à jour.');
    }

    public function destroy(JournalEntry $entry): RedirectResponse
    {
        $entry->delete();

        return redirect()->route('journal.index')
            ->with('success', 'Entrée supprimée.');
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
