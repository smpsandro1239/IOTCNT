<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Valve;
use App\Models\OperationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ValveControlTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();

    // Create test valves
    for ($i = 1; $i <= 5; $i++) {
      Valve::create([
        'name' => "Válvula $i",
        'valve_number' => $i,
        'description' => "Válvula de teste $i",
        'current_state' => false,
        'esp32_pin' => $i + 20,
      ]);
    }
  }

  public function test_authenticated_user_can_get_valve_status(): void
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/valve/status');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'valves' => [
          '*' => [
            'id',
            'name',
            'valve_number',
            'current_state',
            'esp32_pin'
          ]
        ],
        'timestamp'
      ]);
  }

  public function test_authenticated_user_can_control_valve(): void
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $valve = Valve::first();

    $response = $this->postJson('/api/valve/control', [
      'valve_id' => $valve->id,
      'action' => 'on',
      'duration' => 5
    ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'message' => 'Válvula ligada com sucesso'
      ]);

    $this->assertDatabaseHas('valves', [
      'id' => $valve->id,
      'current_state' => true
    ]);

    $this->assertDatabaseHas('operation_logs', [
      'valve_id' => $valve->id,
      'action' => 'manual_on',
      'user_id' => $user->id
    ]);
  }

  public function test_authenticated_user_can_start_irrigation_cycle(): void
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/valve/start-cycle', [
      'duration_per_valve' => 3
    ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'duration_per_valve' => 3,
        'total_valves' => 5
      ]);

    // Verificar se todas as válvulas foram ativadas
    $this->assertEquals(5, Valve::where('current_state', true)->count());

    // Verificar se foram criados logs para todas as válvulas
    $this->assertEquals(5, OperationLog::where('action', 'cycle_start')->count());
  }

  public function test_authenticated_user_can_stop_all_valves(): void
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Ativar algumas válvulas primeiro
    Valve::query()->update(['current_state' => true]);

    $response = $this->postJson('/api/valve/stop-all');

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'stopped_valves' => 5
      ]);

    // Verificar se todas as válvulas foram desativadas
    $this->assertEquals(0, Valve::where('current_state', true)->count());

    // Verificar se foram criados logs de paragem
    $this->assertEquals(5, OperationLog::where('action', 'emergency_stop')->count());
  }

  public function test_unauthenticated_user_cannot_access_valve_api(): void
  {
    $response = $this->getJson('/api/valve/status');

    $response->assertStatus(401);
  }

  public function test_valve_control_validates_input(): void
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/valve/control', [
      'valve_id' => 999, // ID inválido
      'action' => 'invalid_action',
      'duration' => 100 // Duração inválida
    ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['valve_id', 'action', 'duration']);
  }

  public function test_system_stats_are_accurate(): void
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Ativar 2 válvulas
    Valve::limit(2)->update(['current_state' => true]);

    // Criar alguns logs de hoje
    OperationLog::factory()->count(3)->create([
      'created_at' => now(),
      'user_id' => $user->id
    ]);

    $response = $this->getJson('/api/valve/stats');

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'stats' => [
          'total_valves' => 5,
          'active_valves' => 2,
          'inactive_valves' => 3,
          'total_operations_today' => 3
        ]
      ]);
  }
}
