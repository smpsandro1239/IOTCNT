# IOTCNT - Sistema de Irrigação IoT Inteligente

Sistema completo de automação de irrigação baseado em ESP32 e Laravel, desenvolvido para proporcionar controlo total e monitorização avançada de sistemas de rega.

## 🌱 Visão Geral

O IOTCNT é um sistema de irrigação IoT que combina hardware ESP32 com uma aplicação web Laravel para automatizar e monitorizar sistemas de rega. Com funcionalidades avançadas de performance, controlo remoto via Telegram e interface web intuitiva, oferece uma solução completa para gestão de irrigação.

## ✨ Funcionalidades Principais

### 🚀 Sistema de Irrigação
- **Controlo de Múltiplas Válvulas**: Gestão de até 5 válvulas independentes
- **Agendamentos Automáticos**: Ciclos de irrigação programáveis por dia da semana
- **Controlo Manual**: Activação/desactivação manual de válvulas
- **Monitorização em Tempo Real**: Estado actual de todas as válvulas

### 📊 Sistema de Performance (NOVO!)
- **Métricas em Tempo Real**: Tempo de resposta, uso de memória, cache hit rate
- **Optimização Automática**: Limpeza de cache e optimização de base de dados
- **Detecção de Queries Lentas**: Identificação automática de gargalos
- **Recomendações Inteligentes**: Sugestões personalizadas de melhoria
- **Dashboard Visual**: Interface completa de monitorização de performance

### 🌐 Interface Web Completa
- **Dashboard Principal**: Visão geral do sistema e válvulas
- **Painel Administrativo**: Gestão completa de utilizadores, válvulas e agendamentos
- **Sistema de Logs**: Histórico detalhado de todas as operações
- **Gestão de Configurações**: Parâmetros configuráveis do sistema

### 📱 Integração Telegram
- **Bot Telegram**: Controlo remoto via comandos de chat
- **Notificações**: Alertas automáticos de operações e problemas
- **Gestão de Utilizadores**: Autorização e controlo de acesso via Telegram

### 🔧 Hardware ESP32
- **Firmware Optimizado**: Código C++ eficiente para ESP32
- **Comunicação API**: Integração robusta com backend Laravel
- **Armazenamento Local**: Sistema de ficheiros LittleFS para logs offline
- **Sincronização Temporal**: NTP com fallback RTC para precisão

## 🏗️ Arquitectura do Sistema

### Componentes Principais

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   ESP32 Device  │◄──►│ Laravel Backend │◄──►│   Web Interface │
│                 │    │                 │    │                 │
│ • Controlo      │    │ • API REST      │    │ • Dashboard     │
│   Válvulas      │    │ • Base Dados    │    │ • Admin Panel   │
│ • Sensores      │    │ • Cache Redis   │    │ • Performance   │
│ • WiFi/API      │    │ • Telegram Bot  │    │ • Logs          │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Stack Tecnológico

**Backend:**
- Laravel 9 (PHP 8.1+)
- MySQL 8.0
- Redis (Cache e Sessões)
- Laravel Sanctum (Autenticação API)

**Frontend:**
- Blade Templates
- Tailwind CSS
- Alpine.js
- Vite (Build Tool)

**Hardware:**
- ESP32 (ESP32-WROOM-32)
- PlatformIO
- Arduino Framework
- Bibliotecas: NTPClient, RTClib, ArduinoJson

**Deployment:**
- Docker & Docker Compose
- Nginx
- Ambiente containerizado completo

## 🚀 Instalação e Configuração

### Método 1: Instalação Rápida (Recomendado)

#### Windows:
```cmd
# Verificação rápida do sistema
quick_check.bat

# Gestão completa do sistema
iotcnt_complete.bat

# Iniciar sistema
start_iotcnt.bat
```

#### Linux/macOS:
```bash
# Clonar repositório
git clone https://github.com/seu-usuario/iotcnt.git
cd iotcnt

# Iniciar com Make
make install
make start
```

### Método 2: Instalação Manual

#### Pré-requisitos
- Docker Desktop
- Git
- Editor de texto

#### Passos de Instalação

1. **Clonar o Repositório**
```bash
git clone https://github.com/seu-usuario/iotcnt.git
cd iotcnt
```

