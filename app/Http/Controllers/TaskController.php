<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function create(Request $request)
    {
        $task = $this->taskService->create($request->all());

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ], 201);
    }

    public function readAll()
    {
        $tasks = $this->taskService->getAll();

        return response()->json(['tasks' => $tasks], 200);
    }

    public function read($id)
    {
        $task = $this->taskService->getById($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json(['task' => $task], 200);
    }

    public function update(Request $request, $id)
    {
        $task = $this->taskService->update($id, $request->all());

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ], 200);
    }

    public function delete($id)
    {
        $deleted = $this->taskService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
