<?php

namespace App\Http\Controllers;

use App\Models\Valve;
use App\Models\Schedule;
use App\Models\OperationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Se o utilizador for admin, redirecionar para o dashboard de admin
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard'); // Assumindo que esta rota existe e está nomeada
        }

        $valvesStatus = Valve::orderBy('valve_number')->get(['valve_number', 'name', 'current_state', 'last_activated_at']);

        // Próximos agendamentos (lógica similar ao AdminDashboardController)
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
                            'per_valve_duration_minutes' => $schedule->per_valve_duration_minutes
                        ];
                        break;
                    }
                }
            }
        }
        usort($nextOccurrences, function ($a, $b) {
            return $a['datetime'] <=> $b['datetime'];
        });
        $upcomingSchedulesProcessed = array_slice($nextOccurrences, 0, 3); // Mostrar apenas os próximos 3

        // Últimos logs de operação gerais (INFO ou SUCCESS)
        $recentSystemLogs = OperationLog::whereIn('status', ['INFO', 'SUCCESS'])
            ->where(function ($query) { // Logs de sistema ou de agendamentos
                $query->where('source', 'SYSTEM')
                      ->orWhere('source', 'SCHEDULED_TASK')
                      ->orWhere('event_type', 'LIKE', '%CYCLE%'); // Logs relacionados a ciclos
            })
            ->orderBy('logged_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'valvesStatus',
            'upcomingSchedulesProcessed',
            'recentSystemLogs'
        ));
    }
}
