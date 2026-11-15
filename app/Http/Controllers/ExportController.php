<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export user data as JSON
     */
    public function exportJson(Request $request)
    {
        $user = $request->user();

        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'goals' => $user->goals()->with('tasks')->get(),
            'habits' => $user->habits()->with('logs')->get(),
            'journal_entries' => $user->journalEntries()->get(),
            'exported_at' => now()->toISOString(),
        ];

        $fileName = 'thumos_export_' . now()->format('Y-m-d_His') . '.json';

        return Response::json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ], JSON_PRETTY_PRINT);
    }
}
