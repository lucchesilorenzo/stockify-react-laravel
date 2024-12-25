<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
  /**
   * The current password being used by the factory.
   */
  protected static ?string $password;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'email' => fake()->unique()->safeEmail(),
      'password' => (static::$password ??= Hash::make('test')),
      'first_name' => fake()->firstName(),
      'last_name' => fake()->lastName(),
      'date_of_birth' => fake()->dateTimeBetween('-50 years', '-1 year')->format('Y-m-d'),
      'phone' => fake()->unique()->e164PhoneNumber(),
      'bio' => fake()->sentence(),
      'address' => fake()->address(),
      'city' => fake()->city(),
      'zip_code' => fake()->postcode(),
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
    ];
  }

  /**
   * Indicate that the model's email address should be unverified.
   */
  public function unverified(): static
  {
    return $this->state(
      fn(array $attributes) => [
        'email_verified_at' => null,
      ],
    );
  }
}
