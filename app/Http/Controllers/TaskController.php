<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // ── index() ───────────────────────────────────────────────────────────────
    // GET /
    // When called by a browser → returns the Blade view (normal page load)
    // When called by JavaScript (Accept: application/json) → returns JSON tasks
    // This is how we keep SPA behaviour without a page reload on filter changes
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        // If the request wants JSON (sent by our JavaScript fetch() calls)
        // return just the tasks array as JSON — no HTML
        if ($request->expectsJson()) {
            return response()->json($tasks);
        }

        // Otherwise return the full Blade view (first page load in browser)
        return view('tasks.index', compact('tasks'));
    }

    // ── create() ──────────────────────────────────────────────────────────────
    // GET /tasks/create
    // Shows the standalone Add Task page (not used by SPA flow but required
    // as part of the full 7 RESTful methods)
    public function create() //Shows the form
    {
        return view('tasks.create');
    }

    // ── store() ───────────────────────────────────────────────────────────────
    // POST /tasks
    // Called by JavaScript fetch() — always returns JSON
    // TaskRequest runs validation first; if it fails Laravel returns 422 + errors JSON
    public function store(TaskRequest $request) //Saves the task , runs after the user submits the form
    {
        $task = Task::create($request->validated());

        // Return JSON so JavaScript knows it succeeded
        return response()->json([
            'message' => 'Task added successfully.',
            'task'    => $task,
        ], 201); // 201 = Created
    }

    // ── show() ────────────────────────────────────────────────────────────────
    // GET /tasks/{task}
    // Shows detail page for one task
    public function show(Task $task) 
    {
        return view('tasks.show', compact('task'));
    }

    // ── edit() ────────────────────────────────────────────────────────────────
    // GET /tasks/{task}/edit
    // Shows the edit form for one task (used by the standalone edit page)
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    // ── update() ──────────────────────────────────────────────────────────────
    // PUT /tasks/{task}
    // Called by JavaScript fetch() — always returns JSON
    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return response()->json([
            'message' => 'Task updated successfully.',
            'task'    => $task,
        ]);
    }

    // ── destroy() ─────────────────────────────────────────────────────────────
    // DELETE /tasks/{task}
    // Called by JavaScript fetch() — always returns JSON
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }

    // ── toggleStatus() ────────────────────────────────────────────────────────
    // PATCH /tasks/{task}/toggle
    // Called by JavaScript fetch() — always returns JSON
    public function toggleStatus(Task $task)
    {
        $task->update([
            'status' => $task->status === 'completed' ? 'pending' : 'completed',
        ]);

        return response()->json([
            'message' => 'Task status updated.',
            'task'    => $task,
        ]);
    }
}