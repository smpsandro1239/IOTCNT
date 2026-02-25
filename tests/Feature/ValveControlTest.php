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
    // We don't create valves here to avoid conflicts in tests that create their own
  }

  private function createValves($count = 5)
  {
    for ($i = 1; $i <= $count; $i++) {
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
    $this->createValves();
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
      ]);
  }

  public function test_authenticated_user_can_control_valve(): void
  {
    $this->createValves();
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
        'valve_id' => $valve->id,
        'new_state' => true
      ]);

    $this->assertTrue($valve->fresh()->current_state);
  }

  public function test_authenticated_user_can_start_irrigation_cycle(): void
  {
    $this->createValves();
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/valve/start-cycle', [
      'duration_per_valve' => 5
    ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true
      ]);
  }

  public function test_authenticated_user_can_stop_all_valves(): void
  {
    $this->createValves();
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Ligar algumas válvulas primeiro
    Valve::query()->update(['current_state' => true]);

    $response = $this->postJson('/api/valve/stop-all');

    $response->assertStatus(200)
      ->assertJson([
        'success' => true
      ]);

    $this->assertEquals(0, Valve::where('current_state', true)->count());
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

    $response = $this->postJson('/api/valve/control', []);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['valve_id', 'action', 'duration']);
  }

  public function test_system_stats_are_accurate(): void
  {
    $this->createValves();
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Ativar 2 válvulas
    Valve::whereIn('valve_number', [1, 2])->update(['current_state' => true]);

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
