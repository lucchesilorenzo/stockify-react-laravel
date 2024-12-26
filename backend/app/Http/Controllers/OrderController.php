<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\CreateRestockOrderRequest;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return JsonResponse
   */
  public function getOrders(): JsonResponse
  {
    try {
      $orders = Order::with([
        'product' => fn($query) => $query->select('id', 'name'),
        'supplier' => fn($query) => $query->select('id', 'name'),
        'user' => fn($query) => $query->select('id', 'first_name', 'last_name'),
      ])->get();

      return response()->json($orders);
    } catch (\Throwable $e) {
      return response()->json(
        [
          'message' => 'Failed to get orders',
          'error' => $e->getMessage(),
        ],
        500,
      );
    }
  }

  /**
   * Get the monthly orders.
   *
   * @return JsonResponse
   */
  public function getMonthlyOrders(): JsonResponse
  {
    try {
      $start = Carbon::now()->startOfMonth();
      $end = Carbon::now()->endOfMonth();

      $ordersThisMonth = Order::whereBetween('created_at', [
        $start,
        $end,
      ])->get();

      return response()->json($ordersThisMonth);
    } catch (\Throwable $e) {
      return response()->json(
        [
          'message' => 'Failed to get monthly orders.',
          'error' => $e->getMessage(),
        ],
        500,
      );
    }
  }

  /**
   * Get the weekly orders.
   *
   * @return JsonResponse
   */
  public function getWeeklyOrders(): JsonResponse
  {
    try {
      $start = Carbon::now()->locale('it')->startOfWeek();
      $end = Carbon::now()->locale('it')->endOfWeek();

      $ordersThisWeek = Order::whereBetween('created_at', [
        $start,
        $end,
      ])->get();

      return response()->json($ordersThisWeek);
    } catch (\Throwable $e) {
      return response()->json(
        [
          'message' => 'Failed to get weekly orders.',
          'error' => $e->getMessage(),
        ],
        500,
      );
    }
  }

  /**
   * Create a new order.
   *
   * @param CreateOrderRequest $request
   * @return JsonResponse
   */
  public function createOrder(CreateOrderRequest $request): JsonResponse
  {
    // Get validated data
    $validatedOrder = $request->validated();

    // Check if there is enough space in the warehouse
    $warehouse = Warehouse::findOrFail($validatedOrder['warehouse_id']);

    if (
      $warehouse->max_quantity <
      $validatedOrder['quantity'] + $warehouse->quantity
    ) {
      return response()->json(
        ['message' => 'Not enough space in the warehouse.'],
        400,
      );
    }

    // Get category
    $category = Category::findOrFail($validatedOrder['category_id']);

    // Destructure validated data
    $productData = collect($validatedOrder)
      ->except(['supplier_id', 'vat_rate'])
      ->toArray();

    // Generate SKU and slug
    $productData['sku'] = StringHelper::generateSKU($category->name, $productData['name']);
    $productData['slug'] = StringHelper::generateSlug($productData['name']);

    $productData['vat_rate'] = (int) $validatedOrder['vat_rate'];

    // Create a new product
    try {
      $product = Product::create($productData);
    } catch (QueryException $e) {
      if ($e->getCode() === 23000) {
        return response()->json(['message' => 'Product already exists.'], 400);
      }

      return response()->json(
        [
          'message' => 'Failed to create product.',
          'error' => $e->getMessage(),
        ],
        500,
      );
    }

    // Calculate order details
    $subtotal = $validatedOrder['price'] * $validatedOrder['quantity'];
    $shipping = $subtotal > 50 ? 0 : 9.99;
    $vat = $subtotal * ($validatedOrder['vat_rate'] / 100);
    $totalPrice = $subtotal + $shipping + $vat;

    $orderDetails = [
      'product_id' => $product->id,
      'supplier_id' => $validatedOrder['supplier_id'],
      'user_id' => auth()->user()->id,
      'type' => 'NEW',
      'quantity' => $validatedOrder['quantity'],
      'subtotal' => (float) $subtotal,
      'shipping' => (float) $shipping,
      'vat' => (float) $vat,
      'total_price' => (float) $totalPrice,
    ];

    // Create a new order
    try {
      Order::create($orderDetails);
    } catch (QueryException $e) {
      if ($e->getCode() === 23000) {
        return response()->json(['message' => 'Order already exists.'], 400);
      }

      return response()->json(['message' => 'Failed to create order.'], 500);
    }

    // Update warehouse quantity
    try {
      Warehouse::where('id', $validatedOrder['warehouse_id'])->increment(
        'quantity',
        $validatedOrder['quantity'],
      );
    } catch (\Throwable $e) {
      return response()->json(
        [
          'message' => 'Failed to update warehouse quantity.',
          'error' => $e->getMessage(),
        ],
        500,
      );
    }

    // Create a new activity
    try {
      Activity::create([
        'activity' => 'CREATED',
        'entity' => 'Order',
        'product' => $product->name,
        'user_id' => auth()->user()->id,
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(['message' => 'Order created successfully.'], 201);
  }

  /**
   * Create a new restock order.
   *
   * @param CreateRestockOrderRequest $request
   * @return JsonResponse
   */
  public function createRestockOrder(CreateRestockOrderRequest $request): JsonResponse
  {
    // Get validated order data
    $validatedRestockOrder = $request->validated();

    // Check if product exists
    $product = Product::findOrFail($validatedRestockOrder['product_id']);

    // Check if there is enough space in the warehouse
    $warehouse = Warehouse::findOrFail($product->warehouse_id);

    if (
      $warehouse->max_quantity <
      $validatedRestockOrder['quantity'] + $warehouse->quantity
    ) {
      return response()->json(
        ['message' => 'Not enough space in the warehouse.'],
        400,
      );
    }

    // Check if quantity is valid
    $orderedQuantity = $validatedRestockOrder['quantity'];
    $currentQuantity = $product->quantity;
    $maxQuantity = $product->max_quantity;

    switch (true) {
      case $orderedQuantity <= 0:
        return response()->json(
          [
            'message' => 'The selected quantity must be at least 1.',
          ],
          400,
        );
      case $currentQuantity >= $maxQuantity:
        return response()->json(
          [
            'message' =>
            'You cannot order more units of this product. The maximum quantity is already reached.',
          ],
          400,
        );
      case $orderedQuantity + $currentQuantity > $maxQuantity:
        return response()->json(
          [
            'message' =>
            "The total quantity cannot exceed {$maxQuantity}. Remaining: " .
              ($maxQuantity - $currentQuantity) .
              '.',
          ],
          400,
        );
    }

    // Calculate order details
    $subtotal = $product->price * $validatedRestockOrder['quantity'];
    $shipping = $subtotal > 50 ? 0 : 9.99;
    $vat = $subtotal * ($product->vat_rate / 100);
    $totalPrice = $subtotal + $shipping + $vat;

    // Create order details
    $orderDetails = [
      'product_id' => $validatedRestockOrder['product_id'],
      'supplier_id' => $validatedRestockOrder['supplier_id'],
      'user_id' => auth()->user()->id,
      'type' => 'RESTOCK',
      'quantity' => $orderedQuantity,
      'subtotal' => (float) $subtotal,
      'shipping' => (float) $shipping,
      'vat' => (float) $vat,
      'total_price' => (float) $totalPrice,
    ];

    // Create restock order
    try {
      Order::create($orderDetails);
    } catch (QueryException $e) {
      if ($e->getCode() === 23000) {
        return response()->json(
          ['message' => 'Restock order already exists.'],
          500,
        );
      }

      return response()->json(
        ['message' => 'Failed to create restock order.'],
        500,
      );
    }

    // Update product quantity
    try {
      $product->update(['status' => 'IN_STOCK']);
      $product->increment('quantity', $validatedRestockOrder['quantity']);
    } catch (\Throwable $e) {
      return response()->json(
        ['message' => 'Failed to update product quantity.'],
        500,
      );
    }

    // Update warehouse quantity
    try {
      $warehouse->increment('quantity', $validatedRestockOrder['quantity']);
    } catch (\Throwable $e) {
      return response()->json(
        ['message' => 'Failed to update warehouse quantity.'],
        500,
      );
    }

    // Create a new activity
    try {
      Activity::create([
        'activity' => 'CREATED',
        'entity' => 'Restock',
        'product' => $product->name,
        'user_id' => auth()->user()->id,
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(
      ['message' => 'Restock order created successfully.'],
      201,
    );
  }

  /**
   * Update order status
   *
   * @param Order $order
   * @return JsonResponse
   */
  public function updateOrderStatus(Order $order): JsonResponse
  {
    // Update order status
    try {
      $order->update(['status' => 'DELIVERED']);
    } catch (\Throwable $e) {
      return response()->json(
        ['message' => 'Failed to update order status.'],
        500,
      );
    }

    // Create a new activity
    try {
      Activity::create([
        'activity' => 'UPDATED',
        'entity' => 'Order',
        'user_id' => auth()->user()->id,
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(
      ['message' => 'Order status updated successfully.'],
      200,
    );
  }
}
