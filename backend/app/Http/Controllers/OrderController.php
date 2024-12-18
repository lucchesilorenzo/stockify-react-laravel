<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return void
	 */
	public function getOrders(Request $request)
	{
		$orders = Order::with([
			'product' => function ($query) {
				$query->select('name');
			},
			'supplier' => function ($query) {
				$query->select('name');
			},
			'user' => function ($query) {
				$query->select('firstName', 'lastName');
			}
		])->get();

		return response()->json($orders);
	}
}
