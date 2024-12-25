<?php

namespace Database\Factories;

use App\Models\CustomerShipment;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentItem>
 */
class ShipmentItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'customer_shipment_id' => CustomerShipment::inRandomOrder()->first()->id,
            'quantity' => fake()->numberBetween(1, 20),
        ];
    }
}
