<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="description" content="Dashboard de monitorização em tempo real para o sistema IOTCNT">
    <meta name="robots" content="noindex, nofollow">
    <title>IOTCNT - Monitorização</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #333;
            font-size: 16px;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .navbar-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }

        .navbar-brand h1 {
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .navbar-brand span {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.9);
        }

        .navbar-nav {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .nav-link {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            font-size: 0.875rem;
            min-height: 44px;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .user-badge {
            background: #dc2626;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .content {
            padding: 2rem;
        }

        .controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .btn:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #3b82f6;
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-5px);
        }

        .metric-card h3 {
            color: #1f2937;
            margin-bottom: 0.75rem;
            font-size: 1.125rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 0.5rem;
        }

        .metric-unit {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .metric-trend {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .trend-up {
            color: #16a34a;
        }

        .trend-down {
            color: #dc2626;
        }

        .trend-stable {
            color: #3b82f6;
        }

        .charts-section {
            margin-bottom: 2rem;
        }

        .chart-container {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .chart-title {
            color: #1f2937;
            margin-bottom: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .chart-placeholder {
            height: 300px;
            background: #f8fafc;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 1rem;
        }

        .alerts-section {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .alerts-title {
            color: #1f2937;
            margin-bottom: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .alert-item {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            border-left: 4px solid #dc2626;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-critical {
            border-left-color: #dc2626;
        }

        .alert-warning {
            border-left-color: #d97706;
        }

        .alert-info {
            border-left-color: #3b82f6;
        }

        .alert-content h4 {
            color: #1f2937;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .alert-meta {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .alert-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-critical {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 1rem;
            font-size: 0.875rem;
        }

        /* Responsive breakpoints */
        @media (min-width: 480px) {
            .navbar-content { padding: 0 1.5rem; }
            .navbar-brand h1 { font-size: 1.5rem; }
            .navbar-brand span { font-size: 1rem; }
            .nav-link { padding: 0.5rem 1rem; font-size: 1rem; }
            .container { margin: 1.5rem auto; }
            .content { padding: 1.5rem; }
            .metric-card { padding: 1.25rem; }
            .chart-container { padding: 1.25rem; }
            .alerts-section { padding: 1.25rem; }
            .btn { padding: 0.75rem 1.5rem; font-size: 1rem; }
        }

        @media (min-width: 768px) {
            .navbar { padding: 1rem 0; }
            .navbar-content { padding: 0 2rem; flex-wrap: nowrap; }
            .navbar-nav { flex-wrap: nowrap; }
            .container { margin: 2rem auto; }
            .content { padding: 2rem; }
            .metrics-grid { grid-template-columns: repeat(2, 1fr); gap: 2rem; }
            .metric-card { padding: 1.5rem; }
            .chart-container { padding: 1.5rem; }
            .alerts-section { padding: 1.5rem; }
            .user-info { justify-content: flex-end; }
        }

        @media (min-width: 1024px) {
            .metrics-grid { grid-template-columns: repeat(3, 1fr); }
            .metric-card { padding: 1.75rem; }
            .chart-placeholder { height: 280px; }
        }

        @media (min-width: 1441px) {
            .container { max-width: 1500px; }
            .metrics-grid { grid-template-columns: repeat(4, 1fr); gap: 2.5rem; }
            .metric-card { padding: 2rem; }
            .chart-container { padding: 2rem; }
            .chart-placeholder { height: 350px; }
            .alerts-section { padding: 2rem; }
        }

        /* Mobile Portrait */
        @media (max-width: 480px) {
            .navbar-content {
                flex-direction: column;
                gap: 1rem;
                padding: 0 1rem;
            }
            .navbar-brand h1 { font-size: 1.25rem; }
            .navbar-brand span { font-size: 0.75rem; }
            .user-info { flex-direction: column; gap: 0.5rem; }
            .user-badge { font-size: 0.625rem; padding: 0.375rem 0.75rem; }
            .container { margin: 1rem; }
            .content { padding: 1rem; }
            .controls { flex-direction: column; gap: 0.75rem; }
            .btn { width: 100%; padding: 0.75rem; font-size: 0.875rem; }
            .metrics-grid { grid-template-columns: 1fr; gap: 1rem; }
            .metric-card { padding: 1rem; }
            .metric-card h3 { font-size: 1rem; }
            .metric-value { font-size: 1.75rem; }
            .chart-container { padding: 1rem; }
            .chart-title { font-size: 1.125rem; }
            .chart-placeholder { height: 200px; font-size: 0.875rem; }
            .alerts-section { padding: 1rem; }
            .alerts-title { font-size: 1.125rem; }
            .alert-item { flex-direction: column; align-items: flex-start; gap: 0.5rem; padding: 0.75rem; }
            .alert-badge { align-self: flex-end; }
        }

        /* Mobile Landscape */
        @media (min-width: 481px) and (max-width: 768px) {
            .navbar-content { padding: 0 1.5rem; }
            .container { margin: 1.5rem; }
            .content { padding: 1.5rem; }
            .controls { flex-wrap: wrap; gap: 0.75rem; }
            .btn { flex: 1; min-width: 120px; }
            .metrics-grid { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
            .chart-placeholder { height: 250px; }
            .alerts-section { padding: 1.5rem; }
        }

        /* Tablet Portrait */
        @media (min-width: 769px) and (max-width: 1024px) {
            .navbar-content { padding: 0 2rem; }
            .container { margin: 2rem; }
            .metrics-grid { grid-template-columns: repeat(3, 1fr); }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .btn { min-height: 44px; min-width: 44px; }
            .metric-card:hover, .btn:hover { transform: none; }
            .btn:hover::after { display: none; }
        }

        /* Landscape orientation on mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .navbar { padding: 0.5rem 0; }
            .container { margin: 1rem; }
            .content { padding: 1rem; }
            .metrics-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
            .chart-placeholder { height: 180px; }
        }

        /* Accessibility & Dark Mode */
        @media (prefers-reduced-motion: reduce) {
            .metric-card, .btn { transition: none; }
        }

        @media (prefers-color-scheme: dark) {
            body { background: #111827; color: #f3f4f6; }
            .navbar-brand span, .metric-card h3, .chart-title, .alerts-title, .alert-content h4 { color: #f3f4f6; }
            .metric-unit, .alert-meta, .chart-placeholder { color: #d1d5db; }
            .container, .metric-card, .chart-container, .alerts-section { background: #1f2937; border: 1px solid #374151; }
            .alert-item { background: #374151; border: 1px solid #4b5563; }
            .chart-placeholder { background: #374151; }
            .footer { background: #111827; }
            .btn:hover::after { background: #374151; }
        }

        @media (prefers-contrast: high) {
            .metric-card, .chart-container, .alerts-section, .alert-item, .btn { border: 2px solid #000; }
        }

        /* Message styling for notifications */
        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            font-weight: 500;
            z-index: 2000;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .message-success { background: #16a34a; }
        .message-error { background: #dc2626; }
        .message-info { background: #3b82f6; }
    </style>
</head>
<body>
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <div class="navbar-content">
            <a href="/index-iotcnt.html" class="navbar-brand">
                <h1>IOTCNT</h1>
                <span>Monitorização Avançada</span>
            </a>
            <div class="navbar-nav">
                <a href="/dashboard-admin.html" class="nav-link">Dashboard</a>
                <a href="/valve-control.html" class="nav-link">Válvulas</a>
                <a href="/scheduling.html" class="nav-link">Agendamentos</a>
                <a href="/monitoring-dashboard.html" class="nav-link active" aria-current="page">Monitorização</a>
                <a href="/system-settings.html" class="nav-link">Configurações</a>
                <div class="user-info">
                    <span class="user-badge">👨‍💼 Admin</span>
                    <a href="/login-iotcnt.html" class="btn btn-primary" data-tooltip="Terminar sessão">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="content">
            <div class="controls">
                <button type="button" class="btn btn-primary" data-tooltip="Actualizar dados em tempo real" onclick="refreshData()">🔄 Actualizar Dados</button>
                <button type="button" class="btn btn-success" data-tooltip="Exportar relatório do sistema" onclick="exportReport()">📊 Exportar Relatório</button>
                <a href="/dashboard-admin.html" class="btn btn-primary" data-tooltip="Voltar ao dashboard principal">← Voltar ao Dashboard</a>
            </div>

            <div class="metrics-grid" id="metrics-grid"></div>

            <div class="charts-section">
                <div class="chart-container">
                    <h3 class="chart-title">📈 Performance das Válvulas (Últimas 24h)</h3>
                    <div class="chart-placeholder">📊 Gráfico de Performance em Tempo Real</div>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">🌡️ Temperatura e Pressão</h3>
                    <div class="chart-placeholder">🌡️ Gráfico de Temperatura e Pressão</div>
                </div>
            </div>

            <div class="alerts-section">
                <h3 class="alerts-title">🚨 Alertas do Sistema</h3>
                <div id="alerts-container"></div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2025 IOTCNT - Sistema de Monitorização Avançada | CNT</p>
        </div>
    </div>

    <script>
        const sampleMetrics = [
            { title: 'Válvulas Activas', value: 4, unit: 'de 5', trend: 'stable', icon: '⚙️' },
            { title: 'Temperatura Média', value: 18.2, unit: '°C', trend: 'down', icon: '🌡️' },
            { title: 'Pressão Média', value: 2.15, unit: 'bar', trend: 'up', icon: '📊' },
            { title: 'Eficiência Geral', value: 96.8, unit: '%', trend: 'up', icon: '⚡' },
            { title: 'Ciclos Hoje', value: 156, unit: 'ciclos', trend: 'stable', icon: '🔄' },
            { title: 'Tempo Activo', value: 23.8, unit: 'horas', trend: 'stable', icon: '⏰' },
            { title: 'Alertas Activos', value: 2, unit: 'alertas', trend: 'down', icon: '🚨' },
            { title: 'Última Manutenção', value: 3, unit: 'dias', trend: 'stable', icon: '🔧' }
        ];

        const sampleAlerts = [
            { id: 1, type: 'warning', title: 'Condensador 4 em Manutenção', message: 'Válvula desactivada para manutenção programada', time: '2 horas atrás' },
            { id: 2, type: 'info', title: 'Backup Automático Concluído', message: 'Backup da base de dados criado com sucesso', time: '4 horas atrás' },
            { id: 3, type: 'critical', title: 'Temperatura Elevada', message: 'Condensador 3 registou temperatura acima do normal', time: '6 horas atrás' }
        ];

        document.addEventListener('DOMContentLoaded', function() {
            loadMetrics();
            loadAlerts();
            setInterval(refreshData, 30000);
        });

        function loadMetrics() {
            const container = document.getElementById('metrics-grid');
            container.innerHTML = sampleMetrics.map(metric => `
                <div class="metric-card">
                    <h3>${metric.icon} ${metric.title}</h3>
                    <div class="metric-value">${metric.value}</div>
                    <div class="metric-unit">${metric.unit}</div>
                    <div class="metric-trend trend-${metric.trend}">
                        ${getTrendIcon(metric.trend)} ${getTrendText(metric.trend)}
                    </div>
                </div>
            `).join('');
        }

        function loadAlerts() {
            const container = document.getElementById('alerts-container');
            if (sampleAlerts.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6b7280;">Nenhum alerta activo</p>';
                return;
            }
            container.innerHTML = sampleAlerts.map(alert => `
                <div class="alert-item alert-${alert.type}">
                    <div class="alert-content">
                        <h4>${alert.title}</h4>
                        <div class="alert-meta">${alert.message} • ${alert.time}</div>
                    </div>
                    <span class="alert-badge badge-${alert.type}">${alert.type.toUpperCase()}</span>
                </div>
            `).join('');
        }

        function getTrendIcon(trend) {
            switch (trend) {
                case 'up': return '📈';
                case 'down': return '📉';
                case 'stable': return '➡️';
                default: return '➡️';
            }
        }

        function getTrendText(trend) {
            switch (trend) {
                case 'up': return 'A subir';
                case 'down': return 'A descer';
                case 'stable': return 'Estável';
                default: return 'Estável';
            }
        }

        async function refreshData() {
            try {
                showMessage('🔄 A actualizar dados...', 'info');
                sampleMetrics.forEach(metric => {
                    if (typeof metric.value === 'number') {
                        const variation = (Math.random() - 0.5) * 0.1;
                        metric.value = Math.max(0, metric.value + variation);
                        if (metric.unit !== 'de 5' && metric.unit !== 'ciclos' && metric.unit !== 'dias' && metric.unit !== 'alertas') {
                            metric.value = Math.round(metric.value * 10) / 10;
                        } else {
                            metric.value = Math.round(metric.value);
                        }
                    }
                });
                loadMetrics();
                showMessage('✅ Dados actualizados com sucesso!', 'success');
                fetch('/api.php?action=refreshMetrics', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(response => response.json())
                .then(data => console.log('API Response:', data))
                .catch(error => console.error('API Error:', error));
            } catch (error) {
                showMessage('❌ Erro ao actualizar dados: ' + error.message, 'error');
            }
        }

        function exportReport() {
            showMessage('📊 A gerar relatório...', 'info');
            setTimeout(() => {
                showMessage('✅ Relatório exportado com sucesso!', 'success');
                fetch('/api.php?action=exportReport', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(response => response.json())
                .then(data => console.log('API Response:', data))
                .catch(error => console.error('API Error:', error));
            }, 2000);
        }

        function showMessage(message, type = 'info') {
            const messageEl = document.createElement('div');
            messageEl.className = `message message-${type}`;
            messageEl.innerHTML = `${message}`;
            document.body.appendChild(messageEl);
            setTimeout(() => messageEl.remove(), 3000);
        }

        setInterval(() => {
            const tempMetric = sampleMetrics.find(m => m.title === 'Temperatura Média');
            if (tempMetric) {
                tempMetric.value += (Math.random() - 0.5) * 0.2;
                tempMetric.value = Math.max(15, Math.min(25, tempMetric.value));
                tempMetric.value = Math.round(tempMetric.value * 10) / 10;
            }
            const pressureMetric = sampleMetrics.find(m => m.title === 'Pressão Média');
            if (pressureMetric) {
                pressureMetric.value += (Math.random() - 0.5) * 0.05;
                pressureMetric.value = Math.max(1.5, Math.min(3.0, pressureMetric.value));
                pressureMetric.value = Math.round(pressureMetric.value * 100) / 100;
            }
            loadMetrics();
        }, 5000);
    </script>
</body>
</html>
