<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
{
    use RefreshDatabase;  // ← this is critical, wipes DB between tests

    public function test_index_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Tasks Organizer');
    }

    public function test_task_can_be_created(): void
    {
        $response = $this->post('/tasks', [
            'title'    => 'Buy groceries',
            'priority' => 'medium',
            'status'   => 'pending',
            'due_date' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('tasks', [
            'title'    => 'Buy groceries',
            'priority' => 'medium',
        ]);
    }

    public function test_task_can_be_deleted(): void
    {
        $task = Task::create([
            'title'    => 'Task to delete',
            'priority' => 'low',
            'status'   => 'pending',
        ]);

        $response = $this->delete("/tasks/{$task->id}");
        $response->assertRedirect('/');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}