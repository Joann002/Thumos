<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search across goals, habits, and journal entries
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $user = $request->user();

        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'message' => 'La recherche doit contenir au moins 2 caractères.',
            ]);
        }

        $goals = $user->goals()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(fn ($goal) => [
                'type' => 'goal',
                'id' => $goal->id,
                'title' => $goal->title,
                'url' => "/goals/{$goal->id}",
            ]);

        $habits = $user->habits()
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(fn ($habit) => [
                'type' => 'habit',
                'id' => $habit->id,
                'title' => $habit->name,
                'url' => "/habits/{$habit->id}",
            ]);

        $journalEntries = $user->journalEntries()
            ->where('content', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(fn ($entry) => [
                'type' => 'journal',
                'id' => $entry->id,
                'title' => 'Entrée du ' . $entry->date->format('d/m/Y'),
                'url' => "/journal/{$entry->id}",
            ]);

        $results = $goals->concat($habits)->concat($journalEntries);

        return response()->json([
            'results' => $results,
            'count' => $results->count(),
        ]);
    }
}
