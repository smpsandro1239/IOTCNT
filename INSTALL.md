# 🚀 Guia de Instalação IOTCNT

Este guia irá ajudá-lo a instalar e configurar o sistema IOTCNT do zero.

## 📋 Pré-requisitos

### Sistema Operacional
- **Windows 10/11** (com WSL2 recomendado)
- **Linux** (Ubuntu 20.04+ recomendado)
- **macOS** (10.15+ recomendado)

### Software Necessário
- **Docker Desktop** (versão 4.0+)
- **Docker Compose** (versão 2.0+)
- **Git** (para clonar o repositório)

### Hardware Mínimo
- **RAM**: 2GB disponível
- **Disco**: 5GB espaço livre
- **CPU**: 2 cores
- **Rede**: Conexão à internet

### Hardware ESP32
- **ESP32** (qualquer variante)
- **Módulo Relé 5 canais** (5V ou 3.3V)
- **Válvulas solenoides** (5 unidades)
- **Fonte de alimentação** adequada
- **Cabos e conectores**

## 🔧 Instalação Passo a Passo

### 1. Instalar Docker

#### Windows
1. Baixar Docker Desktop: https://www.docker.com/products/docker-desktop
2. Executar instalador e seguir instruções
3. Reiniciar o computador
4. Verificar instalação: `docker --version`

#### Linux (Ubuntu/Debian)
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências
sudo apt install apt-transport-https ca-certificates curl gnupg lsb-release

# Adicionar chave GPG do Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Adicionar repositório
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Instalar Docker
sudo apt update
sudo apt install docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Adicionar usuário ao grupo docker
sudo usermod -aG docker $USER

# Reiniciar sessão ou executar
newgrp docker

# Verificar instalação
docker --version
docker compose version
```

#### macOS
1. Baixar Docker Desktop: https://www.docker.com/products/docker-desktop
2. Arrastar para pasta Applications
3. Executar Docker Desktop
4. Verificar instalação: `docker --version`

### 2. Clonar o Repositório

```bash
# Clonar repositório
git clone https://github.com/seu-usuario/iotcnt.git
cd iotcnt

# Verificar arquivos
ls -la
```

### 3. Configurar Ambiente

#### Copiar Arquivos de Exemplo
```bash
# Copiar configuração principal
cp .env.example .env

# Copiar configuração Docker
cp docker-compose.example.yml docker-compose.yml

# Copiar configuração Nginx
cp docker/nginx/conf.d/app.example.conf docker/nginx/conf.d/app.conf

# Copiar configuração MySQL
cp docker/mysql/my.example.cnf docker/mysql/my.cnf

# Copiar configuração Redis
cp docker/redis/redis.example.conf docker/redis/redis.conf

# Copiar configuração ESP32
cp esp32_irrigation_controller/config.example.h esp32_irrigation_controller/config.h
```

#### Editar Configurações

**1. Editar `.env`:**
```bash
# Abrir editor (escolha um)
nano .env          # Linux/macOS
notepad .env       # Windows
code .env          # VS Code
```

**Configurações obrigatórias:**
```env
# Alterar senhas (OBRIGATÓRIO)
DB_PASSWORD=sua_senha_mysql_segura
MYSQL_ROOT_PASSWORD=sua_senha_root_mysql

# Configurar Telegram Bot
TELEGRAM_BOT_TOKEN=123456789:ABCDEFGHIJKLMNOPQRSTUVWXYZ
TELEGRAM_BOT_USERNAME=seu_bot_username

# Configurar ESP32
ESP32_API_TOKEN=token_esp32_muito_seguro

# Configurar URL (para produção)
APP_URL=https://seu-dominio.com
```

**2. Editar `docker-compose.yml`:**
- Alterar senhas da base de dados
- Ajustar portas se necessário
- Configurar volumes para produção

**3. Editar `esp32_irrigation_controller/config.h`:**
```cpp
// WiFi
#define WIFI_SSID "SUA_REDE_WIFI"
#define WIFI_PASSWORD "SUA_SENHA_WIFI"

// API Server
#define API_SERVER_HOST "http://192.168.1.100"  // IP do seu servidor
#define API_TOKEN "token_esp32_muito_seguro"    // Mesmo token do .env

// Pinos das válvulas (ajustar conforme hardware)
#define VALVE_PIN_1 23
#define VALVE_PIN_2 22
// ... etc
```

### 4. Executar Instalação

#### Método Automático (Windows)
```cmd
# Executar script de instalação
iotcnt.bat
# Escolher opção 1 (Iniciar Sistema Completo)
```

#### Método Manual
```bash
# Construir e iniciar containers
docker compose build --no-cache
docker compose up -d

# Aguardar serviços ficarem prontos (30-60 segundos)
sleep 30

# Executar migrações
docker compose exec app php artisan migrate --force

# Gerar chave da aplicação
docker compose exec app php artisan key:generate --force

# Otimizar configuração
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Criar link de storage
docker compose exec app php artisan storage:link
```

### 5. Verificar Instalação

```bash
# Verificar status dos containers
docker compose ps

# Verificar logs
docker compose logs -f app

