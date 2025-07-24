@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Logs de Operação') }}
        </h2>
        <div class="flex space-x-2">
            <button onclick="toggleFilters()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Filtros
            </button>
            <form method="POST" action="{{ route('admin.logs.bulk-delete') }}" class="inline" onsubmit="return confirm('Tem certeza que deseja eliminar logs antigos?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="days_old" value="30">
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Limpar Logs (30+ dias)
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filtros -->
    <div id="filters" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hidden">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fonte</label>
                <select name="source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Todas</option>
                    <option value="SYSTEM" {{ request('source') === 'SYSTEM' ? 'selected' : '' }}>Sistema</option>
                    <option value="ESP32" {{ request('source') === 'ESP32' ? 'selected' : '' }}>ESP32</option>
                    <option value="WEB_PORTAL" {{ request('source') === 'WEB_PORTAL' ? 'selected' : '' }}>Portal Web</option>
                    <option value="TELEGRAM_BOT" {{ request('source') === 'TELEGRAM_BOT' ? 'selected' : '' }}>Telegram</option>
                    <option value="SCHEDULED_TASK" {{ request('source') === 'SCHEDULED_TASK' ? 'selected' : '' }}>Agendamento</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Todos</option>
                    <option value="SUCCESS" {{ request('status') === 'SUCCESS' ? 'selected' : '' }}>Sucesso</option>
                    <option value="INFO" {{ request('status') === 'INFO' ? 'selected' : '' }}>Info</option>
                    <option value="WARNING" {{ request('status') === 'WARNING' ? 'selected' : '' }}>Aviso</option>
                    <option value="ERROR" {{ request('status') === 'ERROR' ? 'selected' : '' }}>Erro</option>
                    <option value="CRITICAL" {{ request('status') === 'CRITICAL' ? 'selected' : '' }}>Crítico</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data Início</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data Fim</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div class="md:col-span-2 lg:col-span-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Aplicar Filtros
                </button>
                <a href="{{ route('admin.logs.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Lista de Logs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Data/Hora
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tipo/Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Mensagem
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Fonte
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $log->logged_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $log->event_type }}
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $log->status === 'SUCCESS' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->status === 'INFO' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->status === 'WARNING' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $log->status === 'ERROR' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $log->status === 'CRITICAL' ? 'bg-red-200 text-red-900' : '' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ Str::limit($log->message, 100) }}
                                @if($log->valve)
                                    <div class="text-xs text-gray-500">Válvula: {{ $log->valve->name }}</div>
                                @endif
                                @if($log->user)
                                    <div class="text-xs text-gray-500">Utilizador: {{ $log->user->name }}</div>
                                @endif
                                @if($log->telegramUser)
                                    <div class="text-xs text-gray-500">Telegram: {{ $log->telegramUser->first_name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $log->source }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.logs.show', $log) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    Ver
                                </a>
                                <form method="POST" action="{{ route('admin.logs.destroy', $log) }}" class="inline" onsubmit="return confirm('Tem certeza?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Nenhum log encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleFilters() {
    const filters = document.getElementById('filters');
    filters.classList.toggle('hidden');
}
</script>
@endsection
