<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Painel de Controlo') }}
            </h2>
            <div class="flex space-x-2">
                <button onclick="iotcnt.startCycle()" class="btn-success">
                    {{ __('Iniciar Ciclo') }}
                </button>
                <button onclick="iotcnt.stopAll()" class="btn-danger">
                    {{ __('Parar Todas') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Estatísticas Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6" id="stats-container">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Válvulas Ativas') }}</h3>
                            <p class="text-2xl font-bold text-green-600" id="active-valves-count">-</p>
                        </div>
                        <div class="text-3xl text-green-500">💧</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total de Válvulas') }}</h3>
                            <p class="text-2xl font-bold text-blue-600" id="total-valves-count">-</p>
                        </div>
                        <div class="text-3xl text-blue-500">🔧</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Operações Hoje') }}</h3>
                            <p class="text-2xl font-bold text-purple-600" id="operations-today-count">-</p>
                        </div>
                        <div class="text-3xl text-purple-500">📊</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estado Sistema') }}</h3>
                            <p class="text-sm font-medium text-green-600">{{ __('Online') }}</p>
                        </div>
                        <div class="text-3xl text-green-500">✅</div>
                    </div>
                </div>
            </div>

            <!-- Controlo de Válvulas -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('Controlo de Válvulas') }}
                    </h3>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Última atualização:') }} <span id="last-update">{{ now()->format('H:i:s') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="valves-container">
                    @foreach($valves as $valve)
                        <div class="valve-card {{ $valve->current_state ? 'valve-active' : 'valve-inactive' }}" data-valve-id="{{ $valve->id }}">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                        {{ $valve->name }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Válvula') }} {{ $valve->valve_number }} - {{ __('Pino') }} {{ $valve->esp32_pin }}
                                    </p>
                                </div>
                                <span class="status-badge {{ $valve->current_state ? 'status-active' : 'status-inactive' }}">
                                    {{ $valve->current_state ? __('Ligada') : __('Desligada') }}
                                </span>
                            </div>

                            @if($valve->description)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                    {{ $valve->description }}
                                </p>
                            @endif

                            <div class="flex justify-between items-center mb-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Última ativação:') }}<br>
                                    <span class="last-activated">
                                        {{ $valve->last_activated_at ? $valve->last_activated_at->format('d/m/Y H:i') : __('Nunca') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <button
                                    data-valve-control
                                    data-valve-id="{{ $valve->id }}"
                                    data-action="on"
                                    data-duration="5"
                                    data-original-text="{{ __('Ligar') }}"
                                    class="btn-success flex-1 {{ $valve->current_state ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $valve->current_state ? 'disabled' : '' }}
                                >
                                    {{ __('Ligar') }}
                                </button>

                                <button
                                    data-valve-control
                                    data-valve-id="{{ $valve->id }}"
                                    data-action="off"
                                    data-original-text="{{ __('Desligar') }}"
                                    class="btn-secondary flex-1 {{ !$valve->current_state ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ !$valve->current_state ? 'disabled' : '' }}
                                >
                                    {{ __('Desligar') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Logs Recentes -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Atividade Recente') }}
                </h3>

                <div class="space-y-3" id="recent-logs">
                    @forelse($recentLogs as $log)
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full {{ $log->action === 'manual_on' || $log->action === 'cycle_start' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $log->valve->name ?? __('Sistema') }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $log->notes ?? $log->action }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                            {{ __('Nenhuma atividade recente') }}
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        // Atualizar estatísticas automaticamente
        function updateStats() {
            fetch('/api/valve/stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('active-valves-count').textContent = data.stats.active_valves;
                        document.getElementById('total-valves-count').textContent = data.stats.total_valves;
                        document.getElementById('operations-today-count').textContent = data.stats.total_operations_today;
                        document.getElementById('last-update').textContent = new Date().toLocaleTimeString('pt-PT');
                    }
                })
                .catch(error => console.error('Erro ao atualizar estatísticas:', error));
        }

        // Atualizar estatísticas a cada 30 segundos
        setInterval(updateStats, 30000);

        // Atualizar imediatamente
        updateStats();
                                    </span>
                                </div>
                                <p class="text-xs mt-1 {{ $valve->current_state ? 'text-green-600 dark:text-green-300' : 'text-red-600 dark:text-red-300' }}">
                                    {{ __('Última ativação:') }} {{ $valve->last_activated_at ? \Carbon\Carbon::parse($valve->last_activated_at)->diffForHumans() : __('N/A') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Nenhuma válvula configurada ou estado indisponível.') }}</p>
                @endif
            </div>

            <!-- Secção de Próximos Agendamentos -->
            @if(isset($upcomingSchedulesProcessed) && count($upcomingSchedulesProcessed) > 0)
                <div class="mt-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                    @php $daysOfWeek = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']; @endphp
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Próximos Agendamentos Programados') }}
                    </h3>
                    <div class="space-y-3">
                        @foreach ($upcomingSchedulesProcessed as $occurrence)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $occurrence['name'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $daysOfWeek[$occurrence['datetime']->dayOfWeek] }}, {{ $occurrence['datetime']->format('d/m/Y H:i') }}
                                    ({{ __('Duração/Válvula:') }} {{ $occurrence['per_valve_duration_minutes'] }} {{ __('min') }})
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Secção de Logs Recentes do Sistema -->
            @if(isset($recentSystemLogs) && $recentSystemLogs->count() > 0)
                <div class="mt-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Últimas Atividades do Sistema') }}
                    </h3>
                    <ul class="space-y-2">
                        @foreach($recentSystemLogs as $log)
                        <li class="text-sm p-2 rounded bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <strong class="font-medium">
                                {{ \Carbon\Carbon::parse($log->logged_at)->format('d/m H:i') }}
                                [{{$log->source}} - {{$log->status}}]:
                            </strong>
                            {{ Str::limit($log->message, 100) }}
                            {{-- Se quiser link para detalhes (precisa de rota para user comum ver logs)
                            <a href="#" class="text-blue-600 hover:underline ml-2">Detalhes</a>
                            --}}
                        </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
