
<!-- Dashboard de Análise Preditiva em Tempo Real -->
<div class="container ph-10">
    <div class="flex flex-col md:flex-row">
        <div class="w-full md:w-3/12 p-4 mb-4md:mb-0">
            <div class="bg-gray-800 text-white p-4 mb-4">
                <h3 class="text-xl font-bold mb-2">Análise Preditiva</h3>
                <div class="mb-4">
                    <span class="text-gray-400 mr-4">Período: </span>
                    <span class="text-white">{{ now()->diffForHumans() }}</span>
                </div>
            </div>
            <div id="predictionChart" class="flex-1"></div>
        </div>
    </div>
</div>

<script>
    // Configurando o gráfico com Chart.js
    const ctx = document.getElementById('predictionChart').getContext('2d');
    const predictionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Valor Atual',
                    data: [],
                    borderDash: [5, 5],
                    borderColor: '#ffc107',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#ffc107'
                },
                {
                    label: 'Valor Previsto',
                    data: [],
                    borderColor: '#10b981',
                    borderDash: [5, 5],
                    pointRadius: 0,
                    pointHoverRadius: 0
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return `${value} °C`;
                        }
                    }
                },
                x: {
                    ticks: {
                        autoSkip: false
                    }
                }
            }
        }
    });

    // Buscando dados em tempo real
    async function fetchData() {
        try {
            const response = await fetch('{{ route('prediction.analysis.historical', ['device_id' => 1, 'metric_type' => 'temperature'])) }}');
            const data = await response.json();

            // Atualizando rótulos (datas)
            const parsedDates = data.slice().map(item => new Date(item.prediction_timestamp * 1000));
            const formattedDates = parsedDates.map(date =>
                date.toLocaleString('pt-PT', {
                    weekday: 'long',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                })
            );

            predictionChart.data.labels = formattedDates.reverse();

            // Atualizando dados de temperatura
            predictionChart.data.datasets[0].data = data.map(item => item.current_value).reverse() || [];
            predictionChart.data.datasets[1].data = data.map(item => item.predicted_value).reverse() || [];

            predictionChart.update();
        } catch (error) {
            console.error('Erro ao buscar dados:', error);
            document.getElementById('predictionChart').classList.add('error-case');
        }
    }

    // Atualizando a cada 2 segundos
    setInterval(fetchData, 2000);

    fetchData();
</script>
