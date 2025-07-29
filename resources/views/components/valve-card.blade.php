@props(['valve'])

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
