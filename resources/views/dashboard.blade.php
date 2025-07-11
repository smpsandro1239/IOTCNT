<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Painel de Controlo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Bem-vindo(a) ao seu painel de controlo!") }}
                </div>
            </div>

            <!-- Secção de Estado das Válvulas -->
            <div class="mt-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Estado Atual das Válvulas') }}
                </h3>
                @if(isset($valvesStatus) && $valvesStatus->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($valvesStatus as $valve)
                            <div class="p-4 rounded-md {{ $valve->current_state ? 'bg-green-100 dark:bg-green-700' : 'bg-red-100 dark:bg-red-700' }}">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-semibold text-md {{ $valve->current_state ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
                                        Válvula {{ $valve->valve_number }}: {{ $valve->name }}
                                    </h4>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $valve->current_state ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                        {{ $valve->current_state ? __('Ligada') : __('Desligada') }}
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