2. **Configurar Ambiente**
```bash
# Copiar ficheiros de configuração
cp .env.example .env
cp docker-compose.example.yml docker-compose.yml
cp esp32_irrigation_controller/config.example.h esp32_irrigation_controller/config.h
```

3. **Configurar .env**
```env
# Aplicação
APP_NAME=IOTCNT
APP_ENV=production
APP_URL=http://localhost

# Base de Dados
DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=iotcnt
DB_USERNAME=iotcnt_user
DB_PASSWORD=sua_senha_segura

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Telegram Bot
TELEGRAM_BOT_TOKEN=seu_token_telegram
TELEGRAM_BOT_USERNAME=seu_bot_username

# ESP32 API
ESP32_API_TOKEN=token_esp32_seguro
```

4. **Iniciar Sistema**
```bash
# Construir e iniciar containers
docker-compose up -d

# Configurar Laravel
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```

### Configuração do ESP32

1. **Editar Configuração**
```c
// esp32_irrigation_controller/config.h
#define WIFI_SSID "SUA_REDE_WIFI"
#define WIFI_PASSWORD "SUA_SENHA_WIFI"
#define API_SERVER_HOST "http://seu-servidor.com"
#define API_TOKEN "seu_token_sanctum"

// Configuração dos pinos das válvulas
#define VALVE_1_PIN 23
#define VALVE_2_PIN 22
#define VALVE_3_PIN 21
#define VALVE_4_PIN 19
#define VALVE_5_PIN 18
```

2. **Upload do Firmware**
```bash
cd esp32_irrigation_controller
pio run --target upload
pio device monitor
```

### Configuração do Telegram Bot

1. **Criar Bot**
   - Contactar @BotFather no Telegram
   - Executar `/newbot` e seguir instruções
   - Copiar token para `.env`

2. **Configurar Webhook**
   - Aceder a `http://localhost/telegram/set-webhook`
   - Verificar configuração no painel admin

## 💻 Utilização do Sistema

### Interface Web

#### Acesso Principal
- **URL**: `http://localhost`
- **Login Inicial**: admin@iotcnt.local / admin123

#### Interfaces Disponíveis
- **Dashboard Principal**: `/dashboard` - Visão geral do sistema
- **Painel Admin**: `/admin/dashboard` - Gestão completa
- **Performance**: `/admin/performance` - Monitorização avançada
- **Logs**: `/admin/logs` - Histórico de operações
- **Configurações**: `/admin/settings` - Parâmetros do sistema

### Comandos Telegram

#### Utilizadores Gerais
- `/start` - Iniciar interacção com o bot
- `/status` - Estado actual das válvulas
- `/logs` - Últimas operações
- `/schedules` - Agendamentos activos

#### Administradores
- `/valve_on [N]` - Activar válvula N
- `/valve_off [N]` - Desactivar válvula N
- `/start_schedule [ID]` - Iniciar agendamento
- `/stop_all` - Parar todas as válvulas
- `/system_status` - Estado detalhado do sistema

### Sistema de Performance

#### Acesso ao Dashboard
```
http://localhost/admin/performance
```

#### Funcionalidades Disponíveis
- **Métricas em Tempo Real**: Tempo resposta, memória, cache
- **Optimização Automática**: Botão "Optimizar Sistema"
- **Limpeza de Cache**: Botão "Limpar Cache"
- **Queries Lentas**: Detecção automática de problemas
- **Recomendações**: Sugestões inteligentes de melhoria

## 📊 API REST

### Endpoints ESP32

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/esp32/config` | Configuração do sistema |
| POST | `/api/esp32/valve-status` | Reportar estado das válvulas |
| POST | `/api/esp32/log` | Registar eventos |
| GET | `/api/esp32/commands` | Obter comandos pendentes |
| POST | `/api/esp32/heartbeat` | Sinal de vida do dispositivo |

### Endpoints Web

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/valves` | Listar válvulas |
| POST | `/api/valves/{id}/toggle` | Alternar estado da válvula |
| GET | `/api/schedules` | Listar agendamentos |
| GET | `/api/logs` | Obter logs do sistema |
| GET | `/api/performance/metrics` | Métricas de performance |

## 🗄️ Base de Dados

### Tabelas Principais

