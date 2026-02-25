@extends('layouts.app')

@section('title', 'Dashboard em Tempo Real - IOTCNT')

@section('content')
<div class="min-h-screen bg-gray-900 text-white p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-blue-400">Dashboard em Tempo Real - IOTCNT</h1>
        
        <!-- Status do Sistema -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-green-400">ESP32 Status</h2>
                <div id="esp32-status" class="space-y-2">
                    <p>Conexão: <span id="connection-status" class="text-yellow-400">Aguardando...</span></p>
                    <p>Última atualização: <span id="last-update" class="text-blue-400">-</span></p>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Sensores</h2>
                <div id="sensor-data" class="space-y-2">
                    <p>Temperatura: <span id="temperature" class="text-green-400">--</span>°C</p>
                    <p>Umidade: <span id="humidity" class="text-blue-400">--</span>%</p>
                    <p>Pressão: <span id="pressure" class="text-purple-400">--</span> hPa</p>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-yellow-400">Válvulas</h2>
                <div id="valve-status" class="space-y-2">
                    <p>Status: <span id="valve-state" class="text-red-400">--</span></p>
                    <p>Última ação: <span id="last-action" class="text-gray-400">--</span></p>
                </div>
            </div>
        </div>
        
        <!-- Gráficos em Tempo Real -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-green-400">Temperatura ao Longo do Tempo</h2>
                <canvas id="temperature-chart" width="400" height="200"></canvas>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Umidade ao Longo do Tempo</h2>
                <canvas id="humidity-chart" width="400" height="200"></canvas>
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
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: 'white'
                }
            },
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: 'white'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: 'white'
                }
            }
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
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: 'white'
                }
            },
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: 'white'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: 'white'
                }
            }
        }
    }
});

// Simulação de dados em tempo real (substituir por WebSocket real)
function simulateESP32Data() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    
    // Gerar dados simulados
    const temperature = (20 + Math.random() * 15).toFixed(1);
    const humidity = (40 + Math.random() * 30).toFixed(1);
    const pressure = (1000 + Math.random() * 50).toFixed(1);
    const valveStatus = Math.random() > 0.5;
    
    // Atualizar UI
    document.getElementById('connection-status').textContent = 'Conectado';
    document.getElementById('connection-status').className = 'text-green-400';
    document.getElementById('last-update').textContent = timeString;
    document.getElementById('temperature').textContent = temperature;
    document.getElementById('humidity').textContent = humidity;
    document.getElementById('pressure').textContent = pressure;
    document.getElementById('valve-state').textContent = valveStatus ? 'Aberta' : 'Fechada';
    document.getElementById('valve-state').className = valveStatus ? 'text-green-400' : 'text-red-400';
    
    // Adicionar log
    const logEntry = document.createElement('div');
    logEntry.className = 'text-gray-300 mb-1';
    logEntry.textContent = `[${timeString}] ESP32: T=${temperature}°C, U=${humidity}%, P=${pressure}hPa, Válvula=${valveStatus ? 'Aberta' : 'Fechada'}`;
    document.getElementById('real-time-logs').appendChild(logEntry);
    
    // Limitar logs
    const logsContainer = document.getElementById('real-time-logs');
    while (logsContainer.children.length > 50) {
        logsContainer.removeChild(logsContainer.firstChild);
    }
    
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
}

// Iniciar simulação
simulateESP32Data();
setInterval(simulateESP32Data, 2000);

// Função para se conectar ao canal de broadcasting
async function connectToBroadcastChannel() {
    try {
        // Aqui você implementaria a conexão real com WebSocket ou Laravel Echo
        console.log('Conectando ao canal de broadcasting...');
        
        // Exemplo de como seria com Laravel Echo (não implementado devido a restrições)
        // Echo.channel('esp32-data')
        //     .listen('.esp32.data', (e) => {
        //         // Atualizar UI com dados recebidos
        //     });
        
    } catch (error) {
        console.error('Erro ao conectar ao canal:', error);
    }
}

// Iniciar conexão quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', connectToBroadcastChannel);
</script>
@endpush
