<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Logs de Operação do Sistema') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Área de Filtros (Placeholder - pode ser implementada depois) --}}
        {{--
        <div class="mb-6 p-4 bg-white dark:bg-gray-800 shadow-md rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Filtros') }}</h3>
            <form action="{{ route('admin.operation-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div>
                    <label for="event_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tipo de Evento') }}</label>
                    <input type="text" name="event_type" id="event_type" value="{{ request('event_type') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                </div>
                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Origem') }}</label>
                    <select name="source" id="source" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="SYSTEM" {{ request('source') == 'SYSTEM' ? 'selected' : '' }}>{{ __('Sistema') }}</option>
                        <option value="ESP32" {{ request('source') == 'ESP32' ? 'selected' : '' }}>{{ __('ESP32') }}</option>
                        <option value="WEB_PORTAL" {{ request('source') == 'WEB_PORTAL' ? 'selected' : '' }}>{{ __('Portal Web') }}</option>
                        <option value="TELEGRAM_BOT" {{ request('source') == 'TELEGRAM_BOT' ? 'selected' : '' }}>{{ __('Bot Telegram') }}</option>
                        <option value="SCHEDULED_TASK" {{ request('source') == 'SCHEDULED_TASK' ? 'selected' : '' }}>{{ __('Tarefa Agendada') }}</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                     <select name="status" id="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="SUCCESS" {{ request('status') == 'SUCCESS' ? 'selected' : '' }}>{{ __('Sucesso') }}</option>
                        <option value="INFO" {{ request('status') == 'INFO' ? 'selected' : '' }}>{{ __('Info') }}</option>
                        <option value="WARNING" {{ request('status') == 'WARNING' ? 'selected' : '' }}>{{ __('Aviso') }}</option>
                        <option value="ERROR" {{ request('status') == 'ERROR' ? 'selected' : '' }}>{{ __('Erro') }}</option>
                        <option value="CRITICAL" {{ request('status') == 'CRITICAL' ? 'selected' : '' }}>{{ __('Crítico') }}</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                        {{ __('Filtrar') }}
                    </button>
                    <a href="{{ route('admin.operation-logs.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                        {{ __('Limpar') }}
                    </a>
                </div>
            </form>
        </div>
        --}}

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Data/Hora') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Tipo de Evento') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Mensagem') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Origem') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">{{ __('Ações') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($log->logged_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $log->event_type }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 truncate max-w-xs">
                                {{ Str::limit($log->message, 70) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ $log->source }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.operation-logs.show', $log) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">{{ __('Ver Detalhes') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                                {{ __('Nenhum log de operação encontrado.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $logs->appends(request()->query())->links() }} {{-- Manter filtros na paginação --}}
        </div>
    </div>
</x-admin-layout>
