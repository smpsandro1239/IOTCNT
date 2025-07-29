<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Valve;
use App\Models\Schedule;
use App\Models\OperationLog;
use App\Models\TelegramUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Estatísticas gerais
        $stats = [
            'total_valves' => Valve::count(),
            'active_valves' => Valve::where('current_state', true)->count(),
            'total_schedules' => Schedule::count(),
            'enabled_schedules' => Schedule::where(function ($query) {
                $query->where('is_enabled', true)->orWhere('is_active', true);
            })->count(),
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'telegram_users' => TelegramUser::count(),
            'authorized_telegram_users' => TelegramUser::where('is_authorized', true)->count(),
        ];

        // Estado atual das válvulas
        $valvesStatus = Valve::orderBy('valve_number')->get();

        // Próximos agendamentos
        $upcomingSchedules = $this->getUpcomingSchedules();

        // Logs recentes (últimas 24h)
        $recentLogs = OperationLog::with(['valve', 'user'])
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        // Logs de erro recentes
        $errorLogs = OperationLog::where('action', 'LIKE', '%error%')
            ->orWhere('notes', 'LIKE', '%error%')
            ->orWhere('notes', 'LIKE', '%falha%')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Atividade por fonte (últimos 7 dias)
        $activityBySource = OperationLog::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'valvesStatus',
            'upcomingSchedules',
            'recentLogs',
            'errorLogs',
            'activityBySource'
        ));
    }

    /**
     * Get upcoming schedules for the next 7 days.
     */
    private function getUpcomingSchedules()
    {
        $schedules = Schedule::with('user')
            ->where(function ($query) {
                $query->where('is_enabled', true)->orWhere('is_active', true);
            })
            ->get();

        $upcomingSchedules = [];
        $today = Carbon::now();

        foreach ($schedules as $schedule) {
            $nextExecution = $schedule->getNextExecution($today);

            if ($nextExecution && $nextExecution->diffInDays($today) <= 7) {
                $upcomingSchedules[] = [
                    'schedule' => $schedule,
                    'datetime' => $nextExecution,
                    'days_until' => $nextExecution->diffInDays($today)
                ];
            }
        }

        // Ordenar por data
        usort($upcomingSchedules, function ($a, $b) {
            return $a['datetime'] <=> $b['datetime'];
        });

        return array_slice($upcomingSchedules, 0, 8);
    }

    /**
     * Get system metrics for API.
     */
    public function getMetrics()
    {
        $today = Carbon::today();

        $metrics = [
            'operations_today' => OperationLog::whereDate('created_at', $today)->count(),
            'system_uptime' => $this->calculateSystemUptime(),
            'error_count' => OperationLog::where('created_at', '>=', now()->subDays(7))
                ->where(function ($query) {
                    $query->where('action', 'LIKE', '%error%')
                        ->orWhere('notes', 'LIKE', '%error%')
                        ->orWhere('notes', 'LIKE', '%falha%');
                })
                ->count(),
            'active_valves' => Valve::where('current_state', true)->count(),
            'total_valves' => Valve::count(),
            'last_activity' => OperationLog::latest()->first()?->created_at
        ];

        return response()->json([
            'success' => true,
            'metrics' => $metrics
        ]);
    }

    /**
     * Get valve usage statistics.
     */
    public function getValveUsage()
    {
        $today = Carbon::today();

        $usage = OperationLog::with('valve')
            ->whereDate('created_at', $today)
            ->where('action', 'LIKE', '%on%')
            ->selectRaw('valve_id, COUNT(*) as count')
            ->groupBy('valve_id')
            ->get();

        return response()->json([
            'success' => true,
            'usage' => $usage
        ]);
    }

    /**
     * Get ESP32 status.
     */
    public function getEsp32Status()
    {
        $lastEsp32Log = OperationLog::where('source', 'esp32')
            ->latest()
            ->first();

        $status = [
            'connection' => 'ok',
            'last_ping' => $lastEsp32Log?->created_at,
            'last_action' => $lastEsp32Log?->action
        ];

        // Verificar se ESP32 está offline (sem comunicação há mais de 5 minutos)
        if (!$lastEsp32Log || $lastEsp32Log->created_at->lt(Carbon::now()->subMinutes(5))) {
            $status['connection'] = 'offline';
        }

        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }

    /**
     * Test system functionality.
     */
    public function testSystem()
    {
        try {
            // Verificar conexão com base de dados
            \DB::connection()->getPdo();

            // Verificar se há válvulas configuradas
            $valveCount = Valve::count();
            if ($valveCount === 0) {
                throw new \Exception('Nenhuma válvula configurada');
            }

            // Verificar última comunicação ESP32
            $lastEsp32Log = OperationLog::where('source', 'esp32')
                ->where('created_at', '>=', Carbon::now()->subHour())
                ->first();

            $testResults = [
                'database' => 'ok',
                'valves_configured' => $valveCount,
                'esp32_communication' => $lastEsp32Log ? 'ok' : 'warning',
                'last_esp32_ping' => $lastEsp32Log?->created_at,
                'system_health' => 'good'
            ];

            // Registar teste no log
            OperationLog::create([
                'valve_id' => null,
                'user_id' => auth()->id(),
                'action' => 'system_test',
                'source' => 'admin_panel',
                'notes' => 'Teste do sistema executado pelo administrador',
                'duration_minutes' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teste do sistema concluído',
                'results' => $testResults
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no teste do sistema: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send restart command to ESP32.
     */
    public function restartEsp32()
    {
        try {
            // Registar comando de reinício
            OperationLog::create([
                'valve_id' => null,
                'user_id' => auth()->id(),
                'action' => 'esp32_restart_command',
                'source' => 'admin_panel',
                'notes' => 'Comando de reinício enviado para ESP32',
                'duration_minutes' => 0
            ]);

            // Aqui você implementaria a lógica para enviar o comando para o ESP32
            // Por exemplo, através de uma fila ou API específica

            return response()->json([
                'success' => true,
                'message' => 'Comando de reinício enviado para ESP32'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar comando de reinício: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export system data.
     */
    public function exportData(Request $request)
    {
        $format = $request->get('format', 'json');
        $days = $request->get('days', 30);

        try {
            $data = [
                'export_date' => Carbon::now()->toISOString(),
                'period_days' => $days,
                'valves' => Valve::all(),
                'schedules' => Schedule::with('user')->get(),
                'operation_logs' => OperationLog::with(['valve', 'user'])
                    ->where('created_at', '>=', Carbon::now()->subDays($days))
                    ->orderBy('created_at', 'desc')
                    ->get(),
                'users' => User::select('id', 'name', 'email', 'is_admin', 'created_at')->get(),
                'telegram_users' => TelegramUser::all(),
                'system_stats' => [
                    'total_operations' => OperationLog::count(),
                    'total_valves' => Valve::count(),
                    'total_schedules' => Schedule::count(),
                    'total_users' => User::count()
                ]
            ];

            if ($format === 'csv') {
                // Implementar exportação CSV
                return $this->exportToCsv($data);
            }

            $filename = 'iotcnt_export_' . Carbon::now()->format('Y-m-d_H-i-s') . '.json';

            return response()->json($data)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na exportação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data for API.
     */
    public function getDashboardData()
    {
        $stats = [
            'total_valves' => Valve::count(),
            'active_valves' => Valve::where('current_state', true)->count(),
            'operations_today' => OperationLog::whereDate('created_at', Carbon::today())->count(),
            'system_uptime' => $this->calculateSystemUptime(),
            'error_count' => OperationLog::where('created_at', '>=', now()->subDays(7))
                ->where(function ($query) {
                    $query->where('action', 'LIKE', '%error%')
                        ->orWhere('notes', 'LIKE', '%error%');
                })
                ->count()
        ];

        $valves = Valve::orderBy('valve_number')->get();
        $recentLogs = OperationLog::with(['valve', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'valves' => $valves,
                'recent_logs' => $recentLogs,
                'timestamp' => Carbon::now()->toISOString()
            ]
        ]);
    }

    /**
     * Calculate system uptime.
     */
    private function calculateSystemUptime()
    {
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
     * Export data to CSV format.
     */
    private function exportToCsv($data)
    {
        $filename = 'iotcnt_export_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Cabeçalhos CSV para logs de operação
            fputcsv($file, ['Data', 'Válvula', 'Ação', 'Utilizador', 'Fonte', 'Duração', 'Notas']);

            foreach ($data['operation_logs'] as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->valve->name ?? 'N/A',
                    $log->action,
                    $log->user->name ?? 'Sistema',
                    $log->source,
                    $log->duration_minutes,
                    $log->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
