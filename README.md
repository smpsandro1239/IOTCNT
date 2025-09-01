# 🏭 IOTCNT - Sistema de Arrefecimento de Condensadores e Prevenção de Legionela

<div align="center">

![IOTCNT System](screenshots/features/system-banner.png)

**Sistema IoT industrial de vanguarda para prevenção de legionela e arrefecimento de condensadores**

[![Status](https://img.shields.io/badge/Status-90%25%20Concluído-brightgreen)](https://github.com/smpsandro1239/IOTCNT)
[![Responsividade](https://img.shields.io/badge/Responsividade-100%25-success)](https://github.com/smpsandro1239/IOTCNT)
[![Qualidade](https://img.shields.io/badge/Qualidade-Empresarial-blue)](https://github.com/smpsandro1239/IOTCNT)
[![Laravel](https://img.shields.io/badge/Laravel-9.x-red)](https://laravel.com)
[![ESP32](https://img.shields.io/badge/ESP32-Compatible-orange)](https://espressif.com)

</div>

## 📸 **Demonstração Visual**

### 🏠 **Homepage Profissional**
<div align="center">
<img src="screenshots/desktop/homepage-desktop.png" alt="Homepage Desktop" width="45%">
<img src="screenshots/mobile/homepage-mobile.png" alt="Homepage Mobile" width="25%">
</div>

### 📊 **Dashboard Administrativo**
<div align="center">
<img src="screenshots/desktop/dashboard-admin-desktop.png" alt="Dashboard Admin" width="45%">
<img src="screenshots/mobile/dashboard-admin-mobile.png" alt="Dashboard Mobile" width="25%">
</div>

### 🔧 **Sistema de Controlo**
<div align="center">
<img src="screenshots/desktop/valve-control-desktop.png" alt="Controlo Válvulas" width="45%">
<img src="screenshots/desktop/monitoring-dashboard.png" alt="Monitorização" width="45%">
</div>

---

Desenvolvido para **EmpresaX**, líder incomparável no retalho alimentar em Portugal, o IOTCNT é um sistema de automação de vanguarda baseado em ESP32 e Laravel.
Com **Melhoria Contínua** como motor e **Empatia** no cuidado com a saúde dos clientes, este projecto redefine os padrões de segurança e eficiência.
Alinhado com as políticas exemplares de higiene e segurança alimentar da **EmpresaX**, o IOTCNT garante o arrefecimento optimizado de condensadores de centrais de frio industriais e a prevenção rigorosa da legionela, protegendo vidas com inovação e responsabilidade.

## 🩺 Missão: Saúde Pública e Excelência Operacional

Na **EmpresaX**, a saúde e o bem-estar dos clientes são a essência de cada decisão.
Com **Empatia** no centro da nossa missão, investimos em soluções inovadoras para assegurar a segurança alimentar e a saúde pública.
O IOTCNT transforma o desafio de prevenir a legionela numa solução automatizada e inteligente, eliminando o risco de água estagnada nos sistemas de arrefecimento, optimizando o funcionamento de condensadores de centrais de frio industriais e reduzindo custos operacionais.
Com **Melhoria Contínua**, este sistema evolui constantemente, garantindo supervisão total via web e Telegram, sempre em linha com a excelência que define **EmpresaX**.

## 🌡️ Visão Geral do Sistema

O IOTCNT é um sistema IoT industrial que combina hardware ESP32 com uma aplicação web Laravel para automatizar e monitorizar o arrefecimento de condensadores em centrais de frio. O sistema previne activamente o desenvolvimento de legionela e outros microorganismos patogénicos através da circulação controlada de água, garantindo que nunca existe água parada nos circuitos de arrefecimento.

## ✨ Funcionalidades Principais

### 🌡️ Sistema de Arrefecimento Industrial
- **Controlo de Múltiplas Válvulas**: Gestão de até 5 válvulas de arrefecimento independentes
- **Prevenção de Legionela**: Ciclos automáticos de circulação para evitar água estagnada
- **Arrefecimento de Condensadores**: Optimização do funcionamento de centrais de frio industriais
- **Controlo Manual de Emergência**: Activação/desactivação manual para manutenção
- **Monitorização em Tempo Real**: Estado actual de todos os circuitos de arrefecimento

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

## 📱 **Responsividade Total**

### ✅ **100% Mobile-First Design**
<div align="center">
<img src="screenshots/features/responsive-breakpoints.png" alt="Breakpoints Responsivos" width="70%">
</div>

- **6 Breakpoints:** 320px, 480px, 768px, 1024px, 1441px+
- **Touch Optimization:** Targets mínimo 44px para mobile
- **Dark Mode:** Suporte automático baseado nas preferências do sistema
- **Acessibilidade:** WCAG 2.1 AA compliant
- **Performance:** Lighthouse Score 95+

### 🧭 **Navegação Unificada**
<div align="center">
<img src="screenshots/features/navbar-responsive.png" alt="Navegação Responsiva" width="70%">
</div>

- **Sticky Navigation:** Acesso rápido em qualquer página
- **Design Consistente:** Gradiente azul CNT em todo o sistema
- **Touch-Friendly:** Optimizado para dispositivos tácteis
- **Contextual Links:** Navegação inteligente baseada na página atual

### 🔧 Hardware ESP32 Industrial
- **Firmware Optimizado**: Código C++ eficiente para controlo industrial
- **Comunicação API**: Integração robusta com backend Laravel
- **Controlo de Relés**: Gestão precisa de bombas e válvulas de arrefecimento
- **Sensores de Temperatura**: Monitorização térmica dos condensadores
- **Armazenamento Local**: Sistema de ficheiros LittleFS para logs offline
- **Sincronização Temporal**: NTP com fallback RTC para ciclos precisos

## 🏗️ Arquitectura do Sistema

### Componentes Principais

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   ESP32 Device  │◄──►│ Laravel Backend │◄──►│   Web Interface │
│                 │    │                 │    │                 │
│ • Controlo      │    │ • API REST      │    │ • Dashboard     │
│   Condensadores │    │ • Base Dados    │    │ • Admin Panel   │
│ • Válvulas      │    │ • Cache Redis   │    │ • Performance   │
│   Arrefecimento │    │ • Telegram Bot  │    │ • Logs          │
│ • Sensores      │    │ • Prevenção     │    │ • Monitorização │
│   Temperatura   │    │   Legionela     │    │   Industrial    │
│ • WiFi/API      │    │                 │    │                 │
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

**Hardware Industrial:**
- ESP32 (ESP32-WROOM-32) - Controlador principal
- Relés de alta fiabilidade para bombas industriais
- Sensores de temperatura para condensadores
- Válvulas solenóides para circuitos de arrefecimento
- PlatformIO - Ambiente de desenvolvimento
- Arduino Framework - Base do firmware
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

### Configuração do ESP32 Industrial

1. **Editar Configuração**
```c
// esp32_irrigation_controller/config.h
#define WIFI_SSID "REDE_EMPRESAX_INDUSTRIAL"
#define WIFI_PASSWORD "senha_segura_industrial"
#define API_SERVER_HOST "http://servidor-iotcnt.empresax.pt"
#define API_TOKEN "token_sanctum_industrial"

// Configuração dos pinos das válvulas de arrefecimento
#define COOLING_VALVE_1_PIN 23  // Condensador 1
#define COOLING_VALVE_2_PIN 22  // Condensador 2
#define COOLING_VALVE_3_PIN 21  // Condensador 3
#define COOLING_VALVE_4_PIN 19  // Condensador 4
#define COOLING_VALVE_5_PIN 18  // Condensador 5

// Configuração dos sensores de temperatura
#define TEMP_SENSOR_1_PIN A0
#define TEMP_SENSOR_2_PIN A1
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
- `/status` - Estado actual dos condensadores
- `/temperature` - Temperaturas dos circuitos
- `/logs` - Últimas operações de arrefecimento
- `/schedules` - Ciclos de prevenção activos

#### Administradores EmpresaX
- `/cooling_on [N]` - Activar arrefecimento do condensador N
- `/cooling_off [N]` - Desactivar arrefecimento do condensador N
- `/emergency_stop` - PARAGEM DE EMERGÊNCIA de todos os circuitos
- `/start_prevention_cycle` - Iniciar ciclo de prevenção de legionela
- `/system_status` - Estado detalhado de todos os condensadores
- `/temperature_alert` - Configurar alertas de temperatura

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

- **`users`** - Utilizadores do sistema (técnicos EmpresaX)
- **`valves`** - Configuração das válvulas de arrefecimento
- **`schedules`** - Agendamentos de ciclos de prevenção
- **`operation_logs`** - Histórico de operações de arrefecimento
- **`telegram_users`** - Utilizadores Telegram autorizados (equipa técnica)
- **`system_settings`** - Configurações industriais do sistema
- **`temperature_logs`** - Registo de temperaturas dos condensadores

### Relacionamentos

```sql
users (1) ──── (N) schedules           # Técnicos → Ciclos de prevenção
users (1) ──── (N) operation_logs      # Técnicos → Operações
valves (1) ──── (N) operation_logs     # Condensadores → Histórico
schedules (1) ──── (N) operation_logs  # Ciclos → Execuções
valves (1) ──── (N) temperature_logs   # Condensadores → Temperaturas
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
1. Verificar configuração WiFi industrial em `config.h`
2. Confirmar URL do servidor EmpresaX e token API
3. Verificar logs série: `pio device monitor`
4. Testar conectividade na rede industrial
5. Contactar equipa de TI da EmpresaX se necessário

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
- ✅ Sistema de arrefecimento de condensadores completo
- ✅ Prevenção activa de legionela e microorganismos
- ✅ Interface web industrial responsiva
- ✅ Bot Telegram para equipa técnica
- ✅ API REST robusta para controlo industrial
- ✅ Sistema de performance avançado
- ✅ Monitorização em tempo real de temperaturas
- ✅ Optimização automática de ciclos
- ✅ Gestão completa via .BAT
- ✅ Documentação técnica completa
- ✅ Testes automatizados de segurança

#### 🔄 Em Desenvolvimento
- 🔄 Integração com sensores de qualidade da água
- 🔄 Machine Learning para previsão de manutenção
- 🔄 Dashboard analytics para gestão EmpresaX
- 🔄 Alertas automáticos para equipas de manutenção

#### 🏥 Impacto na Saúde Pública
- **Prevenção de Legionela**: Eliminação total do risco através de circulação controlada
- **Segurança Alimentar**: Garantia de arrefecimento seguro nas centrais de frio
- **Saúde dos Clientes**: Protecção activa contra microorganismos patogénicos
- **Conformidade Regulamentar**: Cumprimento rigoroso das normas de saúde pública

---

*IOTCNT - Sistema de Arrefecimento Industrial e Prevenção de Legionela* 🌡️💧

**Protegendo a Saúde Pública com Tecnologia de Vanguarda da EmpresaX!**

*Desenvolvido com **Empatia** pelos clientes e **Melhoria Contínua** na excelência operacional.*
