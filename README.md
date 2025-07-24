# IOTCNT - Sistema de Controlo de Irrigação IoT

Um sistema completo de automação de irrigação baseado em ESP32 e Laravel, com controlo via web e Telegram.

## 🌱 Características

- **Controlo Automático**: Agendamento de irrigação por dias da semana
- **Controlo Manual**: Interface web e comandos Telegram para controlo manual
- **5 Válvulas**: Suporte para até 5 válvulas de irrigação independentes
- **Monitorização**: Logs detalhados de todas as operações
- **Notificações**: Alertas via Telegram para administradores
- **API REST**: Comunicação entre ESP32 e servidor Laravel
- **Dashboard**: Interface web responsiva para gestão completa

## 🏗️ Arquitetura

### Componentes Principais

- **ESP32 Firmware**: Controlo direto das válvulas e comunicação com API
- **Laravel Backend**: API REST, gestão de utilizadores, agendamentos e logs
- **Interface Web**: Dashboard para administração e monitorização
- **Bot Telegram**: Controlo remoto e notificações
- **Base de Dados**: MySQL para armazenamento de dados
- **Redis**: Cache e gestão de filas

### Tecnologias Utilizadas

- **Hardware**: ESP32, Relés, Válvulas solenoides
- **Backend**: Laravel 10, PHP 8.2, MySQL 8.0, Redis
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Comunicação**: HTTP REST API, Telegram Bot API
- **Deployment**: Docker, Docker Compose, Nginx

## 🚀 Instalação Rápida

### Pré-requisitos

- Docker e Docker Compose
- Git

### Deployment Automático

```bash
git clone https://github.com/seu-usuario/iotcnt.git
cd iotcnt
./deploy.sh
```

O script de deployment irá:
1. Configurar o ambiente Docker
2. Instalar dependências Laravel
3. Configurar base de dados
4. Criar utilizador administrador
5. Configurar Telegram webhook

## 📱 Configuração ESP32

### Hardware

```
ESP32 → Módulo Relé → Válvulas Solenoides
```

### Configuração Firmware

1. Abrir `esp32_irrigation_controller/config.h`
2. Configurar WiFi:
   ```cpp
   #define WIFI_SSID "SUA_REDE_WIFI"
   #define WIFI_PASSWORD "SUA_SENHA_WIFI"
   ```

3. Configurar API:
   ```cpp
   #define API_SERVER_HOST "http://seu-servidor.com"
   #define API_TOKEN "seu_token_sanctum"
   ```

4. Configurar pinos das válvulas:
   ```cpp
   #define VALVE_PIN_1 23
   #define VALVE_PIN_2 22
   // ... etc
   ```

### Upload do Firmware

```bash
cd esp32_irrigation_controller
pio run --target upload
pio device monitor
```

## 🤖 Configuração Telegram

### Criar Bot

1. Falar com @BotFather no Telegram
2. Criar novo bot: `/newbot`
3. Obter token do bot
4. Configurar no `.env`:
   ```
   TELEGRAM_BOT_TOKEN=seu_token_aqui
   ```

### Configurar Webhook

Aceder a: `https://seu-dominio.com/telegram/set-webhook`

### Comandos Disponíveis

**Utilizadores:**
- `/start` - Iniciar bot
- `/status` - Estado das válvulas
- `/logs` - Últimos eventos
- `/schedules` - Agendamentos ativos

**Administradores:**
- `/emergency_stop` - Parar todas as válvulas
- `/start_cycle` - Iniciar ciclo manual
- `/valve_on [N]` - Ligar válvula N
- `/valve_off [N]` - Desligar válvula N
- `/system_status` - Estado detalhado

## 🌐 Interface Web

### Acesso

- **URL**: `http://localhost` (ou seu domínio)
- **Admin**: Acesso completo a todas as funcionalidades
- **Utilizador**: Visualização de estado e logs

### Funcionalidades

#### Dashboard Principal
- Estado atual das válvulas
- Próximos agendamentos
- Logs recentes do sistema

