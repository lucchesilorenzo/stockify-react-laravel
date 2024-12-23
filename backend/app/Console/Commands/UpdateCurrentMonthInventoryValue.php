<?php

namespace App\Console\Commands;

use App\Models\MonthlyInventoryValue;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateCurrentMonthInventoryValue extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'inventory:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update the total inventory value for the current month';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		try {
			$inventoryData = Product::where('status', 'IN_STOCK')
				->whereHas('orders', function ($query) {
					$query->where('status', 'DELIVERED');
				})
				->get(['quantity', 'price']);

			$totalValue = $inventoryData->reduce(function ($total, $product) {
				return $total + $product->price * $product->quantity;
			}, 0);

			$currentMonth = Carbon::now()->startOfMonth();

			MonthlyInventoryValue::updateOrCreate(
				['month' => $currentMonth], // Check if the month already exists
				['total_value' => $totalValue] // If not, create a new record with month and total value
			);

			$this->info('Current month inventory value updated successfully.');
		} catch (\Throwable $e) {
			$this->error('Failed to update current month inventory value: ' . $e->getMessage());
		}
	}
}
