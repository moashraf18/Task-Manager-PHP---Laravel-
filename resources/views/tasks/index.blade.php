@extends('layouts.app')

@section('content')

{{-- Daily Quote Section --}}
<section id="daily-quote" class="section-box">
    <div class="section-header">
        <h2>Daily Motivation</h2>
    </div>
    <div id="quote-box" class="quote-display">Loading quote...</div>
    <button id="refresh-quote-btn" class="btn btn-secondary" type="button">
        New Quote
    </button>
</section>

<main class="main-content container">

    {{-- Filters Section --}}
    {{-- No <form> tag here — JS reads these values directly via el.search().value etc. --}}
    <section id="task-filters" class="section-box">
        <div class="section-header">
            <h2>Filters</h2>
        </div>
        <div class="filters-layout">
            <div class="form-group">
                <label for="search-task">Search</label>
                <input type="text" id="search-task" placeholder="Search by title">
                {{-- app.js listens to 'input' event with 400ms debounce --}}
                {{-- fires loadTasks() which fetches JSON from / and re-renders --}}
            </div>
            <div class="form-group">
                <label for="filter-priority">Priority</label>
                <select id="filter-priority">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                {{-- app.js listens to 'change' event → fires loadTasks() immediately --}}
            </div>
            <div class="form-group">
                <label for="filter-status">Status</label>
                <select id="filter-status">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>
    </section>

    {{-- Task List Section --}}
    {{-- This div is fully controlled by JavaScript — app.js renders task cards here --}}
    {{-- The $tasks passed from the controller are only used for the FIRST page load --}}
    <section id="task-list" class="section-box">
        <div class="section-header">
            <h2>My Tasks</h2>
        </div>

        <div id="task-message" class="feedback-message" role="status" aria-live="polite"></div>

        {{-- app.js targets this div and fills it with renderTasks() --}}
        <div id="task-list-content" class="tasks-grid">
            {{-- Initial server-rendered tasks for first load --}}
            {{-- After first load, JavaScript takes over completely --}}
            @forelse($tasks as $task)
                <article class="task-card">
                    <div class="task-card-top">
                        <div>
                            <p class="id">Task ID: #{{ $task->id }}</p>
                            <h3 class="title">{{ $task->title }}</h3>
                        </div>
                        <span class="status-badge {{ $task->status }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>

                    <p class="description">
                        {{ $task->description ?: 'No description provided.' }}
                    </p>

                    <div class="task-meta">
                        <div class="meta-item">
                            <span class="meta-label">Priority</span>
                            <span class="priority-badge {{ $task->priority }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Created At</span>
                            <span class="meta-value">{{ $task->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Due Date</span>
                            <span class="meta-value">{{ $task->due_date ?? 'No due date' }}</span>
                        </div>
                    </div>

                    <div class="task-actions">
                        {{-- NO <form> tags here — all buttons use data-action --}}
                        {{-- app.js handleTaskAction() catches all clicks via event delegation --}}

                        <button class="btn btn-edit"
                                type="button"
                                data-action="edit"
                                data-id="{{ $task->id }}">
                            Edit
                        </button>

                        <button class="btn btn-secondary"
                                type="button"
                                data-action="toggle"
                                data-id="{{ $task->id }}">
                            {{ $task->status === 'completed' ? 'Mark Pending' : 'Mark Complete' }}
                        </button>

                        <button class="btn btn-delete"
                                type="button"
                                data-action="delete"
                                data-id="{{ $task->id }}">
                            Delete
                        </button>
                    </div>
                </article>
            @empty
                <div class="empty-state">No tasks found. Add one below to get started.</div>
            @endforelse
        </div>
    </section>

    {{-- Add Task Form --}}
    {{-- This form's submit is intercepted by app.js handleFormSubmit() --}}
    {{-- event.preventDefault() stops the page reload --}}
    {{-- data is sent via fetch() POST to /tasks --}}
    <section id="task-form" class="section-box">
        <div class="section-header">
            <h2 id="task-form-heading">Add Task</h2>
        </div>

        @if($errors->any())
            <div class="feedback-message error" style="margin-bottom:15px;">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- method="POST" is here but app.js intercepts submit before it fires --}}
        <form action="{{ route('tasks.store') }}" method="POST"
              id="task-form-element" class="task-form-layout">
            @csrf
            <input type="hidden" id="task-id" name="id">

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title"
                       placeholder="Enter task title" value="{{ old('title') }}">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Enter task description">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="task-priority">Priority</label>
                    <select id="task-priority" name="priority">
                        <option value="">Select priority</option>
                        <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="task-status">Status</label>
                    <select id="task-status" name="status">
                        <option value="">Select status</option>
                        <option value="pending"   {{ old('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="due-date">Due Date</label>
                    <input type="date" id="due-date" name="due_date"
                           value="{{ old('due_date') }}">
                </div>
            </div>

            <div id="form-message" class="feedback-message" role="status" aria-live="polite"></div>

            <div class="form-actions">
                <button type="submit" class="btn btn-edit" id="submit-task-btn">Add Task</button>
                <button type="button" class="btn btn-secondary hidden" id="cancel-edit-btn">Cancel Edit</button>
            </div>
        </form>
    </section>

</main>

@endsection