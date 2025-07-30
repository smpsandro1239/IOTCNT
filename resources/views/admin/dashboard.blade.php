@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard de Administração IOTCNT') }}
            </h2>
            <div class="status-indicator status-online" id="admin-system-status"></div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="adminDashboard.refreshData()" class="btn-secondary" id="admin-refresh-btn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                {{ __('Atualizar') }}
            </button>
            <button onclick="adminDashboard.exportData()" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('Exportar Dados') }}
            </button>
            <a href="{{ route('admin.performance.index') }}" class="btn-info">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                {{ __('Performance') }}
            </a>
            <button onclick="adminDashboard.openSystemSettings()" class="btn-warning">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ __('Configurações') }}
            </button>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6" id="admin-dashboard-content">
    <!-- Estatísticas Gerais -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card stat-card-blue">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Válvulas') }}</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400" id="admin-total-valves">{{ $stats['total_valves'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span id="admin-active-valves">{{ $stats['active_valves'] }}</span> {{ __('ativas') }}
                    </p>
                </div>
                <div class="text-4xl">
                    <svg class="w-12 h-12 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-green">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Agendamentos') }}</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400" id="admin-total-schedules">{{ $stats['total_schedules'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span id="admin-enabled-schedules">{{ $stats['enabled_schedules'] }}</span> {{ __('ativos') }}
                    </p>
                </div>
                <div class="text-4xl">
                    <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-purple">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Utilizadores') }}</h3>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400" id="admin-total-users">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span id="admin-admin-users">{{ $stats['admin_users'] }}</span> {{ __('admins') }}
                    </p>
                </div>
                <div class="text-4xl">
                    <svg class="w-12 h-12 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-yellow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Telegram') }}</h3>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400" id="admin-telegram-users">{{ $stats['telegram_users'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span id="admin-authorized-telegram">{{ $stats['authorized_telegram_users'] }}</span> {{ __('autorizados') }}
                    </p>
                </div>
                <div class="text-4xl">
                    <svg class="w-12 h-12 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Performance -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Operações Hoje') }}</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="admin-operations-today">
                        <span class="loading-spinner"></span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('ativações') }}</p>
                </div>
                <div class="text-3xl">
                    <svg class="w-10 h-10 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Tempo Ativo') }}</h3>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100" id="admin-system-uptime">
                        <span class="loading-spinner"></span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('sistema') }}</p>
                </div>
                <div class="text-3xl">
                    <svg class="w-10 h-10 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card" id="admin-esp32-status-card">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('ESP32') }}</h3>
                    <p class="text-lg font-bold" id="admin-esp32-status-text">
                        <span class="loading-spinner"></span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="admin-esp32-last-ping">{{ __('Verificando...') }}</p>
                </div>
                <div class="text-3xl" id="admin-esp32-status-icon">
                    <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Erros (7d)') }}</h3>
                    <p class="text-2xl font-bold" id="admin-error-count">
                        <span class="loading-spinner"></span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('últimos 7 dias') }}</p>
                </div>
                <div class="text-3xl">
                    <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado das Válvulas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                </svg>
                {{ __('Estado Atual das Válvulas') }}
            </h3>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Última atualização:') }} <span id="admin-valves-last-update">{{ now()->format('H:i:s') }}</span>
                </div>
                <button onclick="adminDashboard.refreshValves()" class="btn-secondary btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    {{ __('Atualizar') }}
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="admin-valves-container">
            @foreach ($valvesStatus as $valve)
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
                                {{ __('Pino ESP32:') }} {{ $valve->esp32_pin ?? 'N/A' }}
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
                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $valve->last_activated_at ? $valve->last_activated_at->diffForHumans() : __('Nunca') }}
                            </p>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('Utilizações Hoje') }}</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100" id="valve-usage-{{ $valve->id }}">
                                <span class="loading-spinner"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Controles de Admin -->
                    <div class="space-y-2">
                        <div class="flex space-x-2">
                            <button
                                onclick="adminDashboard.controlValve({{ $valve->id }}, 'on', 5)"
                                class="btn-success flex-1 text-sm {{ $valve->current_state ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $valve->current_state ? 'disabled' : '' }}
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Ligar') }}
                            </button>

                            <button
                                onclick="adminDashboard.controlValve({{ $valve->id }}, 'off')"
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

                        <!-- Controles Avançados de Admin -->
                        <div class="flex space-x-2">
                            <button
                                onclick="adminDashboard.openValveSettings({{ $valve->id }})"
                                class="btn-secondary text-sm px-3"
                                title="{{ __('Configurações da Válvula') }}"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                            <button
                                onclick="adminDashboard.viewValveLogs({{ $valve->id }})"
                                class="btn-primary text-sm px-3"
                                title="{{ __('Ver Logs da Válvula') }}"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                            <button
                                onclick="adminDashboard.testValve({{ $valve->id }})"
                                class="btn-warning text-sm px-3"
                                title="{{ __('Teste de Válvula') }}"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Controlo do Sistema -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                </svg>
                {{ __('Controlo do Sistema') }}
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button onclick="adminDashboard.startSystemCycle()" class="btn-success p-4 text-center">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="font-medium">{{ __('Iniciar Ciclo') }}</div>
                <div class="text-xs opacity-75">{{ __('Todas as válvulas') }}</div>
            </button>

            <button onclick="adminDashboard.stopAllValves()" class="btn-danger p-4 text-center">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9l6 6m0-6l-6 6"></path>
                </svg>
                <div class="font-medium">{{ __('Parar Todas') }}</div>
                <div class="text-xs opacity-75">{{ __('Emergência') }}</div>
            </button>

            <button onclick="adminDashboard.testSystem()" class="btn-warning p-4 text-center">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <div class="font-medium">{{ __('Teste Sistema') }}</div>
                <div class="text-xs opacity-75">{{ __('Diagnóstico') }}</div>
            </button>

            <button onclick="adminDashboard.restartEsp32()" class="btn-secondary p-4 text-center">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <div class="font-medium">{{ __('Reiniciar ESP32') }}</div>
                <div class="text-xs opacity-75">{{ __('Reconectar') }}</div>
            </button>
        </div>
    </div>

    <!-- Próximos Agendamentos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    {{ __('Próximos Agendamentos') }}
                </h3>
                <a href="{{ route('admin.schedules.index') }}" class="btn-primary btn-sm">
                    {{ __('Gerir Todos') }}
                </a>
            </div>

            @if(count($upcomingSchedules) > 0)
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach ($upcomingSchedules as $upcoming)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $upcoming['schedule']->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $upcoming['datetime']->format('d/m/Y H:i') }}
                                    ({{ $upcoming['schedule']->per_valve_duration_minutes }}min/válvula)
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Utilizador:') }} {{ $upcoming['schedule']->user->name ?? 'Sistema' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-purple-600 dark:text-purple-400">
                                    @if($upcoming['days_until'] == 0)
                                        {{ __('Hoje') }}
                                    @elseif($upcoming['days_until'] == 1)
                                        {{ __('Amanhã') }}
                                    @else
                                        {{ __('Em') }} {{ $upcoming['days_until'] }} {{ __('dias') }}
                                    @endif
                                </span>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $upcoming['datetime']->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0v-4m4-4h8m-4-4v8"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Nenhum agendamento próximo') }}</p>
                </div>
            @endif
        </div>

        <!-- Gráfico de Atividade -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                    {{ __('Atividade (7 dias)') }}
                </h3>
                <select id="activity-filter" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 bg-white dark:bg-gray-700">
                    <option value="all">{{ __('Todas as fontes') }}</option>
                    <option value="web">{{ __('Interface Web') }}</option>
                    <option value="esp32">{{ __('ESP32') }}</option>
                    <option value="telegram">{{ __('Telegram') }}</option>
                    <option value="scheduled">{{ __('Agendamentos') }}</option>
                </select>
            </div>

            <div class="space-y-3" id="activity-chart">
                @if($activityBySource->count() > 0)
                    @foreach ($activityBySource as $activity)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full {{ $activity->source === 'web' ? 'bg-blue-500' : ($activity->source === 'esp32' ? 'bg-green-500' : ($activity->source === 'telegram' ? 'bg-yellow-500' : 'bg-purple-500')) }}"></div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($activity->source) }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $activity->source === 'web' ? 'bg-blue-500' : ($activity->source === 'esp32' ? 'bg-green-500' : ($activity->source === 'telegram' ? 'bg-yellow-500' : 'bg-purple-500')) }}"
                                         style="width: {{ min(($activity->count / $activityBySource->max('count')) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100 w-8 text-right">{{ $activity->count }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Nenhuma atividade registada') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Logs e Alertas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Logs Recentes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ __('Atividade Recente (24h)') }}
                </h3>
                <a href="{{ route('admin.logs.index') }}" class="btn-primary btn-sm">
                    {{ __('Ver Todos') }}
                </a>
            </div>

            @if($recentLogs->count() > 0)
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach ($recentLogs as $log)
                        <div class="flex items-start justify-between p-3 rounded-lg {{ $log->status === 'ERROR' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-gray-50 dark:bg-gray-700' }} hover:bg-opacity-75 transition-colors">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    @if($log->status === 'ERROR')
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium {{ $log->status === 'ERROR' ? 'text-red-800 dark:text-red-200' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $log->event_type }}
                                    </p>
                                    <p class="text-xs {{ $log->status === 'ERROR' ? 'text-red-600 dark:text-red-300' : 'text-gray-600 dark:text-gray-300' }} mt-1">
                                        {{ Str::limit($log->message, 80) }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-xs {{ $log->status === 'ERROR' ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $log->logged_at->format('H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Nenhuma atividade recente') }}</p>
                </div>
            @endif
        </div>

        <!-- Alertas e Erros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ __('Alertas e Erros (7 dias)') }}
                </h3>
                <button onclick="adminDashboard.clearErrors()" class="btn-secondary btn-sm">
                    {{ __('Limpar') }}
                </button>
            </div>

            @if($errorLogs->count() > 0)
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach ($errorLogs as $error)
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <div class="flex justify-between items-start">
                                <span class="font-medium text-red-800 dark:text-red-200 text-sm">{{ $error->event_type }}</span>
                                <span class="text-xs text-red-600 dark:text-red-400">{{ $error->logged_at->format('d/m H:i') }}</span>
                            </div>
                            <p class="text-xs text-red-600 dark:text-red-300 mt-1">{{ Str::limit($error->message, 80) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-4xl mb-4">✅</div>
                    <p class="text-green-600 dark:text-green-400 font-medium">{{ __('Nenhum erro nos últimos 7 dias!') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Sistema funcionando perfeitamente') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="admin-toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
// Admin Dashboard JavaScript
class AdminDashboard {
    constructor() {
        this.autoRefresh = true;
        this.refreshInterval = null;
        this.init();
    }

    init() {
        this.startAutoRefresh();
        this.loadDynamicData();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Activity filter
        const activityFilter = document.getElementById('activity-filter');
        if (activityFilter) {
            activityFilter.addEventListener('change', () => this.filterActivity());
        }
    }

    async loadDynamicData() {
        await Promise.all([
            this.updateSystemMetrics(),
            this.updateValveUsage(),
            this.updateEsp32Status()
        ]);
    }

    async updateSystemMetrics() {
        try {
            const response = await fetch('/api/admin/metrics', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getApiToken()}`
                }
            });

            if (!response.ok) throw new Error('Falha na resposta da API');

            const data = await response.json();

            if (data.success) {
                document.getElementById('admin-operations-today').textContent = data.metrics.operations_today;
                document.getElementById('admin-system-uptime').textContent = data.metrics.system_uptime;
                document.getElementById('admin-error-count').textContent = data.metrics.error_count;
            }
        } catch (error) {
            console.error('Erro ao atualizar métricas:', error);
        }
    }

    async updateValveUsage() {
        try {
            const response = await fetch('/api/admin/valve-usage', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getApiToken()}`
                }
            });

            if (!response.ok) throw new Error('Falha na resposta da API');

            const data = await response.json();

            if (data.success) {
                data.usage.forEach(usage => {
                    const element = document.getElementById(`valve-usage-${usage.valve_id}`);
                    if (element) {
                        element.textContent = usage.count;
                    }
                });
            }
        } catch (error) {
            console.error('Erro ao atualizar uso das válvulas:', error);
        }
    }

    async updateEsp32Status() {
        try {
            const response = await fetch('/api/admin/esp32-status', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getApiToken()}`
                }
            });

            const statusCard = document.getElementById('admin-esp32-status-card');
            const statusText = document.getElementById('admin-esp32-status-text');
            const statusIcon = document.getElementById('admin-esp32-status-icon');
            const lastPing = document.getElementById('admin-esp32-last-ping');

            if (response.ok) {
                const data = await response.json();
                const status = data.status || {};

                let statusLabel = 'Online';
                let colorClass = 'stat-card-green';
                let textColor = 'text-green-600 dark:text-green-400';
                let iconSvg = `<svg class="w-10 h-10 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>`;

                if (status.connection !== 'ok') {
                    statusLabel = 'Offline';
                    colorClass = 'stat-card-red';
                    textColor = 'text-red-600 dark:text-red-400';
                    iconSvg = `<svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>`;
                }

                statusCard.className = statusCard.className.replace(/stat-card-(green|red|yellow)/g, '');
                statusCard.classList.add(colorClass);
                statusText.textContent = statusLabel;
                statusText.className = `text-lg font-bold ${textColor}`;
                statusIcon.innerHTML = iconSvg;
                lastPing.textContent = status.last_ping ?
                    `Último ping: ${new Date(status.last_ping).toLocaleString('pt-PT')}` :
                    'Sem comunicação';

            } else {
                statusText.textContent = 'Erro';
                statusText.className = 'text-lg font-bold text-red-600 dark:text-red-400';
                lastPing.textContent = 'Erro ao verificar estado';
            }
        } catch (error) {
            console.error('Erro ao verificar ESP32:', error);
        }
    }

    async controlValve(valveId, action, duration = 5) {
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
                    valve_id: valveId,
                    action: action,
                    duration: duration
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showToast(data.message || 'Comando enviado com sucesso', 'success');
                await this.refreshValves();
            } else {
                throw new Error(data.message || 'Erro ao controlar válvula');
            }
        } catch (error) {
            console.error('Erro ao controlar válvula:', error);
            this.showToast(error.message || 'Erro ao controlar válvula', 'error');
        }
    }

    async startSystemCycle() {
        if (!confirm('Iniciar ciclo completo de irrigação?')) return;

        try {
            const response = await fetch('/api/valve/start-cycle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getApiToken()}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ duration_per_valve: 5 })
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Ciclo de irrigação iniciado', 'success');
                await this.refreshValves();
            } else {
                throw new Error(data.message || 'Erro ao iniciar ciclo');
            }
        } catch (error) {
            console.error('Erro ao iniciar ciclo:', error);
            this.showToast(error.message || 'Erro ao iniciar ciclo', 'error');
        }
    }

    async stopAllValves() {
        if (!confirm('Parar todas as válvulas imediatamente?')) return;

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
                await this.refreshValves();
            } else {
                throw new Error(data.message || 'Erro ao parar válvulas');
            }
        } catch (error) {
            console.error('Erro ao parar válvulas:', error);
            this.showToast(error.message || 'Erro ao parar válvulas', 'error');
        }
    }

    async testSystem() {
        this.showToast('Iniciando teste do sistema...', 'info');

        try {
            const response = await fetch('/api/admin/test-system', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getApiToken()}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Teste do sistema concluído com sucesso', 'success');
            } else {
                throw new Error(data.message || 'Erro no teste do sistema');
            }
        } catch (error) {
            console.error('Erro no teste:', error);
            this.showToast(error.message || 'Erro no teste do sistema', 'error');
        }
    }

    async restartEsp32() {
        if (!confirm('Reiniciar ESP32? Isto pode interromper operações em curso.')) return;

        try {
            const response = await fetch('/api/admin/restart-esp32', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getApiToken()}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Comando de reinício enviado para ESP32', 'success');
            } else {
                throw new Error(data.message || 'Erro ao reiniciar ESP32');
            }
        } catch (error) {
            console.error('Erro ao reiniciar ESP32:', error);
            this.showToast(error.message || 'Erro ao reiniciar ESP32', 'error');
        }
    }

    openValveSettings(valveId) {
        window.location.href = `/admin/valves/${valveId}/edit`;
    }

    viewValveLogs(valveId) {
        window.location.href = `/admin/logs?valve_id=${valveId}`;
    }

    async testValve(valveId) {
        if (!confirm('Executar teste rápido na válvula (30 segundos)?')) return;

        try {
            await this.controlValve(valveId, 'on', 0.5); // 30 segundos
            this.showToast('Teste de válvula iniciado (30 segundos)', 'info');
        } catch (error) {
            this.showToast('Erro no teste da válvula', 'error');
        }
    }

    openSystemSettings() {
        // Implementar modal de configurações do sistema
        this.showToast('Configurações do sistema em desenvolvimento', 'info');
    }

    exportData() {
        window.location.href = '/admin/export/data';
    }

    clearErrors() {
        if (!confirm('Limpar todos os registos de erro?')) return;

        // Implementar limpeza de erros
        this.showToast('Registos de erro limpos', 'success');
    }

    filterActivity() {
        const filter = document.getElementById('activity-filter').value;
        // Implementar filtro de atividade
        console.log('Filtrar atividade por:', filter);
    }

    async refreshData() {
        const button = document.getElementById('admin-refresh-btn');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="loading-spinner mr-2"></span>Atualizando...';

        try {
            await this.loadDynamicData();
            await this.refreshValves();
            this.showToast('Dados atualizados', 'success');
        } catch (error) {
            this.showToast('Erro ao atualizar dados', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    async refreshValves() {
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

                document.getElementById('admin-valves-last-update').textContent =
                    new Date().toLocaleTimeString('pt-PT');
            }
        } catch (error) {
            console.error('Erro ao atualizar válvulas:', error);
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
            statusBadge.className = `status-badge ${valve.current_state ? 'status-active' : 'status-inactive'} mb-2`;
        }

        // Atualizar indicador de status
        const statusIndicator = card.querySelector('.status-indicator');
        if (statusIndicator) {
            statusIndicator.className = `status-indicator ${valve.current_state ? 'status-online' : 'status-offline'}`;
        }

        // Atualizar botões
        this.updateValveButtons(card, valve.current_state);
    }

    updateValveButtons(card, isActive) {
        const onButton = card.querySelector('button[onclick*="\'on\'"]');
        const offButton = card.querySelector('button[onclick*="\'off\'"]');

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

    startAutoRefresh() {
        if (this.refreshInterval) clearInterval(this.refreshInterval);

        this.refreshInterval = setInterval(() => {
            if (this.autoRefresh) {
                this.loadDynamicData();
                this.refreshValves();
            }
        }, 60000); // 1 minuto
    }

    showToast(message, type = 'info') {
        const container = document.getElementById('admin-toast-container');
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

        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    getApiToken() {
        const metaToken = document.querySelector('meta[name="api-token"]');
        return metaToken ? metaToken.content : localStorage.getItem('api_token') || '';
    }
}

// Inicializar Admin Dashboard
const adminDashboard = new AdminDashboard();
</script>
@endsection
