<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create default schedules
    $schedules = [
      [
        'name' => 'Rega Matinal - Segunda, Quarta, Sexta',
        'day_of_week' => 1, // Monday
        'start_time' => '07:00:00',
        'per_valve_duration_minutes' => 5,
        'is_enabled' => true,
      ],
      [
        'name' => 'Rega Matinal - Quarta-feira',
        'day_of_week' => 3, // Wednesday
        'start_time' => '07:00:00',
        'per_valve_duration_minutes' => 5,
        'is_enabled' => true,
      ],
      [
        'name' => 'Rega Matinal - Sexta-feira',
        'day_of_week' => 5, // Friday
        'start_time' => '07:00:00',
        'per_valve_duration_minutes' => 5,
        'is_enabled' => true,
      ],
      [
        'name' => 'Rega de Fim de Semana',
        'day_of_week' => 0, // Sunday
        'start_time' => '08:00:00',
        'per_valve_duration_minutes' => 8,
        'is_enabled' => true,
      ],
      [
        'name' => 'Rega de Verão - Diária',
        'day_of_week' => 2, // Tuesday (example for summer schedule)
        'start_time' => '06:30:00',
        'per_valve_duration_minutes' => 7,
        'is_enabled' => false, // Disabled by default
      ],
    ];

    foreach ($schedules as $scheduleData) {
      Schedule::firstOrCreate(
        [
          'name' => $scheduleData['name'],
          'day_of_week' => $scheduleData['day_of_week'],
          'start_time' => $scheduleData['start_time']
        ],
        $scheduleData
      );
    }
  }
}
