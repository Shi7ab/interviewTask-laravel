<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use App\Events\TaskCreated;
use App\Jobs\SendTaskNotificationJob;
use App\Repositries\BaseRepository;

class TaskService
{
     protected $repository;

    public function __construct()
    {
        $this->repository = new BaseRepository(new Task());
    }

    public function create(array $data)
    {


    /*   $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'] ?? 'pending',
            'priority' => $data['priority'] ?? 'medium',
            'user_id' => auth()->id(),
        ]);*/

        $task = $this->$repository->create([
              'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'] ?? 'pending',
            'priority' => $data['priority'] ?? 'medium',
            'user_id' => auth()->id(),
        ]);

        SendTaskNotificationJob::dispatch($task);
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
        return  $this->repository->findById($id);
    }

    public function update($id, array $data)
    {
        // $task = Task::find($id);
        $task = $this->repositry->update($id, $data);

        if (!$task) {
            return null;
        }

        $task->update($data);

        Cache::forget('tasks');

        return $task;
    }

    public function delete($id)
    {
        // $task = Task::find($id);
        $task = $this->repository->findById($id);

        if (!$task) {
            return false;
        }

        $task->delete();

        Cache::forget('tasks');

        return true;
    }
}
