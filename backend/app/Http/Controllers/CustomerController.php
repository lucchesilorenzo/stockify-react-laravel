<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateShipmentStatus;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\CustomerShipment;
use App\Models\Product;
use App\Models\ShipmentItem;
use App\Models\Warehouse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
	/**
	 * Get all customers.
	 *
	 * @return JsonResponse
	 */
	public function getCustomers(): JsonResponse
	{
		try {
			$customers = Customer::with([
				'customerShipments.shipmentItems' => function ($query) {
					$query->with('product:id,name,price');
				}
			])
				->get();

			return response()->json($customers);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to get customers.',
				'error' => $e->getMessage()
			], 500);
		}
	}

	/**
	 * Create a new customer shipment.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function createCustomerShipment(Request $request): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), [
			'first_name' => 'required|string|max:20',
			'last_name' => 'required|string|max:20',
			'email' => 'required|email',
			'phone' => 'required|string|max:15',
			'address' => 'required|string|max:40',
			'city' => 'required|string|max:20',
			'zip_code' => 'required|string|max:10',
			'products' => 'required|array',
			'products.*.product_id' => 'required|string|exists:products,id',
			'products.*.warehouse_id' => 'required|string|exists:warehouses,id',
			'products.*.name' => 'required|string',
			'products.*.quantity' => 'required|integer',
		]);

		if ($rules->fails()) {
			return response()->json(['message' => 'Invalid customer data.'], 400);
		}

		$validatedShipment = $rules->validated();

		// Check if customer alredy exists
		$customer = Customer::where('email', $validatedShipment['email'])->first();
		$newCustomer = null;

		// If customer does not exist, create a new customer
		if (!$customer) {
			try {
				$newCustomer = Customer::create([
					'first_name' => $validatedShipment['first_name'],
					'last_name' => $validatedShipment['last_name'],
					'email' => $validatedShipment['email'],
					'phone' => $validatedShipment['phone'],
					'address' => $validatedShipment['address'],
					'city' => $validatedShipment['city'],
					'zip_code' => $validatedShipment['zip_code'],
				]);
			} catch (\Throwable $e) {
				return response()->json(['message' => 'Failed to create customer.'], 500);
			}
		}

		// Create new activity if customer was created
		if ($newCustomer) {
			try {
				Activity::create([
					'activity' => 'CREATED',
					'entity' => 'Customer',
					'user_id' => auth()->user()->id,
				]);
			} catch (\Throwable $e) {
				return response()->json(['message' => 'Failed to create activity.'], 500);
			}
		}

		// Take customer ID from new customer or existing customer
		$customerId = $customer->id ?? $newCustomer->id;
		if (!$customerId) {
			return response()->json(['message' => 'Failed to get customer ID.'], 500);
		}

		// Create new customer shipment
		try {
			$newShipment = CustomerShipment::create([
				'customer_id' => $customerId,
			]);

			// Update shipment status with job
			UpdateShipmentStatus::dispatch($newShipment->id)->delay(now()->addMinutes(5));

			// Create items for shipment
			$customerShipmentItems = collect($validatedShipment['products'])->map(function ($p) use ($newShipment) {
				return [
					'product_id' => $p['product_id'],
					'customer_shipment_id' => $newShipment->id,
					'quantity' => $p['quantity'],
				];
			})->toArray();

			// Assign items to shipment and add to customer
			foreach ($customerShipmentItems as $item) {
				ShipmentItem::create($item);
			}
		} catch (QueryException $e) {
			if ($e->getCode() === 23000) {
				return response()->json([
					'message' => 'Customer already exists.',
					'error' => $e->getMessage()
				], 400);
			}
			return response()->json([
				'message' => 'Failed to create customer shipment.',
				'error' => $e->getMessage()
			], 500);
		}

		// Decrement quantity from inventory
		$productsToUpdate = collect($validatedShipment['products'])->map(function ($p) {
			return [
				'id' => $p['product_id'],
				'quantity' => $p['quantity'],
			];
		});

		try {
			foreach ($productsToUpdate as $p) {
				$product = Product::find($p['id']);

				if ($product && $product->quantity >= $p['quantity']) {
					$newQuantity = $product->quantity - $p['quantity'];

					$product->update([
						'quantity' => $newQuantity,
						'status' => $newQuantity === 0 ? 'OUT_OF_STOCK' : 'IN_STOCK',
					]);
				}
			}
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to update inventory.',
				'error' => $e->getMessage()
			], 500);
		}

		// Decrement quantity from warehouse
		$warehousesToUpdate = collect($validatedShipment['products'])->map(function ($p) {
			return [
				'id' => $p['warehouse_id'],
				'quantity' => $p['quantity'],
			];
		});

		try {
			foreach ($warehousesToUpdate as $w) {
				$warehouse = Warehouse::find($w['id']);

				if ($warehouse && $warehouse->quantity >= $w['quantity']) {
					$newQuantity = $warehouse->quantity - $w['quantity'];

					$warehouse->update([
						'quantity' => $newQuantity,
					]);
				}
			}
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to update warehouse.',
				'error' => $e->getMessage()
			], 500);
		}

		// Create new activity
		try {
			Activity::create([
				'activity' => 'CREATED',
				'entity' => 'Shipment',
				'product' => collect($validatedShipment['products'])->map(fn($p) => $p['name'])->implode(', '),
				'user_id' => auth()->user()->id,
			]);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to create activity.'], 500);
		}

		return response()->json(['message' => 'Shipment created successfully.'], 200);
	}

	/**
	 * Create customers.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function createCustomers(Request $request): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), [
			// '*.' is used to validate each field in an array of objects
			'*.first_name' => 'required|string|max:20',
			'*.last_name' => 'required|string|max:20',
			'*.email' => 'required|email',
			'*.phone' => 'required|string|max:15',
			'*.address' => 'required|string|max:40',
			'*.city' => 'required|string|max:20',
			'*.zip_code' => 'required|string|max:10',
		]);

		if ($rules->fails()) {
			return response()->json([
				'message' => 'Invalid CSV file format.',
			], 400);
		}

		$validatedCustomerData = $rules->validated();

		// Create customers
		try {
			foreach ($validatedCustomerData as $customer) {
				if (!str_starts_with($customer['phone'], '+')) {
					$customer['phone'] = '+' . $customer['phone'];
				}

				Customer::create($customer);
			}
		} catch (QueryException $e) {
			if ($e->getCode() === 23000) {
				return response()->json([
					'message' => 'Customer already exists.',
					'error' => $e->getMessage()
				], 400);
			}
			return response()->json([
				'message' => 'Failed to create customer.',
				'error' => $e->getMessage()
			], 500);
		}

		// Create activity
		try {
			Activity::create([
				'activity' => 'CREATED',
				'entity' => 'Customer',
				'user_id' => auth()->user()->id,
			]);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to create activity.'], 500);
		}

		return response()->json(['message' => 'Customers created successfully.'], 200);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param Customer $customer
	 * @return JsonResponse
	 */
	public function updateCustomer(Request $request, Customer $customer): JsonResponse
	{
		// Validation
		$rules = Validator::make($request->all(), [
			'first_name' => 'required|string|max:20',
			'last_name' => 'required|string|max:20',
			'email' => 'required|email',
			'phone' => 'required|string|max:15',
			'address' => 'required|string|max:40',
			'city' => 'required|string|max:20',
			'zip_code' => 'required|string|max:10',
		]);

		if ($rules->fails()) {
			return response()->json([
				'message' => 'Invalid customer data.',
			], 400);
		}

		$validatedCustomer = $rules->validated();

		// Update customer	
		try {
			$customer->update($validatedCustomer);
		} catch (\Throwable $e) {
			return response()->json([
				'message' => 'Failed to update customer.',
				'error' => $e->getMessage()
			], 500);
		}

		// Create activity
		try {
			Activity::create([
				'activity' => 'UPDATED',
				'entity' => 'Customer',
				'user_id' => auth()->user()->id,
			]);
		} catch (\Throwable $e) {
			return response()->json(['message' => 'Failed to create activity.'], 500);
		}

		return response()->json(['message' => 'Customer updated successfully.'], 200);
	}
}
