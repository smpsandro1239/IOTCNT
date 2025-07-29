<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
  /**
   * Display a listing of schedules.
   */
  public function index()
  {
    $schedules = Schedule::where('user_id', auth()->id())
      ->orderBy('day_of_week')
      ->orderBy('start_time')
      ->get();

    // Calcular próximo agendamento
    $nextSchedule = $this->getNextSchedule($schedules);

    // Calcular duração total diária
    $totalDailyDuration = $this->calculateTotalDailyDuration($schedules);

    return view('schedules.index', compact('schedules', 'nextSchedule', 'totalDailyDuration'));
  }

  /**
   * Store a newly created schedule.
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'description' => 'nullable|string|max:500',
      'day_of_week' => 'required|integer|between:0,6',
      'start_time' => 'required|date_format:H:i',
      'per_valve_duration_minutes' => 'required|integer|between:1,60',
      'is_active' => 'boolean'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Dados inválidos',
        'errors' => $validator->errors()
      ], 422);
    }

    // Verificar se já existe um agendamento no mesmo dia e hora
    $existingSchedule = Schedule::where('user_id', auth()->id())
      ->where('day_of_week', $request->day_of_week)
      ->where('start_time', $request->start_time)
      ->first();

    if ($existingSchedule) {
      return response()->json([
        'success' => false,
        'message' => 'Já existe um agendamento para este dia e hora'
      ], 422);
    }

    $schedule = Schedule::create([
      'user_id' => auth()->id(),
      'name' => $request->name,
      'description' => $request->description,
      'day_of_week' => $request->day_of_week,
      'start_time' => $request->start_time,
      'per_valve_duration_minutes' => $request->per_valve_duration_minutes,
      'is_active' => $request->boolean('is_active', true)
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Agendamento criado com sucesso',
      'schedule' => $schedule
    ]);
  }

  /**
   * Display the specified schedule.
   */
  public function show(Schedule $schedule)
  {
    $this->authorize('view', $schedule);

    return response()->json([
      'success' => true,
      'schedule' => $schedule
    ]);
  }

  /**
   * Show the form for editing the specified schedule.
   */
  public function edit(Schedule $schedule)
  {
    $this->authorize('update', $schedule);

    return response()->json([
      'success' => true,
      'schedule' => $schedule
    ]);
  }

  /**
   * Update the specified schedule.
   */
  public function update(Request $request, Schedule $schedule)
  {
    $this->authorize('update', $schedule);

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'description' => 'nullable|string|max:500',
      'day_of_week' => 'required|integer|between:0,6',
      'start_time' => 'required|date_format:H:i',
      'per_valve_duration_minutes' => 'required|integer|between:1,60',
      'is_active' => 'boolean'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Dados inválidos',
        'errors' => $validator->errors()
      ], 422);
    }

    // Verificar conflitos (excluindo o próprio agendamento)
    $existingSchedule = Schedule::where('user_id', auth()->id())
      ->where('day_of_week', $request->day_of_week)
      ->where('start_time', $request->start_time)
      ->where('id', '!=', $schedule->id)
      ->first();

    if ($existingSchedule) {
      return response()->json([
        'success' => false,
        'message' => 'Já existe um agendamento para este dia e hora'
      ], 422);
    }

    $schedule->update([
      'name' => $request->name,
      'description' => $request->description,
      'day_of_week' => $request->day_of_week,
      'start_time' => $request->start_time,
      'per_valve_duration_minutes' => $request->per_valve_duration_minutes,
      'is_active' => $request->boolean('is_active', true)
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Agendamento atualizado com sucesso',
      'schedule' => $schedule
    ]);
  }

  /**
   * Toggle schedule active status.
   */
  public function toggle(Schedule $schedule)
  {
    $this->authorize('update', $schedule);

    $schedule->update([
      'is_active' => !$schedule->is_active
    ]);

    return response()->json([
      'success' => true,
      'message' => $schedule->is_active ? 'Agendamento ativado' : 'Agendamento desativado',
      'schedule' => $schedule
    ]);
  }

  /**
   * Remove the specified schedule.
   */
  public function destroy(Schedule $schedule)
  {
    $this->authorize('delete', $schedule);

    $schedule->delete();

    return response()->json([
      'success' => true,
      'message' => 'Agendamento eliminado com sucesso'
    ]);
  }

  /**
   * Get next schedule execution.
   */
  private function getNextSchedule($schedules)
  {
    $now = Carbon::now();
    $nextSchedules = [];

    foreach ($schedules->where('is_active', true) as $schedule) {
      $nextExecution = $this->calculateNextExecution($schedule, $now);
      if ($nextExecution) {
        $nextSchedules[] = [
          'schedule' => $schedule,
          'next_execution' => $nextExecution,
          'time_until' => $nextExecution->diffForHumans()
        ];
      }
    }

    if (empty($nextSchedules)) {
      return null;
    }

    // Ordenar por próxima execução
    usort($nextSchedules, function ($a, $b) {
      return $a['next_execution']->timestamp - $b['next_execution']->timestamp;
    });

    return $nextSchedules[0];
  }

  /**
   * Calculate next execution time for a schedule.
   */
  private function calculateNextExecution($schedule, $from)
  {
    $dayOfWeek = $schedule->day_of_week;
    $time = $schedule->start_time;

    // Criar data/hora para hoje
    $today = $from->copy()->setTimeFromTimeString($time);

    // Se é hoje e ainda não passou a hora
    if ($from->dayOfWeek === $dayOfWeek && $from->lt($today)) {
      return $today;
    }

    // Calcular próxima ocorrência
    $daysUntilNext = ($dayOfWeek - $from->dayOfWeek + 7) % 7;
    if ($daysUntilNext === 0) {
      $daysUntilNext = 7; // Próxima semana
    }

    return $from->copy()->addDays($daysUntilNext)->setTimeFromTimeString($time);
  }

  /**
   * Calculate total daily duration.
   */
  private function calculateTotalDailyDuration($schedules)
  {
    $dailyDurations = [];

    foreach ($schedules->where('is_active', true) as $schedule) {
      $day = $schedule->day_of_week;
      if (!isset($dailyDurations[$day])) {
        $dailyDurations[$day] = 0;
      }

      // Assumindo 5 válvulas
      $totalDuration = $schedule->per_valve_duration_minutes * 5;
      $dailyDurations[$day] += $totalDuration;
    }

    return !empty($dailyDurations) ? max($dailyDurations) : 0;
  }

  /**
   * Get schedules for API.
   */
  public function apiIndex()
  {
    $schedules = Schedule::where('user_id', auth()->id())
      ->where('is_active', true)
      ->orderBy('day_of_week')
      ->orderBy('start_time')
      ->get();

    return response()->json([
      'success' => true,
      'schedules' => $schedules->map(function ($schedule) {
        return [
          'id' => $schedule->id,
          'name' => $schedule->name,
          'day_of_week' => $schedule->day_of_week,
          'start_time' => $schedule->start_time,
          'per_valve_duration_minutes' => $schedule->per_valve_duration_minutes,
          'is_active' => $schedule->is_active,
          'next_execution' => $this->calculateNextExecution($schedule, Carbon::now())?->toISOString()
        ];
      })
    ]);
  }

  /**
   * Execute a schedule manually.
   */
  public function execute(Schedule $schedule)
  {
    $this->authorize('update', $schedule);

    if (!$schedule->is_active) {
      return response()->json([
        'success' => false,
        'message' => 'Agendamento não está ativo'
      ], 422);
    }

    // Aqui você implementaria a lógica para executar o agendamento
    // Por exemplo, iniciar um ciclo de irrigação

    return response()->json([
      'success' => true,
      'message' => 'Agendamento executado manualmente'
    ]);
  }
}
