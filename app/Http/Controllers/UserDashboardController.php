<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Valve;
use App\Models\Schedule;
use App\Models\OperationLog;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        // Obter válvulas com estado atual
        $valves = Valve::orderBy('valve_number')->get();

        // Obter agendamentos ativos
        $activeSchedules = Schedule::where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Obter logs recentes com informações da válvula
        $recentLogs = OperationLog::with(['valve', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        // Calcular estatísticas rápidas
        $stats = $this->calculateDashboardStats($valves, $recentLogs);

        // Próximos agendamentos (próximos 7 dias)
        $upcomingSchedules = $this->getUpcomingSchedules($activeSchedules);

        // Estado do sistema
        $systemHealth = $this->getSystemHealth();

        return view('dashboard', compact(
            'valves',
            'activeSchedules',
            'recentLogs',
            'stats',
            'upcomingSchedules',
            'systemHealth'
        ));
    }

    /**
     * Calcular estatísticas para o dashboard
     */
    private function calculateDashboardStats($valves, $recentLogs)
    {
        $today = Carbon::today();

        return [
            'total_valves' => $valves->count(),
            'active_valves' => $valves->where('current_state', true)->count(),
            'inactive_valves' => $valves->where('current_state', false)->count(),
            'operations_today' => $recentLogs->where('created_at', '>=', $today)->count(),
            'last_activity' => $recentLogs->first()?->created_at,
            'system_uptime' => $this->calculateSystemUptime()
        ];
    }

    /**
     * Obter próximos agendamentos
     */
    private function getUpcomingSchedules($schedules)
    {
        $upcoming = [];
        $now = Carbon::now();

        foreach ($schedules as $schedule) {
            // Calcular próxima ocorrência
            $nextOccurrence = $this->getNextScheduleOccurrence($schedule, $now);

            if ($nextOccurrence && $nextOccurrence->diffInDays($now) <= 7) {
                $upcoming[] = [
                    'schedule' => $schedule,
                    'next_occurrence' => $nextOccurrence,
                    'time_until' => $nextOccurrence->diffForHumans()
                ];
            }
        }

        // Ordenar por próxima ocorrência
        usort($upcoming, function ($a, $b) {
            return $a['next_occurrence']->timestamp - $b['next_occurrence']->timestamp;
        });

        return array_slice($upcoming, 0, 5); // Mostrar apenas os próximos 5
    }

    /**
     * Calcular próxima ocorrência de um agendamento
     */
    private function getNextScheduleOccurrence($schedule, $from)
    {
        $dayOfWeek = $schedule->day_of_week; // 0 = domingo, 1 = segunda, etc.
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
     * Calcular tempo de atividade do sistema
     */
    private function calculateSystemUptime()
    {
        // Buscar primeiro log do sistema
        $firstLog = OperationLog::orderBy('created_at', 'asc')->first();

        if (!$firstLog) {
            return 'Indisponível';
        }

        $uptime = Carbon::now()->diff($firstLog->created_at);

        if ($uptime->days > 0) {
            return $uptime->days . ' dias, ' . $uptime->h . ' horas';
        } elseif ($uptime->h > 0) {
            return $uptime->h . ' horas, ' . $uptime->i . ' minutos';
        } else {
            return $uptime->i . ' minutos';
        }
    }

    /**
     * Verificar saúde do sistema
     */
    private function getSystemHealth()
    {
        $health = [
            'status' => 'online',
            'database' => 'ok',
            'last_esp32_ping' => null,
            'active_valves' => 0,
            'warnings' => []
        ];

        try {
            // Verificar conexão com base de dados
            \DB::connection()->getPdo();

            // Verificar última comunicação ESP32
            $lastEsp32Log = OperationLog::where('source', 'esp32')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastEsp32Log) {
                $health['last_esp32_ping'] = $lastEsp32Log->created_at;

                // Verificar se ESP32 está offline (sem comunicação há mais de 5 minutos)
                if ($lastEsp32Log->created_at->lt(Carbon::now()->subMinutes(5))) {
                    $health['warnings'][] = 'ESP32 sem comunicação há mais de 5 minutos';
                }
            } else {
                $health['warnings'][] = 'Nenhuma comunicação ESP32 registada';
            }

            // Contar válvulas ativas
            $health['active_valves'] = Valve::where('current_state', true)->count();
        } catch (\Exception $e) {
            $health['status'] = 'error';
            $health['database'] = 'error';
            $health['warnings'][] = 'Erro na base de dados: ' . $e->getMessage();
        }

        return $health;
    }

    /**
     * API endpoint para obter dados do dashboard via AJAX
     */
    public function apiData()
    {
        $valves = Valve::orderBy('valve_number')->get();
        $recentLogs = OperationLog::with(['valve', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $stats = $this->calculateDashboardStats($valves, $recentLogs);
        $systemHealth = $this->getSystemHealth();

        return response()->json([
            'success' => true,
            'data' => [
                'valves' => $valves,
                'stats' => $stats,
                'recent_logs' => $recentLogs,
                'system_health' => $systemHealth,
                'timestamp' => Carbon::now()->toISOString()
            ]
        ]);
    }
}
