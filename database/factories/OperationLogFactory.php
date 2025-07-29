<?php

namespace Database\Factories;

use App\Models\OperationLog;
use App\Models\Valve;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OperationLog>
 */
class OperationLogFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $actions = ['manual_on', 'manual_off', 'cycle_start', 'cycle_end', 'emergency_stop', 'scheduled_on', 'scheduled_off'];
    $sources = ['web_interface', 'telegram_bot', 'esp32_device', 'scheduled_task'];

    return [
      'valve_id' => Valve::factory(),
      'action' => $this->faker->randomElement($actions),
      'duration_minutes' => $this->faker->optional(0.7)->numberBetween(1, 30),
      'source' => $this->faker->randomElement($sources),
      'user_id' => $this->faker->optional(0.8)->randomElement([null, User::factory()]),
      'telegram_user_id' => $this->faker->optional(0.2)->numberBetween(100000000, 999999999),
      'notes' => $this->faker->optional(0.6)->sentence(),
      'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
    ];
  }

  /**
   * Indicate that the operation was triggered manually.
   */
  public function manual(): static
  {
    return $this->state(fn(array $attributes) => [
      'action' => $this->faker->randomElement(['manual_on', 'manual_off']),
      'source' => 'web_interface',
      'user_id' => User::factory(),
    ]);
  }

  /**
   * Indicate that the operation was part of a cycle.
   */
  public function cycle(): static
  {
    return $this->state(fn(array $attributes) => [
      'action' => $this->faker->randomElement(['cycle_start', 'cycle_end']),
      'source' => 'esp32_device',
      'duration_minutes' => $this->faker->numberBetween(3, 10),
    ]);
  }

  /**
   * Indicate that the operation was scheduled.
   */
  public function scheduled(): static
  {
    return $this->state(fn(array $attributes) => [
      'action' => $this->faker->randomElement(['scheduled_on', 'scheduled_off']),
      'source' => 'scheduled_task',
      'duration_minutes' => $this->faker->numberBetween(5, 15),
    ]);
  }

  /**
   * Indicate that the operation was an emergency stop.
   */
  public function emergencyStop(): static
  {
    return $this->state(fn(array $attributes) => [
      'action' => 'emergency_stop',
      'source' => $this->faker->randomElement(['web_interface', 'telegram_bot']),
      'user_id' => User::factory(),
      'notes' => 'Emergency stop activated',
    ]);
  }

  /**
   * Indicate that the operation was triggered via Telegram.
   */
  public function telegram(): static
  {
    return $this->state(fn(array $attributes) => [
      'source' => 'telegram_bot',
      'telegram_user_id' => $this->faker->numberBetween(100000000, 999999999),
      'user_id' => User::factory(),
    ]);
  }
}