- **`users`** - Utilizadores do sistema
- **`valves`** - Configuração das válvulas
- **`schedules`** - Agendamentos de irrigação
- **`operation_logs`** - Histórico de operações
- **`telegram_users`** - Utilizadores Telegram autorizados
- **`system_settings`** - Configurações do sistema

### Relacionamentos

```sql
users (1) ──── (N) schedules
users (1) ──── (N) operation_logs
valves (1) ──── (N) operation_logs
schedules (1) ──── (N) operation_logs
```

## 🔧 Gestão do Sistema

### Ficheiros .BAT (Windows)

#### Verificação e Gestão
```cmd
# Verificação rápida
quick_check.bat

# Gestão completa (RECOMENDADO)
iotcnt_complete.bat

# Iniciar sistema
start_iotcnt.bat

# Parar sistema
stop_iotcnt.bat

# Verificação detalhada
check_iotcnt.bat

# Correcção de problemas
fix_iotcnt.bat
```

#### Menu do Sistema Completo (`iotcnt_complete.bat`)

**Funcionalidades Principais:**
1. 🚀 Iniciar Sistema Completo
2. 🔧 Verificação e Diagnóstico Completo
3. 📊 Monitorização e Performance
4. 📋 Gestão de Logs
5. 🌐 Gestão Web
6. 🗄️ Gestão de Base de Dados
7. ⚙️ Configuração e Manutenção
8. 🔄 Operações de Sistema
9. 📖 Documentação e Ajuda

**Menu de Performance:**
- 📊 Abrir Dashboard de Performance
- 🔍 Verificar Métricas do Sistema
- 🗄️ Estatísticas de Cache
- 🐌 Detectar Queries Lentas
- 🧹 Limpar Cache do Sistema
- ⚡ Optimizar Sistema Completo
- 📈 Monitorização em Tempo Real
- 🔧 Diagnóstico de Performance

### Comandos Make (Linux/macOS)

```bash
# Gestão básica
make start          # Iniciar sistema
make stop           # Parar sistema
make restart        # Reiniciar sistema
make status         # Ver estado

# Desenvolvimento
make install        # Instalação completa
make setup          # Configurar Laravel
make test           # Executar testes
make logs           # Ver logs

# ESP32
make esp32-build    # Compilar firmware
make esp32-upload   # Upload para dispositivo
make esp32-monitor  # Monitor série

# Manutenção
make backup         # Backup da base de dados
make clean          # Limpeza do sistema
make cache-clear    # Limpar caches
```

## 🛡️ Segurança

### Autenticação e Autorização
- **Laravel Sanctum**: Tokens API seguros
- **Roles e Permissões**: Admin/User com controlo granular
- **Middleware**: Protecção de rotas sensíveis
- **CSRF Protection**: Protecção contra ataques CSRF

### Comunicação Segura
- **HTTPS**: Obrigatório em produção
- **API Tokens**: Autenticação segura ESP32
- **Rate Limiting**: Protecção contra abuso
- **Input Validation**: Sanitização rigorosa

### Configurações de Segurança
```env
# Produção
APP_ENV=production
APP_DEBUG=false
HTTPS_ONLY=true

# Tokens seguros
ESP32_API_TOKEN=token_complexo_aqui
TELEGRAM_BOT_TOKEN=token_telegram_aqui
```

## 📈 Monitorização e Performance

### Métricas Disponíveis
- **Tempo de Resposta**: Latência das operações
- **Uso de Memória**: Consumo RAM actual e pico
- **Cache Hit Rate**: Eficiência do sistema de cache
- **Queries de BD**: Número e performance das consultas
- **Uptime**: Tempo de funcionamento do sistema

### Optimizações Automáticas
- **Cache Inteligente**: TTL configurável por tipo de dados
- **Índices de BD**: Optimização automática de consultas
- **Limpeza de Logs**: Remoção automática de dados antigos
- **Compressão**: Optimização de recursos

### Alertas e Notificações
- **Telegram**: Notificações instantâneas
- **Logs**: Registo detalhado de eventos
- **Dashboard**: Indicadores visuais de estado
- **Email**: Alertas críticos (configurável)

## 🔧 Resolução de Problemas

### Problemas Comuns

#### Sistema não inicia
```cmd
# Windows
fix_iotcnt.bat

# Verificar Docker
docker --version
docker-compose --version

# Verificar portas
netstat -an | findstr ":80\|:3306\|:6379"
```

