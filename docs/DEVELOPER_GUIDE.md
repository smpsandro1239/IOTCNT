# 👨‍💻 IOTCNT - Guia do Desenvolvedor

## 🚀 Início Rápido

### 1. Configuração do Ambiente de Desenvolvimento

```bash
# Clonar o repositório
git clone https://github.com/seu-usuario/iotcnt.git
cd iotcnt

# Configurar ambiente
cp .env.example .env
cp docker-compose.example.yml docker-compose.yml

# Iniciar com Docker
docker-compose up -d

# Ou usar scripts Windows
start_iotcnt.bat
```

### 2. Obter Token de API

```bash
# Criar utilizador de teste
php artisan db:seed --class=TestUserSeeder

# Ou via interface web
# Aceder a http://localhost:8000/register
# Criar conta e obter token em /dashboard
```

### 3. Primeiro Request

```bash
curl -H "Authorization: Bearer SEU_TOKEN" \
     -H "Accept: application/json" \
     http://localhost:8000/api/valve/status
```

## 🏗️ Arquitetura da API

### Estrutura de Resposta Padrão

Todas as respostas da API seguem este formato:

```json
{
  "success": true|false,
  "message": "Mensagem descritiva",
  "data": { /* dados específicos */ },
  "errors": { /* erros de validação */ },
  "timestamp": "2024-01-15T14:30:00Z"
}
```

### Códigos de Estado HTTP

| Código | Significado | Quando Usar |
|--------|-------------|-------------|
| 200 | OK | Operação bem-sucedida |
| 201 | Created | Recurso criado |
| 204 | No Content | Operação sem retorno |
| 400 | Bad Request | Dados inválidos |
| 401 | Unauthorized | Token inválido/ausente |
| 403 | Forbidden | Sem permissões |
| 404 | Not Found | Recurso não encontrado |
| 422 | Validation Error | Falha na validação |
| 429 | Too Many Requests | Rate limit excedido |
| 500 | Server Error | Erro interno |

### Autenticação

A API usa Laravel Sanctum para autenticação:

```javascript
// Header obrigatório em todos os requests
headers: {
  'Authorization': 'Bearer ' + token,
  'Accept': 'application/json',
  'Content-Type': 'application/json'
}
```

## 🔧 Endpoints Principais

### Gestão de Válvulas

#### Estado das Válvulas
```http
GET /api/valve/status
```

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
  ]
}
```

#### Controlo de Válvulas
```http
POST /api/valve/control
Content-Type: application/json

{
  "valve_id": 1,
  "action": "on|off|toggle",
  "duration": 5
}
```

### Integração ESP32

#### Configuração
```http
GET /api/esp32/config
```

#### Reportar Estado
```http
POST /api/esp32/valve-status
Content-Type: application/json

{
  "valve_number": 1,
  "state": true,
  "timestamp_device": 1642248600
}
```

## 🛠️ SDKs e Bibliotecas

### JavaScript/Node.js

```javascript
class IotcntClient {
  constructor(baseUrl, token) {
    this.baseUrl = baseUrl;
    this.token = token;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const config = {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...options.headers
      },
      ...options
    };

    const response = await fetch(url, config);

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    return await response.json();
  }

  // Métodos específicos
  async getValveStatus() {
    return this.request('/api/valve/status');
  }

  async controlValve(valveId, action, duration = 5) {
    return this.request('/api/valve/control', {
      method: 'POST',
      body: JSON.stringify({
        valve_id: valveId,
        actionction,
 duration: duration
      })
    });
  }
}

// Uso
const client = new IotcntClient('http://localhost:8000', 'seu_token');
const status = await client.getValveStatus();
```

### Python

```python
import requests
from typing import Dict, Any, Optional

class IotcntClient:
    def __init__(self, base_url: str, token: str):
        self.base_url = base_url.rstrip('/')
        self.session = requests.Session()
        self.session.headers.update({
            'Authorization': f'Bearer {token}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        })

    def _request(self, method: str, endpoint: str, **kwargs) -> Dict[str, Any]:
        url = f'{self.base_url}{endpoint}'
        response = self.session.request(method, url, **kwargs)
        response.raise_for_status()
        return response.json()

    def get_valve_status(self) -> Dict[str, Any]:
        return self._request('GET', '/api/valve/status')

    def control_valve(self, valve_id: int, action: str, duration: int = 5) -> Dict[str, Any]:
        data = {
            'valve_id': valve_id,
            'action': action,
            'duration': duration
        }
        return self._request('POST', '/api/valve/control', json=data)

# Uso
client = IotcntClient('http://localhost:8000', 'seu_token')
status = client.get_valve_status()
```

### PHP

```php
<?php

class IotcntClient
{
    private $baseUrl;
    private $token;
    private $httpClient;

    public function __construct(string $baseUrl, string $token)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
        $this->httpClient = new \GuzzleHttp\Client();
    }

    private function request(string $method, string $endpoint, array $options = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $defaultOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ];

        $options = array_merge_recursive($defaultOptions, $options);

        $response = $this->httpClient->request($method, $url, $options);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getValveStatus(): array
    {
        return $this->request('GET', '/api/valve/status');
    }

    public function controlValve(int $valveId, string $action, int $duration = 5): array
    {
        return $this->request('POST', '/api/valve/control', [
            'json' => [
                'valve_id' => $valveId,
                'action' => $action,
                'duration' => $duration
            ]
        ]);
    }
}

// Uso
$client = new IotcntClient('http://localhost:8000', 'seu_token');
$status = $client->getValveStatus();
```

## 🔄 WebSocket (Tempo Real)

### Configuração Laravel Echo

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    auth: {
        headers: {
            Authorization: 'Bearer ' + token,
        },
    },
});
```

### Escutar Eventos

```javascript
// Mudanças de estado das válvulas
Echo.channel('irrigation-system')
    .listen('ValveStatusChanged', (e) => {
        console.log('Valve changed:', e.valve);
        updateUI(e.valve);
    });

// Início de ciclo
Echo.channel('irrigation-system')
    .listen('IrrigationCycleStarted', (e) => {
        console.log('Cycle started:', e.cycle);
        showNotification('Ciclo de irrigação iniciado');
    });

// Conexão ESP32
Echo.channel('irrigation-system')
    .listen('Esp32Connected', (e) => {
        console.log('ESP32 connected');
        updateConnectionStatus(true);
    });
```

## 🧪 Testes

### Configuração de Testes

```bash
# Configurar base de dados de teste
cp .env .env.testing
# Editar .env.testing com DB_DATABASE=iotcnt_test

# Executar testes
php artisan test
```

### Testes da API

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ValveApiTest extends TestCase
{
    public function test_can_get_valve_status()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/valve/status');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'valves' => [
                        '*' => [
                            'id',
                            'name',
                            'valve_number',
                            'current_state',
                            'esp32_pin'
                        ]
                    ]
                ]);
    }

    public function test_can_control_valve()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/valve/control', [
            'valve_id' => 1,
            'action' => 'on',
            'duration' => 5
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'command_sent' => true
                ]);
    }
}
```

## 📊 Monitorização e Logs

### Logs da Aplicação

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Logs específicos da API
grep "API:" storage/logs/laravel.log
```

