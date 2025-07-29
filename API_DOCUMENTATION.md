# 📡 IOTCNT - Documentação da API

## 🔐 Autenticação

Todas as rotas da API requerem autenticação via Laravel Sanctum. Inclua o token no header:

```
Authorization: Bearer {seu_token}
```

## 🌐 Endpoints Disponíveis

### 📊 Estado das Válvulas

#### `GET /api/valve/status`
Obtém o estado atual de todas as válvulas.

**Resposta:**
```json
{
  "success": true,
  "valves": [
    {
      "id": 1,
      "name": "Válvula 1 - Jardim Principal",
      "valve_number": 1,
      "current_state": false,
      "last_activated_at": "2024-01-15T10:30:00Z",
      "esp32_pin": 23,
      "description": "Rega do jardim principal"
    }
  ],
  "timestamp": "2024-01-15T14:30:00Z"
}
```

#### `GET /api/valve/status/{valve_id}`
Obtém o estado de uma válvula específica com logs recentes.

**Resposta:**
```json
{
  "success": true,
  "valve": {
    "id": 1,
    "name": "Válvula 1",
    "current_state": true,
    "last_activated_at": "2024-01-15T10:30:00Z"
  },
  "recent_logs": [
    {
      "id": 123,
      "action": "manual_on",
      "source": "web_interface",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ],
  "timestamp": "2024-01-15T14:30:00Z"
}
```

### 🎛️ Controlo de Válvulas

#### `POST /api/valve/control`
Controla uma válvula específica.

**Parâmetros:**
```json
{
  "valve_id": 1,
  "action": "on|off|toggle",
  "duration": 5
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Válvula ligada com sucesso",
  "valve": {
    "id": 1,
    "current_state": true,
    "last_activated_at": "2024-01-15T14:30:00Z"
  },
  "command_sent": true
}
```

#### `POST /api/valve/start-cycle`
Inicia um ciclo de irrigação em todas as válvulas.

**Parâmetros:**
```json
{
  "duration_per_valve": 5
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Ciclo de irrigação iniciado com sucesso",
  "duration_per_valve": 5,
  "total_valves": 5,
  "estimated_duration": 25
}
```

#### `POST /api/valve/stop-all`
Para todas as válvulas imediatamente.

**Resposta:**
```json
{
  "success": true,
  "message": "Todas as válvulas foram paradas",
  "stopped_valves": 3
}
```

### 📈 Estatísticas

#### `GET /api/valve/stats`
Obtém estatísticas do sistema.

**Resposta:**
```json
{
  "success": true,
  "stats": {
    "total_valves": 5,
    "active_valves": 2,
    "inactive_valves": 3,
    "total_operations_today": 15,
    "last_activity": "2024-01-15T14:25:00Z",
    "system_uptime": "2 dias, 14 horas, 32 minutos"
  },
  "timestamp": "2024-01-15T14:30:00Z"
}
```

## 🤖 API ESP32

### 📡 Configuração

#### `GET /api/esp32/config`
Obtém configuração para o ESP32.

**Resposta:**
```json
{
  "success": true,
  "data": {
    "valves": [
      {
        "id": 1,
        "valve_number": 1,
        "name": "Válvula 1",
        "esp32_pin": 23,
        "current_state": false
      }
    ],
    "schedules": [
      {
        "id": 1,
        "name": "Rega Matinal",
        "day_of_week": 1,
        "start_time": "07:00:00",
        "per_valve_duration_minutes": 5
      }
    ],
    "server_time": "2024-01-15T14:30:00Z",
    "device_name": "ESP32 Irrigation Controller",
    "timezone": "Europe/Lisbon"
  }
}
```

### 📊 Relatórios ESP32

#### `POST /api/esp32/valve-status`
ESP32 reporta estado das válvulas.

**Parâmetros:**
```json
{
  "valve_number": 1,
  "state": true,
  "timestamp_device": 1642248600
}
```

#### `POST /api/esp32/log`
ESP32 envia logs de operação.

**Parâmetros:**
```json
{
  "valve_number": 1,
  "action": "valve_on",
  "duration_minutes": 5,
  "timestamp_device": 1642248600,
  "notes": "Scheduled irrigation cycle"
}
```

### 🎮 Comandos ESP32

#### `GET /api/esp32/commands`
ESP32 obtém comandos pendentes.

**Resposta:**
```json
{
  "success": true,
  "commands": [
    {
      "id": 1,
      "type": "valve_control",
      "valve_number": 1,
      "action": "on",
      "duration": 5,
      "created_at": "2024-01-15T14:30:00Z"
    }
  ]
}
```

## 🚨 Códigos de Erro

| Código | Descrição |
|--------|-----------|
| 200 | Sucesso |
| 401 | Não autenticado |
| 403 | Sem permissões |
| 422 | Dados inválidos |
| 500 | Erro interno |

## 📝 Exemplos de Uso

### JavaScript (Frontend)
```javascript
// Obter estado das válvulas
const response = await fetch('/api/valve/status', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});

// Controlar válvula
await fetch('/api/valve/control', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken
  },
  body: JSON.stringify({
    valve_id: 1,
    action: 'on',
    duration: 5
  })
});
```

### ESP32 (C++)
```cpp
// Obter configuração
HTTPClient http;
http.begin("http://servidor.com/api/esp32/config");
http.addHeader("Authorization", "Bearer " + String(API_TOKEN));
int httpCode = http.GET();

// Reportar estado
http.begin("http://servidor.com/api/esp32/valve-status");
http.addHeader("Authorization", "Bearer " + String(API_TOKEN));
http.addHeader("Content-Type", "application/json");
String payload = "{\"valve_number\":1,\"state\":true}";
httpCode = http.POST(payload);
```

