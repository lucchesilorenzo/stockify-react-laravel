<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserSettingsRequest;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
	/**
	 * Get logged in user settings.
	 *
	 * @return JsonResponse
	 */
	public function getUserSettings(): JsonResponse
	{
		try {
			$userSettings = auth()->user();
			return response()->json($userSettings);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to get user settings.',
			], 500);
		}
	}

	/**
	 * Update logged in user settings.
	 *
	 * @param UpdateUserSettingsRequest $request
	 * @return JsonResponse
	 */
	public function updateUserSettings(UpdateUserSettingsRequest $request): JsonResponse
	{
		$validatedSettings = $request->validated();

		// Update settings
		try {
			auth()->user()->update($validatedSettings);
			return response()->json(['message' => 'User settings updated successfully.'], 200);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to update settings.'], 500);
		}
	}
}
