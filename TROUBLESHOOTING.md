# 🚨 Guia de Resolução de Problemas - IOTCNT

## ⚡ Soluções Rápidas

### 🔧 Problema: "service redis refers to undefined network iotcnt_network"

**Solução:**
```cmd
# Execute o script de correção
fix_iotcnt.bat

# Ou manualmente:
docker compose down --remove-orphans
docker system prune -f
docker compose up -d --build
```

### 🔧 Problema: "timeout: invalid time interval '/t'"

**Causa:** Comando `timeout` não funciona em alguns sistemas Windows.

**Solução:**
```cmd
# Use o script corrigido
start_iotcnt_fixed.bat

# Ou o script de correção
fix_iotcnt.bat
```

### 🔧 Problema: "Docker Compose version obsolete warning"

**Causa:** Aviso sobre `version:` no docker-compose.yml.

**Solução:** O aviso pode ser ignorado, mas para removê-lo:
```cmd
# O arquivo docker-compose.yml já foi corrigido
# Não é necessário ação adicional
```

## 🐛 Problemas Comuns

### 1. **Containers não iniciam**

**Sintomas:**
- Erro ao executar `docker compose up`
- Containers param imediatamente

**Diagnóstico:**
```cmd
docker compose ps
docker compose logs
```

**Soluções:**
```cmd
# Limpar sistema Docker
docker compose down -v
docker system prune -f

# Recriar containers
docker compose build --no-cache
docker compose up -d
```

### 2. **Base de dados não conecta**

**Sintomas:**
- Erro "Connection refused"
- Migrações falham

**Diagnóstico:**
```cmd
docker compose logs database
docker compose exec database mysql -u root -p
```

**Soluções:**
```cmd
# Aguardar mais tempo (base de dados demora a iniciar)
ping 127.0.0.1 -n 61

# Verificar senhas no .env
notepad .env

# Recriar volume da base de dados
docker compose down -v
docker volume rm iotcnt_mysql_data
docker compose up -d
```

### 3. **Servidor web não responde**

**Sintomas:**
- http://localhost não carrega
- Erro 502 Bad Gateway

**Diagnóstico:**
```cmd
docker compose logs webserver
docker compose logs app
curl -I http://localhost
```

**Soluções:**
```cmd
# Verificar se app está executando
docker compose ps

# Reiniciar serviços web
docker compose restart webserver app

# Verificar configuração nginx
docker compose exec webserver nginx -t
```

### 4. **ESP32 não conecta à API**

**Sintomas:**
- ESP32 não consegue fazer requests HTTP
- Timeout nas requisições

**Diagnóstico:**
- Verificar logs serial do ESP32
- Testar conectividade WiFi
- Verificar URL da API

**Soluções:**
```cpp
// Verificar config.h
#define API_SERVER_HOST "http://192.168.1.100"  // IP correto
#define API_TOKEN "token_correto_aqui"

// Testar conectividade
ping 192.168.1.100
curl http://192.168.1.100/api/ping
```

### 5. **Telegram Bot não funciona**

**Sintomas:**
- Bot não responde
- Webhook não configurado

**Diagnóstico:**
```cmd
# Verificar logs
docker compose logs app | findstr telegram

# Testar bot token
curl "https://api.telegram.org/bot<TOKEN>/getMe"
```

**Soluções:**
```cmd
# Configurar webhook
# Aceder: http://localhost/telegram/set-webhook

# Verificar token no .env
notepad .env

# Autorizar utilizador no admin
# Aceder: http://localhost/admin/telegram-users
```

## 🔍 Comandos de Diagnóstico

### Sistema Docker
```cmd
# Status dos containers
docker compose ps

# Logs de todos os serviços
docker compose logs

# Logs de serviço específico
docker compose logs app
docker compose logs database
docker compose logs webserver

# Uso de recursos
docker stats

# Informações do sistema
docker system df
docker system info
```

### Aplicação Laravel
```cmd
# Entrar no container
docker compose exec app bash

# Verificar configuração
php artisan config:show

# Testar base de dados
php artisan tinker
>>> DB::connection()->getPdo()

# Verificar rotas
php artisan route:list

# Limpar caches
php artisan cache:clear
php artisan config:clear
```

### Rede e Conectividade
```cmd
# Testar portas
netstat -an | findstr ":80\|:3306\|:6379"

# Testar conectividade web
curl -I http://localhost
powershell -Command "Invoke-WebRequest http://localhost"

# Testar API
curl http://localhost/api/ping
```

## 🛠️ Scripts de Correção

### Script Automático
```cmd
# Executa correções automáticas
fix_iotcnt.bat
```

### Script Manual
```cmd
# Parar tudo
docker compose down -v

# Limpar sistema
docker system prune -f

# Remover volumes
docker volume rm iotcnt_mysql_data iotcnt_redis_data

# Recriar configurações
copy .env.example .env
copy docker-compose.example.yml docker-compose.yml

# Iniciar novamente
docker compose up -d --build
```

## 📋 Checklist de Verificação

### Antes de Reportar Problema

- [ ] Docker Desktop está executando?
- [ ] Arquivo `.env` existe e está configurado?
- [ ] Portas 80, 3306, 6379 estão livres?
- [ ] Executou como Administrador?
- [ ] Tentou o script `fix_iotcnt.bat`?
- [ ] Verificou logs com `docker compose logs`?

### Informações para Suporte

Ao reportar problemas, inclua:

```cmd
# Versões
docker --version
docker compose version

# Status
docker compose ps

# Logs (últimas 50 linhas)
docker compose logs --tail=50

# Configuração (sem senhas)
docker compose config

# Sistema
systeminfo | findstr /C:"OS Name" /C:"Total Physical Memory"
```

## 🆘 Suporte Adicional

### Recursos Úteis
- **Documentação Docker:** https://docs.docker.com/
- **Laravel Docs:** https://laravel.com/docs
- **ESP32 Docs:** https://docs.espressif.com/

### Comunidade
- **GitHub Issues:** Para reportar bugs
- **Discussions:** Para perguntas gerais
- **Wiki:** Para documentação adicional

### Logs Importantes
```cmd
# Logs da aplicação
docker compose logs app

# Logs da base de dados
docker compose logs database

# Logs do servidor web
docker compose logs webserver

# Logs do sistema (Windows)
eventvwr.msc
```

---

**💡 Dica:** A maioria dos problemas é resolvida executando `fix_iotcnt.bat` e aguardando os serviços iniciarem completamente (pode demorar 2-3 minutos).
