<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Prompts\TaskPrompt;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function getTasks()
    {
        $tasks = Task::with('user:id,first_name,last_name')->get();
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function createTask(Request $request)
    {
        // Validation
        $rules = Validator::make($request->all(), ([
            'title' => 'required|string|max:100',
            'status' => 'required',
            'priority' => 'required',
            'label' => 'required',
            'due_date' => 'required|date',
        ]));

        // Check if validation fails
        if ($rules->fails()) {
            return response()->json(['message' => 'Invalid task data.'], 400);
        }

        // Get validated data
        $validatedData = $rules->validated();

        // Add user ID
        $validatedData['user_id'] = auth()->user()->id;

        // Create task
        try {
            Task::create($validatedData);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to create task.'], 500);
        }

        // Create a new activity
        $activity = [
            'activity' => 'CREATED',
            'entity' => 'TASK',
            'user_id' => auth()->user()->id,
        ];

        try {
            Activity::create($activity);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to create activity.'], 500);
        }

        return response()->json(['message' => 'Task created successfully.'], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return void
     */
    public function updateTask(Request $request, Task $task)
    {
        // Validation
        $rules = Validator::make($request->all(), ([
            'title' => 'string|max:100',
            'due_date' => 'date',
        ]));

        // Check if validation fails
        if ($rules->fails()) {
            return response()->json(['message' => 'Invalid task data.'], 400);
        }

        // Get validated data
        $validatedData = $rules->validated();

        // Update task
        try {
            $task->update($validatedData);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to update task.'], 500);
        }

        // Create a new activity
        $activity = [
            'activity' => 'UPDATED',
            'entity' => 'Task',
            'user_id' => auth()->user()->id,
        ];

        try {
            Activity::create($activity);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to create activity.'], 500);
        }

        return response()->json(['message' => 'Task updated successfully.'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return void
     */
    public function updateTaskField(Request $request, Task $task)
    {
        // Validation
        $rules = Validator::make($request->all(), ([
            'field' => 'required|in:status,priority,label',
            'value' => 'required|string',
        ]));

        // Check if validation fails
        if ($rules->fails()) {
            return response()->json(['message' => 'Invalid task data.'], 400);
        }

        // Get validated data
        $validatedData = $rules->validated();

        // Extract field and value
        $field = $validatedData['field'];
        $value = $validatedData['value'];

        // Verify if value is in valid values
        $validValues = [
            'status' => ['BACKLOG', 'TO_DO', 'IN_PROGRESS', 'DONE', 'CANCELED'],
            'priority' => ['LOW', 'MEDIUM', 'HIGH'],
            'label' => ['INVENTORY', 'ORDER', 'SHIPPING', 'QUALITY', 'CUSTOMER', 'MAINTENANCE'],
        ];

        // Check if value is in valid values
        if (!in_array($value, $validValues[$field])) {
            return response()->json(['message' => 'Invalid value.'], 400);
        }

        // Update task
        try {
            $task->update([$field => $value]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to update task.'], 500);
        }

        // Create a new activity
        $activity = [
            'activity' => 'UPDATED',
            'entity' => 'Task',
            'user_id' => auth()->user()->id,
        ];

        try {
            Activity::create($activity);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to create activity.'], 500);
        }

        return response()->json(['message' => 'Task updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     * @return void
     */
    public function deleteTask(Task $task)
    {
        // Delete task
        try {
            $task->delete();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to delete task.'], 500);
        }

        // Create a new activity
        $activity = [
            'activity' => 'DELETED',
            'entity' => 'Task',
            'user_id' => auth()->user()->id,
        ];

        try {
            Activity::create($activity);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to create activity.'], 500);
        }

        return response()->json(['message' => 'Task deleted successfully.'], 200);
    }

    // TODO
    /**
     * Generate tasks
     *
     * @return void
     */
    public function generateTasks()
    {
        // Validation
        $rules = Validator::make(request()->all(), ([
            'prompt' => 'required|string|max:1000',
            'num_tasks' => 'required|integer',
        ]));

        // Check if validation fails
        if ($rules->fails()) {
            return response()->json(['message' => 'Invalid task data.'], 400);
        }

        // Get validated data
        $validatedData = $rules->validated();

        $numTasks = $validatedData['num_tasks'] || 5;
        $description = $validatedData['prompt'];
        $talkStructure = TaskPrompt::getTaskStructure();
        $exampleOutput = TaskPrompt::getExampleOutput();

        $finalPrompt = "{$description}. Generate {$numTasks} tasks. {$talkStructure} {$exampleOutput} Return only a pure JSON array of tasks, without extra formatting.";

        try {
            $response = Ollama::ask([
                'messages' => ['role' => 'user', 'content' => $finalPrompt],
                'format' => 'json',
            ]);
            return response()->json(['response' => $response]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to generate tasks.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
