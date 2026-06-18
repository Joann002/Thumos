<?php

use App\Http\Controllers\GoalController;
use App\Http\Controllers\GoalTaskController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\JournalEntryController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/goals');

Route::resource('goals', GoalController::class);

Route::post('goals/{goal}/tasks', [GoalTaskController::class, 'store'])->name('goals.tasks.store');
Route::post('goals/{goal}/tasks/reorder', [GoalTaskController::class, 'reorder'])->name('goals.tasks.reorder');
Route::patch('tasks/{task}', [GoalTaskController::class, 'update'])->name('tasks.update');
Route::delete('tasks/{task}', [GoalTaskController::class, 'destroy'])->name('tasks.destroy');

Route::resource('habits', HabitController::class)->except('show');
Route::post('habits/{habit}/toggle', [HabitLogController::class, 'toggle'])->name('habits.toggle');

Route::resource('journal', JournalEntryController::class)
    ->parameters(['journal' => 'entry'])
    ->except('show');
