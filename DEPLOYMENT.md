# 🚀 Guia de Deployment - IOTCNT

## Sistema de Arrefecimento Industrial - A minha empresa x

Este documento descreve como fazer o deployment do sistema IOTCNT para produção.

## 📋 Pré-requisitos

### Servidor de Produção
- **Sistema Operativo**: Ubuntu 20.04+ ou CentOS 8+
- **RAM**: Mínimo 4GB, recomendado 8GB
- **Armazenamento**: Mínimo 50GB SSD
- **CPU**: 2+ cores
- **Rede**: Conexão estável à internet

### Software Necessário
- Docker 20.10+
- Docker Compose 2.0+
- Git
- Nginx (opcional, para proxy reverso)
- Certbot (para SSL/HTTPS)

## 🔧 Configuração Inicial

### 1. Clonar o Repositório
```bash
git clone https://github.com/smpsandro1239/IOTCNT.git
cd IOTCNT
```

### 2. Configurar Variáveis de Ambiente
```bash
# Copiar arquivo de configuração de produção
cp .env.production .env

# Editar configurações
nano .env
```

### 3. Configurações Obrigatórias
Altere as seguintes variáveis no arquivo `.env`:

```env
# Gerar nova chave de aplicação
APP_KEY=base64:NOVA_CHAVE_AQUI

# Configurar domínio
APP_URL=https://seu-dominio.com

# Configurar base de dados
DB_PASSWORD=senha_forte_aqui
DB_ROOT_PASSWORD=senha_root_forte_aqui

# Configurar Redis
REDIS_PASSWORD=senha_redis_forte_aqui

# Configurar email (opcional)
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
```

## 🚀 Deployment

### Método 1: Script Automático
```bash
# Tornar o script executável
chmod +x deploy-production.sh

# Executar deployment
./deploy-production.sh production
```

### Método 2: Manual
```bash
# 1. Parar containers existentes
docker-compose -f docker-compose.prod.yml down

# 2. Construir imagens
docker-compose -f docker-compose.prod.yml build --no-cache

# 3. Iniciar containers
docker-compose -f docker-compose.prod.yml up -d

# 4. Executar migrações
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 5. Executar seeders
docker-compose -f docker-compose.prod.yml exec app php artisan db:seed --force

# 6. Otimizar para produção
docker-compose -f docker-compose.prod.yml exec app php artisan optimize
```

## 🔒 Configuração SSL/HTTPS

### Usando Certbot (Let's Encrypt)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obter certificado
sudo certbot --nginx -d seu-dominio.com -d www.seu-dominio.com

# Renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 📊 Monitorização

### Verificar Status dos Containers
```bash
docker-compose -f docker-compose.prod.yml ps
```

### Ver Logs
```bash
# Todos os serviços
docker-compose -f docker-compose.prod.yml logs -f

# Serviço específico
docker-compose -f docker-compose.prod.yml logs -f app
```

### Verificar Saúde da Aplicação
```bash
# Teste de conectividade
curl -I http://localhost

# Verificar base de dados
docker-compose -f docker-compose.prod.yml exec app php artisan tinker --execute="echo 'DB OK: ' . DB::connection()->getPdo();"
```

## 🔧 Manutenção

### Backup da Base de Dados
```bash
# Criar backup
docker-compose -f docker-compose.prod.yml exec database mysqldump -u root -p iotcnt_prod > backup_$(date +%Y%m%d).sql

# Restaurar backup
docker-compose -f docker-compose.prod.yml exec -T database mysql -u root -p iotcnt_prod < backup_20240101.sql
```

### Actualizar Aplicação
```bash
# 1. Fazer backup
./backup.sh

# 2. Actualizar código
git pull origin main

# 3. Reconstruir e reiniciar
./deploy-production.sh production
```

### Corrigir Passwords de Utilizadores
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan users:fix-passwords
```

## 🌐 URLs de Acesso

Após o deployment bem-sucedido:

- **Aplicação Principal**: `https://seu-dominio.com`
- **Página de Login**: `https://seu-dominio.com/login-direct`
- **Teste de Login**: `https://seu-dominio.com/test-login.html`
- **Dashboard Admin**: `https://seu-dominio.com/admin/dashboard`
- **Dashboard User**: `https://seu-dominio.com/dashboard`

## 🔐 Credenciais Padrão

**⚠️ ALTERE IMEDIATAMENTE EM PRODUÇÃO!**

- **Admin**: `admin@iotcnt.local` / `password`
- **User**: `user@iotcnt.local` / `password`

## 🆘 Resolução de Problemas

### Container não inicia
```bash
# Verificar logs
docker-compose -f docker-compose.prod.yml logs app

# Verificar configuração
docker-compose -f docker-compose.prod.yml config
```

### Erro de base de dados
```bash
# Verificar conexão
docker-compose -f docker-compose.prod.yml exec app php artisan tinker --execute="DB::connection()->getPdo();"

# Recriar base de dados
docker-compose -f docker-compose.prod.yml exec app php artisan migrate:fresh --seed --force
```

### Problemas de performance
```bash
# Limpar cache
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

## 📞 Suporte

Para suporte técnico, contacte:
- **Email**: suporte@iotcnt.aminhaempresax.pt
- **GitHub**: https://github.com/smpsandro1239/IOTCNT/issues

---

**🏭 Sistema IOTCNT - A minha empresa x**
*Prevenção de Legionela em Sistemas de Arrefecimento Industrial*
