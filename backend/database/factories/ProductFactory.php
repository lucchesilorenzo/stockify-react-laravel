<?php

namespace Database\Factories;

use App\Helpers\StringHelper;
use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id,
            'warehouse_id' => Warehouse::inRandomOrder()->first()->id,
            'name' => fake()->name(),
            'slug' => fn(array $attributes) => StringHelper::generateSlug($attributes['name']),
            'sku' => function (array $attributes) {
                $category = Category::find($attributes['category_id']);
                return StringHelper::generateSKU($category->name, $attributes['name']);
            },
            'price' => fake()->randomFloat(2, 1, 100000),
            'quantity' => fake()->numberBetween(1, 100),
            'max_quantity' => fn(array $attributes) => fake()->numberBetween($attributes['quantity'], 100),
            'vat_rate' => fake()->randomElement([4, 10, 22]),
            'description' => fake()->sentence(10),
            'status' => fake()->randomElement(['IN_STOCK', 'OUT_OF_STOCK', 'ARCHIVED']),
        ];
    }
}
