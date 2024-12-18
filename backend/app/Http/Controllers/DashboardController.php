<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     */
    public function getInventoryValue(Request $request)
    {
        $products = Product::where('status', 'IN_STOCK')
            ->whereDoesntHave('orders', function ($query) {
                $query->where('status', '!=', 'DELIVERED');
            })
            ->get(['quantity', 'price']);

        $inventoryValue = $products->reduce(function ($total, $product) {
            return $total + ($product->quantity * $product->price);
        }, 0);

        return response()->json([
            'inventoryValue' => $inventoryValue
        ]);
    }
}
