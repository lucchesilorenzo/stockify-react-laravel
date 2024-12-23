<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return JsonResponse
   */
  public function getProducts(): JsonResponse
  {
    try {
      $products = Product::whereHas('orders', function ($query) {
        $query->where('status', 'DELIVERED');
      })
        ->with([
          'category' => fn($query) => $query->select('id', 'name'),
          'warehouse' => fn($query) => $query->select('id', 'name'),
        ])
        ->get();

      return response()->json($products);
    } catch (\Throwable $e) {
      return response()->json([
        'message' => 'Failed to get products.',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Display a listing of the resource.
   *
   * @return JsonResponse
   */
  public function getAvailableProducts(): JsonResponse
  {
    try {
      $availableProducts = Product::where('status', 'IN_STOCK')
        ->where('quantity', '>', 0)
        ->whereHas('orders', function ($query) {
          $query->where('status', 'DELIVERED');
        })->get();

      return response()->json($availableProducts);
    } catch (\Throwable $e) {
      return response()->json([
        'message' => 'Failed to get available products.',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Display a listing of the resource.
   *
   * @return JsonResponse
   */
  public function getProductsToRestock(): JsonResponse
  {
    try {
      $productsToRestock = Product::whereNot('status', 'ARCHIVED')
        ->whereHas('orders', function ($query) {
          $query->where('status', 'DELIVERED');
        })->get();

      return response()->json($productsToRestock);
    } catch (\Throwable $e) {
      return response()->json([
        'message' => 'Failed to get products to restock.',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param string $productSlug
   * @return JsonResponse
   */
  public function getProductBySlug(string $productSlug): JsonResponse
  {
    try {
      $product = Product::where('slug', $productSlug)->first();
      return response()->json($product);
    } catch (\Throwable $e) {
      return response()->json([
        'message' => 'Failed to get product by slug.',
        'error' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param Request $request
   * @param Product $product
   * @return JsonResponse
   */
  public function updateProduct(Request $request, Product $product): JsonResponse
  {
    // Validation
    $rules = Validator::make($request->all(), [
      'description' => 'nullable|string|max:200',
      'price' => 'required|numeric|max:99999',
      'max_quantity' => 'required|integer|max:500',
      'category_id' => 'nullable|string|exists:categories,id',
      'warehouse_id' => 'nullable|string|exists:warehouses,id',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
    ]);

    // Check if validation fails
    if ($rules->fails()) {
      return response()->json(['message' => 'Invalid product data.'], 400);
    }

    $validatedProduct = $rules->validated();

    // Check if max quantity is greater than current quantity
    if (!($validatedProduct['max_quantity'] >= $product->max_quantity)) {
      return response()->json(['message' => 'Max quantity must be greater than current quantity.'], 400);
    }

    $warehouseId = $validatedProduct['warehouse_id'] ?? null;

    // Decrement quantity of old warehouse if new warehouse is different
    if ($warehouseId && $warehouseId !== $product->warehouse_id) {
      // Get new warehouse
      $newWarehouse = Warehouse::findOrFail($validatedProduct['warehouse_id']);

      try {
        if ($newWarehouse->quantity + $product->quantity <= $newWarehouse->max_quantity) {
          $newWarehouse->increment('quantity', $product->quantity);
          $product->warehouse()->decrement('quantity', $product->quantity);
        } else {
          return response()->json(['message' => "There is not enough space in {$newWarehouse->name}."], 400);
        }
      } catch (\Throwable $e) {
        return response()->json([
          'message' => 'Failed to update warehouses.',
          'error' => $e->getMessage(),
        ], 500);
      }
    }

    // Delete previous file if it exists
    if ($request->hasFile('image')) {
      if ($product->image && $product->image !== "placeholder.svg") {
        if (Storage::disk('public')->exists($product->image)) {
          Storage::disk('public')->delete($product->image);
        }
      }
      $path = $request->file('image')->store('products/images', 'public');
      $validatedProduct['image'] = $path;
    }

    // Update product
    try {
      $product->update($validatedProduct);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to update product.'], 500);
    }

    // Create a new activity
    $activity = [
      'activity' => 'UPDATED',
      'entity' => 'Product',
      'product' => $product->name,
      'user_id' => auth()->user()->id,
    ];

    try {
      Activity::create($activity);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(['message' => 'Product updated successfully.'], 200);
  }


  /**
   * Update the specified resource in storage.
   *
   * @param Request $request
   * @param Product $product
   * @return JsonResponse
   */
  public function updateProductStatus(Request $request, Product $product): JsonResponse
  {
    // Validation
    $rules = Validator::make($request->all(), [
      'status' => 'required|in:IN_STOCK,OUT_OF_STOCK,ARCHIVED',
    ]);

    // Check if validation fails
    if ($rules->fails()) {
      return response()->json(['message' => 'Invalid product status.'], 400);
    }

    $validatedData = $rules->validated();

    // Set product status
    try {
      if ($validatedData['status'] !== 'ARCHIVED') {
        $product->update(['status' => 'ARCHIVED']);
      } else {
        $product->update(['status' => $product->quantity === 0 ? 'OUT_OF_STOCK' : 'IN_STOCK']);
      }
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to update product status.'], 500);
    }

    // Create a new activity
    $activity = [
      'activity' => $validatedData['status'] !== 'ARCHIVED' ? 'ARCHIVED' : 'RESTORED',
      'entity' => 'Product',
      'product' => $product->name,
      'user_id' => auth()->user()->id,
    ];

    try {
      Activity::create($activity);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Failed to create activity.'], 500);
    }

    return response()->json(['message' => 'Product status updated successfully.'], 200);
  }
}
