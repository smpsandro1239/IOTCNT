<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Valve;
use App\Models\OperationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValveTest extends TestCase
{
  use RefreshDatabase;

  public function test_valve_creation()
  {
    $valve = Valve::create([
      'name' => 'Test Valve',
      'valve_number' => 1,
      'description' => 'Test valve description',
      'current_state' => false,
      'esp32_pin' => 23
    ]);

    $this->assertInstanceOf(Valve::class, $valve);
    $this->assertEquals('Test Valve', $valve->name);
    $this->assertEquals(1, $valve->valve_number);
    $this->assertFalse($valve->current_state);
  }

  public function test_valve_state_casting()
  {
    $valve = Valve::create([
      'name' => 'Test Valve',
      'valve_number' => 1,
      'current_state' => 1, // Integer
      'esp32_pin' => 23
    ]);

    // Should be cast to boolean
    $this->assertTrue($valve->current_state);
    $this->assertIsBool($valve->current_state);
  }

  public function test_valve_has_operation_logs_relationship()
  {
    $valve = Valve::create([
      'name' => 'Test Valve',
      'valve_number' => 1,
      'esp32_pin' => 23
    ]);

    $log = OperationLog::create([
      'valve_id' => $valve->id,
      'event_type' => 'TEST_EVENT',
      'message' => 'Test message',
      'source' => 'TEST',
      'status' => 'INFO'
    ]);

    $this->assertTrue($valve->operationLogs->contains($log));
  }

  public function test_valve_number_uniqueness()
  {
    Valve::create([
      'name' => 'Valve 1',
      'valve_number' => 1,
      'esp32_pin' => 23
    ]);

    $this->expectException(\Illuminate\Database\QueryException::class);

    Valve::create([
      'name' => 'Valve 2',
      'valve_number' => 1, // Duplicate valve number
      'esp32_pin' => 22
    ]);
  }

  public function test_valve_last_activated_at_casting()
  {
    $valve = Valve::create([
      'name' => 'Test Valve',
      'valve_number' => 1,
      'last_activated_at' => '2024-01-01 12:00:00',
      'esp32_pin' => 23
    ]);

    $this->assertInstanceOf(\Carbon\Carbon::class, $valve->last_activated_at);
  }

  public function test_valve_fillable_attributes()
  {
    $valve = new Valve();

    $expectedFillable = [
      'name',
      'valve_number',
      'description',
      'current_state',
      'last_activated_at',
      'esp32_pin',
    ];

    $this->assertEquals($expectedFillable, $valve->getFillable());
  }

  public function test_valve_casts()
  {
    $valve = new Valve();

    $expectedCasts = [
      'current_state' => 'boolean',
      'last_activated_at' => 'datetime',
      'valve_number' => 'integer',
      'esp32_pin' => 'integer',
    ];

    foreach ($expectedCasts as $attribute => $cast) {
      $this->assertEquals($cast, $valve->getCasts()[$attribute]);
    }
  }
}
