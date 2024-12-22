<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function getCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
}
