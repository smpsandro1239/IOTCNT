@extends('layouts.app')

@section('title', 'Dashboard Industrial - IOTCNT')

@section('content')
<div class="min-h-screen bg-gray-900 text-white p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Cabeçalho do Dashboard -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2 text-blue-400">Dashboard Industrial IOTCNT</h1>
            <p class="text-gray-400">Sistema de Monitorização em Tempo Real de Condensadores</p>
            <div class="flex items-center mt-4 space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-green-400">Sistema Online</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-gray-400">Última atualização:</span>
                    <span id="last-update" class="text-blue-400">--</span>
                </div>
            </div>
        </div>

        <!-- Cards de Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Temperatura -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-green-400">Temperatura</h3>
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-green-400" id="temperature-value">--°C</div>
                <div class="text-sm text-gray-400 mt-1">Média: <span id="temp-avg">--</span>°C</div>
            </div>

            <!-- Umidade -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-blue-400">Umidade</h3>
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-blue-400" id="humidity-value">--%</div>
                <div class="text-sm text-gray-400 mt-1">Média: <span id="humidity-avg">--</span>%</div>
            </div>

            <!-- Pressão -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-purple-400">Pressão</h3>
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-purple-400" id="pressure-value">--hPa</div>
                <div class="text-sm text-gray-400 mt-1">Tendência: <span id="pressure-trend">--</span></div>
            </div>

            <!-- Válvulas -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-yellow-400">Válvulas</h3>
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-yellow-400" id="valves-count">0/0</div>
                <div class="text-sm text-gray-400 mt-1">Ativas: <span id="valves-active">--</span></div>
            </div>
        </div>

        <!-- Gráficos em Tempo Real -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gráfico de Temperatura -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-green-400">Temperatura ao Longo do Tempo</h2>
                <canvas id="temperature-chart" width="400" height="200"></canvas>
            </div>

            <!-- Gráfico de Umidade -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Umidade ao Longo do Tempo</h2>
                <canvas id="humidity-chart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Indicadores Industriais -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Gauge de Temperatura -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-green-400">Gauge de Temperatura</h2>
                <div class="relative w-48 h-48 mx-auto">
                    <canvas id="temperature-gauge" width="192" height="192"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-400" id="gauge-temp-value">--°C</div>
                            <div class="text-sm text-gray-400">Temperatura</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gauge de Umidade -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Gauge de Umidade</h2>
                <div class="relative w-48 h-48 mx-auto">
                    <canvas id="humidity-gauge" width="192" height="192"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-400" id="gauge-humidity-value">--%</div>
                            <div class="text-sm text-gray-400">Umidade</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapa de Calor -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-purple-400">Mapa de Calor Condensadores</h2>
                <div class="grid grid-cols-3 gap-2" id="heat-map">
                    <!-- Será preenchido dinamicamente -->
                </div>
            </div>
        </div>

        <!-- Logs em Tempo Real -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-xl font-semibold mb-4 text-purple-400">Logs em Tempo Real</h2>
            <div id="real-time-logs" class="bg-gray-900 rounded p-4 h-64 overflow-y-auto font-mono text-sm">
                <p class="text-gray-500">Aguardando dados...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Inicialização de gráficos
const tempChart = new Chart(document.getElementById('temperature-chart'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Temperatura (°C)',
            data: [],
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                ticks: { color: 'white' }
            },
            x: {
                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                ticks: { color: 'white' }
            }
        },
        plugins: {
            legend: { labels: { color: 'white' } }
        }
    }
});

const humidityChart = new Chart(document.getElementById('humidity-chart'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Umidade (%)',
            data: [],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                ticks: { color: 'white' }
            },
            x: {
                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                ticks: { color: 'white' }
            }
        },
        plugins: {
            legend: { labels: { color: 'white' } }
        }
    }
});

// Função para atualizar dados em tempo real
function updateIndustrialData() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    
    // Gerar dados simulados
    const temperature = (20 + Math.random() * 15).toFixed(1);
    const humidity = (40 + Math.random() * 30).toFixed(1);
    const pressure = (1000 + Math.random() * 50).toFixed(1);
    const valveCount = 6;
    const activeValves = Math.floor(Math.random() * valveCount) + 1;
    
    // Atualizar UI
    document.getElementById('last-update').textContent = timeString;
    document.getElementById('temperature-value').textContent = temperature + '°C';
    document.getElementById('humidity-value').textContent = humidity + '%';
    document.getElementById('pressure-value').textContent = pressure + 'hPa';
    document.getElementById('valves-count').textContent = `${activeValves}/${valveCount}`;
    document.getElementById('valves-active').textContent = activeValves;
    
    // Atualizar gráficos
    if (tempChart.data.labels.length > 20) {
        tempChart.data.labels.shift();
        tempChart.data.datasets[0].data.shift();
        humidityChart.data.labels.shift();
        humidityChart.data.datasets[0].data.shift();
    }
    
    tempChart.data.labels.push(timeString);
    tempChart.data.datasets[0].data.push(parseFloat(temperature));
    tempChart.update('none');
    
    humidityChart.data.labels.push(timeString);
    humidityChart.data.datasets[0].data.push(parseFloat(humidity));
    humidityChart.update('none');
    
    // Adicionar log
    const logEntry = document.createElement('div');
    logEntry.className = 'text-gray-300 mb-1';
    logEntry.textContent = `[${timeString}] SISTEMA: T=${temperature}°C, U=${humidity}%, P=${pressure}hPa, Válvulas=${activeValves}/${valveCount}`;
    document.getElementById('real-time-logs').appendChild(logEntry);
    
    // Limitar logs
    const logsContainer = document.getElementById('real-time-logs');
    while (logsContainer.children.length > 50) {
        logsContainer.removeChild(logsContainer.firstChild);
    }
}

// Iniciar atualização
updateIndustrialData();
setInterval(updateIndustrialData, 2000);

// Inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Industrial IOTCNT carregado');
});
</script>
@endpush
