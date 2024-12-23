<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Get settings by user ID.
     *
     * @return JsonResponse
     */
    public function getSettingsByUserId(): JsonResponse
    {
        try {
            $userSettings = auth()->user();
            return response()->json($userSettings);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to get settings by user ID.',
            ], 500);
        }
    }

    /**
     * Update settings by user ID.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSettingsByUserId(Request $request): JsonResponse
    {
        // Validation
        $rules = Validator::make($request->all(), [
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'bio' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|max:10',
        ]);

        if ($rules->fails()) {
            return response()->json(['message' => 'Invalid settings data.'], 400);
        }

        $validatedSettings = $rules->validated();

        // Update settings
        try {
            auth()->user()->update($validatedSettings);
            return response()->json(['message' => 'Settings updated successfully.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to update settings.'], 500);
        }
    }
}
