<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\Task;

class TaskController extends Controller
{
    //
    public function create(){

        $task = Task::create([
            'title' => 'Task 1',
            'description' => 'This is the first task',
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function readAll(){
        $tasks = Task::paginate(10);
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
