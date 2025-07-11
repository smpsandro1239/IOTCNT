<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard de Administração') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Cartões de Sumário -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex items-center">
                    {{-- <x-heroicon-o-cpu-chip class="h-8 w-8 text-blue-500"/> --}}
                    <svg class="h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" /><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a.75.75 0 001.13-.65l.75-3.75a.75.75 0 00-.641-.862l-3.75-.75a.75.75 0 00-.862.64L6.35 2.87a.75.75 0 00.65 1.13l3.5 1.25zM12.75 6h1.5a.75.75 0 000-1.5h-1.5a.75.75 0 000 1.5zM10.5 8.25h5.25a.75.75 0 000-1.5H10.5a.75.75 0 000 1.5zM10.5 10.5h5.25a.75.75 0 000-1.5H10.5a.75.75 0 000 1.5zM10.5 12.75h5.25a.75.75 0 000-1.5H10.5a.75.75 0 000 1.5zM15 15.75a.75.75 0 00.75-.75v-1.5a.75.75 0 00-1.5 0v1.5a.75.75 0 00.75.75z" /></svg>

                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total de Válvulas</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalValves }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex items-center">
                    {{-- <x-heroicon-o-clock class="h-8 w-8 text-green-500"/> --}}
                    <svg class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Agendamentos Ativos</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $activeSchedules }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex items-center">
                    {{-- <x-heroicon-o-users class="h-8 w-8 text-purple-500"/> --}}
                    <svg class="h-8 w-8 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total de Utilizadores</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Estado Atual das Válvulas -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Estado Atual das Válvulas</h3>
                <div class="space-y-3">
                    @forelse ($valvesStatus as $valve)
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <div>
                                <span class="font-medium text-gray-800 dark:text-gray-200">Válvula {{ $valve->valve_number }}: {{ $valve->name }}</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Última ativação: {{ $valve->last_activated_at ? \Carbon\Carbon::parse($valve->last_activated_at)->diffForHumans() : 'N/A' }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                {{ $valve->current_state ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                {{ $valve->current_state ? 'Ligada' : 'Desligada' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">Nenhuma válvula configurada.</p>
                    @endforelse
                </div>
                 <div class="mt-4 text-right">
                    <a href="{{ route('admin.valves.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Gerir Válvulas &rarr;</a>
                </div>
            </div>

            <!-- Próximos Agendamentos -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                @php $daysOfWeek = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']; @endphp
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Próximos Agendamentos (Próximos 7 dias)</h3>
                <div class="space-y-3">
                    @forelse ($upcomingSchedulesProcessed as $occurrence)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $occurrence['name'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $daysOfWeek[$occurrence['datetime']->dayOfWeek] }}, {{ $occurrence['datetime']->format('d/m/Y H:i') }}
                                (Duração/Válvula: {{ $occurrence['original_schedule']->per_valve_duration_minutes }} min)
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">Nenhum agendamento ativo encontrado para os próximos dias.</p>
                    @endforelse
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.schedules.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Gerir Agendamentos &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Logs Recentes -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Últimos Logs de Erro/Críticos</h3>
                @if($recentErrorLogs->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum log de erro recente.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($recentErrorLogs as $log)
                        <li class="text-sm p-2 rounded {{ $log->status === 'CRITICAL' ? 'bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-300' : 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' }}">
                            <a href="{{ route('admin.operation-logs.show', $log) }}" class="hover:underline">
                                <strong class="font-medium">{{ \Carbon\Carbon::parse($log->logged_at)->format('d/m H:i') }} [{{$log->source}}]:</strong> {{ Str::limit($log->message, 60) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                @endif
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.operation-logs.index', ['status' => 'ERROR']) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Ver todos os logs de erro &rarr;</a>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Últimos Logs do Sistema</h3>
                 @if($recentSystemLogs->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum log recente.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($recentSystemLogs as $log)
                        <li class="text-sm p-2 rounded bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <a href="{{ route('admin.operation-logs.show', $log) }}" class="hover:underline">
                                <strong class="font-medium">{{ \Carbon\Carbon::parse($log->logged_at)->format('d/m H:i') }} [{{$log->source}} - {{$log->status}}]:</strong> {{ Str::limit($log->message, 60) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                @endif
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.operation-logs.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Ver todos os logs &rarr;</a>
                </div>
            </div>
        </div>
         <!-- Links Rápidos Adicionais -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Acesso Rápido</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.valves.index') }}" class="block p-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-center font-medium transition-colors">
                    Gerir Válvulas
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="block p-4 bg-green-500 hover:bg-green-600 text-white rounded-lg text-center font-medium transition-colors">
                    Gerir Agendamentos
                </a>
                <a href="{{ route('admin.users.index') }}" class="block p-4 bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-center font-medium transition-colors">
                    Gerir Utilizadores
                </a>
                <a href="{{ route('admin.telegram-users.index') }}" class="block p-4 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-center font-medium transition-colors">
                    Utilizadores Telegram
                </a>
                 <a href="{{ route('admin.operation-logs.index') }}" class="block p-4 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-center font-medium transition-colors">
                    Ver Logs
                </a>
                {{-- Adicionar mais links conforme necessário --}}
            </div>
        </div>
    </div>
</x-admin-layout>
