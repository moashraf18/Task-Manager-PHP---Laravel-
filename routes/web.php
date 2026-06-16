<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\QuoteController;

// Main page — shows all tasks
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');

// Add a new task
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

// Show edit form for a task
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');

// Update a task
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

// Delete a task
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Toggle task status (pending ↔ completed)
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');

// Get motivational quote (called by JavaScript)
Route::get('/quote', [QuoteController::class, 'fetch'])->name('quote.fetch');