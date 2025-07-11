<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Log de Operação') }} #{{ $log->id }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Informações Gerais') }}</h3>
                    <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('ID do Log') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $log->id }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Data/Hora') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($log->logged_at)->format('d/m/Y H:i:s') }} ({{ \Carbon\Carbon::parse($log->logged_at)->diffForHumans() }})</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Tipo de Evento') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $log->event_type }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Origem') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $log->source }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                            <dd>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($log->status)
                                        @case('SUCCESS') bg-green-100 text-green-800 @break
                                        @case('INFO') bg-blue-100 text-blue-800 @break
                                        @case('WARNING') bg-yellow-100 text-yellow-800 @break
                                        @case('ERROR') bg-red-100 text-red-800 @break
                                        @case('CRITICAL') bg-pink-100 text-pink-800 @break
                                        @default bg-gray-100 text-gray-800 @break
                                    @endswitch
                                ">
                                    {{ $log->status }}
                                </span>
                            </dd>
                        </div>
                        @if($log->duration_seconds !== null)
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Duração (segundos)') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $log->duration_seconds }}s</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Mensagem') }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-3 rounded-md whitespace-pre-wrap">{{ $log->message }}</p>

                    @if($log->valve)
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Válvula Associada') }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        <a href="{{ route('admin.valves.show', $log->valve) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $log->valve->name }} (Nº {{ $log->valve->valve_number }})
                        </a>
                    </p>
                    @endif

                    @if($log->schedule)
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Agendamento Associado') }}</h3>
                     <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        <a href="{{ route('admin.schedules.edit', $log->schedule) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $log->schedule->name }}
                        </a>
                    </p>
                    @endif

                    @if($log->user)
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Utilizador Web Associado') }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        <a href="{{ route('admin.users.show', $log->user) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $log->user->name }} ({{ $log->user->email }})
                        </a>
                    </p>
                    @endif

                    @if($log->telegramUser)
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Utilizador Telegram Associado') }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        <a href="{{ route('admin.telegram-users.edit', $log->telegramUser) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $log->telegramUser->first_name }} {{ $log->telegramUser->telegram_username ? '(@' . $log->telegramUser->telegram_username . ')' : '' }} (Chat ID: {{ $log->telegramUser->telegram_chat_id }})
                        </a>
                    </p>
                    @endif
                </div>
            </div>

            @if ($log->details)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Detalhes Adicionais (JSON)') }}</h3>
                <pre class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-200 rounded-md overflow-x-auto"><code class="language-json">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
            @endif


            <div class="mt-8 flex items-center justify-start">
                <a href="{{ route('admin.operation-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Voltar à Lista de Logs') }}
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
