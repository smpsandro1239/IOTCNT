<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Valve>
 */
class ValveFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    static $valveNumber = 1;

    return [
      'name' => 'Válvula ' . $valveNumber,
      'valve_number' => $valveNumber++,
      'description' => fake()->sentence(),
      'current_state' => fake()->boolean(20), // 20% chance of being active
      'last_activated_at' => fake()->optional(0.7)->dateTimeBetween('-1 month', 'now'),
      'esp32_pin' => fake()->numberBetween(18, 27),
    ];
  }

  /**
   * Indicate that the valve should be active.
   */
  public function active(): static
  {
    return $this->state(fn(array $attributes) => [
      'current_state' => true,
      'last_activated_at' => now(),
    ]);
  }

  /**
   * Indicate that the valve should be inactive.
   */
  public function inactive(): static
  {
    return $this->state(fn(array $attributes) => [
      'current_state' => false,
    ]);
  }

  /**
   * Reset the valve number counter.
   */
  public static function resetValveNumber(): void
  {
    static $valveNumber = 1;
  }
}
