<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Gestão ESP32 - IOTCNT</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 10px;
            font-size: 16px; /* Prevent iOS zoom */
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 15px;
            text-align: center;
        }

        .header h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
            font-weight: 300;
        }

        .content {
            padding: 15px;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        /* Responsive breakpoints */
        @media (min-width: 480px) {
            .container { border-radius: 12px; }
            .header { padding: 20px; }
            .header h1 { font-size: 1.8rem; }
            .content { padding: 20px; }
            .controls { gap: 15px; margin-bottom: 25px; }
        }

        @media (min-width: 768px) {
            body { padding: 20px; }
            .container { border-radius: 15px; }
            .header { padding: 25px; }
            .header h1 { font-size: 2.2rem; }
            .content { padding: 25px; }
            .controls { margin-bottom: 30px; }
        }

        @media (min-width: 1024px) {
            .header { padding: 30px; }
            .header h1 {
                font-size: 2.5rem;
                margin-bottom: 10px;
            }
            .content { padding: 30px; }
        }

        .btn {
            padding: 12px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px; /* Prevent iOS zoom */
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px; /* Touch target */
            flex: 1;
            min-width: 120px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #00b894, #00a085);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #fdcb6e, #e17055);
            color: white;
        }

        .btn-small {
            padding: 8px 12px;
            font-size: 14px;
            flex: none;
            min-width: 100px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #e9ecef;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #74b9ff;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 0.875rem;
        }

        .devices-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .devices-title {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .device-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 12px;
            border-left: 4px solid #74b9ff;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .device-info {
            flex: 1;
        }

        .device-info h4 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .device-meta {
            color: #7f8c8d;
            font-size: 0.875rem;
        }

        .device-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            align-self: flex-start;
        }

        .device-status.online {
            background: #dcfce7;
            color: #166534;
        }

        .device-status.offline {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Responsive stats and devices */
        @media (min-width: 480px) {
            .btn { flex: none; }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 18px;
                margin-bottom: 25px;
            }
            .devices-section {
                padding: 18px;
                border-radius: 10px;
                margin-bottom: 25px;
            }
            .devices-title { font-size: 1.2rem; }
            .device-card {
                padding: 18px;
                border-radius: 8px;
                margin-bottom: 15px;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }
            .stat-card { padding: 18px; }
            .devices-section {
                padding: 20px;
                margin-bottom: 30px;
            }
            .devices-title { font-size: 1.25rem; }
            .device-card { padding: 20px; }
        }

        @media (min-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            .stat-card {
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            .stat-value { font-size: 2rem; }
            .devices-section {
                padding: 25px;
            }
            .devices-title {
                font-size: 1.3rem;
                margin-bottom: 20px;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
        }

        .device-card.online {
            border-left-color: #00b894;
        }

        .device-card.offline {
            border-left-color: #e17055;
        }

        .device-card.warning {
            border-left-color: #fdcb6e;
        }

        /* Accessibility & Dark Mode */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            }
            .container {
                background: #2c3e50;
                color: #ecf0f1;
            }
            .stat-card {
                background: #34495e;
                border-color: #4b5563;
            }
            .stat-label { color: #bdc3c7; }
            .devices-section {
                background: #34495e;
            }
            .devices-title { color: #ecf0f1; }
            .device-card {
                background: #2c3e50;
                border-left-color: #74b9ff;
            }
            .device-info h4 { color: #ecf0f1; }
            .device-meta { color: #bdc3c7; }
            .device-card.online { border-left-color: #00b894; }
            .device-card.offline { border-left-color: #e17055; }
            .device-card.warning { border-left-color: #fdcb6e; }
        }

        @media (prefers-contrast: high) {
            .btn {
                border: 2px solid currentColor;
            }
            .stat-card {
                border-width: 3px;
            }
            .device-card {
                border: 2px solid #74b9ff;
            }
        }

        /* Landscape orientation */
        @media (orientation: landscape) and (max-height: 500px) {
            .header { padding: 10px; }
            .header h1 { font-size: 1.3rem; }
            .content { padding: 10px; }
            .stats-grid { gap: 10px; margin-bottom: 15px; }
            .devices-section { padding: 10px; margin-bottom: 15px; }
        }

        .device-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .status-online {
            background: #00b894;
        }

        .status-offline {
            background: #e17055;
        }

        .status-warning {
            background: #fdcb6e;
        }

        .device-actions {
            display: flex;
            gap: 10px;
        }

        .command-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .command-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
            font-weight: 600;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #74b9ff;
        }

        .sensor-data {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .sensor-data h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .sensor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .sensor-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #74b9ff;
        }

        .sensor-card h5 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .sensor-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #74b9ff;
        }

        .sensor-time {
            color: #7f8c8d;
            font-size: 0.8em;
            margin-top: 5px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .error {
            background: #ff7675;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success {
            background: #00b894;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .device-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .device-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .controls {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Gestão ESP32</h1>
            <p>Sistema de Integração com Hardware ESP32 - IOTCNT</p>
        </div>

        <div class="content">
            <div class="controls">
                <button class="btn btn-primary" onclick="refreshDevices()">
                    🔄 Actualizar Dispositivos
                </button>
                <button class="btn btn-success" onclick="scanDevices()">
                    🔍 Procurar Dispositivos
                </button>
                <a href="/dashboard-admin.html" class="btn btn-primary">
                    ← Voltar ao Dashboard
                </a>
            </div>

            <div id="message-area"></div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" id="total-devices">0</div>
                    <div class="stat-label">Total de Dispositivos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="online-devices">0</div>
                    <div class="stat-label">Dispositivos Online</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="offline-devices">0</div>
                    <div class="stat-label">Dispositivos Offline</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="last-update">-</div>
                    <div class="stat-label">Última Actualização</div>
                </div>
            </div>

            <div class="devices-section">
                <h3 class="devices-title">📱 Dispositivos ESP32</h3>
                <div id="devices-list">
                    <div class="loading">
                        <p>🔄 A carregar dispositivos...</p>
                    </div>
                </div>
            </div>

            <div class="command-section">
                <h3>📡 Enviar Comando</h3>
                <div class="form-group">
                    <label for="device-select">Dispositivo:</label>
                    <select id="device-select">
                        <option value="">Seleccione um dispositivo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="command-select">Comando:</label>
                    <select id="command-select">
                        <option value="status">Verificar Estado</option>
                        <option value="restart">Reiniciar</option>
                        <option value="valve_open">Abrir Válvula</option>
                        <option value="valve_close">Fechar Válvula</option>
                        <option value="read_sensors">Ler Sensores</option>
                        <option value="update_config">Actualizar Configuração</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="command-params">Parâmetros (JSON):</label>
                    <input type="text" id="command-params" placeholder='{"valve_id": 1}'>
                </div>
                <button class="btn btn-warning" onclick="sendCommand()">
                    📡 Enviar Comando
                </button>
            </div>

            <div class="sensor-data">
                <h3>📊 Dados dos Sensores</h3>
                <div id="sensor-grid" class="sensor-grid">
                    <div class="loading">
                        <p>🔄 A carregar dados dos sensores...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2025 IOTCNT - Sistema de Integração ESP32 | CNT</p>
        </div>
    </div>

    <script>
        let devicesData = [];
        let sensorData = [];

        // Carregar dados iniciais
        document.addEventListener('DOMContentLoaded', function() {
            refreshDevices();
            loadSensorData();

            // Auto-refresh a cada 30 segundos
            setInterval(() => {
                refreshDevices();
                loadSensorData();
            }, 30000);
        });

        // Actualizar lista de dispositivos
        async function refreshDevices() {
            try {
                const response = await fetch('/esp32-integration.php?action=status');
                const data = await response.json();

                if (data.status === 'success') {
                    devicesData = data.devices;
                    renderDevices(data.devices);
                    updateStats(data.devices);
                    updateDeviceSelect(data.devices);
                } else {
                    showError('Erro ao carregar dispositivos: ' + data.message);
                }
            } catch (error) {
                showError('Erro ao carregar dispositivos: ' + error.message);
            }
        }

        // Renderizar lista de dispositivos
        function renderDevices(devices) {
            const container = document.getElementById('devices-list');

            if (devices.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #7f8c8d;">Nenhum dispositivo registado</p>';
                return;
            }

            container.innerHTML = devices.map(device => `
                <div class="device-card ${device.status}">
                    <div class="device-info">
                        <h4>📱 ${device.device_name}</h4>
                        <div class="device-meta">
                            MAC: ${device.mac_address} |
                            IP: ${device.ip_address || 'N/A'} |
                            Firmware: ${device.firmware_version || 'N/A'} |
                            Último contacto: ${formatTime(device.last_seen)}
                        </div>
                    </div>
                    <div class="device-status">
                        <span class="status-indicator status-${device.status}"></span>
                        <span>${device.status.toUpperCase()}</span>
                    </div>
                    <div class="device-actions">
                        <button class="btn btn-primary btn-small" onclick="pingDevice('${device.mac_address}')">
                            📡 Ping
                        </button>
                        <button class="btn btn-warning btn-small" onclick="restartDevice('${device.mac_address}')">
                            🔄 Reiniciar
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Actualizar estatísticas
        function updateStats(devices) {
            const total = devices.length;
            const online = devices.filter(d => d.status === 'online').length;
            const offline = devices.filter(d => d.status === 'offline').length;

            document.getElementById('total-devices').textContent = total;
            document.getElementById('online-devices').textContent = online;
            document.getElementById('offline-devices').textContent = offline;
            document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
        }

        // Actualizar select de dispositivos
        function updateDeviceSelect(devices) {
            const select = document.getElementById('device-select');
            select.innerHTML = '<option value="">Seleccione um dispositivo</option>';

            devices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.mac_address;
                option.textContent = `${device.device_name} (${device.status})`;
                select.appendChild(option);
            });
        }

        // Carregar dados dos sensores
        async function loadSensorData() {
            try {
                const response = await fetch('/esp32-integration.php?action=sensor_data&limit=20');
                const data = await response.json();

                if (data.status === 'success') {
                    renderSensorData(data.data);
                }
            } catch (error) {
                console.error('Erro ao carregar dados dos sensores:', error);
            }
        }

        // Renderizar dados dos sensores
        function renderSensorData(sensors) {
            const container = document.getElementById('sensor-grid');

            if (sensors.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #7f8c8d;">Nenhum dado de sensor disponível</p>';
                return;
            }

            // Agrupar por tipo de sensor
            const grouped = sensors.reduce((acc, sensor) => {
                if (!acc[sensor.sensor_type]) {
                    acc[sensor.sensor_type] = [];
                }
                acc[sensor.sensor_type].push(sensor);
                return acc;
            }, {});

            container.innerHTML = Object.entries(grouped).map(([type, readings]) => {
                const latest = readings[0]; // Mais recente
                return `
                    <div class="sensor-card">
                        <h5>📊 ${type.toUpperCase()}</h5>
                        <div class="sensor-value">${latest.value} ${latest.unit}</div>
                        <div class="sensor-time">${formatTime(latest.timestamp)}</div>
                    </div>
                `;
            }).join('');
        }

        // Procurar dispositivos
        async function scanDevices() {
            showMessage('🔍 A procurar dispositivos ESP32...', 'info');

            // Simular procura (em produção faria broadcast na rede)
            setTimeout(() => {
                showMessage('✅ Procura concluída. Dispositivos encontrados serão registados automaticamente.', 'success');
                refreshDevices();
            }, 3000);
        }

        // Enviar comando
        async function sendCommand() {
            const deviceMac = document.getElementById('device-select').value;
            const command = document.getElementById('command-select').value;
            const paramsText = document.getElementById('command-params').value;

            if (!deviceMac) {
                showError('Por favor, seleccione um dispositivo');
                return;
            }

            let parameters = {};
            if (paramsText.trim()) {
                try {
                    parameters = JSON.parse(paramsText);
                } catch (e) {
                    showError('Parâmetros JSON inválidos');
                    return;
                }
            }

            showMessage(`📡 A enviar comando "${command}" para dispositivo...`, 'info');

            try {
                const response = await fetch('/esp32-integration.php?action=send_command', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mac_address: deviceMac,
                        command: command,
                        parameters: parameters,
                        priority: 1
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showMessage(`✅ Comando enviado com sucesso! ID: ${data.command_id}`, 'success');
                } else {
                    showError('Erro ao enviar comando: ' + data.message);
                }
            } catch (error) {
                showError('Erro ao enviar comando: ' + error.message);
            }
        }

        // Ping dispositivo
        async function pingDevice(macAddress) {
            showMessage('📡 A fazer ping ao dispositivo...', 'info');

            try {
                const response = await fetch('/esp32-integration.php?action=send_command', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mac_address: macAddress,
                        command: 'ping',
                        parameters: {},
                        priority: 2
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showMessage('✅ Comando ping enviado!', 'success');
                } else {
                    showError('Erro no ping: ' + data.message);
                }
            } catch (error) {
                showError('Erro no ping: ' + error.message);
            }
        }

        // Reiniciar dispositivo
        async function restartDevice(macAddress) {
            if (!confirm('Tem a certeza que deseja reiniciar este dispositivo?')) {
                return;
            }

            showMessage('🔄 A enviar comando de reinício...', 'info');

            try {
                const response = await fetch('/esp32-integration.php?action=send_command', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mac_address: macAddress,
                        command: 'restart',
                        parameters: {},
                        priority: 3
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showMessage('✅ Comando de reinício enviado!', 'success');
                } else {
                    showError('Erro ao reiniciar: ' + data.message);
                }
            } catch (error) {
                showError('Erro ao reiniciar: ' + error.message);
            }
        }

        // Formatar tempo
        function formatTime(timestamp) {
            return new Date(timestamp).toLocaleString('pt-PT');
        }

        // Mostrar mensagem
        function showMessage(message, type = 'info') {
            const messageArea = document.getElementById('message-area');
            const className = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';

            messageArea.innerHTML = `<div class="${className}">${message}</div>`;

            setTimeout(() => {
                messageArea.innerHTML = '';
            }, 5000);
        }

        // Mostrar erro
        function showError(message) {
            showMessage(message, 'error');
        }
    </script>
</body>
</html>
