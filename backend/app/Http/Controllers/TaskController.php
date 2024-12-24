<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Prompts\TaskPrompt;
use ArdaGnsrn\Ollama\Ollama;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
	/**
	 * Display a listing of the resource.
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
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function createTask(Request $request): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), [
			'title' => 'required|string|max:100',
			'status' => 'required',
			'priority' => 'required',
			'label' => 'required',
			'due_date' => 'required|date',
		]);

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
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param Task $task
	 * @return JsonResponse
	 */
	public function updateTask(Request $request, Task $task): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), [
			'title' => 'string|max:100',
			'due_date' => 'date',
		]);

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
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param Task $task
	 * @return JsonResponse
	 */
	public function updateTaskField(Request $request, Task $task)
	{
		// Validation
		$rules = Validator::make($request->all(), [
			'field' => 'required|in:status,priority,label',
			'value' => 'required|string',
		]);

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
	 * Remove the specified resource from storage.
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
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function generateTasks(Request $request): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), [
			'prompt' => 'required|string|max:1000',
			'num_tasks' => 'required|integer|min:1',
		]);

		// Check if validation fails
		if ($rules->fails()) {
			return response()->json(['message' => 'Invalid task data.'], 400);
		}

		// Get validated data
		$validatedData = $rules->validated();

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
					'Authorization' => $request->header('Authorization')
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
