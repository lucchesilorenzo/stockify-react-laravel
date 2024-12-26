<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSupplierRequest;
use App\Http\Requests\UpdateSupplierRatingRequest;
use App\Models\Activity;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
	/**
	 * Get all suppliers.
	 *
	 * @return JsonResponse
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
	 * Create a new supplier.
	 *
	 * @param CreateSupplierRequest $request
	 * @return JsonResponse
	 */
	public function createSupplier(CreateSupplierRequest $request): JsonResponse
	{
		// Get validated data
		$validatedData = $request->validated();

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
	 * Update supplier rating.
	 *
	 * @param UpdateSupplierRatingRequest $request
	 * @param Supplier $supplier
	 * @return JsonResponse
	 */
	public function updateSupplierRating(UpdateSupplierRatingRequest $request, Supplier $supplier): JsonResponse
	{
		// Get validated data
		$validatedData = $request->validated();

		// Update supplier rating
		try {
			$supplier->update($validatedData);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to update supplier rating.'], 500);
		}

		// Create activity
		try {
			Activity::create([
				'activity' => 'UPDATED',
				'entity' => 'Supplier',
				'user_id' => auth()->user()->id,
			]);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to create activity.'], 500);
		}

		return response()->json(['message' => 'Supplier rating updated successfully.'], 200);
	}
}
