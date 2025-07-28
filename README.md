# IOTCNT - Controlo e arrefecimento de Centrais de Frio e Prevenção de Legionella

Desenvolvido para **O Continente**, líder incomparável no retalho alimentar em Portugal, o IOTCNT é um sistema de automação de vanguarda baseado em ESP32 e Laravel. 
Com **Melhoria Contínua** como motor e **Empatia** no cuidado com a saúde dos clientes, este projeto redefine os padrões de segurança e eficiência. 
Alinhado com as políticas exemplares de higiene e segurança alimentar do **Continente**, o IOTCNT garante o arrefecimento otimizado de centrais de frio e a prevenção rigorosa da legionella, protegendo vidas com inovação e responsabilidade.

## 🩺 Missão: Saúde Pública e Excelência Operacional

No **Continente**, a saúde e o bem-estar dos clientes são a essência de cada decisão. 
Com **Empatia** no centro da nossa missão, investimos em soluções inovadoras para assegurar a segurança alimentar e a saúde pública. 
O IOTCNT transforma o desafio de prevenir a legionella numa solução automatizada e inteligente, eliminando o risco de água estagnada, otimizando o arrefecimento de condensadores e reduzindo custos operacionais. 
Com **Melhoria Contínua**, este sistema evolui constantemente, garantindo supervisão total via web e Telegram, sempre em linha com a excelência que define **O Continente**.

## 🚀 Características que Redefinem o Futuro

- **Automação de Elite**: Ciclos automáticos de arrefecimento e renovação de água, eliminando riscos de legionella.
- **Prevenção Implacável**: Agendamentos inteligentes para evitar água parada, com **Empatia** pela saúde pública.
- **Controlo Total**: Interface web intuitiva e comandos Telegram para ajustes imediatos.
- **Monitorização Avançada**: Logs detalhados em tempo real de operações, alertas e anomalias.
- **Notificações Instantâneas**: Alertas via Telegram para respostas ágeis, refletindo **Melhoria Contínua**.
- **API REST Poderosa**: Integração perfeita entre ESP32 e backend Laravel.
- **Dashboard Visionário**: Supervisão completa com uma interface web moderna e responsiva.

## 🏗️ Arquitetura

### Componentes Principais

- **ESP32 Firmware**: Controlo preciso de bombas e válvulas, com comunicação robusta via API.
- **Laravel Backend**: API REST de alto desempenho para gestão de utilizadores, agendamentos e logs.
- **Interface Web**: Dashboard dinâmico para administração e monitorização em tempo real.
- **Bot Telegram**: Controlo remoto e alertas instantâneos, com **Empatia** na interação com os utilizadores.
- **Base de Dados MySQL**: Armazenamento seguro de eventos, configurações e históricos.
- **Redis**: Gestão de filas e cache para operações ultra-rápidas.

### Tecnologias de Ponta

- **Hardware**: ESP32, relés de alta fiabilidade, sensores de temperatura e fluxo.
- **Backend**: Laravel 9, PHP 8.1+, MySQL 8.0, Redis.
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js.
- **Comunicação**: HTTP REST API, Telegram Bot API.
- **Deployment**: Docker, Docker Compose, Nginx.

## ⚡ Instalação Rápida e Eficiente

### Pré-requisitos

- PHP 8.1+ ou Docker
- Composer
- MySQL/SQLite
- Git

### Setup Local Simplificado

```bash
git clone https://github.com/seu-usuario/iotcnt.git
cd iotcnt
composer install
cp .env.example .env
php artisan key:generate
php simple_setup.php
php artisan serve
```

### Deployment Automático com Docker

```bash
git clone https://github.com/seu-usuario/iotcnt.git
cd iotcnt
./deploy.sh
```

O script de deployment do **Continente**:

- Configura o ambiente Docker com precisão.
- Instala dependências Laravel automaticamente.
- Configura a base de dados e cria um utilizador administrador.
- Ativa o webhook Telegram para controlo instantâneo.

## ⚙️ Configuração do Sistema

### ESP32: O Coração da Automação

- **Ligações**: ESP32 conectado a relés para bombas e válvulas, garantindo circulação de água precisa.
- **Configuração**: Edite `esp32_irrigation_controller/config.h`:

```c
#define WIFI_SSID "SUA_REDE_WIFI"
#define WIFI_PASSWORD "SUA_SENHA_WIFI"
#define API_SERVER_HOST "http://seu-servidor.com"
#define API_TOKEN "seu_token_sanctum"
#define VALVE_PIN_1 23
#define VALVE_PIN_2 22
// ... etc
```

- **Upload do Firmware**:

```bash
cd esp32_irrigation_controller
pio run --target upload
pio device monitor
```

### Telegram: Controlo com telegram

1. Crie um bot com o @BotFather e obtenha o token.
2. Configure no `.env`:

```env
TELEGRAM_BOT_TOKEN=seu_token_aqui
```

3. Ative o webhook: `https://seu-dominio.com/telegram/set-webhook`.

#### Comandos Disponíveis

**Utilizadores**:

- `/start`: Inicia a interação com o bot.
- `/status`: Estado em tempo real dos condensadores.
- `/logs`: Histórico de eventos críticos.
- `/schedules`: Agendamentos ativos.

**Administradores**:

- `/emergency_stop`: Para todas as operações imediatamente.
- `/start_cycle`: Inicia um ciclo manual de arrefecimento.
- `/valve_on [N]`: Ativa a válvula N.
- `/valve_off [N]`: Desativa a válvula N.
- `/system_status`: Relatório detalhado do sistema.