### Métricas Personalizadas

```php
// No seu controller
use Illuminate\Support\Facades\Log;

Log::channel('api')->info('Valve controlled', [
    'valve_id' => $valveId,
    'action' => $action,
    'user_id' => auth()->id(),
    'ip' => request()->ip()
]);
```

### Integração com Monitoring

```php
// config/logging.php
'channels' => [
    'api' => [
        'driver' => 'stack',
        'channels' => ['single', 'slack'],
    ],

    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'IOTCNT Bot',
        'emoji' => ':droplet:',
        'level' => 'error',
    ],
]
```

## 🔒 Segurança

### Rate Limiting

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/valve/status', [ValveController::class, 'status']);
});

// Personalizado para ESP32
Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    Route::post('/esp32/valve-status', [Esp32Controller::class, 'reportStatus']);
});
```

### Validação de Dados

```php
// app/Http/Requests/ValveControlRequest.php
class ValveControlRequest extends FormRequest
{
    public function rules()
    {
        return [
            'valve_id' => 'required|integer|exists:valves,id',
            'action' => 'required|in:on,off,toggle',
            'duration' => 'integer|min:1|max:60'
        ];
    }

    public function messages()
    {
        return [
            'valve_id.exists' => 'A válvula especificada não existe.',
            'action.in' => 'Ação deve ser: on, off ou toggle.',
            'duration.max' => 'Duração máxima é 60 minutos.'
        ];
    }
}
```

### Middleware de Segurança

```php
// app/Http/Middleware/ValidateApiToken.php
class ValidateApiToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token required'], 401);
        }

        // Validações adicionais
        if ($this->isTokenExpired($token)) {
            return response()->json(['error' => 'Token expired'], 401);
        }

        return $next($request);
    }
}
```

## 🚀 Deploy e Produção

### Configuração de Produção

```bash
# Otimizações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrações
php artisan migrate --force

# Permissões
chmod -R 755 storage bootstrap/cache
```

### Docker para Produção

```dockerfile
# Dockerfile.prod
FROM php:8.1-fpm-alpine

# Instalar dependências
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client

# Copiar aplicação
COPY . /var/www/html
WORKDIR /var/www/html

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Configurar nginx e supervisor
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

### Monitorização em Produção

```yaml
# docker-compose.prod.yml
version: '3.8'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.prod
    environment:
      - APP_ENV=production
      - APP_DEBUG=false

  prometheus:
    image: prom/prometheus
    ports:
      - "9090:9090"
    volumes:
      - ./monitoring/prometheus.yml:/etc/prometheus/prometheus.yml

  grafana:
    image: grafana/grafana
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
```

## 🤝 Contribuição

### Estrutura de Commits

```bash
# Formato: tipo(escopo): descrição
git commit -m "feat(api): adicionar endpoint de estatísticas"
git commit -m "fix(esp32): corrigir timeout de conexão"
git commit -m "docs(api): atualizar documentação de autenticação"
```

### Pull Requests

1. Fork do repositório
2. Criar branch feature: `git checkout -b feature/nova-funcionalidade`
3. Commit das alterações: `git commit -m 'feat: adicionar nova funcionalidade'`
4. Push para branch: `git push origin feature/nova-funcionalidade`
5. Abrir Pull Request

### Testes Obrigatórios

```bash
# Antes de submeter PR
php artisan test
php artisan test --coverage
php-cs-fixer fix
phpstan analyse
```

## 📞 Suporte

- **Documentação**: `/docs`
- **Issues**: GitHub Issues
- **Discussões**: GitHub Discussions
- **Email**: suporte@iotcnt.com

## 📚 Recursos Adicionais

- [Laravel Documentation](https://laravel.com/docs)
- [ESP32 Arduino Core](https://github.com/espressif/arduino-esp32)
- [Postman Collection](./postman/IOTCNT_API.json)
- [API Examples](./API_EXAMPLES.md)
- [Troubleshooting Guide](../TROUBLESHOOTING.md)
