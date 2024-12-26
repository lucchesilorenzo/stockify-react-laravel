<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\GenerateTasksRequest;
use App\Http\Requests\UpdateTaskFieldRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Activity;
use App\Models\Task;
use App\Prompts\TaskPrompt;
use ArdaGnsrn\Ollama\Ollama;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class TaskController extends Controller
{
  /**
   * Get all tasks.
   *
   * @return JsonResponse
   */
  public function getTasks(): JsonResponse
  {
    try {
      $tasks = Task::with('user:id,first_name,last_name')->get();

      return response()->json($tasks);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to get tasks.'], 500);
    }
  }

  /**
   * Create a new task.
   *
   * @param CreateTaskRequest $request
   * @return JsonResponse
   */
  public function createTask(CreateTaskRequest $request): JsonResponse
  {
    // Get validated data
    $validatedData = $request->validated();

    // Add user ID
    $validatedData['user_id'] = auth()->user()->id;

    // Create task
    try {
      Task::create($validatedData);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create task.'], 500);
    }

    // Create a new activity
    try {
      Activity::create([
        'activity' => 'CREATED',
        'entity' => 'Task',
        'user_id' => auth()->user()->id,
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(['message' => 'Task created successfully.'], 201);
  }

  /**
   * Update task.
   *
   * @param UpdateTaskRequest $request
   * @param Task $task
   * @return JsonResponse
   */
  public function updateTask(UpdateTaskRequest $request, Task $task): JsonResponse
  {
    // Get validated data
    $validatedData = $request->validated();

    // Update task
    try {
      $task->update($validatedData);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to update task.'], 500);
    }

    // Create a new activity
    try {
      Activity::create([
        'activity' => 'UPDATED',
        'entity' => 'Task',
        'user_id' => auth()->user()->id,
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(['message' => 'Task updated successfully.'], 200);
  }

  /**
   * Update task field.
   *
   * @param UpdateTaskFieldRequest $request
   * @param Task $task
   * @return JsonResponse
   */
  public function updateTaskField(UpdateTaskFieldRequest $request, Task $task): JsonResponse
  {
    // Get validated data
    $validatedData = $request->validated();

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
    if (! in_array($value, $validValues[$field])) {
      return response()->json(['message' => 'Invalid value.'], 400);
    }

    // Update task
    try {
      $task->update([$field => $value]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to update task.'], 500);
    }

    // Create a new activity
    try {
      Activity::create([
        'activity' => 'UPDATED',
        'entity' => 'Task',
        'user_id' => auth()->user()->id,
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(['message' => 'Task updated successfully.'], 200);
  }

  /**
   * Delete task.
   *
   * @param Task $task
   * @return JsonResponse
   */
  public function deleteTask(Task $task): JsonResponse
  {
    // Delete task
    try {
      $task->delete();
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to delete task.'], 500);
    }

    // Create a new activity
    try {
      Activity::create([
        'activity' => 'DELETED',
        'entity' => 'Task',
        'user_id' => auth()->user()->id,
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(['message' => 'Task deleted successfully.'], 200);
  }

  /**
   * Generate tasks.
   *
   * @param GenerateTasksRequest $request
   * @return JsonResponse
   */
  public function generateTasks(GenerateTasksRequest $request): JsonResponse
  {
    // Get validated data
    $validatedData = $request->validated();

    $numTasks = $validatedData['num_tasks'] ?? 5;
    $description = $validatedData['prompt'];
    $talkStructure = TaskPrompt::getTaskStructure();
    $exampleOutput = TaskPrompt::getExampleOutput();

    $finalPrompt = "{$description}. Generate {$numTasks} tasks. {$talkStructure} {$exampleOutput} Return only a pure JSON array of tasks, without extra formatting.";

    $client = Ollama::client();

    try {
      $res = $client->chat()->create([
        'model' => 'qwen2.5-coder:1.5b',
        'messages' => [
          ['role' => 'user', 'content' => $finalPrompt],
        ],
        'format' => 'json',
      ]);

      $decodedRes = json_decode($res->message->content, true);

      foreach ($decodedRes['tasks'] as $task) {
        Http::withHeaders([
          'Authorization' => $request->header('Authorization'),
        ])->post(env('APP_URL') . '/api/tasks', $task);
      }
    } catch (\Throwable $e) {
      return response()->json([
        'message' => 'Failed to generate tasks.',
        'error' => $e->getMessage(),
      ], 500);
    }

    return response()->json(['message' => 'Tasks generated successfully.'], 201);
  }
}
