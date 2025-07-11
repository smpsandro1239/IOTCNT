<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes da Válvula') }}: {{ $valve->name }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Informações da Válvula') }}</h3>
                    <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('ID') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $valve->id }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Nome') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $valve->name }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Número da Válvula') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $valve->valve_number }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Pino GPIO no ESP32') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $valve->esp32_pin ?? '-' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Descrição') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100 prose dark:prose-invert max-w-none">
                                {{ $valve->description ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Estado e Operação') }}</h3>
                    <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Estado Atual') }}</dt>
                            <dd>
                                @if ($valve->current_state)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ __('Ligada') }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ __('Desligada') }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Última Ativação') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $valve->last_activated_at ? $valve->last_activated_at->format('d/m/Y H:i:s') : 'Nunca ativada' }}</dd>
                        </div>
                         <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Criada em') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $valve->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Atualizada em') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $valve->updated_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.valves.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Voltar à Lista') }}
                </a>
                <a href="{{ route('admin.valves.edit', $valve->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Editar Válvula') }}
                </a>
            </div>

            {{-- Futuramente, poderia adicionar aqui uma secção com os últimos logs de operação desta válvula --}}
            {{-- Ex: @include('admin.valves.partials.operation-logs-for-valve', ['logs' => $valve->operationLogs()->latest()->take(10)->get()]) --}}

        </div>
    </div>
</x-admin-layout>
