<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function getSuppliers(): JsonResponse
	{
		try {
			$suppliers = Supplier::withCount('orders')->get();
			return response()->json($suppliers);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to get suppliers.'], 500);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function createSupplier(Request $request): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), ([
			'name' => 'required|string|max:20',
			'email' => 'required|email|unique:users',
			'phone' => 'required|string|max:15',
			'address' => 'required|string|max:40',
			'city' => 'required|string|max:20',
			'zip_code' => 'required|string|max:10',
			'website' => 'nullable|string|max:100',
		]));

		// Check if validation fails
		if ($rules->fails()) {
			return response()->json(['message' => 'Invalid supplier data.'], 400);
		}

		// Get validated data
		$validatedData = $rules->validated();

		// Create supplier
		try {
			Supplier::create($validatedData);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to create supplier.',
				'error' => $e->getMessage(),
			], 500);
		}

		// Create activity
		$activity = [
			'activity' => 'CREATED',
			'entity' => 'Supplier',
			'user_id' => auth()->user()->id,
		];

		try {
			Activity::create($activity);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to create activity.',
				'error' => $e->getMessage(),
			], 500);
		}

		return response()->json(['message' => 'Supplier created successfully.'], 201);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function updateSupplierRating(Request $request, Supplier $supplier): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), ([
			'rating' => 'required|integer|min:1|max:5',
		]));

		// Check if validation fails
		if ($rules->fails()) {
			return response()->json(['message' => 'Invalid rating data.'], 400);
		}

		// Get validated data
		$validatedData = $rules->validated();

		// Update supplier rating
		try {
			$supplier->update($validatedData);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to update supplier rating.'], 500);
		}

		// Create activity
		$activity = [
			'activity' => 'UPDATED',
			'entity' => 'Supplier',
			'user_id' => auth()->user()->id,
		];

		try {
			Activity::create($activity);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to create activity.'], 500);
		}

		return response()->json(['message' => 'Supplier rating updated successfully.'], 200);
	}
}
