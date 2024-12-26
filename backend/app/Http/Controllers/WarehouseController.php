<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    /**
     * Get all warehouses.
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
