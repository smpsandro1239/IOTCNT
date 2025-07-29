<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Valve;
use App\Models\Schedule;
use App\Models\OperationLog;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

class ApiTest extends TestCase
{
  use RefreshDatabase;

  protected $user;
  protected $adminUser;
  protected $regularUser;

  protected function setUp(): void
  {
    parent::setUp();

    // Create test users
    $this->adminUser = User::factory()->create([
      'is_admin' => true,
      'name' => 'Admin User',
      'email' => 'admin@test.com'
    ]);

    $this->regularUser = User::factory()->create([
      'is_admin' => false,
      'name' => 'Regular User',
      'email' => 'user@test.com'
    ]);

    $this->user = $this->adminUser; // Default to admin for most tests

    // Create test valves
    for ($i = 1; $i <= 5; $i++) {
      Valve::create([
        'name' => "Válvula $i - Teste",
        'valve_number' => $i,
        'description' => "Válvula de teste número $i para irrigação",
        'current_state' => false,
        'esp32_pin' => 20 + $i,
        'last_activated_at' => null
      ]);
    }

    // Create test schedules
    Schedule::create([
      'user_id' => $this->regularUser->id,
      'name' => 'Rega Matinal Teste',
      'description' => 'Agendamento de teste para manhã',
      'day_of_week' => 1, // Segunda-feira
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    // Create system settings
    SystemSetting::create([
      'key' => 'system_name',
      'value' => 'IOTCNT Test System',
      'type' => 'string'
    ]);

    SystemSetting::create([
      'key' => 'default_valve_duration',
      'value' => '5',
      'type' => 'integer'
    ]);
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

  public function test_valve_stats_endpoint()
  {
    Sanctum::actingAs($this->user);

    // Create some operation logs
    OperationLog::create([
      'valve_id' => 1,
      'user_id' => $this->user->id,
      'action' => 'manual_on',
      'source' => 'web_interface',
      'duration_minutes' => 5,
      'notes' => 'Test operation'
    ]);

    $response = $this->get('/api/valve/stats');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'stats' => [
          'total_valves',
          'active_valves',
          'inactive_valves',
          'total_operations_today',
          'last_activity'
        ]
      ]);
  }

  public function test_valve_control_with_duration()
  {
    Sanctum::actingAs($this->user);

    $response = $this->post('/api/valve/control', [
      'valve_id' => 1,
      'action' => 'on',
      'duration' => 10
    ]);

    $response->assertStatus(200)
      ->assertJson([
        'success' => true,
        'command_sent' => true
      ]);

    // Verify operation log was created
    $this->assertDatabaseHas('operation_logs', [
      'valve_id' => 1,
      'action' => 'manual_on',
      'duration_minutes' => 10
    ]);
  }

  public function test_valve_control_toggle_action()
  {
    Sanctum::actingAs($this->user);

    // First, turn valve on
    $valve = Valve::find(1);
    $valve->update(['current_state' => true]);

    $response = $this->post('/api/valve/control', [
      'valve_id' => 1,
      'action' => 'toggle'
    ]);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    // Verify valve was turned off
    $valve->refresh();
    $this->assertFalse($valve->current_state);
  }

  public function test_start_cycle_with_custom_duration()
  {
    Sanctum::actingAs($this->user);

    $response = $this->post('/api/valve/start-cycle', [
      'duration_per_valve' => 3
    ]);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'message',
        'duration_per_valve',
        'total_valves',
        'estimated_duration'
      ]);

