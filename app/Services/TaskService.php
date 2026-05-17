<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use App\Events\TaskCreated;

class TaskService
{
    public function create(array $data)
    {
        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'] ?? 'pending',
            'priority' => $data['priority'] ?? 'medium',
            'user_id' => auth()->id(),
        ]);

        event(new TaskCreated($task));

        Cache::forget('tasks');

        return $task;
    }

    public function getAll()
    {
        return Cache::remember('tasks', 60, function () {
            return Task::paginate(10);
        });
    }

    public function getById($id)
    {
        return Task::find($id);
    }

    public function update($id, array $data)
    {
        $task = Task::find($id);

        if (!$task) {
            return null;
        }

        $task->update($data);

        Cache::forget('tasks');

        return $task;
    }

    public function delete($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return false;
        }

        $task->delete();

        Cache::forget('tasks');

        return true;
    }
}
