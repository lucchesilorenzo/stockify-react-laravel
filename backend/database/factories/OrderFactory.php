<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'supplier_id' => Supplier::inRandomOrder()->first()->id,
            'product_id' => Product::inRandomOrder()->first()->id,
            'type' => fake()->randomElement(['NEW', 'RESTOCK']),
            'quantity' => fake()->numberBetween(1, 100),
            'subtotal' => fake()->randomFloat(2, 1, 100000),
            'shipping' => fake()->randomElement([0, 9.99]),
            'vat' => fake()->randomFloat(2, 1, 100000),
            'total_price' => fake()->randomFloat(2, 1, 100000),
            'status' => fake()->randomElement(['SHIPPED', 'DELIVERED']),
        ];
    }
}
