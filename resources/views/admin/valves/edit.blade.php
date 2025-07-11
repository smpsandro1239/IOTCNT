<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Válvula') }}: {{ $valve->name }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('admin.valves.update', $valve->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- ou PATCH --}}

                <!-- Nome da Válvula -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nome da Válvula') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $valve->name) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Número da Válvula -->
                <div class="mb-4">
                    <label for="valve_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Número da Válvula (1-5)') }}</label>
                    <input type="number" name="valve_number" id="valve_number" value="{{ old('valve_number', $valve->valve_number) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('valve_number') border-red-500 @enderror"
                           required min="1" max="5">
                    @error('valve_number')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pino ESP32 -->
                <div class="mb-4">
                    <label for="esp32_pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Pino GPIO no ESP32 (Opcional)') }}</label>
                    <input type="number" name="esp32_pin" id="esp32_pin" value="{{ old('esp32_pin', $valve->esp32_pin) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('esp32_pin') border-red-500 @enderror"
                           min="0">
                    @error('esp32_pin')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descrição -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Descrição (Opcional)') }}</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('description') border-red-500 @enderror">{{ old('description', $valve->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botões de Ação -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.valves.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Atualizar Válvula') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
