<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ScheduleModelTest extends TestCase
{
  use RefreshDatabase;

  protected $user;

  protected function setUp(): void
  {
    parent::setUp();

    $this->user = User::factory()->create();
  }

  public function test_schedule_creation()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Test Schedule',
      'description' => 'A test irrigation schedule',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $this->assertInstanceOf(Schedule::class, $schedule);
    $this->assertEquals('Test Schedule', $schedule->name);
    $this->assertEquals('A test irrigation schedule', $schedule->description);
    $this->assertEquals(1, $schedule->day_of_week);
    $this->assertEquals('07:00:00', $schedule->start_time);
    $this->assertEquals(5, $schedule->per_valve_duration_minutes);
    $this->assertTrue($schedule->is_active);
    $this->assertEquals($this->user->id, $schedule->user_id);
  }

  public function test_schedule_user_relationship()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Relationship Test',
      'day_of_week' => 2,
      'start_time' => '08:00:00',
      'per_valve_duration_minutes' => 3,
      'is_active' => true
    ]);

    $this->assertInstanceOf(User::class, $schedule->user);
    $this->assertEquals($this->user->id, $schedule->user->id);
    $this->assertEquals($this->user->name, $schedule->user->name);
  }

  public function test_schedule_next_execution_calculation()
  {
    $now = Carbon::now();
    $tomorrow = $now->copy()->addDay();

    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Next Execution Test',
      'day_of_week' => $tomorrow->dayOfWeek,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $nextExecution = $schedule->getNextExecution($now);

    $this->assertNotNull($nextExecution);
    $this->assertTrue($nextExecution->isFuture());
    $this->assertEquals(7, $nextExecution->hour);
    $this->assertEquals(0, $nextExecution->minute);
    $this->assertEquals($tomorrow->dayOfWeek, $nextExecution->dayOfWeek);
  }

  public function test_schedule_next_execution_today_future_time()
  {
    $now = Carbon::now()->setTime(6, 0, 0); // 6:00 AM

    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Today Future Test',
      'day_of_week' => $now->dayOfWeek,
      'start_time' => '07:00:00', // 7:00 AM (1 hour later)
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $nextExecution = $schedule->getNextExecution($now);

    $this->assertNotNull($nextExecution);
    $this->assertTrue($nextExecution->isToday());
    $this->assertEquals(7, $nextExecution->hour);
  }

  public function test_schedule_next_execution_today_past_time()
  {
    $now = Carbon::now()->setTime(8, 0, 0); // 8:00 AM

    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Today Past Test',
      'day_of_week' => $now->dayOfWeek,
      'start_time' => '07:00:00', // 7:00 AM (1 hour ago)
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $nextExecution = $schedule->getNextExecution($now);

    $this->assertNotNull($nextExecution);
    $this->assertFalse($nextExecution->isToday());
    $this->assertEquals(7, $nextExecution->hour);
    $this->assertEquals($now->dayOfWeek, $nextExecution->dayOfWeek);
    $this->assertTrue($nextExecution->addWeek()->isSameDay($nextExecution));
  }

  public function test_inactive_schedule_no_next_execution()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Inactive Test',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => false
    ]);

    $nextExecution = $schedule->getNextExecution();

    $this->assertNull($nextExecution);
  }

  public function test_schedule_day_name_attribute()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Day Name Test',
      'day_of_week' => 1, // Monday
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $this->assertEquals('Segunda-feira', $schedule->day_name);

    $schedule->update(['day_of_week' => 0]); // Sunday
    $this->assertEquals('Domingo', $schedule->fresh()->day_name);

    $schedule->update(['day_of_week' => 6]); // Saturday
    $this->assertEquals('Sábado', $schedule->fresh()->day_name);
  }

  public function test_schedule_formatted_time_attribute()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Time Format Test',
      'day_of_week' => 1,
      'start_time' => '07:30:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $this->assertEquals('07:30', $schedule->formatted_time);

    $schedule->update(['start_time' => '14:45:00']);
    $this->assertEquals('14:45', $schedule->fresh()->formatted_time);
  }

  public function test_schedule_total_duration_attribute()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Duration Test',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 8,
      'is_active' => true
    ]);

    // Assuming 5 valves total
    $expectedDuration = 8 * 5; // 40 minutes
    $this->assertEquals($expectedDuration, $schedule->total_duration);
  }

  public function test_schedule_active_scope()
  {
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

    $activeSchedules = Schedule::active()->get();

    $this->assertCount(1, $activeSchedules);
    $this->assertTrue($activeSchedules->contains($activeSchedule));
    $this->assertFalse($activeSchedules->contains($inactiveSchedule));
  }

  public function test_schedule_for_day_scope()
  {
    $mondaySchedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Monday Schedule',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $tuesdaySchedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Tuesday Schedule',
      'day_of_week' => 2,
      'start_time' => '08:00:00',
      'per_valve_duration_minutes' => 3,
      'is_active' => true
    ]);

    $mondaySchedules = Schedule::forDay(1)->get();

    $this->assertCount(1, $mondaySchedules);
    $this->assertTrue($mondaySchedules->contains($mondaySchedule));
    $this->assertFalse($mondaySchedules->contains($tuesdaySchedule));
  }

  public function test_schedule_for_user_scope()
  {
    $otherUser = User::factory()->create();

    $userSchedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'User Schedule',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $otherUserSchedule = Schedule::create([
      'user_id' => $otherUser->id,
      'name' => 'Other User Schedule',
      'day_of_week' => 2,
      'start_time' => '08:00:00',
      'per_valve_duration_minutes' => 3,
      'is_active' => true
    ]);

    $userSchedules = Schedule::forUser($this->user->id)->get();

    $this->assertCount(1, $userSchedules);
    $this->assertTrue($userSchedules->contains($userSchedule));
    $this->assertFalse($userSchedules->contains($otherUserSchedule));
  }

  public function test_schedule_conflicts_with_method()
  {
    $schedule1 = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Schedule 1',
      'day_of_week' => 1,
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $schedule2 = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Schedule 2',
      'day_of_week' => 1,
      'start_time' => '07:00:00', // Same day and time
      'per_valve_duration_minutes' => 3,
      'is_active' => true
    ]);

    $schedule3 = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Schedule 3',
      'day_of_week' => 1,
      'start_time' => '08:00:00', // Different time
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    $this->assertTrue($schedule1->conflictsWith($schedule2));
    $this->assertFalse($schedule1->conflictsWith($schedule3));
  }

  public function test_schedule_should_run_now_static_method()
  {
    $now = Carbon::now();

    // Create schedule for current time
    $currentSchedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Current Schedule',
      'day_of_week' => $now->dayOfWeek,
      'start_time' => $now->format('H:i:s'),
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    // Create schedule for different time
    $futureSchedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Future Schedule',
      'day_of_week' => $now->dayOfWeek,
      'start_time' => $now->addHour()->format('H:i:s'),
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);

    // Create inactive schedule for current time
    $inactiveSchedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Inactive Schedule',
      'day_of_week' => $now->dayOfWeek,
      'start_time' => $now->format('H:i:s'),
      'per_valve_duration_minutes' => 5,
      'is_active' => false
    ]);

    $schedulesToRun = Schedule::shouldRunNow(60); // 60 seconds tolerance

    $this->assertCount(1, $schedulesToRun);
    $this->assertTrue($schedulesToRun->contains($currentSchedule));
    $this->assertFalse($schedulesToRun->contains($futureSchedule));
    $this->assertFalse($schedulesToRun->contains($inactiveSchedule));
  }

  public function test_schedule_is_currently_active_attribute()
  {
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

    $this->assertTrue($activeSchedule->is_currently_active);
    $this->assertFalse($inactiveSchedule->is_currently_active);
  }

  public function test_schedule_factory()
  {
    $schedule = Schedule::factory()->create(['user_id' => $this->user->id]);

    $this->assertInstanceOf(Schedule::class, $schedule);
    $this->assertNotNull($schedule->name);
    $this->assertNotNull($schedule->day_of_week);
    $this->assertNotNull($schedule->start_time);
    $this->assertNotNull($schedule->per_valve_duration_minutes);
    $this->assertIsBool($schedule->is_active);
    $this->assertEquals($this->user->id, $schedule->user_id);
  }

  public function test_schedule_validation_constraints()
  {
    // Test day_of_week constraint (should be 0-6)
    $this->expectException(\Exception::class);

    Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Invalid Day',
      'day_of_week' => 8, // Invalid day
      'start_time' => '07:00:00',
      'per_valve_duration_minutes' => 5,
      'is_active' => true
    ]);
  }

  public function test_schedule_time_calculations()
  {
    $schedule = Schedule::create([
      'user_id' => $this->user->id,
      'name' => 'Time Calculation Test',
      'day_of_week' => 1,
      'start_time' => '07:30:00',
      'per_valve_duration_minutes' => 6,
      'is_active' => true
    ]);

    // Test if schedule calculates next execution correctly across weeks
    $monday = Carbon::now()->startOfWeek(); // This Monday
    $nextMonday = $monday->copy()->addWeek(); // Next Monday

    $nextExecution = $schedule->getNextExecution($monday->copy()->addHours(8)); // After 7:30 AM

    $this->assertEquals($nextMonday->dayOfWeek, $nextExecution->dayOfWeek);
    $this->assertEquals(7, $nextExecution->hour);
    $this->assertEquals(30, $nextExecution->minute);
  }
}
