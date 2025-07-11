<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Valve;
use App\Models\Schedule;
use App\Models\User;
use App\Models\OperationLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalValves = Valve::count();
        $activeSchedules = Schedule::where('is_enabled', true)->count();
        $totalUsers = User::count();

        $valvesStatus = Valve::orderBy('valve_number')->get(['valve_number', 'name', 'current_state', 'last_activated_at']);

        // Próximos agendamentos (exemplo simples para os próximos 7 dias)
        // Esta lógica pode ser mais complexa para calcular a próxima execução real de cada agendamento
        $upcomingSchedules = Schedule::where('is_enabled', true)
            // ->where('day_of_week', '>=', Carbon::now()->dayOfWeek) // Simplificação, precisa de lógica mais robusta
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Lógica mais precisa para próximos agendamentos
        $nextOccurrences = [];
        $schedules = Schedule::where('is_enabled', true)->get();
        $today = Carbon::now();

        foreach ($schedules as $schedule) {
            $scheduleTime = Carbon::createFromTimeString($schedule->start_time);
            for ($i = 0; $i < 7; $i++) { // Verificar nos próximos 7 dias
                $checkDay = $today->copy()->addDays($i);
                if ($checkDay->dayOfWeek == $schedule->day_of_week) {
                    $occurrence = $checkDay->setTime($scheduleTime->hour, $scheduleTime->minute, $scheduleTime->second);
                    if ($occurrence->isFuture()) {
                        $nextOccurrences[] = [
                            'name' => $schedule->name,
                            'datetime' => $occurrence,
                            'original_schedule' => $schedule // Para links ou mais detalhes
                        ];
                        break; // Pegar apenas a próxima ocorrência deste agendamento
                    }
                }
            }
        }
        // Ordenar as ocorrências e pegar as primeiras 5
        usort($nextOccurrences, function ($a, $b) {
            return $a['datetime'] <=> $b['datetime'];
        });
        $upcomingSchedulesProcessed = array_slice($nextOccurrences, 0, 5);


        $recentErrorLogs = OperationLog::whereIn('status', ['ERROR', 'CRITICAL'])
            ->orderBy('logged_at', 'desc')
            ->take(5)
            ->get();

        $recentSystemLogs = OperationLog::orderBy('logged_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalValves',
            'activeSchedules',
            'totalUsers',
            'valvesStatus',
            'upcomingSchedulesProcessed', // Usar esta variável processada
            'recentErrorLogs',
            'recentSystemLogs'
        ));
    }
}
