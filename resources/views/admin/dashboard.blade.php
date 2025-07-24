@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Dashboard de Administração') }}
    </h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Estatísticas Gerais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Válvulas</h3>
                    <p class="text-2xl font-bold">{{ $stats['total_valves'] }}</p>
                    <p class="text-sm opacity-75">{{ $stats['active_valves'] }} ativas</p>
                </div>
                <div class="text-3xl opacity-75">💧</div>
            </div>
        </div>

        <div class="bg-green-500 text-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Agendamentos</h3>
                    <p class="text-2xl font-bold">{{ $stats['total_schedules'] }}</p>
                    <p class="text-sm opacity-75">{{ $stats['enabled_schedules'] }} ativos</p>
                </div>
                <div class="text-3xl opacity-75">⏰</div>
            </div>
        </div>

        <div class="bg-purple-500 text-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Utilizadores</h3>
                    <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
                    <p class="text-sm opacity-75">{{ $stats['admin_users'] }} admins</p>
                </div>
                <div class="text-3xl opacity-75">👥</div>
            </div>
        </div>

        <div class="bg-orange-500 text-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Telegram</h3>
                    <p class="text-2xl font-bold">{{ $stats['telegram_users'] }}</p>
                    <p class="text-sm opacity-75">{{ $stats['authorized_telegram_users'] }} autorizados</p>
                </div>
                <div class="text-3xl opacity-75">📱</div>
            </div>
        </div>
    </div>

    <!-- Estado das Válvulas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            Estado Atual das Válvulas
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($valvesStatus as $valve)
                <div class="border rounded-lg p-4 {{ $valve->current_state ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }}">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-semibold">Válvula {{ $valve->valve_number }}</h4>
                        <span class="px-2 py-1 text-xs rounded-full {{ $valve->current_state ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                            {{ $valve->current_state ? 'ON' : 'OFF' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $valve->name }}</p>
                    <p class="text-xs text-gray-500">
                        Pin ESP32: {{ $valve->esp32_pin ?? 'N/A' }}
                    </p>
                    @if($valve->last_activated_at)
                        <p class="text-xs text-gray-500">
                            Última ativação: {{ $valve->last_activated_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Próximos Agendamentos -->
    @if(count($upcomingSchedules) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Próximos Agendamentos
            </h3>
            <div class="space-y-3">
                @foreach ($upcomingSchedules as $upcoming)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium">{{ $upcoming['schedule']->name }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ $upcoming['datetime']->format('d/m/Y H:i') }}
                                ({{ $upcoming['schedule']->per_valve_duration_minutes }}min/válvula)
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-gray-500">
                                @if($upcoming['days_until'] == 0)
                                    Hoje
                                @elseif($upcoming['days_until'] == 1)
                                    Amanhã
                                @else
                                    Em {{ $upcoming['days_until'] }} dias
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Logs Recentes e Atividade -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Logs Recentes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Atividade Recente (24h)
            </h3>
            @if($recentLogs->count() > 0)
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach ($recentLogs as $log)
                        <div class="text-sm p-2 rounded {{ $log->status === 'ERROR' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                            <div class="flex justify-between items-start">
                                <span class="font-medium">{{ $log->event_type }}</span>
                                <span class="text-xs">{{ $log->logged_at->format('H:i') }}</span>
                            </div>
                            <p class="text-xs mt-1">{{ Str::limit($log->message, 80) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.logs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        Ver todos os logs →
                    </a>
                </div>
            @else
                <p class="text-gray-500">Nenhuma atividade recente.</p>
            @endif
        </div>

        <!-- Erros Recentes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Erros Recentes (7 dias)
            </h3>
            @if($errorLogs->count() > 0)
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach ($errorLogs as $error)
                        <div class="text-sm p-2 rounded bg-red-100 text-red-800">
                            <div class="flex justify-between items-start">
                                <span class="font-medium">{{ $error->event_type }}</span>
                                <span class="text-xs">{{ $error->logged_at->format('d/m H:i') }}</span>
                            </div>
                            <p class="text-xs mt-1">{{ Str::limit($error->message, 80) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-green-600 text-center py-4">
                    <div class="text-2xl mb-2">✅</div>
                    <p>Nenhum erro nos últimos 7 dias!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Atividade por Fonte -->
    @if($activityBySource->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Atividade por Fonte (7 dias)
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($activityBySource as $activity)
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $activity->count }}</div>
                        <div class="text-sm text-gray-600">{{ $activity->source }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
