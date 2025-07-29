<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Valve;
use App\Models\OperationLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ValveModelTest extends TestCase
{
  use RefreshDatabase;

  protected $user;

  protected function setUp(): void
  {
    parent::setUp();

    $this->user = User::factory()->create();
  }

  public function test_valve_creation()
  {
    $valve = Valve::create([
      'name' => 'Test Valve',
      'valve_number' => 1,
      'description' => 'A test valve for irrigation',
      'current_state' => false,
      'esp32_pin' => 23
    ]);

    $this->assertInstanceOf(Valve::class, $valve);
    $this->assertEquals('Test Valve', $valve->name);
    $this->assertEquals(1, $valve->valve_number);
    $this->assertEquals('A test valve for irrigation', $valve->description);
    $this->assertFalse($valve->current_state);
    $this->assertEquals(23, $valve->esp32_pin);
    $this->assertNull($valve->last_activated_at);
  }

  public function test_valve_state_toggle()
  {
    $valve = Valve::create([
      'name' => 'Toggle Test Valve',
      'valve_number' => 2,
      'current_state' => false,
      'esp32_pin' => 24
    ]);

    // Test turning on
    $valve->update(['current_state' => true]);
    $this->assertTrue($valve->fresh()->current_state);

    // Test turning off
    $valve->update(['current_state' => false]);
    $this->assertFalse($valve->fresh()->current_state);
  }

  public function test_valve_last_activated_timestamp()
  {
    $valve = Valve::create([
      'name' => 'Timestamp Test Valve',
      'valve_number' => 3,
      'current_state' => false,
      'esp32_pin' => 25
    ]);

    $this->assertNull($valve->last_activated_at);

    $activationTime = Carbon::now();
    $valve->update([
      'current_state' => true,
      'last_activated_at' => $activationTime
    ]);

    $freshValve = $valve->fresh();
    $this->assertNotNull($freshValve->last_activated_at);
    $this->assertEquals($activationTime->format('Y-m-d H:i:s'), $freshValve->last_activated_at->format('Y-m-d H:i:s'));
  }

  public function test_valve_operation_logs_relationship()
  {
    $valve = Valve::create([
      'name' => 'Relationship Test Valve',
      'valve_number' => 4,
      'current_state' => false,
      'esp32_pin' => 26
    ]);

    // Create operation logs for this valve
    OperationLog::create([
      'valve_id' => $valve->id,
      'user_id' => $this->user->id,
      'action' => 'manual_on',
      'source' => 'web_interface',
      'duration_minutes' => 5,
      'notes' => 'Test operation 1'
    ]);

    OperationLog::create([
      'valve_id' => $valve->id,
      'user_id' => $this->user->id,
      'action' => 'manual_off',
      'source' => 'web_interface',
      'duration_minutes' => 0,
      'notes' => 'Test operation 2'
    ]);

    $this->assertCount(2, $valve->operationLogs);
    $this->assertEquals('manual_on', $valve->operationLogs->first()->action);
  }

  public function test_valve_usage_statistics()
  {
    $valve = Valve::create([
      'name' => 'Stats Test Valve',
      'valve_number' => 1,
      'current_state' => false,
      'esp32_pin' => 23
    ]);

    // Create operation logs for different days
    $today = Carbon::today();
    $yesterday = Carbon::yesterday();

    // Today's operations
    OperationLog::create([
      'valve_id' => $valve->id,
      'user_id' => $this->user->id,
      'action' => 'manual_on',
      'source' => 'web_interface',
      'duration_minutes' => 5,
      'created_at' => $today->copy()->addHours(8)
    ]);

    OperationLog::create([
      'valve_id' => $valve->id,
      'user_id' => $this->user->id,
      'action' => 'scheduled_on',
      'source' => 'esp32',
      'duration_minutes' => 10,
      'created_at' => $today->copy()->addHours(18)
    ]);

    // Yesterday's operations
    OperationLog::create([
      'valve_id' => $valve->id,
      'user_id' => $this->user->id,
      'action' => 'manual_on',
      'source' => 'web_interface',
      'duration_minutes' => 7,
      'created_at' => $yesterday->copy()->addHours(10)
    ]);

    // Test today's usage
    $todayUsage = $valve->operationLogs()
      ->whereDate('created_at', $today)
      ->where('action', 'LIKE', '%_on')
      ->count();
    $this->assertEquals(2, $todayUsage);

    // Test total duration today
    $todayDuration = $valve->operationLogs()
      ->whereDate('created_at', $today)
      ->sum('duration_minutes');
    $this->assertEquals(15, $todayDuration);

    // Test weekly usage
    $weeklyUsage = $valve->operationLogs()
      ->where('created_at', '>=', $today->copy()->subDays(7))
      ->where('action', 'LIKE', '%_on')
      ->count();
    $this->assertEquals(3, $weeklyUsage);
  }
}
