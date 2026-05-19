<?php

namespace Tests\Feature; // Updated to match the actual folder location

use App\Events\TaskCreated;
use App\Jobs\SendTaskNotificationJob;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test; // Import the Attribute for PHPUnit 11
use Tests\TestCase;

class TaskServicesTest extends TestCase // Renamed class to match your exact filename
{
    use RefreshDatabase;

    protected TaskService $taskService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskService = new TaskService();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test] // Replaced old /** @test */ comment with a native PHP attribute
    public function it_can_create_a_task_successfully_and_triggers_side_effects()
    {
        Queue::fake();
        Event::fake();
        Cache::spy();

        $taskData = [
            'title' => 'Test Task Title',
            'description' => 'Test Task Description',
            'status' => 'pending',
            'priority' => 'high',
        ];

        $task = $this->taskService->create($taskData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Test Task Title',
            'user_id' => $this->user->id,
            'status' => 'pending',
            'priority' => 'high',
        ]);

        Queue::assertPushed(SendTaskNotificationJob::class, function ($job) use ($task) {
            return $job->task->id === $task->id;
        });

        Event::assertDispatched(TaskCreated::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });

        Cache::shouldHaveReceived('forget')->once()->with('tasks');
    }

    #[Test]
    public function it_can_create_a_task_with_default_status_and_priority()
    {
        Queue::fake();
        Event::fake();

        $taskData = [
            'title' => 'Minimal Task',
            'description' => 'No status or priority provided',
        ];

        $task = $this->taskService->create($taskData);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
            'priority' => 'medium',
        ]);
    }

    #[Test]
    public function it_caches_all_tasks_on_get_all()
    {
        Task::factory()->count(3)->create();
        Cache::forget('tasks');

        $this->taskService->getAll();
        $this->assertTrue(Cache::has('tasks'));

        Task::query()->delete();

        $resultsSecondCall = $this->taskService->getAll();
        $this->assertCount(3, $resultsSecondCall->items());
    }
}
