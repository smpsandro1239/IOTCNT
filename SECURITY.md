# 🔒 Política de Segurança

## 🛡️ Versões Suportadas

Apenas a versão mais recente do IOTCNT recebe atualizações de segurança.

| Versão | Suportada          |
| ------ | ------------------ |
| 1.x.x  | ✅ Sim             |
| < 1.0  | ❌ Não             |

## 🚨 Reportar Vulnerabilidades

### Como Reportar
Se descobrir uma vulnerabilidade de segurança, **NÃO** abra um issue público. Em vez disso:

1. **Email**: Envie detalhes para [security@iotcnt.com](mailto:security@iotcnt.com)
2. **Assunto**: "IOTCNT Security Vulnerability Report"
3. **Conteúdo**: Inclua o máximo de detalhes possível

### Informações a Incluir
- Descrição da vulnerabilidade
- Passos para reproduzir
- Impacto potencial
- Versão afetada
- Ambiente de teste

### Processo de Resposta
- **24 horas**: Confirmação de recebimento
- **72 horas**: Avaliação inicial
- **7 dias**: Plano de correção
- **30 dias**: Correção implementada (objetivo)

## 🔐 Configurações de Segurança

### Produção - Checklist Obrigatório

#### 🌐 Servidor Web
- [ ] HTTPS configurado com certificados válidos
- [ ] HTTP redireciona para HTTPS
- [ ] Headers de segurança configurados
- [ ] Rate limiting ativado
- [ ] Firewall configurado (apenas portas 80/443)

#### 🗄️ Base de Dados
- [ ] Senhas fortes configuradas
- [ ] Acesso restrito (não expor porta 3306)
- [ ] Backup criptografado
- [ ] Logs de auditoria ativados

#### 🔧 Aplicação
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Senhas/tokens únicos e seguros
- [ ] Logs de segurança ativados

#### 📱 ESP32
- [ ] WiFi com WPA2/WPA3
- [ ] Token API único e seguro
- [ ] OTA com senha (se ativado)
- [ ] Firmware atualizado

#### 🤖 Telegram
- [ ] Bot token seguro
- [ ] Webhook HTTPS
- [ ] Autorização de utilizadores ativa

### Configurações Recomendadas

#### Nginx Security Headers
```nginx
# Adicionar ao app.conf
add_header X-Frame-Options "DENY" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

#### MySQL Security
```sql
-- Remover usuários desnecessários
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');

-- Remover base de dados de teste
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';

-- Aplicar mudanças
FLUSH PRIVILEGES;
```

#### Redis Security
```conf
# redis.conf
requirepass senha_muito_segura_aqui
rename-command FLUSHDB ""
rename-command FLUSHALL ""
rename-command DEBUG ""
bind 127.0.0.1
```

## 🚫 Vulnerabilidades Conhecidas

### Mitigadas
Nenhuma vulnerabilidade conhecida no momento.

### Em Investigação
Nenhuma vulnerabilidade em investigação no momento.

## 🔍 Auditoria de Segurança

### Ferramentas Recomendadas
- **Docker**: `docker scan` para imagens
- **PHP**: `composer audit` para dependências
- **Nginx**: `nmap` para portas expostas
- **SSL**: `ssllabs.com` para certificados

### Comandos de Verificação
```bash
# Verificar portas expostas
nmap -sS -O localhost

# Verificar certificados SSL
openssl s_client -connect seu-dominio.com:443

# Verificar headers de segurança
curl -I https://seu-dominio.com

# Verificar dependências PHP
composer audit

# Verificar imagens Docker
docker scan iotcnt_app
```

## 📋 Boas Práticas

### Desenvolvimento
- Nunca commitar senhas/tokens
- Usar .env para configurações sensíveis
- Validar todas as entradas
- Sanitizar saídas
- Usar HTTPS em desenvolvimento

### Deployment
- Usar secrets do Docker para senhas
- Configurar backup automático
- Monitorar logs de segurança
- Atualizar dependências regularmente
- Testar em ambiente de staging

### Operação
- Monitorar tentativas de acesso
- Fazer backup regular
- Atualizar sistema operacional
- Revisar logs periodicamente
- Ter plano de recuperação

## 🚨 Incidentes de Segurança

### Procedimento de Resposta
1. **Identificação**: Detectar o incidente
2. **Contenção**: Isolar sistemas afetados
3. **Erradicação**: Remover a ameaça
4. **Recuperação**: Restaurar operações
5. **Lições**: Documentar e melhorar

### Contatos de Emergência
- **Administrador**: [admin@iotcnt.com](mailto:admin@iotcnt.com)
- **Segurança**: [security@iotcnt.com](mailto:security@iotcnt.com)

## 📚 Recursos Adicionais

### Documentação
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security](https://laravel.com/docs/security)
- [ESP32 Security](https://docs.espressif.com/projects/esp-idf/en/latest/esp32/security/security.html)

### Ferramentas
- [Security Headers](https://securityheaders.com/)
- [SSL Labs](https://www.ssllabs.com/ssltest/)
- [OWASP ZAP](https://www.zaproxy.org/)

---

**A segurança é responsabilidade de todos. Reporte problemas responsavelmente.** 🔒
