@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Utilizadores Telegram') }}
    </h2>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Total</h3>
                    <p class="text-2xl font-bold">{{ $telegramUsers->total() }}</p>
                </div>
                <div class="text-3xl opacity-75">👥</div>
            </div>
        </div>

        <div class="bg-green-500 text-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Autorizados</h3>
                    <p class="text-2xl font-bold">{{ $telegramUsers->where('is_authorized', true)->count() }}</p>
                </div>
                <div class="text-3xl opacity-75">✅</div>
            </div>
        </div>

        <div class="bg-orange-500 text-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Administradores</h3>
                    <p class="text-2xl font-bold">{{ $telegramUsers->where('authorization_level', 'admin')->count() }}</p>
                </div>
                <div class="text-3xl opacity-75">👑</div>
            </div>
        </div>
    </div>

    <!-- Lista de Utilizadores -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Utilizador
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Chat ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Utilizador Web
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Registado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                    @forelse ($telegramUsers as $telegramUser)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $telegramUser->first_name }} {{ $telegramUser->last_name }}
                                        </div>
                                        @if($telegramUser->telegram_username)
                                            <div class="text-sm text-gray-500">
                                                @{{ $telegramUser->telegram_username }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <code class="bg-gray-100 px-2 py-1 rounded">{{ $telegramUser->telegram_chat_id }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $telegramUser->is_authorized ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $telegramUser->is_authorized ? 'Autorizado' : 'Não Autorizado' }}
                                    </span>
                                    @if($telegramUser->authorization_level)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($telegramUser->authorization_level) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                @if($telegramUser->user)
                                    <div>
                                        <div class="font-medium">{{ $telegramUser->user->name }}</div>
                                        <div class="text-gray-500">{{ $telegramUser->user->email }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">Não vinculado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $telegramUser->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(!$telegramUser->is_authorized)
                                        <form method="POST" action="{{ route('admin.telegram-users.authorize', $telegramUser) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Autorizar">
                                                ✅
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.telegram-users.revoke', $telegramUser) }}" class="inline" onsubmit="return confirm('Revogar autorização?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" title="Revogar">
                                                🚫
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.telegram-users.edit', $telegramUser) }}" class="text-blue-600 hover:text-blue-900" title="Editar">
                                        ✏️
                                    </a>

                                    <form method="POST" action="{{ route('admin.telegram-users.destroy', $telegramUser) }}" class="inline" onsubmit="return confirm('Eliminar utilizador?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Nenhum utilizador Telegram encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($telegramUsers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $telegramUsers->links() }}
            </div>
        @endif
    </div>

    <!-- Informações -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="font-semibold text-blue-800 mb-2">ℹ️ Informações</h4>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Novos utilizadores são automaticamente registados quando interagem com o bot</li>
            <li>• Utilizadores precisam ser autorizados manualmente por um administrador</li>
            <li>• Administradores podem executar comandos de controlo do sistema</li>
            <li>• Utilizadores normais podem apenas consultar informações</li>
        </ul>
    </div>
</div>
@endsection
