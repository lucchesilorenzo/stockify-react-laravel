<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'activity' => fake()->randomElement([
                'CREATED',
                'UPDATED',
                'DELETED',
                'ARCHIVED',
                'RESTORED',
            ]),
            'entity' => fake()->randomElement([
                'Product',
                'Order',
                'Restock',
                'Shipment',
                'Customer',
                'Supplier',
                'Task'
            ]),
            'product' => function (array $attributes) {
                if ($attributes['entity'] !== 'Product') return null;
                Product::inRandomOrder()->first()->name;
            },
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
