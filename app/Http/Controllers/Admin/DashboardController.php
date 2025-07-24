<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Valve;
use App\Models\Schedule;
use App\Models\OperationLog;
use App\Models\TelegramUser;
use App\Models\User;
use Carbon\Carbon;

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
            'enabled_schedules' => Schedule::where('is_enabled', true)->count(),
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'telegram_users' => TelegramUser::count(),
            'authorized_telegram_users' => TelegramUser::where('is_authorized', true)->count(),
        ];

        // Estado atual das válvulas
        $valvesStatus = Valve::orderBy('valve_number')->get();

        // Próximos agendamentos
        $upcomingSchedules = $this->getUpcomingSchedules();

        // Logs recentes (últimas 24h)
        $recentLogs = OperationLog::with(['valve', 'user', 'telegramUser'])
            ->where('logged_at', '>=', now()->subDay())
            ->orderBy('logged_at', 'desc')
            ->take(10)
            ->get();

        // Logs de erro recentes
        $errorLogs = OperationLog::where('status', 'ERROR')
            ->where('logged_at', '>=', now()->subDays(7))
            ->orderBy('logged_at', 'desc')
            ->take(5)
            ->get();

        // Atividade por fonte (últimos 7 dias)
        $activityBySource = OperationLog::where('logged_at', '>=', now()->subDays(7))
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
        $schedules = Schedule::where('is_enabled', true)->get();
        $upcomingSchedules = [];
        $today = Carbon::now();

        foreach ($schedules as $schedule) {
            $scheduleTime = Carbon::createFromTimeString($schedule->start_time);

            for ($i = 0; $i < 7; $i++) {
                $checkDay = $today->copy()->addDays($i);

                if ($checkDay->dayOfWeek == $schedule->day_of_week) {
                    $occurrence = $checkDay->setTime($scheduleTime->hour, $scheduleTime->minute, $scheduleTime->second);

                    if ($occurrence->isFuture()) {
                        $upcomingSchedules[] = [
                            'schedule' => $schedule,
                            'datetime' => $occurrence,
                            'days_until' => $i
                        ];
                        break;
                    }
                }
            }
        }

        // Ordenar por data
        usort($upcomingSchedules, function ($a, $b) {
            return $a['datetime'] <=> $b['datetime'];
        });

        return array_slice($upcomingSchedules, 0, 5);
    }
}
