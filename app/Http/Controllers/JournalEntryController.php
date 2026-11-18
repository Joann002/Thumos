<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJournalEntryRequest;
use App\Http\Requests\UpdateJournalEntryRequest;
use App\Models\JournalEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class JournalEntryController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'mood' => ['nullable', Rule::in(JournalEntry::MOODS)],
        ]);
        $search = trim($filters['q'] ?? '');
        $entries = $request->user()->journalEntries()
            ->when($search !== '', fn ($query) => $query->where('content', 'like', '%'.$search.'%'))
            ->when($filters['mood'] ?? null, fn ($query, $mood) => $query->where('mood', $mood))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Journal/Index', [
            'entries' => $entries,
            'filters' => ['q' => $search, 'mood' => $filters['mood'] ?? ''],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Journal/Create', [
            'today' => Carbon::today()->toDateString(),
        ]);
    }

    public function store(StoreJournalEntryRequest $request): RedirectResponse
    {
        $request->user()->journalEntries()->create($request->validated());

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

    public function update(UpdateJournalEntryRequest $request, JournalEntry $entry): RedirectResponse
    {
        $this->authorizeOwner($entry);

        $entry->update($request->validated());

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
}
