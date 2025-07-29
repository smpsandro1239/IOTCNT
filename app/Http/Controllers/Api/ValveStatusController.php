<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Valve;
use App\Models\OperationLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ValveStatusController extends Controller
{
  /**
   * Obter estado atual de todas as válvulas
   */
  public function index(): JsonResponse
  {
    $valves = Valve::select([
      'id',
      'name',
      'valve_number',
      'current_state',
      'last_activated_at',
      'esp32_pin',
      'description'
    ])
      ->orderBy('valve_number')
      ->get();

    return response()->json([
      'success' => true,
      'valves' => $valves,
      'timestamp' => now()->toISOString()
    ]);
  }

  /**
   * Obter estado de uma válvula específica
   */
  public function show(Valve $valve): JsonResponse
  {
    return response()->json([
      'success' => true,
      'valve' => $valve,
      'recent_logs' => $valve->operationLogs()
        ->latest()
        ->limit(5)
        ->get(),
      'timestamp' => now()->toISOString()
    ]);
  }

  /**
   * Controlar válvula manualmente
   */
  public function control(Request $request): JsonResponse
  {
    $request->validate([
      'valve_id' => 'required|exists:valves,id',
      'action' => 'required|in:on,off,toggle',
      'duration' => 'nullable|integer|min:1|max:60'
    ]);

    $valve = Valve::findOrFail($request->valve_id);
    $action = $request->action;
    $duration = $request->duration ?? 5;

    // Determinar novo estado
    $newState = match ($action) {
      'on' => true,
      'off' => false,
      'toggle' => !$valve->current_state,
    };

    // Atualizar válvula
    $valve->update([
      'current_state' => $newState,
      'last_activated_at' => $newState ? now() : $valve->last_activated_at
    ]);

    // Registar operação
    OperationLog::create([
      'valve_id' => $valve->id,
      'action' => $newState ? 'manual_on' : 'manual_off',
      'duration_minutes' => $newState ? $duration : null,
      'source' => 'web_interface',
      'user_id' => auth()->id(),
      'notes' => "Controlo manual via interface web - {$action}"
    ]);

    // Enviar comando para ESP32 (simulado)
    $this->sendCommandToESP32($valve, $newState, $duration);

    return response()->json([
      'success' => true,
      'message' => $newState ? 'Válvula ligada com sucesso' : 'Válvula desligada com sucesso',
      'valve' => $valve->fresh(),
      'command_sent' => true
    ]);
  }

  /**
   * Iniciar ciclo de irrigação
   */
  public function startCycle(Request $request): JsonResponse
  {
    $request->validate([
      'duration_per_valve' => 'nullable|integer|min:1|max:30'
    ]);

    $duration = $request->duration_per_valve ?? 5;
    $valves = Valve::orderBy('valve_number')->get();

    foreach ($valves as $valve) {
      // Atualizar estado da válvula
      $valve->update([
        'current_state' => true,
        'last_activated_at' => now()
      ]);

      // Registar operação
      OperationLog::create([
        'valve_id' => $valve->id,
        'action' => 'cycle_start',
        'duration_minutes' => $duration,
        'source' => 'web_interface',
        'user_id' => auth()->id(),
        'notes' => "Ciclo de irrigação iniciado - {$duration} minutos por válvula"
      ]);
    }

    // Enviar comando de ciclo para ESP32
    $this->sendCycleCommandToESP32($duration);

    return response()->json([
      'success' => true,
      'message' => 'Ciclo de irrigação iniciado com sucesso',
      'duration_per_valve' => $duration,
      'total_valves' => $valves->count(),
      'estimated_duration' => $valves->count() * $duration
    ]);
  }

  /**
   * Parar todas as válvulas
   */
  public function stopAll(): JsonResponse
  {
    $valves = Valve::where('current_state', true)->get();

    foreach ($valves as $valve) {
      $valve->update(['current_state' => false]);

      OperationLog::create([
        'valve_id' => $valve->id,
        'action' => 'emergency_stop',
        'source' => 'web_interface',
        'user_id' => auth()->id(),
        'notes' => 'Paragem de emergência via interface web'
      ]);
    }

    // Enviar comando de paragem para ESP32
    $this->sendStopAllCommandToESP32();

    return response()->json([
      'success' => true,
      'message' => 'Todas as válvulas foram paradas',
      'stopped_valves' => $valves->count()
    ]);
  }

  /**
   * Obter estatísticas do sistema
   */
  public function stats(): JsonResponse
  {
    $stats = [
      'total_valves' => Valve::count(),
      'active_valves' => Valve::where('current_state', true)->count(),
      'inactive_valves' => Valve::where('current_state', false)->count(),
      'total_operations_today' => OperationLog::whereDate('created_at', today())->count(),
      'last_activity' => OperationLog::latest()->first()?->created_at,
      'system_uptime' => $this->getSystemUptime(),
    ];

    return response()->json([
      'success' => true,
      'stats' => $stats,
      'timestamp' => now()->toISOString()
    ]);
  }

  /**
   * Simular envio de comando para ESP32
   */
  private function sendCommandToESP32(Valve $valve, bool $state, int $duration): void
  {
    // Em produção, isto enviaria um comando HTTP para o ESP32
    // Por agora, apenas simular o envio
    logger()->info("Comando enviado para ESP32", [
      'valve_id' => $valve->id,
      'valve_number' => $valve->valve_number,
      'pin' => $valve->esp32_pin,
      'state' => $state,
      'duration' => $duration
    ]);
  }

  private function sendCycleCommandToESP32(int $duration): void
  {
    logger()->info("Comando de ciclo enviado para ESP32", [
      'action' => 'start_cycle',
      'duration_per_valve' => $duration
    ]);
  }

  private function sendStopAllCommandToESP32(): void
  {
    logger()->info("Comando de paragem enviado para ESP32", [
      'action' => 'stop_all'
    ]);
  }

  private function getSystemUptime(): string
  {
    // Simular uptime do sistema
    return '2 dias, 14 horas, 32 minutos';
  }
}
