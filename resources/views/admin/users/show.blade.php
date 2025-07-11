<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Utilizador') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Informações do Utilizador') }}</h3>
                    <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('ID') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->id }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Nome') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->name }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Email') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->email }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Papel (Role)') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Email Verificado em') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i:s') : 'Não verificado' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Registado em') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Atualizado em') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Pode adicionar mais secções aqui, como logs de atividade do utilizador, etc. --}}

            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Voltar à Lista') }}
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Editar Utilizador') }}
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
