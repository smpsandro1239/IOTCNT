<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Valve;
use App\Models\Schedule;
use App\Models\OperationLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class Esp32Controller extends Controller
{
  /**
   * Get ESP32 configuration
   */
  public function getConfig(Request $request): JsonResponse
  {
    $valves = Valve::select('id', 'valve_number', 'name', 'esp32_pin', 'current_state')
      ->orderBy('valve_number')
      ->get();

    $schedules = Schedule::where('is_enabled', true)
      ->select('id', 'name', 'day_of_week', 'start_time', 'per_valve_duration_minutes')
      ->get();

    return response()->json([
      'success' => true,
      'data' => [
        'valves' => $valves,
        'schedules' => $schedules,
        'server_time' => now()->toISOString(),
        'device_name' => 'ESP32 Irrigation Controller',
        'timezone' => config('app.timezone', 'UTC')
      ]
    ]);
  }

  /**
   * Update valve status from ESP32
   */
  public function updateValveStatus(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'valve_number' => 'required|integer|min:1|max:5',
      'state' => 'required|boolean',
      'timestamp_device' => 'nullable|integer'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $validator->errors()
      ], 422);
    }

    $valve = Valve::where('valve_number', $request->valve_number)->first();

    if (!$valve) {
      return response()->json([
        'success' => false,
        'message' => 'Valve not found'
      ], 404);
    }

    // Update valve state
    $valve->update([
      'current_state' => $request->state,
      'last_activated_at' => $request->state ? now() : $valve->last_activated_at
    ]);

    // Log the operation
    OperationLog::create([
      'valve_id' => $valve->id,
      'event_type' => $request->state ? 'VALVE_ACTIVATED' : 'VALVE_DEACTIVATED',
      'message' => "Valve {$valve->name} " . ($request->state ? 'activated' : 'deactivated') . " by ESP32",
      'source' => 'ESP32',
      'status' => 'SUCCESS',
      'details' => [
        'valve_number' => $request->valve_number,
        'new_state' => $request->state,
        'device_timestamp' => $request->timestamp_device
      ]
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Valve status updated successfully',
      'data' => [
        'valve_id' => $valve->id,
        'valve_number' => $valve->valve_number,
        'current_state' => $valve->current_state
      ]
    ]);
  }

  /**
   * Receive log from ESP32
   */
  public function receiveLog(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'level' => 'required|string|in:INFO,ERROR,WARNING,DEBUG',
      'message' => 'required|string|max:1000',
      'details' => 'nullable|array',
      'source' => 'nullable|string|max:50'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $validator->errors()
      ], 422);
    }

    // Map ESP32 log levels to our status enum
    $statusMap = [
      'INFO' => 'INFO',
      'ERROR' => 'ERROR',
      'WARNING' => 'WARNING',
      'DEBUG' => 'INFO'
    ];

    OperationLog::create([
      'event_type' => 'ESP32_LOG',
      'message' => $request->message,
      'source' => 'ESP32',
      'status' => $statusMap[$request->level],
      'details' => array_merge($request->details ?? [], [
        'original_level' => $request->level,
        'esp32_source' => $request->source ?? 'ESP32'
      ])
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Log received successfully'
    ]);
  }

  /**
   * Get pending commands for ESP32
   */
  public function getCommands(Request $request): JsonResponse
  {
    // Esta funcionalidade pode ser implementada para comandos pendentes
    // Por agora, retorna array vazio
    return response()->json([
      'success' => true,
      'commands' => [],
      'server_time' => now()->toISOString()
    ]);
  }

  /**
   * Manual valve control from web interface
   */
  public function controlValve(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'valve_number' => 'required|integer|min:1|max:5',
      'state' => 'required|boolean',
      'duration_minutes' => 'nullable|integer|min:1|max:60'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $validator->errors()
      ], 422);
    }

    $valve = Valve::where('valve_number', $request->valve_number)->first();

    if (!$valve) {
      return response()->json([
        'success' => false,
        'message' => 'Valve not found'
      ], 404);
    }

    // Update valve state
    $valve->update([
      'current_state' => $request->state,
      'last_activated_at' => $request->state ? now() : $valve->last_activated_at
    ]);

    // Log the manual operation
    OperationLog::create([
      'valve_id' => $valve->id,
      'user_id' => auth()->id(),
      'event_type' => $request->state ? 'MANUAL_VALVE_ON' : 'MANUAL_VALVE_OFF',
      'message' => "Valve {$valve->name} manually " . ($request->state ? 'activated' : 'deactivated') . " by " . auth()->user()->name,
      'source' => 'WEB_PORTAL',
      'status' => 'SUCCESS',
      'details' => [
        'valve_number' => $request->valve_number,
        'new_state' => $request->state,
        'duration_minutes' => $request->duration_minutes,
        'user_id' => auth()->id()
      ]
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Valve controlled successfully',
      'data' => [
        'valve_id' => $valve->id,
        'valve_number' => $valve->valve_number,
        'current_state' => $valve->current_state,
        'duration_minutes' => $request->duration_minutes
      ]
    ]);
  }

  /**
   * Start irrigation cycle manually
   */
  public function startCycle(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'duration_per_valve' => 'nullable|integer|min:1|max:30'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $validator->errors()
      ], 422);
    }

    $duration = $request->duration_per_valve ?? 5; // Default 5 minutes

    // Log the manual cycle start
    OperationLog::create([
      'user_id' => auth()->id(),
      'event_type' => 'MANUAL_CYCLE_START',
      'message' => "Manual irrigation cycle started by " . auth()->user()->name,
      'source' => 'WEB_PORTAL',
      'status' => 'INFO',
      'details' => [
        'duration_per_valve' => $duration,
        'user_id' => auth()->id(),
        'total_valves' => Valve::count()
      ]
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Irrigation cycle started',
      'data' => [
        'duration_per_valve' => $duration,
        'total_valves' => Valve::count()
      ]
    ]);
  }

  /**
   * Stop all valves immediately
   */
  public function stopAll(Request $request): JsonResponse
  {
    // Update all valves to off state
    Valve::query()->update(['current_state' => false]);

    // Log the emergency stop
    OperationLog::create([
      'user_id' => auth()->id(),
      'event_type' => 'EMERGENCY_STOP',
      'message' => "All valves stopped by " . auth()->user()->name,
      'source' => 'WEB_PORTAL',
      'status' => 'WARNING',
      'details' => [
        'user_id' => auth()->id(),
        'stopped_valves' => Valve::count()
      ]
    ]);

    return response()->json([
      'success' => true,
      'message' => 'All valves stopped successfully'
    ]);
  }
}
