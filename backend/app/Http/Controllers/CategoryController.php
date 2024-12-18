<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function getCategories(Request $request)
    {
        $categories = Category::all();
        return response()->json($categories);
    }
}
