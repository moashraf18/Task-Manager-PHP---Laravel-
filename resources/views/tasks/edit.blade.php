@extends('layouts.app')

@section('content')
<main class="main-content container">
    <section id="task-form" class="section-box">
        <div class="section-header">
            <h2>Edit Task #{{ $task->id }}</h2>
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="feedback-message error" style="margin-bottom:15px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tasks.update', $task) }}" method="POST"
              id="task-form-element" class="task-form-layout">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title"
                       value="{{ old('title', $task->title) }}">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"
                          rows="4">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="task-priority">Priority</label>
                    <select id="task-priority" name="priority">
                        <option value="low"    {{ old('priority', $task->priority) === 'low'    ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high"   {{ old('priority', $task->priority) === 'high'   ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="task-status">Status</label>
                    <select id="task-status" name="status">
                        <option value="pending"   {{ old('status', $task->status) === 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="due-date">Due Date</label>
                    <input type="date" id="due-date" name="due_date"
                           value="{{ old('due_date', $task->due_date) }}">
                </div>
            </div>

            <div id="form-message" class="feedback-message"
                 role="status" aria-live="polite"></div>

            <div class="form-actions">
                <button type="submit" class="btn btn-edit">Save Changes</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</main>
@endsection