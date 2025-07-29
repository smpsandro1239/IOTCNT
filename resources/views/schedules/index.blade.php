<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Agendamentos de Irrigação') }}
                </h2>
                <div class="status-indicator status-online"></div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="scheduleManager.refreshSchedules()" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    {{ __('Atualizar') }}
                </button>
                <button onclick="scheduleManager.openCreateModal()" class="btn-success">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('Novo Agendamento') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Resumo dos Agendamentos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-stat-card
                    title="{{ __('Total de Agendamentos') }}"
                    :value="$schedules->count()"
                    subtitle="{{ __('configurados') }}"
                    icon="operations"
                    color="blue"
                    id="total-schedules"
                />

                <x-stat-card
                    title="{{ __('Agendamentos Ativos') }}"
                    :value="$schedules->where('is_active', true)->count()"
                    subtitle="{{ __('em funcionamento') }}"
                    icon="valves"
                    color="green"
                    id="active-schedules"
                />

                <x-stat-card
                    title="{{ __('Próximo Agendamento') }}"
                    :value="$nextSchedule ? $nextSchedule['time_until'] : __('Nenhum')"
                    subtitle="{{ $nextSchedule ? $nextSchedule['schedule']->name : '' }}"
                    icon="time"
                    color="purple"
                    id="next-schedule"
                />

                <x-stat-card
                    title="{{ __('Duração Total/Dia') }}"
                    :value="$totalDailyDuration . ' min'"
                    subtitle="{{ __('tempo estimado') }}"
                    icon="time"
                    color="yellow"
                    id="daily-duration"
                />
            </div>

            <!-- Calendário Semanal -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Calendário Semanal') }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Semana atual') }}</span>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-xs text-gray-400">{{ __('Ativo') }}</span>
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                        <span class="text-xs text-gray-400">{{ __('Inativo') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-2 mb-4">
                    @php
                        $daysOfWeek = [
                            0 => ['name' => 'Domingo', 'short' => 'Dom'],
                            1 => ['name' => 'Segunda', 'short' => 'Seg'],
                            2 => ['name' => 'Terça', 'short' => 'Ter'],
                            3 => ['name' => 'Quarta', 'short' => 'Qua'],
                            4 => ['name' => 'Quinta', 'short' => 'Qui'],
                            5 => ['name' => 'Sexta', 'short' => 'Sex'],
                            6 => ['name' => 'Sábado', 'short' => 'Sáb']
                        ];
                    @endphp

                    @foreach($daysOfWeek as $dayNum => $day)
                        <div class="text-center">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">{{ $day['short'] }}</h4>
                            <div class="min-h-32 bg-gray-50 dark:bg-gray-700 rounded-lg p-2 space-y-1">
                                @foreach($schedules->where('day_of_week', $dayNum) as $schedule)
                                    <div class="schedule-item {{ $schedule->is_active ? 'bg-green-100 dark:bg-green-900 border-green-300 dark:border-green-700' : 'bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500' }} border rounded p-2 cursor-pointer hover:shadow-md transition-all duration-200"
                                         onclick="scheduleManager.openEditModal({{ $schedule->id }})"
                                         data-schedule-id="{{ $schedule->id }}">
                                        <div class="text-xs font-medium {{ $schedule->is_active ? 'text-green-800 dark:text-green-200' : 'text-gray-600 dark:text-gray-300' }}">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                        </div>
                                        <div class="text-xs {{ $schedule->is_active ? 'text-green-600 dark:text-green-300' : 'text-gray-500 dark:text-gray-400' }} truncate" title="{{ $schedule->name }}">
                                            {{ Str::limit($schedule->name, 15) }}
                                        </div>
                                        <div class="text-xs {{ $schedule->is_active ? 'text-green-500 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                                            {{ $schedule->per_valve_duration_minutes }}min
                                        </div>
                                    </div>
                                @endforeach

                                @if($schedules->where('day_of_week', $dayNum)->isEmpty())
                                    <div class="text-center py-4">
                                        <button onclick="scheduleManager.openCreateModal({{ $dayNum }})"
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                            <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <div class="text-xs">{{ __('Adicionar') }}</div>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Lista Detalhada de Agendamentos -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        {{ __('Todos os Agendamentos') }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <select id="filter-day" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 bg-white dark:bg-gray-700">
                            <option value="">{{ __('Todos os dias') }}</option>
                            @foreach($daysOfWeek as $dayNum => $day)
                                <option value="{{ $dayNum }}">{{ $day['name'] }}</option>
                            @endforeach
                        </select>
                        <select id="filter-status" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 bg-white dark:bg-gray-700">
                            <option value="">{{ __('Todos os estados') }}</option>
                            <option value="1">{{ __('Ativos') }}</option>
                            <option value="0">{{ __('Inativos') }}</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Nome') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Dia da Semana') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Hora') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Duração') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Estado') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Próxima Execução') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Ações') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="schedules-table-body">
                            @forelse($schedules as $schedule)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" data-schedule-id="{{ $schedule->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full mr-3 {{ $schedule->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $schedule->name }}
                                                </div>
                                                @if($schedule->description)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ Str::limit($schedule->description, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $daysOfWeek[$schedule->day_of_week]['name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $schedule->per_valve_duration_minutes }} {{ __('minutos') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $schedule->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                            {{ $schedule->is_active ? __('Ativo') : __('Inativo') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @php
                                            $nextExecution = $schedule->getNextExecution();
                                        @endphp
                                        {{ $nextExecution ? $nextExecution->diffForHumans() : __('N/A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button onclick="scheduleManager.toggleSchedule({{ $schedule->id }})"
                                                class="btn-warning btn-sm">
                                            {{ $schedule->is_active ? __('Desativar') : __('Ativar') }}
                                        </button>
                                        <button onclick="scheduleManager.openEditModal({{ $schedule->id }})"
                                                class="btn-primary btn-sm">
                                            {{ __('Editar') }}
                                        </button>
                                        <button onclick="scheduleManager.deleteSchedule({{ $schedule->id }})"
                                                class="btn-danger btn-sm">
                                            {{ __('Eliminar') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="text-gray-500 dark:text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0v-4m4-4h8m-4-4v8"></path>
                                            </svg>
                                            <p class="text-lg font-medium mb-2">{{ __('Nenhum agendamento configurado') }}</p>
                                            <p class="text-sm mb-4">{{ __('Crie o seu primeiro agendamento para automatizar a irrigação') }}</p>
                                            <button onclick="scheduleManager.openCreateModal()" class="btn-primary">
                                                {{ __('Criar Agendamento') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Criar/Editar Agendamento -->
    <div id="schedule-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="scheduleManager.closeModal()"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                        {{ __('Novo Agendamento') }}
                    </h3>
                    <button onclick="scheduleManager.closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="schedule-form" class="space-y-4">
                    <input type="hidden" id="schedule-id" name="id">

                    <div>
                        <label for="schedule-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Nome do Agendamento') }}
                        </label>
                        <input type="text" id="schedule-name" name="name" required
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                               placeholder="{{ __('Ex: Rega Matinal') }}">
                    </div>

                    <div>
                        <label for="schedule-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Descrição (opcional)') }}
                        </label>
                        <textarea id="schedule-description" name="description" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                  placeholder="{{ __('Descrição do agendamento...') }}"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="schedule-day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Dia da Semana') }}
                            </label>
                            <select id="schedule-day" name="day_of_week" required
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                @foreach($daysOfWeek as $dayNum => $day)
                                    <option value="{{ $dayNum }}">{{ $day['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="schedule-time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Hora') }}
                            </label>
                            <input type="time" id="schedule-time" name="start_time" required
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label for="schedule-duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Duração por Válvula (minutos)') }}
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="range" id="schedule-duration" name="per_valve_duration_minutes"
                                   min="1" max="30" value="5"
                                   class="flex-1"
                                   oninput="document.getElementById('duration-display').textContent = this.value">
                            <span id="duration-display" class="text-sm font-medium text-gray-900 dark:text-gray-100 w-8">5</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">min</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('Tempo total estimado:') }} <span id="total-duration">25</span> {{ __('minutos') }}
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="schedule-active" name="is_active" checked
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="schedule-active" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                            {{ __('Agendamento ativo') }}
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="scheduleManager.closeModal()" class="btn-secondary">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="submit" class="btn-success" id="save-schedule-btn">
                            {{ __('Guardar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        // Schedule Manager JavaScript
        class ScheduleManager {
            constructor() {
                this.currentScheduleId = null;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.updateTotalDuration();
            }

            setupEventListeners() {
                // Filtros
                document.getElementById('filter-day').addEventListener('change', () => this.applyFilters());
                document.getElementById('filter-status').addEventListener('change', () => this.applyFilters());

                // Form submission
                document.getElementById('schedule-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.saveSchedule();
                });

                // Duration slider
                document.getElementById('schedule-duration').addEventListener('input', () => {
                    this.updateTotalDuration();
                });
            }

            updateTotalDuration() {
                const duration = document.getElementById('schedule-duration').value;
                const totalValves = 5; // Assumindo 5 válvulas
                const total = duration * totalValves;
                document.getElementById('total-duration').textContent = total;
            }

            applyFilters() {
                const dayFilter = document.getElementById('filter-day').value;
                const statusFilter = document.getElementById('filter-status').value;
                const rows = document.querySelectorAll('#schedules-table-body tr[data-schedule-id]');

                rows.forEach(row => {
                    let show = true;

                    if (dayFilter) {
                        // Implementar filtro por dia
                        // Esta lógica precisa ser ajustada baseada nos dados da linha
                    }

                    if (statusFilter) {
                        // Implementar filtro por status
                        // Esta lógica precisa ser ajustada baseada nos dados da linha
                    }

                    row.style.display = show ? '' : 'none';
                });
            }

            openCreateModal(dayOfWeek = null) {
                this.currentScheduleId = null;
                document.getElementById('modal-title').textContent = 'Novo Agendamento';
                document.getElementById('schedule-form').reset();

                if (dayOfWeek !== null) {
                    document.getElementById('schedule-day').value = dayOfWeek;
                }

                document.getElementById('schedule-modal').classList.remove('hidden');
                document.getElementById('schedule-name').focus();
            }

            async openEditModal(scheduleId) {
                this.currentScheduleId = scheduleId;
                document.getElementById('modal-title').textContent = 'Editar Agendamento';

                try {
                    // Buscar dados do agendamento
                    const response = await fetch(`/schedules/${scheduleId}/edit`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (!response.ok) throw new Error('Erro ao carregar agendamento');

                    const data = await response.json();
                    const schedule = data.schedule;

                    // Preencher formulário
                    document.getElementById('schedule-id').value = schedule.id;
                    document.getElementById('schedule-name').value = schedule.name;
                    document.getElementById('schedule-description').value = schedule.description || '';
                    document.getElementById('schedule-day').value = schedule.day_of_week;
                    document.getElementById('schedule-time').value = schedule.start_time;
                    document.getElementById('schedule-duration').value = schedule.per_valve_duration_minutes;
                    document.getElementById('schedule-active').checked = schedule.is_active;

                    this.updateTotalDuration();
                    document.getElementById('schedule-modal').classList.remove('hidden');

                } catch (error) {
                    console.error('Erro ao carregar agendamento:', error);
                    this.showToast('Erro ao carregar agendamento', 'error');
                }
            }

            closeModal() {
                document.getElementById('schedule-modal').classList.add('hidden');
                this.currentScheduleId = null;
            }

            async saveSchedule() {
                const form = document.getElementById('schedule-form');
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Converter checkbox para boolean
                data.is_active = document.getElementById('schedule-active').checked;

                const button = document.getElementById('save-schedule-btn');
                const originalText = button.textContent;
                button.disabled = true;
                button.innerHTML = '<span class="loading-spinner mr-2"></span>Guardando...';

                try {
                    const url = this.currentScheduleId ?
                        `/schedules/${this.currentScheduleId}` :
                        '/schedules';

                    const method = this.currentScheduleId ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showToast(
                            this.currentScheduleId ? 'Agendamento atualizado com sucesso' : 'Agendamento criado com sucesso',
                            'success'
                        );
                        this.closeModal();
                        this.refreshSchedules();
                    } else {
                        throw new Error(result.message || 'Erro ao guardar agendamento');
                    }

                } catch (error) {
                    console.error('Erro ao guardar agendamento:', error);
                    this.showToast(error.message || 'Erro ao guardar agendamento', 'error');
                } finally {
                    button.disabled = false;
                    button.textContent = originalText;
                }
            }

            async toggleSchedule(scheduleId) {
                try {
                    const response = await fetch(`/schedules/${scheduleId}/toggle`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showToast('Estado do agendamento alterado', 'success');
                        this.refreshSchedules();
                    } else {
                        throw new Error(result.message || 'Erro ao alterar estado');
                    }

                } catch (error) {
                    console.error('Erro ao alterar estado:', error);
                    this.showToast(error.message || 'Erro ao alterar estado', 'error');
                }
            }

            async deleteSchedule(scheduleId) {
                if (!confirm('Tem a certeza que deseja eliminar este agendamento?')) {
                    return;
                }

                try {
                    const response = await fetch(`/schedules/${scheduleId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showToast('Agendamento eliminado com sucesso', 'success');
                        this.refreshSchedules();
                    } else {
                        throw new Error(result.message || 'Erro ao eliminar agendamento');
                    }

                } catch (error) {
                    console.error('Erro ao eliminar agendamento:', error);
                    this.showToast(error.message || 'Erro ao eliminar agendamento', 'error');
                }
            }

            refreshSchedules() {
                window.location.reload();
            }

            showToast(message, type = 'info') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');

                toast.className = `toast toast-${type} transform translate-x-full`;
                toast.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                `;

                container.appendChild(toast);

                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);

                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            }
        }

        // Inicializar Schedule Manager
        const scheduleManager = new ScheduleManager();
    </script>
</x-app-layout>
