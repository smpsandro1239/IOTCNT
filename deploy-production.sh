#!/bin/bash

# IOTCNT Production Deployment Script
# Este script automatiza o processo de deployment da aplicação IOTCNT para produção

set -e

echo "🚀 Iniciando deployment de produção do IOTCNT..."

# Verificar se estamos no ambiente correto
if [ "$1" != "production" ]; then
    echo "❌ Este script deve ser executado com: ./deploy-production.sh production"
    echo "⚠️  ATENÇÃO: Este é um deployment de PRODUÇÃO!"
    exit 1
fi

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não está instalado. Por favor, instale o Docker primeiro."
    exit 1
fi

# Verificar se Docker Compose está instalado
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose não está instalado. Por favor, instale o Docker Compose primeiro."
    exit 1
fi

# Verificar se o arquivo .env.production existe
if [ ! -f ".env.production" ]; then
    echo "❌ Arquivo .env.production não encontrado!"
    echo "📝 Por favor, configure as variáveis de produção no arquivo .env.production"
    exit 1
fi

# Backup da configuração atual (se existir)
if [ -f ".env" ]; then
    echo "💾 Fazendo backup da configuração atual..."
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
fi

# Copiar configuração de produção
echo "⚙️ Aplicando configuração de produção..."
cp .env.production .env

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker-compose -f docker-compose.prod.yml down --remove-orphans

# Construir imagens para produção
echo "🔨 Construindo imagens Docker para produção..."
docker-compose -f docker-compose.prod.yml build --no-cache

# Iniciar containers de produção
echo "🚀 Iniciando containers de produção..."
docker-compose -f docker-compose.prod.yml up -d

# Aguardar que os containers estejam prontos
echo "⏳ Aguardando containers ficarem prontos..."
sleep 45

# Executar migrações
echo "🗄️ Executando migrações da base de dados..."
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Executar seeders
echo "🌱 Executando seeders..."
docker-compose -f docker-compose.prod.yml exec app php artisan db:seed --force

# Corrigir passwords dos utilizadores
echo "🔐 Corrigindo passwords dos utilizadores..."
docker-compose -f docker-compose.prod.yml exec app php artisan users:fix-passwords

# Limpar e otimizar cache para produção
echo "🧹 Otimizando para produção..."
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
docker-compose -f docker-compose.prod.yml exec app php artisan optimize

# Verificar status
echo "✅ Verificando status dos containers..."
docker-compose -f docker-compose.prod.yml ps

# Teste de conectividade
echo "🔍 Testando conectividade..."
sleep 10
if curl -f -s http://localhost > /dev/null; then
    echo "✅ Aplicação respondendo correctamente!"
else
    echo "⚠️ Aplicação pode não estar respondendo. Verifique os logs."
fi

echo ""
echo "🎉 Deployment de produção concluído com sucesso!"
echo "🌐 Aplicação disponível em: http://localhost (porta 80)"
echo "🔒 HTTPS disponível em: https://localhost (porta 443)"
echo ""
echo "📋 Credenciais de acesso:"
echo "   Admin: admin@iotcnt.local / password"
echo "   User:  user@iotcnt.local / password"
echo ""
echo "📊 Para monitorizar:"
echo "   docker-compose -f docker-compose.prod.yml logs -f"
echo ""
echo "⚠️  IMPORTANTE: Altere as passwords padrão em produção!"
