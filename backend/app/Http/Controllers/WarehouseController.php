<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     */
    public function getWarehouses(Request $request)
    {
        $warehouses = Warehouse::all();
        return response()->json($warehouses);
    }
}
