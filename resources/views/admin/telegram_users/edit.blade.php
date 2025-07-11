<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gerir Autorização do Utilizador Telegram') }}: {{ $telegramUser->telegram_username ? '@' . $telegramUser->telegram_username : $telegramUser->telegram_chat_id }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('admin.telegram-users.update', $telegramUser->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Chat ID Telegram') }}</p>
                        <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $telegramUser->telegram_chat_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Username Telegram') }}</p>
                        <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $telegramUser->telegram_username ? '@' . $telegramUser->telegram_username : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nome Telegram') }}</p>
                        <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $telegramUser->first_name }} {{ $telegramUser->last_name }}</p>
                    </div>
                     <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Registado em (Bot)') }}</p>
                        <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $telegramUser->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- Utilizador Web Vinculado -->
                <div class="mb-4">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Vincular a Utilizador Web (Opcional)') }}</label>
                    <select name="user_id" id="user_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('user_id') border-red-500 @enderror">
                        <option value="">{{ __('-- Nenhum --') }}</option>
                        @foreach ($webUsers as $webUser)
                            <option value="{{ $webUser->id }}" {{ old('user_id', $telegramUser->user_id) == $webUser->id ? 'selected' : '' }}>
                                {{ $webUser->name }} ({{ $webUser->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Autorizado -->
                <div class="mb-4">
                    <label for="is_authorized" class="flex items-center">
                        <input type="hidden" name="is_authorized" value="0"> <!-- Valor default se checkbox não for marcado -->
                        <input type="checkbox" name="is_authorized" id="is_authorized" value="1" {{ old('is_authorized', $telegramUser->is_authorized) ? 'checked' : '' }}
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Autorizar este utilizador a interagir com o bot') }}</span>
                    </label>
                     @error('is_authorized')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nível de Autorização Telegram -->
                <div class="mb-4">
                    <label for="authorization_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nível de Autorização para Comandos Telegram') }}</label>
                    <select name="authorization_level" id="authorization_level"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('authorization_level') border-red-500 @enderror">
                        <option value="">{{ __('-- Nenhum --') }}</option>
                        <option value="user" {{ old('authorization_level', $telegramUser->authorization_level) == 'user' ? 'selected' : '' }}>{{ __('Utilizador (comandos básicos)') }}</option>
                        <option value="admin" {{ old('authorization_level', $telegramUser->authorization_level) == 'admin' ? 'selected' : '' }}>{{ __('Administrador (todos os comandos)') }}</option>
                    </select>
                    @error('authorization_level')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Receber Notificações -->
                 <div class="mb-6">
                    <label for="receive_notifications" class="flex items-center">
                        <input type="hidden" name="receive_notifications" value="0">
                        <input type="checkbox" name="receive_notifications" id="receive_notifications" value="1" {{ old('receive_notifications', $telegramUser->receive_notifications) ? 'checked' : '' }}
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Permitir que este utilizador receba notificações do bot') }}</span>
                    </label>
                     @error('receive_notifications')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botões de Ação -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.telegram-users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Guardar Alterações') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
