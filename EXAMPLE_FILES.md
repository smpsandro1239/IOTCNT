# 📁 ARQUIVOS DE EXEMPLO - IOTCNT

## 🎯 Propósito

Todos os arquivos sensíveis foram removidos do repositório por segurança. Para cada arquivo protegido, existe um arquivo de exemplo correspondente que pode ser usado como template.

## 📋 Lista Completa de Arquivos de Exemplo

### 🔧 Configurações Principais

| Arquivo de Exemplo | Arquivo Real | Descrição |
|-------------------|--------------|-----------|
| `.env.example` | `.env` | Configurações principais do Laravel |
| `esp32_irrigation_controller/config.example.h` | `config.h` | Configurações do ESP32 |

### 🔐 Configurações Avançadas

| Arquivo de Exemplo | Arquivo Real | Descrição |
|-------------------|--------------|-----------|
| `config/secrets.example.php` | `config/secrets.php` | Tokens e chaves sensíveis |

### 📊 Dados de Sistema

| Arquivo de Exemplo | Arquivo Real | Descrição |
|-------------------|--------------|-----------|
| `storage/users.example.json` | `storage/users.json` | Dados de utilizadores (modo file) |
| `storage/app/data/users.example.json` | `storage/app/data/users.json` | Dados de utilizadores (backup) |

### 🌐 Configurações de Servidor

| Arquivo de Exemplo | Arquivo Real | Descrição |
|-------------------|--------------|-----------|
| `.htaccess.example` | `.htaccess` | Configurações Apache |
| `robots.example.txt` | `robots.txt` | Configurações SEO |

### 🐳 Docker (Opcionais)

| Arquivo de Exemplo | Arquivo Real | Descrição |
|-------------------|--------------|-----------|
| `docker-compose.example.yml` | `docker-compose.yml` | Configuração Docker principal |
| `docker-compose.override.example.yml` | `docker-compose.override.yml` | Overrides locais |
| `docker/mysql/my.example.cnf` | `docker/mysql/my.cnf` | Configuração MySQL |
| `docker/nginx/conf.d/app.example.conf` | `docker/nginx/conf.d/app.conf` | Configuração Nginx |
| `docker/redis/redis.example.conf` | `docker/redis/redis.conf` | Configuração Redis |

## 🚀 Setup Automático

### Método 1: Script Completo (Recomendado)
```bash
# Copia todos os arquivos de exemplo
php setup_examples.php

# Gera credenciais seguras
php secure_setup.php
```

### Método 2: Manual
```bash
# Configurações essenciais
cp .env.example .env
cp esp32_irrigation_controller/config.example.h esp32_irrigation_controller/config.h

# Gerar chave da aplicação
php artisan key:generate

# Configurações opcionais
cp config/secrets.example.php config/secrets.php
cp .htaccess.example .htaccess
cp robots.example.txt robots.txt
```

## 📝 Configuração Pós-Setup

### 1. Arquivo .env
```bash
nano .env
```
**Configure:**
- `TELEGRAM_BOT_TOKEN` - Token do bot Telegram
- `ESP32_API_TOKEN` - Token para ESP32
- `DB_*` - Configurações da base de dados

### 2. Arquivo config.h (ESP32)
```bash
nano esp32_irrigation_controller/config.h
```
**Configure:**
- `WIFI_SSID` - Nome da rede WiFi
- `WIFI_PASSWORD` - Senha da rede WiFi
- `API_SERVER_HOST` - URL do servidor Laravel
- `API_TOKEN` - Token da API

### 3. Arquivo secrets.php (Opcional)
```bash
nano config/secrets.php
```
**Configure tokens adicionais se necessário**

## ✅ Verificação

### Verificar se todos os arquivos foram criados:
```bash
# Arquivos essenciais
ls -la .env esp32_irrigation_controller/config.h

# Arquivos opcionais
ls -la config/secrets.php .htaccess robots.txt
```

### Verificar se não há credenciais expostas:
```bash
# Procurar por possíveis credenciais no código
grep -r "password\|token" --exclude-dir=vendor --exclude="*.log" .
```

## 🔒 Segurança

### ⚠️ NUNCA Commitar:
- `.env`
- `esp32_irrigation_controller/config.h`
- `config/secrets.php`
- `storage/users.json`
- `storage/app/data/users.json`

### ✅ Sempre Commitar:
- `.env.example`
- `config.example.h`
- `secrets.example.php`
- `users.example.json`

## 🆘 Resolução de Problemas

### Arquivo de exemplo não encontrado:
```bash
# Verificar se o repositório está atualizado
git pull origin main

# Listar arquivos de exemplo disponíveis
find . -name "*.example.*" -o -name "example.*"
```

### Erro de permissões:
```bash
# Corrigir permissões dos diretórios
chmod 755 storage/ storage/app/ storage/app/data/
chmod 644 .env esp32_irrigation_controller/config.h
```

### Credenciais não funcionam:
```bash
# Regenerar todas as credenciais
php secure_setup.php

# Verificar configuração
php artisan config:show
```

## 📚 Documentação Relacionada

- `SECURITY_SETUP.md` - Guia completo de segurança
- `SECURITY_STATUS.md` - Status atual de segurança
- `README.md` - Documentação principal
- `INSTALL.md` - Guia de instalação detalhado

---

**💡 Dica:** Use sempre o script `setup_examples.php` para garantir que todos os arquivos necessários sejam criados corretamente!
