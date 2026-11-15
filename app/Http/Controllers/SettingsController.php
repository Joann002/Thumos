<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Show the settings page
     */
    public function index(Request $request)
    {
        return Inertia::render('Settings/Index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', 'in:light,dark,auto'],
            'language' => ['required', 'in:fr,en'],
            'notifications_enabled' => ['boolean'],
        ]);

        // TODO: Store user preferences
        
        return back()->with('success', 'Préférences mises à jour.');
    }
}
