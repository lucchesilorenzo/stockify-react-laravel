<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return JsonResponse
	 */
	public function getWarehouses(): JsonResponse
	{
		try {
			$warehouses = Warehouse::all();
			return response()->json($warehouses);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to get warehouses.'], 500);
		}
	}
}
