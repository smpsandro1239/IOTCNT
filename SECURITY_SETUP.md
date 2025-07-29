# 🔒 GUIA DE CONFIGURAÇÃO DE SEGURANÇA - IOTCNT

## ⚠️ AÇÃO IMEDIATA NECESSÁRIA

Este projeto contém credenciais sensíveis que precisam ser protegidas. Siga este guia **ANTES** de fazer qualquer commit.

## 🚨 Credenciais Identificadas

### Arquivos com Credenciais Expostas:
- ✅ `.env` - **REMOVIDO do Git**
- ✅ `esp32_irrigation_controller/config.h` - **REMOVIDO do Git**
- ✅ `storage/users.json` - **REMOVIDO do Git**
- ✅ `storage/app/data/users.json` - **REMOVIDO do Git**

## 🛡️ Configuração Segura

### 1. Configurar Laravel (.env)

```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Editar .env com suas credenciais reais
nano .env
```

**Variáveis críticas para configurar:**
```env
APP_KEY=base64:SUA_CHAVE_GERADA_AUTOMATICAMENTE
TELEGRAM_BOT_TOKEN=1234567890:ABCDEFGHIJKLMNOPQRSTUVWXYZ
ESP32_API_TOKEN=token_super_seguro_aqui_123456789
ENCRYPTION_KEY=chave_criptografia_muito_segura_aqui
DB_PASSWORD=senha_base_dados_segura
```

### 2. Configurar ESP32

```bash
# Copiar arquivo de exemplo
cp esp32_irrigation_controller/config.example.h esp32_irrigation_controller/config.h

# Editar com suas credenciais
nano esp32_irrigation_controller/config.h
```

**Configurações críticas:**
```c
#define WIFI_SSID "SUA_REDE_WIFI_REAL"
#define WIFI_PASSWORD "SUA_SENHA_WIFI_REAL"
#define API_SERVER_HOST "https://seu-dominio-real.com"
#define API_TOKEN "SEU_TOKEN_SANCTUM_REAL"
```

### 3. Gerar Tokens Seguros

#### Token Telegram:
1. Falar com @BotFather no Telegram
2. Criar bot: `/newbot`
3. Copiar token para `.env`

#### Token ESP32:
```bash
# Gerar token aleatório seguro
php artisan tinker
>>> Str::random(64)
```

#### Chave de Criptografia:
```bash
# Gerar chave segura
openssl rand -base64 32
```

## 🔐 Senhas Padrão a Alterar

### Admin do Sistema:
- **Email**: admin@iotcnt.local
- **Password**: `admin123` ⚠️ **ALTERAR IMEDIATAMENTE**

```bash
# Alterar via interface web ou tinker
php artisan tinker
>>> $user = User::where('email', 'admin@iotcnt.local')->first();
>>> $user->password = bcrypt('nova_senha_super_segura');
>>> $user->save();
```

## 🚫 Arquivos NUNCA Commitar

Estes arquivos estão no `.gitignore` e **NUNCA** devem ser commitados:

```
.env
.env.local
.env.production
esp32_irrigation_controller/config.h
storage/app/data/users.json
storage/users.json
*.key
*.pem
*.p12
```

## ✅ Verificação de Segurança

### Antes de cada commit:
```bash
# Verificar se não há credenciais expostas
git status
git diff --cached

# Verificar .gitignore
cat .gitignore | grep -E "(\.env|config\.h|users\.json)"
```

### Comando de verificação automática:
```bash
# Procurar por possíveis credenciais
grep -r -i "password\|token\|key\|secret" --exclude-dir=vendor --exclude-dir=node_modules --exclude="*.log" .
```

## 🔄 Rotação de Credenciais

### Periodicidade recomendada:
- **Tokens API**: A cada 90 dias
- **Senhas admin**: A cada 30 dias
- **Chaves de criptografia**: A cada 180 dias

### Script de rotação:
```bash
# Gerar novas credenciais
php artisan key:generate --force
# Atualizar tokens no .env
# Reiniciar serviços
```

## 🚨 Em Caso de Exposição

Se credenciais foram expostas acidentalmente:

1. **Revogar imediatamente** todos os tokens
2. **Alterar todas as senhas**
3. **Gerar novas chaves**
4. **Limpar histórico do Git** (se necessário)
5. **Notificar equipe**

### Limpeza do histórico Git:
```bash
# CUIDADO: Isto reescreve o histórico
git filter-branch --force --index-filter \
'git rm --cached --ignore-unmatch .env' \
--prune-empty --tag-name-filter cat -- --all
```

## 📋 Checklist de Segurança

- [ ] `.env` configurado com credenciais reais
- [ ] `config.h` configurado com credenciais reais
- [ ] Senha admin alterada
- [ ] Tokens gerados com segurança
- [ ] `.gitignore` atualizado
- [ ] Verificação de arquivos sensíveis
- [ ] Backup das credenciais em local seguro
- [ ] Documentação de acesso restrita

## 🆘 Suporte

Em caso de dúvidas sobre segurança:
1. Consultar documentação Laravel Security
2. Verificar OWASP guidelines
3. Contactar administrador do sistema

---

**⚠️ LEMBRE-SE: A segurança é responsabilidade de todos!**
