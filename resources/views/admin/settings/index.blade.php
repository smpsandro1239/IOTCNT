@extends('layouts.admin')

@section('header')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Configurações do Sistema') }}
            </h2>
            <div class="status-indicator status-online"></div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="settingsManager.exportSettings()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('Exportar') }}
            </button>
            <button onclick="settingsManager.openImportModal()" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                {{ __('Importar') }}
            </button>
            <button onclick="settingsManager.resetToDefaults()" class="btn-warning">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                {{ __('Restaurar Padrões') }}
            </button>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Navegação por Abas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6" id="settings-tabs">
                <button onclick="settingsManager.showTab('general')"
                        class="settings-tab active py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="general">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ __('Geral') }}
                </button>

                <button onclick="settingsManager.showTab('irrigation')"
                        class="settings-tab py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="irrigation">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Irrigação') }}
                </button>

                <button onclick="settingsManager.showTab('notifications')"
                        class="settings-tab py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="notifications">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.868 19.718A8.966 8.966 0 003 12a9 9 0 0118 0 8.966 8.966 0 00-1.868 7.718"></path>
                    </svg>
                    {{ __('Notificações') }}
                </button>

                <button onclick="settingsManager.showTab('api')"
                        class="settings-tab py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="api">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('API & ESP32') }}
                </button>

                <button onclick="settingsManager.showTab('maintenance')"
                        class="settings-tab py-4 px-1 border-b-2 font-medium text-sm"
                        data-tab="maintenance">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    {{ __('Manutenção') }}
                </button>
            </nav>
        </div>
    </div>

    <!-- Formulário de Configurações -->
    <form id="settings-form" class="space-y-6">
        @csrf

        <!-- Aba Geral -->
        <div id="tab-general" class="settings-tab-content">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('Configurações Gerais') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="system_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Nome do Sistema') }}
                        </label>
                        <input type="text" id="system_name" name="system_name"
                               value="{{ $settings['system_name'] }}"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Fuso Horário') }}
                        </label>
                        <select id="timezone" name="timezone"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="Europe/Lisbon" {{ $settings['timezone'] === 'Europe/Lisbon' ? 'selected' : '' }}>Europe/Lisbon</option>
                            <option value="Europe/London" {{ $settings['timezone'] === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            <option value="Europe/Madrid" {{ $settings['timezone'] === 'Europe/Madrid' ? 'selected' : '' }}>Europe/Madrid</option>
                            <option value="Europe/Paris" {{ $settings['timezone'] === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                            <option value="UTC" {{ $settings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode"
                                       {{ $settings['maintenance_mode'] ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ __('Modo de Manutenção') }}
                                </span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" id="debug_mode" name="debug_mode"
                                       {{ $settings['debug_mode'] ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ __('Modo Debug') }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba Irrigação -->
        <div id="tab-irrigation" class="settings-tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('Configurações de Irrigação') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="default_valve_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Duração Padrão das Válvulas (minutos)') }}
                        </label>
                        <input type="number" id="default_valve_duration" name="default_valve_duration"
                               value="{{ $settings['default_valve_duration'] }}" min="1" max="60"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    <div>
                        <label for="max_concurrent_valves" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Máximo de Válvulas Simultâneas') }}
                        </label>
                        <select id="max_concurrent_valves" name="max_concurrent_valves"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $settings['max_concurrent_valves'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="cycle_duration_per_valve" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Duração do Ciclo por Válvula (minutos)') }}
                        </label>
                        <input type="number" id="cycle_duration_per_valve" name="cycle_duration_per_valve"
                               value="{{ $settings['cycle_duration_per_valve'] }}" min="1" max="30"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center">
                            <input type="checkbox" id="auto_cycle_enabled" name="auto_cycle_enabled"
                                   {{ $settings['auto_cycle_enabled'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                {{ __('Ciclo Automático Ativado') }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba Notificações -->
        <div id="tab-notifications" class="settings-tab-content hidden">
            <div class="space-y-6">
                <!-- Telegram -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('Notificações Telegram') }}
                        </h3>
                        <button type="button" onclick="settingsManager.testTelegram()" class="btn-secondary btn-sm">
                            {{ __('Testar') }}
                        </button>
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="telegram_notifications" name="telegram_notifications"
                                   {{ $settings['telegram_notifications'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                {{ __('Ativar Notificações Telegram') }}
                            </span>
                        </label>

                        <div>
                            <label for="telegram_bot_token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Token do Bot Telegram') }}
                            </label>
                            <input type="password" id="telegram_bot_token" name="telegram_bot_token"
                                   value="{{ $settings['telegram_bot_token'] }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                   placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz">
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('Notificações por Email') }}
                        </h3>
                        <button type="button" onclick="settingsManager.testEmail()" class="btn-secondary btn-sm">
                            {{ __('Testar') }}
                        </button>
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="email_notifications" name="email_notifications"
                                   {{ $settings['email_notifications'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                {{ __('Ativar Notificações por Email') }}
                            </span>
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="smtp_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Servidor SMTP') }}
                                </label>
                                <input type="text" id="smtp_host" name="smtp_host"
                                       value="{{ $settings['smtp_host'] }}"
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                       placeholder="smtp.gmail.com">
                            </div>

                            <div>
                                <label for="smtp_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Porta SMTP') }}
                                </label>
                                <input type="number" id="smtp_port" name="smtp_port"
                                       value="{{ $settings['smtp_port'] }}" min="1" max="65535"
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label for="smtp_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Utilizador SMTP') }}
                                </label>
                                <input type="text" id="smtp_username" name="smtp_username"
                                       value="{{ $settings['smtp_username'] }}"
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label for="smtp_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Palavra-passe SMTP') }}
                                </label>
                                <input type="password" id="smtp_password" name="smtp_password"
                                       value="{{ $settings['smtp_password'] }}"
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Encriptação') }}
                                </label>
                                <select id="smtp_encryption" name="smtp_encryption"
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="tls" {{ $settings['smtp_encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $settings['smtp_encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba API & ESP32 -->
        <div id="tab-api" class="settings-tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('Configurações API & ESP32') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="api_rate_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Limite de Requisições API (por minuto)') }}
                        </label>
                        <input type="number" id="api_rate_limit" name="api_rate_limit"
                               value="{{ $settings['api_rate_limit'] }}" min="10" max="1000"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    <div>
                        <label for="esp32_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Timeout ESP32 (segundos)') }}
                        </label>
                        <input type="number" id="esp32_timeout" name="esp32_timeout"
                               value="{{ $settings['esp32_timeout'] }}" min="5" max="300"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba Manutenção -->
        <div id="tab-maintenance" class="settings-tab-content hidden">
            <div class="space-y-6">
                <!-- Logs -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        {{ __('Gestão de Logs') }}
                    </h3>

                    <div>
                        <label for="log_retention_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Retenção de Logs (dias)') }}
                        </label>
                        <input type="number" id="log_retention_days" name="log_retention_days"
                               value="{{ $settings['log_retention_days'] }}" min="7" max="365"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('Logs mais antigos serão automaticamente removidos') }}
                        </p>
                    </div>
                </div>

                <!-- Backup -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        {{ __('Backup Automático') }}
                    </h3>

                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="backup_enabled" name="backup_enabled"
                                   {{ $settings['backup_enabled'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                {{ __('Ativar Backup Automático') }}
                            </span>
                        </label>

                        <div>
                            <label for="backup_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Frequência do Backup') }}
                            </label>
                            <select id="backup_frequency" name="backup_frequency"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="daily" {{ $settings['backup_frequency'] === 'daily' ? 'selected' : '' }}>{{ __('Diário') }}</option>
                                <option value="weekly" {{ $settings['backup_frequency'] === 'weekly' ? 'selected' : '' }}>{{ __('Semanal') }}</option>
                                <option value="monthly" {{ $settings['backup_frequency'] === 'monthly' ? 'selected' : '' }}>{{ __('Mensal') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="settingsManager.resetForm()" class="btn-secondary">
                    {{ __('Cancelar') }}
                </button>
                <button type="submit" class="btn-success" id="save-settings-btn">
                    {{ __('Guardar Configurações') }}
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal de Importação -->
<div id="import-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="settingsManager.closeImportModal()"></div>

        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Importar Configurações') }}
                </h3>
                <button onclick="settingsManager.closeImportModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="import-form" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="settings_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Arquivo de Configurações') }}
                    </label>
                    <input type="file" id="settings_file" name="settings_file" accept=".json"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('Selecione um arquivo JSON exportado anteriormente') }}
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="settingsManager.closeImportModal()" class="btn-secondary">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="btn-primary">
                        {{ __('Importar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="settings-toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
// Settings Manager JavaScript
class SettingsManager {
    constructor() {
        this.currentTab = 'general';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.showTab('general');
    }

    setupEventListeners() {
        // Form submission
        document.getElementById('settings-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveSettings();
        });

        // Import form submission
        document.getElementById('import-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.importSettings();
        });
    }

    showTab(tabName) {
        // Ocultar todas as abas
        document.querySelectorAll('.settings-tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Remover classe ativa de todas as abas
        document.querySelectorAll('.settings-tab').forEach(tab => {
            tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        // Mostrar aba selecionada
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');

        // Ativar aba selecionada
        const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
        activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

        this.currentTab = tabName;
    }

    async saveSettings() {
        const form = document.getElementById('settings-form');
        const formData = new FormData(form);
        const data = {};

        // Converter FormData para objeto
        for (let [key, value] of formData.entries()) {
            if (key !== '_token') {
                // Converter checkboxes para boolean
                if (form.querySelector(`[name="${key}"]`).type === 'checkbox') {
                    data[key] = form.querySelector(`[name="${key}"]`).checked;
                } else {
                    data[key] = value;
                }
            }
        }

        const button = document.getElementById('save-settings-btn');
        const originalText = button.textContent;
        button.disabled = true;
        button.innerHTML = '<span class="loading-spinner mr-2"></span>Guardando...';

        try {
            const response = await fetch('/admin/settings', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showToast('Configurações guardadas com sucesso', 'success');
            } else {
                throw new Error(result.message || 'Erro ao guardar configurações');
            }

        } catch (error) {
            console.error('Erro ao guardar configurações:', error);
            this.showToast(error.message || 'Erro ao guardar configurações', 'error');
        } finally {
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    async resetToDefaults() {
        if (!confirm('Restaurar todas as configurações para os valores padrão? Esta ação não pode ser desfeita.')) {
            return;
        }

        try {
            const response = await fetch('/admin/settings/reset', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showToast('Configurações restauradas para os valores padrão', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                throw new Error(result.message || 'Erro ao restaurar configurações');
            }

        } catch (error) {
            console.error('Erro ao restaurar configurações:', error);
            this.showToast(error.message || 'Erro ao restaurar configurações', 'error');
        }
    }

    async exportSettings() {
        try {
            const response = await fetch('/admin/settings/export', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `iotcnt_settings_${new Date().toISOString().slice(0, 10)}.json`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                this.showToast('Configurações exportadas com sucesso', 'success');
            } else {
                throw new Error('Erro na exportação');
            }

        } catch (error) {
            console.error('Erro na exportação:', error);
            this.showToast('Erro ao exportar configurações', 'error');
        }
    }

    openImportModal() {
        document.getElementById('import-modal').classList.remove('hidden');
    }

    closeImportModal() {
        document.getElementById('import-modal').classList.add('hidden');
        document.getElementById('import-form').reset();
    }

    async importSettings() {
        const form = document.getElementById('import-form');
        const formData = new FormData(form);

        try {
            const response = await fetch('/admin/settings/import', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showToast(result.message, 'success');
                this.closeImportModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                throw new Error(result.message || 'Erro na importação');
            }

        } catch (error) {
            console.error('Erro na importação:', error);
            this.showToast(error.message || 'Erro ao importar configurações', 'error');
        }
    }

    async testEmail() {
        try {
            const response = await fetch('/admin/settings/test-email', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showToast('Email de teste enviado com sucesso', 'success');
            } else {
                throw new Error(result.message || 'Erro no teste de email');
            }

        } catch (error) {
            console.error('Erro no teste de email:', error);
            this.showToast(error.message || 'Erro no teste de email', 'error');
        }
    }

    async testTelegram() {
        try {
            const response = await fetch('/admin/settings/test-telegram', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                let message = 'Conexão com Telegram testada com sucesso';
                if (result.bot_info) {
                    message += `\nBot: ${result.bot_info.name} (@${result.bot_info.username})`;
                }
                this.showToast(message, 'success');
            } else {
                throw new Error(result.message || 'Erro no teste do Telegram');
            }

        } catch (error) {
            console.error('Erro no teste do Telegram:', error);
            this.showToast(error.message || 'Erro no teste do Telegram', 'error');
        }
    }

    resetForm() {
        if (confirm('Descartar alterações não guardadas?')) {
            window.location.reload();
        }
    }

    showToast(message, type = 'info') {
        const container = document.getElementById('settings-toast-container');
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
}

// Inicializar Settings Manager
const settingsManager = new SettingsManager();
</script>

<style>
.settings-tab.active {
    @apply border-blue-500 text-blue-600;
}

.settings-tab:not(.active) {
    @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
}
</style>
@endsection
