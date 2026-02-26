<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta IOTCNT</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .alert-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .alert-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        .alert-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .alert-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .alert-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 5px 0 0 0;
        }
        .alert-body {
            background: white;
            padding: 30px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .alert-message {
            font-size: 1.1rem;
            line-height: 1.6;
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
        }
        .alert-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 1rem;
            font-weight: 500;
            color: #1e293b;
        }
        .alert-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
        }
        .system-info {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: 'Roboto Mono', monospace;
            font-size: 0.85rem;
        }
        .level-critical { border-left-color: #ef4444; }
        .level-error { border-left-color: #ef4444; }
        .level-warning { border-left-color: #f59e0b; }
        .level-info { border-left-color: #3b82f6; }
        
        @media (max-width: 640px) {
            .container { padding: 15px; }
            .alert-header { padding: 20px; }
            .alert-body { padding: 20px; }
            .alert-details { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert-header">
            <div class="alert-icon">{{ $emoji }}</div>
            <h1 class="alert-title">Alerta IOTCNT</h1>
            <p class="alert-subtitle">Sistema de Monitorização Industrial</p>
        </div>
        
        <div class="alert-body">
            <div class="alert-message level-{{ $level }}">
                {{ $message }}
            </div>
            
            <div class="alert-details">
                <div class="detail-item">
                    <span class="detail-label">Nível do Alerta</span>
                    <span class="detail-value">{{ ucfirst($level) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Timestamp</span>
                    <span class="detail-value">{{ $timestamp }}</span>
                </div>
            </div>
            
            <div class="system-info">
                <strong>Sistema:</strong> IOTCNT - Industrial IoT Condensadores<br>
                <strong>Versão:</strong> 1.0.0<br>
                <strong>ID do Dispositivo:</strong> {{ config('app.name', 'Desconhecido') }}
            </div>
            
            <div class="alert-footer">
                <p>Este é um alerta automático do sistema IOTCNT. Por favor, verifique o sistema para mais detalhes.</p>
                <p>© 2026 IOTCNT - Sistema de Gestão Industrial IoT para Condensadores</p>
            </div>
        </div>
    </div>
</body>
</html>
