<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Valve;

class ValveSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create 5 default valves
    $valves = [
      [
        'name' => 'Jardim Frontal',
        'valve_number' => 1,
        'description' => 'Irrigação do jardim da frente da casa',
        'esp32_pin' => 23,
        'current_state' => false,
      ],
      [
        'name' => 'Jardim Traseiro',
        'valve_number' => 2,
        'description' => 'Irrigação do jardim das traseiras',
        'esp32_pin' => 22,
        'current_state' => false,
      ],
      [
        'name' => 'Horta',
        'valve_number' => 3,
        'description' => 'Irrigação da horta de vegetais',
        'esp32_pin' => 21,
        'current_state' => false,
      ],
      [
        'name' => 'Estufa',
        'valve_number' => 4,
        'description' => 'Irrigação da estufa',
        'esp32_pin' => 19,
        'current_state' => false,
      ],
      [
        'name' => 'Vasos Terraço',
        'valve_number' => 5,
        'description' => 'Irrigação dos vasos do terraço',
        'esp32_pin' => 18,
        'current_state' => false,
      ],
    ];

    foreach ($valves as $valveData) {
      Valve::firstOrCreate(
        ['valve_number' => $valveData['valve_number']],
        $valveData
      );
    }
  }
}
