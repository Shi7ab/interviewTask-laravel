<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Events\TaskCreated;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    //
    public function create(Request $request){
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'priority' => $request->priority,
            'user_id' => auth()->id()
        ]);

        event(new TaskCreated($task));

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ], 201);
    }

    public function readAll(){
        // $tasks = Task::paginate(10);
        // at this point we can use cache to store the tasks for 60 seconds
        $tasks = Cache::remember('tasks', 60, function () {
            // return Task::all();
            return Task::paginate(10);
        });

        return response()->json(['tasks' => $tasks], 200);
    }

    public function read($id){
        $task = Task::find($id);
        if(!$task){
            return response()->json(['message' => 'Task not found'], 404);
        }
        return response()->json(['task' => $task], 200);
    }

    public function update(Request $request, $id){
        $task = Task::find($id);
        if(!$task){
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->update($request->all());
        return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);
    }

    public function delete($id){
        $task = Task::find($id);
        if(!$task){
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