# Testar acesso web
curl http://localhost
```

**Acessar interface web:**
- Abrir navegador: http://localhost
- Criar conta de administrador
- Configurar válvulas e agendamentos

### 6. Configurar ESP32

#### Instalar PlatformIO
```bash
# Instalar PlatformIO CLI
pip install platformio

# Ou usar VS Code com extensão PlatformIO
```

#### Upload do Firmware
```bash
# Navegar para diretório ESP32
cd esp32_irrigation_controller

# Conectar ESP32 via USB
# Verificar porta (Linux: /dev/ttyUSB0, Windows: COM3, macOS: /dev/cu.usbserial)

# Compilar e fazer upload
pio run --target upload

# Monitorar serial
pio device monitor
```

### 7. Configurar Telegram Bot

#### Criar Bot
1. Abrir Telegram e procurar @BotFather
2. Enviar `/newbot`
3. Seguir instruções para criar bot
4. Copiar token para `.env`

#### Configurar Webhook
1. Acessar: http://localhost/telegram/set-webhook
2. Ou executar via API:
```bash
curl -X GET "https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://seu-dominio.com/telegram/webhook"
```

### 8. Teste Final

#### Verificar Sistema
```bash
# Executar verificação completa (Windows)
check_iotcnt.bat

# Ou manualmente
docker compose ps
curl http://localhost/api/ping
```

#### Testar ESP32
1. Verificar logs serial do ESP32
2. Confirmar conexão WiFi
3. Verificar comunicação com API
4. Testar controlo de válvulas

#### Testar Telegram
1. Enviar `/start` para o bot
2. Autorizar utilizador no painel admin
3. Testar comandos básicos

## 🔧 Configuração de Produção

### SSL/HTTPS
```bash
# Instalar Certbot (Let's Encrypt)
sudo apt install certbot

# Obter certificado
sudo certbot certonly --standalone -d seu-dominio.com

# Copiar certificados
sudo cp /etc/letsencrypt/live/seu-dominio.com/fullchain.pem docker/nginx/ssl/
sudo cp /etc/letsencrypt/live/seu-dominio.com/privkey.pem docker/nginx/ssl/

# Configurar nginx para HTTPS
# Editar docker/nginx/conf.d/app.conf
```

### Firewall
```bash
# Ubuntu/Debian
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# CentOS/RHEL
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --reload
```

### Backup Automático
```bash
# Criar script de backup
cat > /opt/iotcnt/backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker compose exec -T database mysqldump -u root -p$MYSQL_ROOT_PASSWORD iotcnt > /opt/backups/iotcnt_$DATE.sql
find /opt/backups -name "iotcnt_*.sql" -mtime +7 -delete
EOF

# Tornar executável
chmod +x /opt/iotcnt/backup.sh

# Adicionar ao cron
echo "0 2 * * * /opt/iotcnt/backup.sh" | crontab -
```

## 🚨 Resolução de Problemas

### Problemas Comuns

#### Docker não inicia
```bash
# Verificar se Docker está executando
sudo systemctl status docker

# Iniciar Docker
sudo systemctl start docker

# Verificar logs
sudo journalctl -u docker
```

#### Containers não sobem
```bash
# Verificar logs
docker compose logs

# Verificar recursos
docker system df
docker system prune

# Recriar containers
docker compose down
docker compose up -d --force-recreate
```

#### ESP32 não conecta
1. Verificar configuração WiFi
2. Verificar URL da API
3. Verificar token de autenticação
4. Verificar logs serial

#### Telegram não funciona
1. Verificar token do bot
2. Configurar webhook
3. Verificar logs da aplicação
4. Autorizar utilizador no admin

#### Base de dados com erro
```bash
# Verificar logs MySQL
docker compose logs database

# Verificar espaço em disco
df -h

# Reparar tabelas (se necessário)
docker compose exec database mysqlcheck -u root -p --auto-repair iotcnt
```

### Logs Úteis
```bash
# Logs da aplicação
docker compose logs -f app

# Logs do nginx
docker compose logs -f webserver

# Logs da base de dados
docker compose logs -f database

# Logs do ESP32
pio device monitor
```

### Comandos de Diagnóstico
```bash
# Status geral
docker compose ps
docker stats

# Conectividade
curl -I http://localhost
ping seu-dominio.com

# Base de dados
docker compose exec database mysql -u root -p -e "SHOW DATABASES;"

# Redis
docker compose exec redis redis-cli ping
```

## 📞 Suporte

### Documentação
- **README.md**: Visão geral do projeto
- **Código fonte**: Comentários detalhados
- **Issues GitHub**: Problemas conhecidos

### Comunidade
- **GitHub Issues**: Reportar bugs
- **Discussions**: Perguntas e sugestões
- **Wiki**: Documentação adicional

### Logs de Debug
Sempre incluir ao reportar problemas:
```bash
# Informações do sistema
docker --version
docker compose version

# Status dos containers
docker compose ps

# Logs relevantes
docker compose logs app
```

---

**Parabéns! 🎉 Seu sistema IOTCNT está agora instalado e funcionando!**

Para próximos passos, consulte o README.md principal.