    $data = $response->json();
    $this->assertEquals(3, $data['duration_per_valve']);
    $this->assertEquals(5, $data['total_valves']);
    $this->assertEquals(15, $data['estimated_duration']);
  }

  public function test_unauthorized_access_returns_401()
  {
    $endpoints = [
      ['GET', '/api/valve/status'],
      ['POST', '/api/valve/control'],
      ['GET', '/api/esp32/config'],
      ['POST', '/api/esp32/valve-status'],
      ['GET', '/api/valve/stats']
    ];

    foreach ($endpoints as [$method, $endpoint]) {
      $response = $this->json($method, $endpoint);
      $response->assertStatus(401);
    }
  }

  public function test_regular_user_cannot_access_admin_endpoints()
  {
    Sanctum::actingAs($this->regularUser);

    $adminEndpoints = [
      ['GET', '/api/admin/metrics'],
      ['GET', '/api/admin/valve-usage'],
      ['POST', '/api/admin/test-system']
    ];

    foreach ($adminEndpoints as [$method, $endpoint]) {
      $response = $this->json($method, $endpoint);
      $response->assertStatus(403);
    }
  }

  public function test_valve_control_validation_errors()
  {
    Sanctum::actingAs($this->user);

    // Test missing valve_id
    $response = $this->post('/api/valve/control', [
      'action' => 'on'
    ]);
    $response->assertStatus(422);

    // Test invalid action
    $response = $this->post('/api/valve/control', [
      'valve_id' => 1,
      'action' => 'invalid_action'
    ]);
    $response->assertStatus(422);

    // Test invalid valve_id
    $response = $this->post('/api/valve/control', [
      'valve_id' => 999,
      'action' => 'on'
    ]);
    $response->assertStatus(422);

    // Test invalid duration
    $response = $this->post('/api/valve/control', [
      'valve_id' => 1,
      'action' => 'on',
      'duration' => -1
    ]);
    $response->assertStatus(422);
  }

  public function test_esp32_valve_status_creates_operation_log()
  {
    Sanctum::actingAs($this->user);

    $response = $this->post('/api/esp32/valve-status', [
      'valve_number' => 2,
      'state' => true,
      'timestamp_device' => time()
    ]);

    $response->assertStatus(200);

    // Verify operation log was created
    $this->assertDatabaseHas('operation_logs', [
      'valve_id' => 2,
      'action' => 'esp32_status_update',
      'source' => 'esp32'
    ]);
  }

  public function test_esp32_log_endpoint_stores_log()
  {
    Sanctum::actingAs($this->user);

    $logData = [
      'valve_number' => 3,
      'action' => 'scheduled_on',
      'duration_minutes' => 7,
      'timestamp_device' => time(),
      'notes' => 'Scheduled irrigation started'
    ];

    $response = $this->post('/api/esp32/log', $logData);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    // Verify log was stored
    $this->assertDatabaseHas('operation_logs', [
      'valve_id' => 3,
      'action' => 'scheduled_on',
      'duration_minutes' => 7,
      'source' => 'esp32',
      'notes' => 'Scheduled irrigation started'
    ]);
  }

  public function test_system_health_endpoint()
  {
    Sanctum::actingAs($this->user);

    $response = $this->get('/api/system/health');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'health' => [
          'database',
          'esp32_connection',
          'active_valves',
          'system_load'
        ]
      ]);
  }

  public function test_logs_endpoint_with_filters()
  {
    Sanctum::actingAs($this->user);

    // Create test logs
    OperationLog::create([
      'valve_id' => 1,
      'user_id' => $this->user->id,
      'action' => 'manual_on',
      'source' => 'web_interface',
      'duration_minutes' => 5,
      'created_at' => now()
    ]);

    OperationLog::create([
      'valve_id' => 2,
      'user_id' => $this->user->id,
      'action' => 'manual_off',
      'source' => 'telegram',
      'duration_minutes' => 0,
      'created_at' => now()->subHour()
    ]);

    // Test without filters
    $response = $this->get('/api/logs');
    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'logs',
        'pagination'
      ]);

    // Test with valve filter
    $response = $this->get('/api/logs?valve_id=1');
    $response->assertStatus(200);
    $logs = $response->json('logs');
    $this->assertCount(1, $logs);
    $this->assertEquals(1, $logs[0]['valve_id']);

    // Test with action filter
    $response = $this->get('/api/logs?action=manual_on');
    $response->assertStatus(200);
    $logs = $response->json('logs');
    $this->assertCount(1, $logs);
    $this->assertEquals('manual_on', $logs[0]['action']);

    // Test with date filter
    $response = $this->get('/api/logs?date_from=' . now()->toDateString());
    $response->assertStatus(200);
    $logs = $response->json('logs');
    $this->assertCount(1, $logs);

    // Test with limit
    $response = $this->get('/api/logs?limit=1');
    $response->assertStatus(200);
    $logs = $response->json('logs');
    $this->assertCount(1, $logs);
  }

  public function test_rate_limiting()
  {
    Sanctum::actingAs($this->user);

    // Make multiple requests quickly to test rate limiting
    for ($i = 0; $i < 65; $i++) {
      $response = $this->get('/api/valve/status');

      if ($i < 60) {
        $response->assertStatus(200);
      } else {
        // Should be rate limited after 60 requests
        $response->assertStatus(429);
        break;
      }
    }
  }

  public function test_concurrent_valve_operations()
  {
    Sanctum::actingAs($this->user);

    // Try to turn on multiple valves simultaneously
    $valve1Response = $this->post('/api/valve/control', [
      'valve_id' => 1,
      'action' => 'on',
      'duration' => 5
    ]);

    $valve2Response = $this->post('/api/valve/control', [
      'valve_id' => 2,
      'action' => 'on',
      'duration' => 5
    ]);

    // Both should succeed (system allows multiple valves)
    $valve1Response->assertStatus(200);
    $valve2Response->assertStatus(200);

    // Verify both valves are marked as active
    $this->assertTrue(Valve::find(1)->current_state);
    $this->assertTrue(Valve::find(2)->current_state);
  }

  public function test_esp32_config_includes_schedules()
  {
    Sanctum::actingAs($this->user);

    $response = $this->get('/a32/config');

    $response->assertStatus(200);
    $data = $response->json();

    $this->assertArrayHasKey('schedules', $data['data']);
    $this->assertCount(1, $data['data']['schedules']);
    $this->assertEquals('Rega Matinal Teste', $data['data']['schedules'][0]['name']);
  }

  public function test_valve_last_activated_timestamp_updates()
  {
    Sanctum::actingAs($this->user);

    $valve = Valve::find(1);
    $this->assertNull($valve->last_activated_at);

    $response = $this->post('/api/valve/control', [
      'valve_id' => 1,
      'action' => 'on',
      'duration' => 5
    ]);

    $response->assertStatus(200);

    $valve->refresh();
    $this->assertNotNull($valve->last_activated_at);
    $this->assertTrue($valve->last_activated_at->isToday());
  }

  public function test_system_statistics_accuracy()
  {
    Sanctum::actingAs($this->user);

    // Turn on 2 valves
    Valve::whereIn('id', [1, 2])->update(['current_state' => true]);

    // Create some operation logs for today
    for ($i = 0; $i < 3; $i++) {
      OperationLog::create([
        'valve_id' => 1,
        'user_id' => $this->user->id,
        'action' => 'test_operation',
        'source' => 'test',
        'created_at' => now()
      ]);
    }

    $response = $this->get('/api/valve/stats');
    $response->assertStatus(200);

    $stats = $response->json('stats');
    $this->assertEquals(5, $stats['total_valves']);
    $this->assertEquals(2, $stats['active_valves']);
    $this->assertEquals(3, $stats['inactive_valves']);
    $this->assertEquals(3, $stats['total_operations_today']);
  }
}
