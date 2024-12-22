<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return JsonResponse
	 */
	public function getCategories(): JsonResponse
	{
		try {
			$categories = Category::all();
			return response()->json($categories);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to get categories.'], 500);
		}
	}
}
