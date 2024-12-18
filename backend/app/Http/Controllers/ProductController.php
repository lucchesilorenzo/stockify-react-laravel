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
        $products = Product::all();
        return response()->json($products);
    }
}