#### Administração
- Gestão de válvulas
- Configuração de agendamentos
- Gestão de utilizadores
- Logs de operação
- Utilizadores Telegram

## 📊 API Endpoints

### ESP32 Endpoints

```
GET  /api/esp32/config          - Obter configuração
POST /api/esp32/valve-status    - Reportar estado válvula
POST /api/esp32/log            - Enviar log
GET  /api/esp32/commands       - Obter comandos pendentes
```

### Controlo Manual

```
POST /api/esp32/control-valve  - Controlar válvula
POST /api/esp32/start-cycle    - Iniciar ciclo
POST /api/esp32/stop-all       - Parar todas
```

## 🗄️ Base de Dados

### Tabelas Principais

- `users` - Utilizadores do sistema
- `valves` - Configuração das válvulas
- `schedules` - Agendamentos de irrigação
- `operation_logs` - Logs de todas as operações
- `telegram_users` - Utilizadores Telegram
- `system_settings` - Configurações do sistema

## 🔧 Configuração Avançada

### Variáveis de Ambiente

```env
# Aplicação
APP_NAME=IOTCNT
APP_ENV=production
APP_URL=https://seu-dominio.com

# Base de Dados
DB_HOST=database
DB_DATABASE=iotcnt
DB_USERNAME=iotcnt_user
DB_PASSWORD=senha_segura

# Telegram
TELEGRAM_BOT_TOKEN=seu_token
TELEGRAM_BOT_USERNAME=seu_bot

# ESP32
ESP32_API_TOKEN=token_esp32
```

### Docker Compose

O sistema inclui:
- **app**: Aplicação Laravel
- **webserver**: Nginx
- **database**: MySQL 8.0
- **redis**: Cache e filas
- **queue**: Worker de filas
- **scheduler**: Tarefas agendadas

## 📈 Monitorização

### Logs

- **Sistema**: `/admin/logs`
- **ESP32**: Logs locais em LittleFS
- **Telegram**: Notificações automáticas

### Métricas

- Estado das válvulas em tempo real
- Histórico de ativações
- Estatísticas de uso por fonte
- Alertas de erro

## 🔒 Segurança

### Autenticação

- Laravel Sanctum para API
- Roles (admin/user)
- Autorização Telegram por admin

### Comunicação

- HTTPS recomendado para produção
- Tokens API seguros
- Validação de entrada

## 🚨 Troubleshooting

### ESP32 não conecta

1. Verificar credenciais WiFi
2. Verificar URL do servidor
3. Verificar token API
4. Verificar logs serial

### Telegram não funciona

1. Verificar token do bot
2. Configurar webhook
3. Autorizar utilizadores
4. Verificar logs

### Válvulas não respondem

1. Verificar ligações hardware
2. Verificar pinos configurados
3. Verificar estado dos relés
4. Verificar logs ESP32

## 📝 Desenvolvimento

### Estrutura do Projeto

```
├── app/                    # Laravel application
├── esp32_irrigation_controller/  # ESP32 firmware
├── resources/views/        # Blade templates
├── docker/                # Docker configuration
├── routes/                # Laravel routes
└── database/migrations/   # Database migrations
```

### Comandos Úteis

```bash
# Laravel
php artisan migrate
php artisan queue:work
php artisan schedule:run

# Docker
docker-compose up -d
docker-compose logs -f app
docker-compose exec app bash

# ESP32
pio run
pio device monitor
pio run --target upload
```

## 🤝 Contribuição

1. Fork o projeto
2. Criar branch para feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit das alterações (`git commit -am 'Adicionar nova funcionalidade'`)
4. Push para branch (`git push origin feature/nova-funcionalidade`)
5. Criar Pull Request

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - ver arquivo [LICENSE](LICENSE) para detalhes.

## 🙏 Agradecimentos

- Comunidade ESP32
- Laravel Framework
- Telegram Bot API
- Contribuidores do projeto

---

**IOTCNT** - Irrigação Inteligente para o Futuro 🌱💧
