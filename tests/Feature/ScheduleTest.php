<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

class ScheduleTest extends TestCase
{
  use RefreshDatabase;

  protected $user;
  protected $adminUser;

  protected function setUp(): void
  {
    parent::setUp();

    $this->user = User::factory()->create([
      'role' => 'user',
      'name' => 'Test User',
      'email' => 'user@test.com'
    ]);

    $this->adminUser = User::factory()->create([
      'role' => 'admin',
      'name' => 'Admin User',
      'email' => 'admin@test.com'
    ]);
  }

  public function test_user_can_view_their_schedules()
  {
    Sanctum::actingAs($this->user);

    // Create schedules for this user
    Schedule::factory()->create([
      'user_id' => $this->user->id,
      'name' => 'Meu Agendamento',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'is_active' => true
    ]);

    // Create schedule for another user
    $otherUser = User::factory()->create();
    Schedule::factory()->create([
      'user_id' => $otherUser->id,
      'name' => 'Agendamento de Outro',
      'day_of_week' => 2,
      'start_time' => '08:00:00',
      'is_active' => true
    ]);

    $response = $this->get('/schedules');

    $response->assertStatus(200);
    $response->assertSee('Meu Agendamento');
    $response->assertDontSee('Agendamento de Outro');
  }

  public function test_user_can_create_schedule()
  {
    Sanctum::actingAs($this->user);

    $scheduleData = [
      'name' => 'Nova Rega',
      'description' => 'Rega para o jardim',
      'day_of_week' => 3,
      'start_time' => '06:30',
      'per_valve_duration_minutes' => 7,
      'is_active' => true
    ];

    $response = $this->post('/schedules', $scheduleData);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    $this->assertDatabaseHas('schedules', [
      'user_id' => $this->user->id,
      'name' => 'Nova Rega',
      'day_of_week' => 3,
      'start_time' => '06:30:00',
      'per_valve_duration_minutes' => 7,
      'is_active' => true
    ]);
  }

  public function test_schedule_creation_validation()
  {
    Sanctum::actingAs($this->user);

    // Test missing required fields
    $response = $this->post('/schedules', []);
    $response->assertStatus(422);

    // Test invalid day_of_week
    $response = $this->post('/schedules', [
      'name' => 'Test',
      'day_of_week' => 8, // Invalid (0-6 only)
      'start_time' => '07:00',
      'per_valve_duration_minutes' => 5
    ]);
    $response->assertStatus(422);

    // Test invalid time format
    $response = $this->post('/schedules', [
      'name' => 'Test',
      'day_of_week' => 1,
      'start_time' => '25:00', // Invalid hour
      'per_valve_duration_minutes' => 5
    ]);
    $response->assertStatus(422);

    // Test invalid duration
    $response = $this->post('/schedules', [
      'name' => 'Test',
      'day_of_week' => 1,
      'start_time' => '07:00',
      'per_valve_duration_minutes' => 0 // Must be at least 1
    ]);
    $response->assertStatus(422);
  }

  public function test_user_cannot_create_conflicting_schedules()
  {
    Sanctum::actingAs($this->user);

    // Create first schedule
    Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Primeiro Agendamento',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    // Try to create conflicting schedule
    $response = $this->post('/schedules', [
      'name' => 'Segundo Agendamento',
      'day_of_week' => 1,
      'start_time' => '07:00',
      'per_valve_duration_minutes' => 3,
      'is_active' => true
    ]);

    $response->assertStatus(422)
      ->assertJson([
        'success' => false,
        'message' => 'Já existe um agendamento para este dia e hora'
      ]);
  }

  public function test_user_can_update_their_schedule()
  {
    Sanctum::actingAs($this->user);

    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Agendamento Original',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $updateData = [
      'name' => 'Agendamento Atualizado',
      'description' => 'Nova descrição',
      'day_of_week' => 2,
      'start_time' => '08:00',
      'per_valve_duration_minutes' => 8,
      'is_active' => false
    ];

