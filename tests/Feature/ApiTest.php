<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Valve;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ApiTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();

    // Create test user
    $this->user = User::factory()->create([
      'role' => 'admin'
    ]);

    // Create test valves
    for ($i = 1; $i <= 5; $i++) {
      Valve::create([
        'name' => "Válvula $i",
        'valve_number' => $i,
        'description' => "Válvula de teste $i",
        'current_state' => false,
        'esp32_pin' => 20 + $i
      ]);
    }
  }

  public function test_ping_endpoint()
  {
    $response = $this->get('/api/ping');

    $response->assertStatus(200)
      ->assertJson(['message' => 'pong']);
  }

  public function test_esp32_config_requires_authentication()
  {
    $response = $this->get('/api/esp32/config');

    $response->assertStatus(401);
  }

  public function test_esp32_config_returns_valves()
  {
    Sanctum::actingAs($this->user);

    $response = $this->get('/api/esp32/config');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'data' => [
          'valves',
          'schedules',
          'server_time',
          'device_name',
          'timezone'
        ]
      ]);
  }

  public function test_valve_status_update()
  {
    Sanctum::actingAs($this->user);

    $response = $this->post('/api/esp32/valve-status', [
      'valve_number' => 1,
      'state' => true,
      'timestamp_device' => time()
    ]);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    // Verify valve state was updated
    $valve = Valve::where('valve_number', 1)->first();
    $this->assertTrue($valve->current_state);
  }

  public function test_valve_status_validation()
  {
    Sanctum::actingAs($this->user);

    // Test invalid valve number
    $response = $this->post('/api/esp32/valve-status', [
      'valve_number' => 10,
      'state' => true
    ]);

    $response->assertStatus(422);

    // Test missing required fields
    $response = $this->post('/api/esp32/valve-status', []);

    $response->assertStatus(422);
  }

  public function test_log_endpoint()
  {
    Sanctum::actingAs($this->user);

    $response = $this->post('/api/esp32/log', [
      'level' => 'INFO',
      'message' => 'Test log message',
      'details' => ['test' => 'data'],
      'source' => 'TEST'
    ]);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);
  }

  public function test_valve_control()
  {
    Sanctum::actingAs($this->user);

    $response = $this->post('/api/esp32/control-valve', [
      'valve_number' => 2,
      'state' => true,
      'duration_minutes' => 10
    ]);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    // Verify valve state
    $valve = Valve::where('valve_number', 2)->first();
    $this->assertTrue($valve->current_state);
  }

  public function test_start_cycle()
  {
    Sanctum::actingAs($this->user);

    $response = $this->post('/api/esp32/start-cycle', [
      'duration_per_valve' => 5
    ]);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);
  }

  public function test_stop_all_valves()
  {
    Sanctum::actingAs($this->user);

    // First, turn on some valves
    Valve::where('valve_number', '<=', 3)->update(['current_state' => true]);

    $response = $this->post('/api/esp32/stop-all');

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    // Verify all valves are off
    $activeValves = Valve::where('current_state', true)->count();
    $this->assertEquals(0, $activeValves);
  }

  public function test_commands_endpoint()
  {
    Sanctum::actingAs($this->user);

    $response = $this->get('/api/esp32/commands');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'commands',
        'server_time'
      ]);
  }
}
