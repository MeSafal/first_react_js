<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Visobotics Web Routes
|--------------------------------------------------------------------------
*/

// Guest / Welcome
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('my-work')
        : redirect()->route('login');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Page Routes (Blade views via DashboardController) ──
    Route::get('/dashboard', [DashboardController::class, 'myWork'])->name('dashboard');
    Route::get('/my-work', [DashboardController::class, 'myWork'])->name('my-work');
    Route::get('/projects/{project}', [DashboardController::class, 'projectView'])->name('project.view');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    Route::get('/kanban', [DashboardController::class, 'kanban'])->name('kanban');
    Route::get('/notes', [DashboardController::class, 'notes'])->name('notes');
    Route::get('/team-workload', [DashboardController::class, 'teamWorkload'])->name('team-workload');

    // ── Profile (Breeze) ──
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── JSON API Routes ──
    Route::prefix('api')->group(function () {
        // Tasks CRUD
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::get('/tasks/{task}', [TaskController::class, 'show']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
        Route::post('/tasks/{task}/duplicate', [TaskController::class, 'duplicate']);

        // Subtasks
        Route::post('/tasks/{task}/subtasks', [TaskController::class, 'addSubtask']);
        Route::put('/tasks/{task}/subtasks/{subtask}', [TaskController::class, 'toggleSubtask']);
        Route::delete('/tasks/{task}/subtasks/{subtask}', [TaskController::class, 'deleteSubtask']);

        // Chat Messages
        Route::post('/tasks/{task}/messages', [TaskController::class, 'sendMessage']);

        // Projects
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::put('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

        // Notes
        Route::get('/notes', [NoteController::class, 'index']);
        Route::post('/notes', [NoteController::class, 'store']);
        Route::put('/notes/{note}', [NoteController::class, 'update']);
        Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

        // Teams
        Route::get('/teams', [TeamController::class, 'index']);
        Route::post('/teams', [TeamController::class, 'store']);
        Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
    });
});

require __DIR__.'/auth.php';