#### ESP32 não conecta
1. Verificar configuração WiFi em `config.h`
2. Confirmar URL do servidor e token API
3. Verificar logs série: `pio device monitor`
4. Testar conectividade de rede

#### Telegram não responde
1. Verificar token no `.env`
2. Configurar webhook: `/telegram/set-webhook`
3. Autorizar utilizador no painel admin
4. Verificar logs do bot

#### Performance baixa
1. Aceder ao dashboard: `/admin/performance`
2. Executar "Optimizar Sistema"
3. Verificar queries lentas
4. Limpar cache se necessário

### Logs e Diagnósticos

#### Localização dos Logs
- **Laravel**: `storage/logs/laravel.log`
- **Docker**: `docker-compose logs -f`
- **ESP32**: Monitor série PlatformIO
- **Nginx**: Container webserver logs

#### Comandos de Diagnóstico
```bash
# Estado dos containers
docker-compose ps

# Logs em tempo real
docker-compose logs -f

# Verificar conectividade
curl -I http://localhost

# Testar API
curl http://localhost/api/ping
```

## 🧪 Testes

### Executar Testes
```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Com cobertura
php artisan test --coverage
```

### Testes Disponíveis
- **Unit Tests**: Modelos e serviços
- **Feature Tests**: Endpoints e funcionalidades
- **Performance Tests**: Sistema de optimização
- **API Tests**: Endpoints ESP32 e web

## 📚 Documentação Adicional

### Ficheiros de Documentação
- **`API_DOCUMENTATION.md`** - Documentação completa da API
- **`DEVELOPER_GUIDE.md`** - Guia para programadores
- **`TROUBLESHOOTING.md`** - Resolução de problemas
- **`SECURITY.md`** - Guia de segurança
- **`CHANGELOG.md`** - Histórico de alterações

### Colecções Postman
- **`docs/postman/IOTCNT_API.json`** - Colecção completa da API
- Importar no Postman para testes rápidos

## 🤝 Contribuição

### Como Contribuir
1. Fork do repositório
2. Criar branch: `git checkout -b feature/nova-funcionalidade`
3. Commit: `git commit -am 'Adiciona nova funcionalidade'`
4. Push: `git push origin feature/nova-funcionalidade`
5. Criar Pull Request

### Padrões de Código
- **PSR-12**: Padrão de código PHP
- **Laravel Conventions**: Convenções do framework
- **Comentários**: Documentação inline
- **Testes**: Cobertura obrigatória para novas funcionalidades

## 📄 Licença

Este projecto está licenciado sob a Licença MIT - consulte o ficheiro [LICENSE](LICENSE) para detalhes.

## 🙏 Agradecimentos

- **Comunidade ESP32** - Hardware e bibliotecas
- **Laravel Framework** - Framework web robusto
- **Telegram Bot API** - Integração de mensagens
- **Docker Community** - Containerização
- **Open Source Community** - Ferramentas e inspiração

## 📞 Suporte

### Recursos de Ajuda
- **Documentação**: Este README e ficheiros em `/docs`
- **Issues**: GitHub Issues para bugs e sugestões
- **Discussions**: GitHub Discussions para dúvidas
- **Wiki**: Documentação adicional no GitHub Wiki

### Contacto
- **Email**: suporte@iotcnt.local
- **GitHub**: [Issues](https://github.com/seu-usuario/iotcnt/issues)
- **Telegram**: @iotcnt_support

---

## 🎯 Estado do Projecto

### Versão Actual: v2.0.0

#### ✅ Funcionalidades Implementadas
- ✅ Sistema de irrigação completo
- ✅ Interface web responsiva
- ✅ Bot Telegram integrado
- ✅ API REST robusta
- ✅ Sistema de performance avançado
- ✅ Monitorização em tempo real
- ✅ Optimização automática
- ✅ Gestão completa via .BAT
- ✅ Documentação completa
- ✅ Testes automatizados

#### 🔄 Em Desenvolvimento
- 🔄 Aplicação móvel nativa
- 🔄 Integração com sensores de humidade
- 🔄 Machine Learning para optimização
- 🔄 Dashboard analytics avançado

---

*IOTCNT - Sistema de Irrigação IoT Inteligente* 🌱💧

**Transforme a sua irrigação com tecnologia de ponta!**
