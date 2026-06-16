<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all users
    }

    public function rules(): array
    {
        // 'sometimes' means: only validate if the field is present
        // This handles both ADD and UPDATE in one class
        return [
            'title'       => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,completed',
            'due_date'    => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Title is required.',
            'title.min'             => 'Title must be at least 3 characters.',
            'priority.in'           => 'Priority must be low, medium, or high.',
            'status.in'             => 'Status must be pending or completed.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
        ];
    }
}