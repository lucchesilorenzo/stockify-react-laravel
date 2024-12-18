<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return void
   */
  public function getProducts(Request $request)
  {
    $products = Product::whereHas('orders', function ($query) {
      $query->where('status', 'DELIVERED');
    })
      ->with([
        'category' => function ($query) {
          $query->select('name');
        },
        'warehouse' => function ($query) {
          $query->name('name');
        }
      ])
      ->get();

    return response()->json($products);
  }
}
