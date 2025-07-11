<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Novo Agendamento') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('admin.schedules.store') }}" method="POST">
                @csrf
                @php
                    $daysOfWeek = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                @endphp

                <!-- Nome do Agendamento -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nome do Agendamento') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', 'Rega Semanal Principal') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dia da Semana -->
                <div class="mb-4">
                    <label for="day_of_week" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Dia da Semana') }}</label>
                    <select name="day_of_week" id="day_of_week"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('day_of_week') border-red-500 @enderror"
                            required>
                        @foreach ($daysOfWeek as $index => $dayName)
                            <option value="{{ $index }}" {{ old('day_of_week', 5) == $index ? 'selected' : '' }}>{{ $dayName }}</option>
                        @endforeach
                    </select>
                    @error('day_of_week')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hora de Início -->
                <div class="mb-4">
                    <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Hora de Início (HH:MM)') }}</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '10:00') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('start_time') border-red-500 @enderror"
                           required>
                    @error('start_time')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duração por Válvula -->
                <div class="mb-4">
                    <label for="per_valve_duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Duração por Válvula (minutos)') }}</label>
                    <input type="number" name="per_valve_duration_minutes" id="per_valve_duration_minutes" value="{{ old('per_valve_duration_minutes', 5) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200 @error('per_valve_duration_minutes') border-red-500 @enderror"
                           required min="1">
                    @error('per_valve_duration_minutes')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ativo/Inativo -->
                <div class="mb-6">
                    <label for="is_enabled" class="flex items-center">
                        <input type="checkbox" name="is_enabled" id="is_enabled" value="1" {{ old('is_enabled', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Ativar este agendamento') }}</span>
                    </label>
                     @error('is_enabled')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Botões de Ação -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Guardar Agendamento') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
