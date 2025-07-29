<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Painel de Controlo IOTCNT') }}
                </h2>
                <div class="status-indicator status-online" id="system-status"></div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="iotcnt.refreshData()" class="btn-secondary" id="refresh-btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    {{ __('Atualizar') }}
                </button>
                <button onclick="iotcnt.startCycle()" class="btn-success" id="start-cycle-btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Iniciar Ciclo') }}
                </button>
                <button onclick="iotcnt.stopAll()" class="btn-danger" id="stop-all-btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9l6 6m0-6l-6 6"></path>
                    </svg>
                    {{ __('Parar Todas') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Estatísticas Rápidas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="stats-container">
                <div class="stat-card stat-card-green">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Válvulas Ativas') }}</h3>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400" id="active-valves-count">
                                <span class="loading-spinner"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('de') }} <span id="total-valves-inline">5</span></p>
                        </div>
                        <div class="text-4xl">
                            <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card stat-card-blue">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Operações Hoje') }}</h3>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400" id="operations-today-count">
                                <span class="loading-spinner"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('ativações') }}</p>
                        </div>
                        <div class="text-4xl">
                            <svg class="w-12 h-12 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card stat-card-purple">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Tempo Ativo') }}</h3>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="system-uptime">
                                <span class="loading-spinner"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('sistema') }}</p>
                        </div>
                        <div class="text-4xl">
                            <svg class="w-12 h-12 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card" id="system-status-card">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Estado Sistema') }}</h3>
                            <p class="text-lg font-bold" id="system-status-text">
                                <span class="loading-spinner"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="last-update-text">{{ __('Verificando...') }}</p>
                        </div>
                        <div class="text-4xl" id="system-status-icon">
                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progresso do Ciclo (oculto por padrão) -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 hidden" id="cycle-progress-container">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('Ciclo de Irrigação em Progresso') }}
                    </h3>
                    <button onclick="iotcnt.stopCycle()" class="btn-danger btn-sm">
                        {{ __('Parar Ciclo') }}
                    </button>
                </div>
                <div class="progress-bar mb-2">
                    <div class="progress-fill" id="cycle-progress-fill" style="width: 0%"></div>
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span id="cycle-current-valve">{{ __('Válvula 1') }}</span>
                    <span id="cycle-progress-text">0%</span>
                </div>
            </div>

            <!-- Controlo de Válvulas -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        {{ __('Controlo de Válvulas') }}
                    </h3>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Última atualização:') }} <span id="last-update" class="font-medium">{{ now()->format('H:i:s') }}</span>
                        </div>
                        <button onclick="iotcnt.toggleAutoRefresh()" class="btn-secondary btn-sm" id="auto-refresh-btn">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            {{ __('Auto') }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="valves-container">
                    @foreach($valves as $valve)
                        <div class="valve-card-enhanced {{ $valve->current_state ? 'valve-card-active valve-pulse' : 'valve-card-inactive' }}"
                             data-valve-id="{{ $valve->id }}"
                             data-valve-number="{{ $valve->valve_number }}">

                            <!-- Header da Válvula -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100 flex items-center">
                                        <span class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                            {{ $valve->valve_number }}
                                        </span>
                                        {{ $valve->name }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 ml-11">
                                        {{ __('Pino ESP32:') }} {{ $valve->esp32_pin }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="status-badge {{ $valve->current_state ? 'status-active' : 'status-inactive' }} mb-2">
                                        {{ $valve->current_state ? __('LIGADA') : __('DESLIGADA') }}
                                    </span>
                                    <div class="status-indicator {{ $valve->current_state ? 'status-online' : 'status-offline' }}"></div>
                                </div>
                            </div>

                            <!-- Descrição -->
                            @if($valve->description)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ $valve->description }}
                                    </p>
                                </div>
                            @endif

                            <!-- Informações de Estado -->
                            <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('Última Ativação') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100 last-activated" data-valve-id="{{ $valve->id }}">
                                        {{ $valve->last_activated_at ? $valve->last_activated_at->diffForHumans() : __('Nunca') }}
                                    </p>
                                </div>
                                <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('Duração Padrão') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">5 min</p>
                                </div>
                            </div>

                            <!-- Controles -->
                            <div class="space-y-2">
                                <div class="flex space-x-2">
                                    <button
                                        data-valve-control
                                        data-valve-id="{{ $valve->id }}"
                                        data-action="on"
                                        data-duration="5"
                                        class="btn-success flex-1 text-sm {{ $valve->current_state ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $valve->current_state ? 'disabled' : '' }}
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ __('Ligar') }}
                                    </button>

                                    <button
                                        data-valve-control
                                        data-valve-id="{{ $valve->id }}"
                                        data-action="off"
                                        class="btn-danger flex-1 text-sm {{ !$valve->current_state ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ !$valve->current_state ? 'disabled' : '' }}
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9l6 6m0-6l-6 6"></path>
                                        </svg>
                                        {{ __('Desligar') }}
                                    </button>
                                </div>

                                <!-- Controles Avançados -->
                                <div class="flex space-x-2">
                                    <select class="flex-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 bg-white dark:bg-gray-700"
                                            data-duration-select data-valve-id="{{ $valve->id }}">
                                        <option value="1">1 min</option>
                                        <option value="3">3 min</option>
                                        <option value="5" selected>5 min</option>
                                        <option value="10">10 min</option>
                                        <option value="15">15 min</option>
                                        <option value="30">30 min</option>
                                    </select>
                                    <button
                                        data-valve-control
                                        data-valve-id="{{ $valve->id }}"
                                        data-action="toggle"
                                        class="btn-warning text-sm px-3"
                                        title="{{ __('Alternar Estado') }}"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Logs Recentes -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Atividade Recente') }}
                    </h3>
                    <div class="flex space-x-2">
                        <button onclick="iotcnt.refreshLogs()" class="btn-secondary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            {{ __('Atualizar') }}
                        </button>
                        @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.logs.index') }}" class="btn-primary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('Ver Todos') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="space-y-3 max-h-96 overflow-y-auto" id="recent-logs">
                    @forelse($recentLogs as $log)
                        <div class="flex items-start justify-between py-3 px-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    @php
                                        $iconColor = match($log->action) {
                                            'manual_on', 'cycle_start', 'scheduled_on' => 'text-green-500',
                                            'manual_off', 'cycle_stop', 'scheduled_off' => 'text-red-500',
                                            'system_start', 'esp32_connected' => 'text-blue-500',
                                            default => 'text-gray-500'
                                        };
                                    @endphp
                                    <svg class="w-5 h-5 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                        @if(in_array($log->action, ['manual_on', 'cycle_start', 'scheduled_on']))
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                        @elseif(in_array($log->action, ['manual_off', 'cycle_stop', 'scheduled_off']))
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                        @else
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        @endif
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $log->valve->name ?? __('Sistema') }}
                                        @if($log->valve)
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                                ({{ __('Válvula') }} {{ $log->valve->valve_number }})
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                        {{ $log->notes ?? ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </p>
                                    @if($log->duration_minutes)
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            {{ __('Duração:') }} {{ $log->duration_minutes }} {{ __('minutos') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col items-end text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $log->created_at->format('H:i') }}
                                </div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                                @if($log->source)
                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ ucfirst($log->source) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">
                                {{ __('Nenhuma atividade recente') }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                {{ __('As atividades aparecerão aqui quando o sistema for utilizado') }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        // IOTCNT Dashboard JavaScript
        class IotcntDashboard {
            constructor() {
                this.autoRefresh = true;
                this.refreshInterval = null;
                this.cycleInProgress = false;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.startAutoRefresh();
                this.loadInitialData();
            }

            setupEventListeners() {
                // Controles de válvulas
                document.addEventListener('click', (e) => {
                    if (e.target.matches('[data-valve-control]') || e.target.closest('[data-valve-control]')) {
                        const button = e.target.matches('[data-valve-control]') ? e.target : e.target.closest('[data-valve-control]');
                        this.handleValveControl(button);
                    }
                });

                // Seletores de duração
                document.addEventListener('change', (e) => {
                    if (e.target.matches('[data-duration-select]')) {
                        const valveId = e.target.dataset.valveId;
                        const duration = e.target.value;
                        this.updateValveDuration(valveId, duration);
                    }
                });

                // Teclas de atalho
                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey || e.metaKey) {
                        switch(e.key) {
                            case 'r':
                                e.preventDefault();
                                this.refreshData();
                                break;
                            case 's':
                                e.preventDefault();
                                this.startCycle();
                                break;
                            case 'x':
                                e.preventDefault();
                                this.stopAll();
                                break;
                        }
                    }
                });
            }

            async loadInitialData() {
                await Promise.all([
                    this.updateStats(),
                    this.updateValveStatus(),
                    this.updateSystemHealth()
                ]);
            }

            async updateStats() {
                try {
                    const response = await fetch('/api/valve/stats', {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${this.getApiToken()}`
                        }
                    });

                    if (!response.ok) throw new Error('Falha na resposta da API');

                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('active-valves-count').textContent = data.stats.active_valves;
                        document.getElementById('total-valves-inline').textContent = data.stats.total_valves;
                        document.getElementById('operations-today-count').textContent = data.stats.total_operations_today;
                        document.getElementById('system-uptime').textContent = data.stats.system_uptime || '---';
                        document.getElementById('last-update').textContent = new Date().toLocaleTimeString('pt-PT');
                    }
                } catch (error) {
                    console.error('Erro ao atualizar estatísticas:', error);
                    this.showToast('Erro ao atualizar estatísticas', 'error');
                }
            }

            async updateValveStatus() {
                try {
                    const response = await fetch('/api/valve/status', {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${this.getApiToken()}`
                        }
                    });

                    if (!response.ok) throw new Error('Falha na resposta da API');

                    const data = await response.json();

                    if (data.success) {
                        data.valves.forEach(valve => {
                            this.updateValveCard(valve);
                        });
                    }
                } catch (error) {
                    console.error('Erro ao atualizar estado das válvulas:', error);
                }
            }

            updateValveCard(valve) {
                const card = document.querySelector(`[data-valve-id="${valve.id}"]`);
                if (!card) return;

                // Atualizar classes do card
                card.className = card.className.replace(/valve-card-(active|inactive)/g, '');
                card.className = card.className.replace(/valve-pulse/g, '');

                if (valve.current_state) {
                    card.classList.add('valve-card-active', 'valve-pulse');
                } else {
                    card.classList.add('valve-card-inactive');
                }

                // Atualizar badge de status
                const statusBadge = card.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.textContent = valve.current_state ? 'LIGADA' : 'DESLIGADA';
                    statusBadge.className = `status-badge ${valve.current_state ? 'status-active' : 'status-inactive'}`;
                }

                // Atualizar indicador de status
                const statusIndicator = card.querySelector('.status-indicator');
                if (statusIndicator) {
                    statusIndicator.className = `status-indicator ${valve.current_state ? 'status-online' : 'status-offline'}`;
                }

                // Atualizar última ativação
                const lastActivated = card.querySelector('.last-activated');
                if (lastActivated) {
                    lastActivated.textContent = valve.last_activated_at ?
                        new Date(valve.last_activated_at).toLocaleString('pt-PT') : 'Nunca';
                }

                // Atualizar botões
                this.updateValveButtons(card, valve.current_state);
            }

            updateValveButtons(card, isActive) {
                const onButton = card.querySelector('[data-action="on"]');
                const offButton = card.querySelector('[data-action="off"]');

                if (onButton) {
                    onButton.disabled = isActive;
                    onButton.className = onButton.className.replace(/opacity-50|cursor-not-allowed/g, '');
                    if (isActive) {
                        onButton.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }

                if (offButton) {
                    offButton.disabled = !isActive;
                    offButton.className = offButton.className.replace(/opacity-50|cursor-not-allowed/g, '');
                    if (!isActive) {
                        offButton.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
            }

            async updateSystemHealth() {
                try {
                    const response = await fetch('/api/system/health', {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${this.getApiToken()}`
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.updateSystemStatus(data.health || {});
                    } else {
                        this.updateSystemStatus({ status: 'offline' });
                    }
                } catch (error) {
                    this.updateSystemStatus({ status: 'error' });
                }
            }

            updateSystemStatus(health) {
                const statusCard = document.getElementById('system-status-card');
                const statusText = document.getElementById('system-status-text');
                const statusIcon = document.getElementById('system-status-icon');
                const lastUpdateText = document.getElementById('last-update-text');

                let status = 'Online';
                let colorClass = 'stat-card-green';
                let textColor = 'text-green-600 dark:text-green-400';
                let iconSvg = `<svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>`;

                if (health.status === 'offline' || health.database !== 'ok') {
                    status = 'Offline';
                    colorClass = 'stat-card-red';
                    textColor = 'text-red-600 dark:text-red-400';
                    iconSvg = `<svg class="w-12 h-12 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>`;
                }

                statusCard.className = statusCard.className.replace(/stat-card-(green|red|blue|purple)/g, '');
                statusCard.classList.add(colorClass);
                statusText.textContent = status;
                statusText.className = `text-lg font-bold ${textColor}`;
                statusIcon.innerHTML = iconSvg;
                lastUpdateText.textContent = `Atualizado: ${new Date().toLocaleTimeString('pt-PT')}`;
            }

            async handleValveControl(button) {
                if (button.disabled) return;

                const valveId = button.dataset.valveId;
                const action = button.dataset.action;
                let duration = button.dataset.duration || 5;

                // Obter duração do seletor se existir
                const durationSelect = document.querySelector(`[data-duration-select][data-valve-id="${valveId}"]`);
                if (durationSelect && (action === 'on' || action === 'toggle')) {
                    duration = durationSelect.value;
                }

                const originalText = button.textContent;
                button.disabled = true;
                button.innerHTML = '<span class="loading-spinner mr-2"></span>Processando...';

                try {
                    const response = await fetch('/api/valve/control', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${this.getApiToken()}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            valve_id: parseInt(valveId),
                            action: action,
                            duration: parseInt(duration)
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showToast(data.message || 'Comando enviado com sucesso', 'success');
                        await this.updateValveStatus();
                        await this.updateStats();
                    } else {
                        throw new Error(data.message || 'Erro ao controlar válvula');
                    }
                } catch (error) {
                    console.error('Erro ao controlar válvula:', error);
                    this.showToast(error.message || 'Erro ao controlar válvula', 'error');
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            async startCycle() {
                if (this.cycleInProgress) {
                    this.showToast('Ciclo já em progresso', 'warning');
                    return;
                }

                const button = document.getElementById('start-cycle-btn');
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="loading-spinner mr-2"></span>Iniciando...';

                try {
                    const response = await fetch('/api/valve/start-cycle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${this.getApiToken()}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            duration_per_valve: 5
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showToast('Ciclo de irrigação iniciado', 'success');
                        this.cycleInProgress = true;
                        this.showCycleProgress();

                        // Simular progresso do ciclo
                        this.simulateCycleProgress(data.total_valves || 5, data.duration_per_valve || 5);
                    } else {
                        throw new Error(data.message || 'Erro ao iniciar ciclo');
                    }
                } catch (error) {
                    console.error('Erro ao iniciar ciclo:', error);
                    this.showToast(error.message || 'Erro ao iniciar ciclo', 'error');
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            async stopAll() {
                const button = document.getElementById('stop-all-btn');
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="loading-spinner mr-2"></span>Parando...';

                try {
                    const response = await fetch('/api/valve/stop-all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${this.getApiToken()}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showToast(`${data.stopped_valves || 0} válvulas paradas`, 'success');
                        this.cycleInProgress = false;
                        this.hideCycleProgress();
                        await this.updateValveStatus();
                        await this.updateStats();
                    } else {
                        throw new Error(data.message || 'Erro ao parar válvulas');
                    }
                } catch (error) {
                    console.error('Erro ao parar válvulas:', error);
                    this.showToast(error.message || 'Erro ao parar válvulas', 'error');
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            showCycleProgress() {
                const container = document.getElementById('cycle-progress-container');
                if (container) {
                    container.classList.remove('hidden');
                }
            }

            hideCycleProgress() {
                const container = document.getElementById('cycle-progress-container');
                if (container) {
                    container.classList.add('hidden');
                }
            }

            simulateCycleProgress(totalValves, durationPerValve) {
                const totalDuration = totalValves * durationPerValve * 60 * 1000; // em ms
                const updateInterval = 1000; // 1 segundo
                let elapsed = 0;

                const progressInterval = setInterval(() => {
                    elapsed += updateInterval;
                    const progress = Math.min((elapsed / totalDuration) * 100, 100);
                    const currentValve = Math.min(Math.floor(elapsed / (durationPerValve * 60 * 1000)) + 1, totalValves);

                    const progressFill = document.getElementById('cycle-progress-fill');
                    const progressText = document.getElementById('cycle-progress-text');
                    const currentValveText = document.getElementById('cycle-current-valve');

                    if (progressFill) progressFill.style.width = `${progress}%`;
                    if (progressText) progressText.textContent = `${Math.round(progress)}%`;
                    if (currentValveText) currentValveText.textContent = `Válvula ${currentValve}`;

                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        this.cycleInProgress = false;
                        this.hideCycleProgress();
                        this.showToast('Ciclo de irrigação concluído', 'success');
                        this.updateValveStatus();
                        this.updateStats();
                    }
                }, updateInterval);
            }

            async refreshData() {
                const button = document.getElementById('refresh-btn');
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="loading-spinner mr-2"></span>Atualizando...';

                try {
                    await this.loadInitialData();
                    this.showToast('Dados atualizados', 'success');
                } catch (error) {
                    this.showToast('Erro ao atualizar dados', 'error');
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            async refreshLogs() {
                // Recarregar a página para atualizar logs (implementação simples)
                window.location.reload();
            }

            toggleAutoRefresh() {
                this.autoRefresh = !this.autoRefresh;
                const button = document.getElementById('auto-refresh-btn');

                if (this.autoRefresh) {
                    this.startAutoRefresh();
                    button.classList.remove('opacity-50');
                    this.showToast('Atualização automática ativada', 'info');
                } else {
                    this.stopAutoRefresh();
                    button.classList.add('opacity-50');
                    this.showToast('Atualização automática desativada', 'info');
                }
            }

            startAutoRefresh() {
                if (this.refreshInterval) clearInterval(this.refreshInterval);

                this.refreshInterval = setInterval(() => {
                    if (this.autoRefresh) {
                        this.updateStats();
                        this.updateValveStatus();
                        this.updateSystemHealth();
                    }
                }, 30000); // 30 segundos
            }

            stopAutoRefresh() {
                if (this.refreshInterval) {
                    clearInterval(this.refreshInterval);
                    this.refreshInterval = null;
                }
            }

            updateValveDuration(valveId, duration) {
                // Atualizar duração nos botões de controle
                const onButton = document.querySelector(`[data-valve-id="${valveId}"][data-action="on"]`);
                if (onButton) {
                    onButton.dataset.duration = duration;
                }
            }

            showToast(message, type = 'info') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');

                toast.className = `toast toast-${type} transform translate-x-full`;
                toast.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                `;

                container.appendChild(toast);

                // Animar entrada
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);

                // Auto remover após 5 segundos
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            }

            getApiToken() {
                // Obter token da meta tag ou localStorage
                const metaToken = document.querySelector('meta[name="api-token"]');
                return metaToken ? metaToken.content : localStorage.getItem('api_token') || '';
            }
        }

        // Inicializar dashboard
        const iotcnt = new IotcntDashboard();

        // Expor métodos globalmente para compatibilidade
        window.iotcnt = {
            refreshData: () => iotcnt.refreshData(),
            startCycle: () => iotcnt.startCycle(),
            stopAll: () => iotcnt.stopAll(),
            refreshLogs: () => iotcnt.refreshLogs(),
            toggleAutoRefresh: () => iotcnt.toggleAutoRefresh()
        };
    </script>
</x-app-layout>