## 💻 Interface Web: Visão Total

- **URL**: `http://localhost:8000` (ou seu domínio).
- **Login**: admin@iotcnt.local
- **Password**: admin123
- **Admin**: Gestão completa de agendamentos, logs, estados e utilizadores.
- **Utilizador**: Visualização de estados e históricos em tempo real e usabilidade.

### Funcionalidades

- **Dashboard Principal**: Visão imediata do estado dos condensadores, agendamentos e logs.
- **Administração**: Configuração de válvulas, agendamentos, utilizadores e análise de eventos.

## 📊 Endpoints da API: Conectividade Sem Limites

| Endpoint                        | Função                                         |
|----------------------------------|------------------------------------------------|
| GET  `/api/esp32/config`         | Obtém configurações do sistema                 |
| POST `/api/esp32/valve-status`   | Reporta estado das válvulas                    |
| POST `/api/esp32/log`            | Regista eventos e alertas                      |
| GET  `/api/esp32/commands`       | Obtém comandos pendentes para o ESP32         |
| POST `/api/esp32/control-valve`  | Ativa/desativa válvulas                        |
| POST `/api/esp32/start-cycle`    | Inicia ciclo de arrefecimento/renovação        |
| POST `/api/esp32/stop-all`       | Interrompe todas as operações                  |

## 🗄️ Modelos de Dados

- `users`: Gestão de utilizadores do sistema.
- `valves`: Configurações de condensadores e válvulas.
- `schedules`: Agendamentos para ciclos automáticos.
- `operation_logs`: Registo detalhado de operações e alertas.
- `telegram_users`: Utilizadores associados ao Telegram.
- `system_settings`: Parâmetros globais do sistema.

## 🛡️ Segurança Intransigente

- **Autenticação**: Laravel Sanctum para proteção robusta da API.
- **Permissões**: Roles Admin/Utilizador com controlo granular.
- **Comunicação**: HTTPS obrigatório em produção para máxima segurança.
- **Tokens**: Tokens de API seguros e renováveis.
- **Validação**: Sanitização rigorosa de entradas para prevenir vulnerabilidades.

## 🏥 Impacto Transformador

- **Saúde Pública Garantida**: Eliminação do risco de legionella com automação precisa, refletindo **Empatia** pelos clientes.
- **Eficiência Inigualável**: Arrefecimento otimizado, reduzindo custos operacionais.
- **Transparência Total**: Auditoria digital com logs detalhados e monitorização em tempo real.
- **Resposta Imediata**: Alertas instantâneos para falhas, alinhados com **Melhoria Contínua**.
- **Sustentabilidade**: Redução de desperdícios e manutenção otimizada, com responsabilidade ambiental.

## 🔧 Configuração Avançada

### Variáveis de Ambiente

```env
# Aplicação
APP_NAME=IOTCNT
APP_ENV=production
APP_URL=https://seu-dominio.com

# Base de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=iotcnt
DB_USERNAME=root
DB_PASSWORD=

# Telegram
TELEGRAM_BOT_TOKEN=seu_token
TELEGRAM_BOT_USERNAME=seu_bot

# ESP32
ESP32_API_TOKEN=token_esp32
```

### Docker Compose

Inclui:

- `app`: Aplicação Laravel de alto desempenho.
- `webserver`: Nginx para acesso seguro.
- `database`: MySQL 8.0 para dados robustos.
- `redis`: Cache e filas para operações ágeis.
- `queue`: Worker de filas para notificações.
- `scheduler`: Tarefas agendadas para automação.

## 📈 Monitorização de Excelência

- **Logs**: Acessíveis via `/admin/logs` e Telegram, com histórico completo.
- **Métricas**: Estado em tempo real, histórico de ativações e estatísticas de uso.
- **Alertas**: Notificações automáticas para erros, garantindo resposta imediata e **Melhoria Contínua**.

## 🚨 Resolução de Problemas

**ESP32 não conecta**:

- Verifique credenciais WiFi, URL do servidor e token API.
- Consulte logs via `pio device monitor`.

**Telegram sem resposta**:

- Confirme token e webhook.
- Verifique autorização de utilizadores.

**Válvulas inativas**:

- Inspecione ligações físicas e pinos configurados.
- Analise logs do ESP32.

## 📝 Desenvolvimento

### Estrutura do Projeto

```
├── app/                    # Core da aplicação Laravel
├── esp32_irrigation_controller/  # Firmware ESP32
├── resources/views/        # Templates Blade
├── docker/                # Configuração Docker
├── routes/                # Rotas Laravel
└── database/migrations/   # Migrações da base de dados
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

1. Faça um fork do projeto.
2. Crie uma branch para sua funcionalidade (`git checkout -b feature/nova-funcionalidade`).
3. Registe as alterações (`git commit -am 'Adiciona nova funcionalidade'`).
4. Envie para a branch (`git push origin feature/nova-funcionalidade`).
5. Crie um Pull Request.

## 📄 Licença

Licença **MIT**. Consulte o arquivo `LICENSE` para detalhes.

## 🙏 Agradecimentos

- Comunidade ESP32, por impulsionar a inovação em IoT.
- Laravel Framework, por sua robustez e flexibilidade.
- Telegram Bot API, por conectar o sistema ao mundo.
- Equipa do **Continente**, por liderar com **Empatia** e **Melhoria Contínua** em higiene e segurança alimentar.

---

*IOTCNT - A Automação que Protege Vidas e Eleva a Excelência do Continente* 🌡️💧
