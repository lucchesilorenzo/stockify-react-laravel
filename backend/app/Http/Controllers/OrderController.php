<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
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
			return response()->json([
				'message' => 'Failed to get orders',
				'error' => $e->getMessage(),
			], 500);
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return JsonResponse
	 */
	public function getMonthlyOrders(): JsonResponse
	{
		try {
			$start = Carbon::now()->startOfMonth();
			$end = Carbon::now()->endOfMonth();

			$ordersThisMonth = Order::whereBetween('created_at', [$start, $end])->get();
			return response()->json($ordersThisMonth);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to get monthly orders.',
				'error' => $e->getMessage(),
			], 500);
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return JsonResponse
	 */
	public function getWeeklyOrders(): JsonResponse
	{
		try {
			$start = Carbon::now()->locale('it')->startOfWeek();
			$end = Carbon::now()->locale('it')->endOfWeek();

			$ordersThisWeek = Order::whereBetween('created_at', [$start, $end])->get();
			return response()->json($ordersThisWeek);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to get weekly orders.',
				'error' => $e->getMessage(),
			], 500);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return JsonResponse
	 */
	public function createOrder(Request $request): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), [
			'name' => 'required|string|max:20',
			'category_id' => 'required|string|exists:categories,id',
			'warehouse_id' => 'required|string|exists:warehouses,id',
			'supplier_id' => 'required|string|exists:suppliers,id',
			'vat_rate' => 'required|string',
			'price' => 'required|numeric|max:99999',
			'quantity' => 'required|integer|lte:max_quantity',
			'max_quantity' => 'required|integer',
		]);

		// Check if validation fails
		if ($rules->fails()) {
			return response()->json(['message' => 'Invalid order data.'], 400);
		}

		// Get validated data
		$validatedOrder = $rules->validated();

		// Check if there is enough space in the warehouse
		$warehouse = Warehouse::findOrFail($validatedOrder['warehouse_id']);

		if ($validatedOrder['quantity'] + $warehouse->quantity > $warehouse->max_quantity) {
			return response()->json(['message' => 'Not enough space in the warehouse.'], 400);
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
			if ($e->getCode() === '23000') {
				return response()->json(['message' => 'Product already exists.'], 400);
			}
			return response()->json([
				'message' => 'Failed to create product.',
				'error' => $e->getMessage()
			], 500);
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
			if ($e->getCode() === '23000') {
				return response()->json(['message' => 'Order already exists.'], 400);
			}
			return response()->json(['message' => 'Failed to create order.'], 500);
		}

		// Update warehouse quantity
		try {
			Warehouse::where('id', $validatedOrder['warehouse_id'])
				->increment('quantity', $validatedOrder['quantity']);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to update warehouse quantity.',
				'error' => $e->getMessage(),
			], 500);
		}

		// Create a new activity
		$activity = [
			'activity' => 'CREATED',
			'entity' => 'Order',
			'product' => $product->name,
			'user_id' => auth()->user()->id,
		];

		try {
			Activity::create($activity);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to create activity.'], 500);
		}

		return response()->json(['message' => 'Order created successfully.'], 201);
	}

	/**
	 * Create restock order
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function createRestockOrder(Request $request): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), ([
			'product_id' => 'required|string|exists:products,id',
			'supplier_id' => 'required|string|exists:suppliers,id',
			'quantity' => 'required|integer',
		]));

		// Check if validation fails
		if ($rules->fails()) {
			return response()->json(['message' => 'Invalid restock order data.'], 400);
		}

		// Get validated order data
		$validatedRestockOrder = $rules->validated();

		// Check if product exists
		$product = Product::findOrFail($validatedRestockOrder['product_id']);

		// Check if there is enough space in the warehouse
		$warehouse = Warehouse::findOrFail($product->warehouse_id);

		if ($validatedRestockOrder['quantity'] + $warehouse->quantity > $warehouse->max_quantity) {
			return response()->json(['message' => 'Not enough space in the warehouse.'], 400);
		}

		// Check if quantity is valid
		$orderedQuantity = $validatedRestockOrder['quantity'];
		$currentQuantity = $product->quantity;
		$maxQuantity = $product->max_quantity;

		switch (true) {
			case $orderedQuantity <= 0:
				return response()->json([
					'message' => 'The selected quantity must be at least 1.'
				], 400);
			case $currentQuantity >= $maxQuantity:
				return response()->json([
					'message' => 'You cannot order more units of this product. The maximum quantity is already reached.'
				], 400);
			case $orderedQuantity + $currentQuantity > $maxQuantity:
				return response()->json([
					'message' => "The total quantity cannot exceed {$maxQuantity}. Remaining: " . ($maxQuantity - $currentQuantity) . "."
				], 400);
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
			if ($e->getCode() === '23000') {
				return response()->json([
					'message' => 'Restock order already exists.',
				], 500);
			}
			return response()->json([
				'message' => 'Failed to create restock order.',
			], 500);
		}

		// Update product quantity
		try {
			$product->update(['status' => 'IN_STOCK']);
			$product->increment('quantity', $validatedRestockOrder['quantity']);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to update product quantity.'], 500);
		}

		// Update warehouse quantity
		try {
			$warehouse->increment('quantity', $validatedRestockOrder['quantity']);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to update warehouse quantity.'], 500);
		}

		// Create a new activity
		$activity = [
			'activity' => 'CREATED',
			'entity' => 'Restock',
			'product' => $product->name,
			'user_id' => auth()->user()->id,
		];

		try {
			Activity::create($activity);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to create activity.'], 500);
		}

		return response()->json(['message' => 'Restock order created successfully.'], 201);
	}

	/**
	 * Update order status.
	 *
	 * @param Order $order
	 * @return void
	 */
	public function updateOrderStatus(Order $order): JsonResponse
	{
		// Update order status
		try {
			$order->update(['status' => 'DELIVERED']);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to update order status.'], 500);
		}

		// Create a new activity
		$activity = [
			'activity' => 'UPDATED',
			'entity' => 'Order',
			'user_id' => auth()->user()->id,
		];

		try {
			Activity::create($activity);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to create activity.'], 500);
		}

		return response()->json(['message' => 'Order status updated successfully.'], 200);
	}
}
