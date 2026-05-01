<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\TaskApiController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('auth.login');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware('auth')->group(function () {
    Route::get('/task-manager', fn () => redirect()->route('dashboard'));
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/projects', [ProjectApiController::class, 'index'])->name('projects.index');
        Route::post('/projects', [ProjectApiController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}', [ProjectApiController::class, 'show'])->name('projects.show');
        Route::post('/projects/{project}/tasks', [TaskApiController::class, 'store'])->name('tasks.store');
        Route::patch('/tasks/{task}', [TaskApiController::class, 'update'])->name('tasks.update');
    });
});

require __DIR__.'/auth.php';

Route::fallback(function () {
    return auth()->check()
        ? redirect()->route('dashboard')->with('status', 'That page does not exist, so you were sent back to the dashboard.')
        : redirect()->route('login');
});