    $response = $this->put("/schedules/{$schedule->id}", $updateData);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    $schedule->refresh();
    $this->assertEquals('Agendamento Atualizado', $schedule->name);
    $this->assertEquals(2, $schedule->day_of_week);
    $this->assertEquals('08:00:00', $schedule->start_time);
    $this->assertEquals(8, $schedule->per_valve_duration_minutes);
    $this->assertFalse($schedule->is_active);
  }

  public function test_user_cannot_update_others_schedule()
  {
    Sanctum::actingAs($this->user);

    $otherUser = User::factory()->create();
    $schedule = Schedule::create([
      'user_id' => $otherUser->id,
      'name' => 'Agendamento de Outro',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $response = $this->put("/schedules/{$schedule->id}", [
      'name' => 'Tentativa de Hack',
      'day_of_week' => 2,
      'start_time' => '08:00',
      'per_valve_duration_minutes' => 3
    ]);

    $response->assertStatus(403);
  }

  public function test_user_can_toggle_schedule_status()
  {
    Sanctum::actingAs($this->user);

    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Agendamento Teste',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $response = $this->patch("/schedules/{$schedule->id}/toggle");

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'message' => 'Agendamento desativado'
      ]);

    $schedule->refresh();
    $this->assertFalse($schedule->is_active);

    // Toggle again
    $response = $this->patch("/schedules/{$schedule->id}/toggle");

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'message' => 'Agendamento ativado'
      ]);

    $schedule->refresh();
    $this->assertTrue($schedule->is_active);
  }

  public function test_user_can_delete_their_schedule()
  {
    Sanctum::actingAs($this->user);

    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Agendamento para Deletar',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $response = $this->delete("/schedules/{$schedule->id}");

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    $this->assertDatabaseMissing('schedules', [
      'id' => $schedule->id
    ]);
  }

  public function test_schedule_next_execution_calculation()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Teste Execução',
      'day_of_week' => Carbon::now()->addDay()->dayOfWeek,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $nextExecution = $schedule->getNextExecution();

    $this->assertNotNull($nextExecution);
    $this->assertTrue($nextExecution->isFuture());
    $this->assertEquals(7, $nextExecution->hour);
    $this->assertEquals(0, $nextExecution->minute);
  }

  public function test_inactive_schedule_has_no_next_execution()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Agendamento Inativo',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => false
    ]);

    $nextExecution = $schedule->getNextExecution();

    $this->assertNull($nextExecution);
  }

  public function test_schedule_api_endpoint()
  {
    Sanctum::actingAs($this->user);

    Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'API Test Schedule',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $response = $this->get('/api/schedules');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'schedules' => [
          '*' => [
            'id',
            'name',
            'day_of_week',
            'start_time',
            'per_valve_duration_minutes',
            'is_active',
            'next_execution'
          ]
        ]
      ]);
  }

  public function test_admin_can_view_all_schedules()
  {
    Sanctum::actingAs($this->adminUser);

    // Create schedules for different users
    Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'User Schedule',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    Schedule::create([
      'user_id' => $this->adminUser->id,
      'name' => 'Admin Schedule',
      'day_of_week' => 2,
      'start_time' => '08:00:00',
      'per_valve_duration_minutes' => 3,
      'is_active' => true
    ]);

    $response = $this->get('/admin/schedules');

    $response->assertStatus(200);
    $response->assertSee('User Schedule');
    $response->assertSee('Admin Schedule');
  }

  public function test_schedule_execution_manual_trigger()
  {
    Sanctum::actingAs($this->user);

    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Manual Execution Test',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $response = $this->post("/schedules/{$schedule->id}/execute");

    $response->assertStatus(200)
      ->assertJson(['success' => true]);
  }

  public function test_cannot_execute_inactive_schedule()
  {
    Sanctum::actingAs($this->user);

    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Inactive Schedule',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => false
    ]);

    $response = $this->post("/schedules/{$schedule->id}/execute");

    $response->assertStatus(422)
      ->assertJson([
        'success' => false,
        'message' => 'Agendamento não está ativo'
      ]);
  }

  public function test_schedule_model_scopes()
  {
    // Create test schedules
    $activeSchedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Active Schedule',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $inactiveSchedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Inactive Schedule',
      'day_of_week' => 2,
      'start_time' => '08:00:00',
      'per_valve_duration_minutes' => 3,
      'is_active' => false
    ]);

    // Test active scope
    $activeSchedules = Schedule::active()->get();
    $this->assertCount(1, $activeSchedules);
    $this->assertEquals($activeSchedule->id, $activeSchedules->first()->id);

    // Test forDay scope
    $mondaySchedules = Schedule::forDay(1)->get();
    $this->assertCount(1, $mondaySchedules);
    $this->assertEquals($activeSchedule->id, $mondaySchedules->first()->id);

    // Test forUser scope
    $userSchedules = Schedule::forUser($this->user->id)->get();
    $this->assertCount(2, $userSchedules);
  }

  public function test_schedule_should_run_now()
  {
    $now = Carbon::now();

    // Create schedule for current time
    Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Current Time Schedule',
      'day_of_week' => $now->dayOfWeek,
      'start_time' => $now->format('H:i:s'),
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    // Create schedule for different time
    Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Different Time Schedule',
      'day_of_week' => $now->dayOfWeek,
      'start_time' => $now->addHour()->format('H:i:s'),
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $schedulesToRun = Schedule::shouldRunNow(60); // 60 seconds tolerance

    $this->assertCount(1, $schedulesToRun);
    $this->assertEquals('Current Time Schedule', $schedulesToRun->first()->name);
  }
}
