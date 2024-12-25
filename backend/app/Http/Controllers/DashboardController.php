<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function getInventoryValue(): JsonResponse
  {
    $products = Product::where('status', 'IN_STOCK')
      ->whereHas('orders', function ($query) {
        $query->where('status', 'DELIVERED');
      })
      ->get(['quantity', 'price']);

    $inventoryValue = $products->reduce(function ($total, $product) {
      return $total + $product->quantity * $product->price;
    }, 0);

    return response()->json($inventoryValue);
  }

  /**
   * Display a listing of the resource.
   */
  public function getLowStockProducts(): JsonResponse
  {
    try {
      $products = Product::where('status', 'IN_STOCK')
        ->whereHas('orders', function ($query) {
          $query->where('status', 'DELIVERED');
        })
        ->get(['quantity']);

      $lowStockProducts = count(
        $products->filter(function ($product) {
          return $product->quantity <= 10;
        }),
      );

      return response()->json($lowStockProducts);
    } catch (\Throwable $e) {
      return response()->json(
        ['message' => 'Failed to get low stock products.'],
        500,
      );
    }
  }

  /**
   * Display a listing of the resource.
   */
  public function getShippedOrders(): JsonResponse
  {
    try {
      $shippedOrders = Order::where('status', 'SHIPPED')->count();
      return response()->json($shippedOrders);
    } catch (\Throwable $e) {
      return response()->json(
        ['message' => 'Failed to get shipped orders.'],
        500,
      );
    }
  }

  /**
   * Display a listing of the resource.
   */
  public function getUnitsInStock(): JsonResponse
  {
    try {
      $products = Product::whereHas('orders', function ($query) {
        $query->where('status', 'DELIVERED');
      })->sum('quantity');

      return response()->json($products);
    } catch (\Throwable $e) {
      return response()->json(
        ['message' => 'Failed to get units in stock.'],
        500,
      );
    }
  }

  /**
   * Display a listing of the resource.
   */
  public function getActivities(): JsonResponse
  {
    try {
      $activites = Activity::with('user:id,first_name,last_name')->get();
      return response()->json($activites);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to get activities.'], 500);
    }
  }
}
