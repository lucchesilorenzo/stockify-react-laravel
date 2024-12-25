<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerShipment;
use App\Models\MonthlyInventoryValue;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShipmentItem;
use App\Models\Supplier;
use App\Models\Task;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(5)->create();
        Category::factory(5)->create();
        Warehouse::factory(4)->create();
        Supplier::factory(4)->create();
        Task::factory(4)->create();
        Product::factory(20)->create();
        Order::factory(20)->create();
        Customer::factory(5)->create();
        CustomerShipment::factory(5)->create();
        ShipmentItem::factory(5)->create();
        Activity::factory(5)->create();
        MonthlyInventoryValue::factory(5)->create();
    }
}
