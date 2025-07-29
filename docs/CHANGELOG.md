# 📋 IOTCNT - Changelog da API

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [Não Lançado]

### Planejado
- Suporte a múltiplos dispositivos ESP32
- API GraphQL como alternativa ao REST
- Integração com sensores de humidade
- Dashboard mobile nativo
- Backup automático de configurações

## [1.0.0] - 2024-01-15

### Adicionado
- API REST completa para controlo de irrigação
- Autenticação via Laravel Sanctum
- Suporte a 5 válvulas de irrigação
- Sistema de agendamento semanal
- Integração com ESP32 via HTTP
- Dashboard web administrativo
- Bot Telegram para controlo remoto
- Sistema de logs e auditoria
- Documentação completa da API
- Testes automatizados
- Configuração Docker
- Scripts de instalação para Windows

### Endpoints da API v1.0.0

#### Gestão de Válvulas
- `GET /api/valve/status` - Estado de todas as válvulas
- `GET /api/valve/status/{id}` - Estado de válvula específica
- `POST /api/valve/control` - Controlar válvula
- `POST /api/valve/start-cycle` - Iniciar ciclo de irrigação
- `POST /api/valve/stop-all` - Parar todas as válvulas
- `GET /api/valve/stats` - Estatísticas do sistema

#### Integração ESP32
- `GET /api/esp32/config` - Configuração para ESP32
- `POST /api/esp32/valve-status` - Reportar estado das válvulas
- `POST /api/esp32/log` - Enviar logs de operação
- `GET /api/esp32/commands` - Obter comandos pendentes

#### Agendamentos
- `GET /api/schedules` - Listar agendamentos
- `POST /api/schedules` - Criar agendamento
- `PUT /api/schedules/{id}` - Atualizar agendamento
- `DELETE /api/schedules/{id}` - Remover agendamento

#### Logs e Histórico
- `GET /api/logs` - Obter logs com filtros
- `GET /api/logs/export` - Exportar logs

#### Sistema
- `GET /api/system/settings` - Configurações do sistema
- `PUT /api/system/settings` - Atualizar configurações
- `GET /api/system/health` - Verificação de saúde
- `GET /api/system/diagnostics` - Diagnósticos detalhados

#### Telegram
- `GET /api/telegram/users` - Utilizadores Telegram
- `POST /api/telegram/send-notification` - Enviar notificação

### Funcionalidades
- **Controlo Manual**: Ligar/desligar válvulas individualmente
- **Ciclos Automáticos**: Irrigação sequencial de todas as válvulas
- **Agendamento**: Programação semanal de irrigação
- **Monitorização**: Estado em tempo real das válvulas
- **Logs**: Histórico completo de operações
- **Notificações**: Alertas via Telegram
- **Multi-utilizador**: Suporte a múltiplos utilizadores
- **Segurança**: Autenticação e autorização robustas

### Tecnologias
- **Backend**: Laravel 10, PHP 8.1+
- **Base de Dados**: MySQL/MariaDB
- **Cache**: Redis
- **Hardware**: ESP32 com 5 relés
- **Frontend**: Blade templates, JavaScript vanilla
- **Containerização**: Docker e Docker Compose

## [0.9.0] - 2024-01-10 (Beta)

### Adicionado
- Estrutura básica da API
- Modelos de base de dados
- Controladores principais
- Middleware de autenticação
- Testes unitários básicos

### Alterado
- Estrutura de resposta da API padronizada
- Melhorias na validação de dados

### Corrigido
- Problemas de timezone em agendamentos
- Validação de duração de irrigação

## [0.8.0] - 2024-01-05 (Alpha)

### Adicionado
- Protótipo inicial da API
- Integração básica com ESP32
- Sistema de utilizadores
- Dashboard administrativo básico

### Limitações Conhecidas
- Sem suporte a WebSocket
- Logs limitados
- Sem integração Telegram

## [0.7.0] - 2024-01-01 (Desenvolvimento)

### Adicionado
- Configuração inicial do projeto
- Estrutura de base de dados
- Modelos Eloquent
- Migrações iniciais

## Tipos de Mudanças

- `Added` - para novas funcionalidades
- `Changed` - para mudanças em funcionalidades existentes
- `Deprecated` - para funcionalidades que serão removidas
- `Removed` - para funcionalidades removidas
- `Fixed` - para correções de bugs
- `Security` - para correções de segurança

## Compatibilidade

### Versão 1.0.0
- **PHP**: 8.1 ou superior
- **Laravel**: 10.x
- **MySQL**: 8.0 ou superior / MariaDB 10.4+
- **Redis**: 6.0 ou superior
- **ESP32**: Arduino Core 2.0+

### Breaking Changes

Nenhuma breaking change até à versão 1.0.0.

### Deprecações

Nenhuma funcionalidade depreciada na versão atual.

## Roadmap

### v1.1.0 (Q2 2024)
- [ ] Suporte a sensores de humidade
- [ ] API GraphQL
- [ ] Melhorias no dashboard
- [ ] Notificações por email
- [ ] Backup automático

### v1.2.0 (Q3 2024)
- [ ] App mobile nativo
- [ ] Múltiplos dispositivos ESP32
- [ ] Integração com weather APIs
- [ ] Relatórios avançados
- [ ] Suporte a diferentes tipos de válvulas

### v2.0.0 (Q4 2024)
- [ ] Arquitetura microserviços
- [ ] Machine learning para otimização
- [ ] Suporte a diferentes protocolos (MQTT, LoRa)
- [ ] Interface multi-idioma
- [ ] API versioning completo

## Migração

### De 0.x para 1.0.0

1. **Base de Dados**:
   ```bash
   php artisan migrate
   ```

2. **Configuração**:
   ```bash
   cp .env.example .env
   # Atualizar configurações necessárias
   ```

3. **Cache**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

4. **Tokens de API**:
   - Regenerar todos os tokens existentes
   - Atualizar configuração do ESP32

### Mudanças de Configuração

#### ESP32
```cpp
// Antes (v0.x)
#define API_ENDPOINT "/api/valve"

// Depois (v1.0.0)
#define API_ENDPOINT "/api/valve/control"
```

#### Laravel
```php
// Antes (v0.x)
'api_version' => 'v0',

// Depois (v1.0.0)
'api_version' => 'v1',
```

## Suporte

### Versões Suportadas

| Versão | Suporte | Fim do Suporte |
|--------|---------|----------------|
| 1.0.x  | ✅ Ativo | TBD |
| 0.9.x  | ⚠️ Crítico apenas | 2024-03-15 |
| 0.8.x  | ❌ Não suportado | 2024-02-01 |

### Política de Suporte

- **Versões Major**: Suporte por 2 anos
- **Versões Minor**: Suporte por 1 ano
- **Versões Patch**: Suporte por 6 meses

### Atualizações de Segurança

Atualizações de segurança são fornecidas para:
- Versão atual (1.0.x)
- Versão anterior (0.9.x) - apenas críticas

## Contribuição

Para contribuir com mudanças:

1. Verificar issues existentes
2. Criar branch feature
3. Implementar com testes
4. Atualizar documentação
5. Submeter pull request

### Formato de Commit

```
tipo(escopo): descrição

Descrição detalhada da mudança.

Closes #123
```

Tipos válidos: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

## Links Úteis

- [Documentação da API](./API_DOCUMENTATION.md)
- [Guia do Desenvolvedor](./DEVELOPER_GUIDE.md)
- [Exemplos de Integração](./API_EXAMPLES.md)
- [Troubleshooting](../TROUBLESHOOTING.md)
- [Repositório GitHub](https://github.com/seu-usuario/iotcnt)
