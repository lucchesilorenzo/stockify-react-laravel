<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MonthlyInventoryValue;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
	/**
	 * Get products by category.
	 *
	 * @return JsonResource
	 */
	public function getProductsByCategory(): JsonResponse
	{
		// Define colors
		$colors = [
			'var(--chart-1)',
			'var(--chart-2)',
			'var(--chart-3)',
			'var(--chart-4)',
			'var(--chart-5)',
		];

		try {
			$productsByCategory = Category::with([
				'products' => function ($query) {
					$query->where('status', 'IN_STOCK')
						->whereHas('orders', function ($query) {
							$query->where('status', 'DELIVERED');
						})
						->select('quantity', 'category_id');
				}
			])->get();

			$pieChartData = $productsByCategory->map(function ($category, $index) use ($colors) {
				return [
					'category' => $category->name,
					'units' => $category->products->sum('quantity'),
					'fill' => 'hsl(' . $colors[$index % count($colors)] . ')',
				];
			});

			$pieChartConfig = $pieChartData->reduce(function ($config, $data) {
				$config[$data['category']] = [
					'label' => $data['category'],
					'color' => $data['fill'],
				];

				return $config;
			}, []);

			return response()->json([
				'pieChartData' => $pieChartData,
				'pieChartConfig' => $pieChartConfig,
			]);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to get products by category.',
			], 500);
		}
	}

	/**
	 * Get monthly inventory value.
	 *
	 * @return JsonResponse
	 */
	public function getMonthlyInventoryValue(): JsonResponse
	{
		try {
			$inventoryData = MonthlyInventoryValue::orderBy('month', 'asc')->get();
			$lineChartData = $inventoryData->map(function ($data) {
				return [
					'month' => Carbon::parse($data->month)->format('F'),
					'value' => $data->total_value,
				];
			});

			return response()->json($lineChartData);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to get monthly inventory value.',
			], 500);
		}
	}

	/**
	 * Get top products.
	 *
	 * @return JsonResponse
	 */
	public function getTopProducts(): JsonResponse
	{
		try {
			$products =	Product::where('status', 'IN_STOCK')
				->whereHas('orders', function ($query) {
					$query->where('status', 'DELIVERED');
				})
				->get(['name', 'quantity', 'price',]);

			$topProducts = $products->map(function ($product) {
				return [
					'product' => $product->name,
					'value' => $product->quantity * $product->price,
				];
			})
				->sortByDesc('value')
				->take(5)
				->values();

			return response()->json($topProducts);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to get top products.',
			], 500);
		}
	}
}