## 🔄 Fluxo de Dados

1. **ESP32** obtém configuração via `/api/esp32/config`
2. **ESP32** reporta estados via `/api/esp32/valve-status`
3. **Frontend** obtém estados via `/api/valve/status`
4. **Utilizador** controla válvulas via `/api/valve/control`
5. **ESP32** obtém comandos via `/api/esp32/commands`
6. **Sistema** regista todas as operações automaticamente

## 🛡️ Segurança

- Todas as rotas requerem autenticação
- Tokens têm expiração configurável
- Logs de auditoria para todas as operações
- Validação rigorosa de parâmetros
- Rate limiting implementado
- HTTPS obrigatório em produção

## 📋 Agendamentos

### `GET /api/schedules`
Lista todos os agendamentos ativos.

**Resposta:**
```json
{
  "success": true,
  "schedules": [
    {
      "id": 1,
      "name": "Rega Matinal",
      "day_of_week": 1,
      "start_time": "07:00:00",
      "per_valve_duration_minutes": 5,
      "is_active": true,
      "created_at": "2024-01-10T10:00:00Z"
    }
  ]
}
```

### `POST /api/schedules`
Cria um novo agendamento.

**Parâmetros:**
```json
{
  "name": "Rega Vespertina",
  "day_of_week": 2,
  "start_time": "18:00:00",
  "per_valve_duration_minutes": 3,
  "is_active": true
}
```

### `PUT /api/schedules/{id}`
Atualiza um agendamento existente.

### `DELETE /api/schedules/{id}`
Remove um agendamento.

## 📊 Logs e Histórico

### `GET /api/logs`
Obtém logs de operação com filtros.

**Parâmetros de Query:**
- `valve_id`: Filtrar por válvula específica
- `action`: Filtrar por tipo de ação
- `date_from`: Data inicial (YYYY-MM-DD)
- `date_to`: Data final (YYYY-MM-DD)
- `limit`: Número máximo de registos (padrão: 50)

**Resposta:**
```json
{
  "success": true,
  "logs": [
    {
      "id": 123,
      "valve_id": 1,
      "valve_name": "Válvula 1",
      "action": "manual_on",
      "source": "web_interface",
      "user_id": 1,
      "duration_minutes": 5,
      "created_at": "2024-01-15T10:30:00Z",
      "notes": "Ativação manual pelo utilizador"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_records": 247
  }
}
```

## 🔧 Configurações do Sistema

### `GET /api/system/settings`
Obtém configurações do sistema.

**Resposta:**
```json
{
  "success": true,
  "settings": {
    "timezone": "Europe/Lisbon",
    "auto_cycle_enabled": true,
    "default_valve_duration": 5,
    "max_concurrent_valves": 1,
    "telegram_notifications": true,
    "system_name": "IOTCNT Irrigation System"
  }
}
```

### `PUT /api/system/settings`
Atualiza configurações do sistema (apenas admin).

## 🤖 Telegram Bot Integration

### `GET /api/telegram/users`
Lista utilizadores registados no Telegram (apenas admin).

### `POST /api/telegram/send-notification`
Envia notificação via Telegram.

**Parâmetros:**
```json
{
  "message": "Sistema de irrigação ativado",
  "chat_id": 123456789
}
```

## 🔍 Monitorização e Diagnóstico

### `GET /api/system/health`
Verifica estado do sistema.

**Resposta:**
```json
{
  "success": true,
  "health": {
    "database": "ok",
    "redis": "ok",
    "esp32_connection": "ok",
    "last_esp32_ping": "2024-01-15T14:29:00Z",
    "active_valves": 0,
    "system_load": "low",
    "memory_usage": "45%",
    "disk_space": "78% free"
  }
}
```

### `GET /api/system/diagnostics`
Informações detalhadas do sistema (apenas admin).

## 📱 WebSocket Events (Tempo Real)

O sistema suporta atualizações em tempo real via WebSocket:

### Eventos Disponíveis:
- `valve.status.changed` - Estado de válvula alterado
- `system.cycle.started` - Ciclo de irrigação iniciado
- `system.cycle.completed` - Ciclo de irrigação concluído
- `esp32.connected` - ESP32 conectado
- `esp32.disconnected` - ESP32 desconectado

### Exemplo JavaScript:
```javascript
const echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

echo.channel('irrigation-system')
    .listen('ValveStatusChanged', (e) => {
        console.log('Valve status changed:', e.valve);
        updateValveUI(e.valve);
    });
```

## 🧪 Ambiente de Teste

### Base URL de Teste:
```
http://localhost:8000/api
```

### Token de Teste:
Para testes locais, use o seeder para criar um utilizador de teste:
```bash
php artisan db:seed --class=TestUserSeeder
```

### Postman Collection:
Importe a coleção Postman disponível em `/docs/postman/IOTCNT_API.json`

## 📈 Rate Limiting

| Endpoint | Limite |
|----------|--------|
| `/api/valve/*` | 60 req/min |
| `/api/esp32/*` | 120 req/min |
| `/api/logs` | 30 req/min |
| `/api/system/*` | 20 req/min |

## 🐛 Troubleshooting

### Erros Comuns:

**401 Unauthorized**
- Verificar se o token está incluído no header
- Verificar se o token não expirou

**422 Validation Error**
- Verificar formato dos parâmetros
- Consultar documentação específica do endpoint

**500 Internal Server Error**
- Verificar logs do Laravel em `storage/logs/laravel.log`
- Verificar conectividade com base de dados

### Debug Mode:
Para ativar logs detalhados, definir no `.env`:
```
APP_DEBUG=true
LOG_LEVEL=debug
```
