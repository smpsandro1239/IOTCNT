# 🤝 Contribuindo para o IOTCNT

Obrigado pelo seu interesse em contribuir para o projeto IOTCNT! Este documento fornece diretrizes para contribuições.

## 📋 Como Contribuir

### 1. Reportar Bugs
- Use o [GitHub Issues](https://github.com/seu-usuario/iotcnt/issues)
- Descreva o problema detalhadamente
- Inclua passos para reproduzir
- Adicione logs relevantes
- Especifique ambiente (OS, versões, etc.)

### 2. Sugerir Funcionalidades
- Abra um Issue com tag "enhancement"
- Descreva a funcionalidade desejada
- Explique o caso de uso
- Considere a compatibilidade existente

### 3. Contribuir com Código

#### Preparação
```bash
# Fork o repositório no GitHub
# Clone seu fork
git clone https://github.com/seu-usuario/iotcnt.git
cd iotcnt

# Adicione o repositório original como upstream
git remote add upstream https://github.com/original-user/iotcnt.git

# Crie uma branch para sua feature
git checkout -b feature/nova-funcionalidade
```

#### Desenvolvimento
1. Siga os padrões de código existentes
2. Adicione testes quando apropriado
3. Documente mudanças significativas
4. Teste localmente antes de submeter

#### Submissão
```bash
# Commit suas mudanças
git add .
git commit -m "feat: adicionar nova funcionalidade"

# Push para seu fork
git push origin feature/nova-funcionalidade

# Abra um Pull Request no GitHub
```

## 📝 Padrões de Código

### PHP/Laravel
- Seguir PSR-12
- Usar nomes descritivos para variáveis e funções
- Comentar código complexo
- Usar type hints quando possível

```php
// ✅ Bom
public function updateValveStatus(int $valveNumber, bool $state): bool
{
    // Validar entrada
    if ($valveNumber < 1 || $valveNumber > 5) {
        return false;
    }

    // Atualizar estado
    return $this->valve->update($valveNumber, $state);
}

// ❌ Evitar
function update($v, $s) {
    return $this->valve->update($v, $s);
}
```

### C++ (ESP32)
- Usar nomes descritivos
- Comentar configurações importantes
- Seguir convenções Arduino/ESP32

```cpp
// ✅ Bom
void controlValve(int valveNumber, bool state) {
    if (valveNumber < 0 || valveNumber >= NUM_VALVES) {
        Serial.println("[ERROR] Invalid valve number");
        return;
    }

    digitalWrite(VALVE_PINS[valveNumber], state ? RELAY_ON_STATE : RELAY_OFF_STATE);
}

// ❌ Evitar
void ctrl(int v, bool s) {
    digitalWrite(pins[v], s);
}
```

### JavaScript/Frontend
- Usar const/let em vez de var
- Nomes descritivos para funções
- Comentar lógica complexa

## 🧪 Testes

### Laravel
```bash
# Executar testes
php artisan test

# Executar testes específicos
php artisan test --filter=ValveTest
```

### ESP32
- Testar em hardware real quando possível
- Verificar logs serial
- Testar cenários de erro

## 📚 Documentação

### Comentários no Código
```php
/**
 * Update valve state and log operation
 *
 * @param int $valveNumber Valve number (1-5)
 * @param bool $state New valve state
 * @param string $source Operation source
 * @return bool Success status
 */
public function updateValveState(int $valveNumber, bool $state, string $source): bool
```

### README Updates
- Atualizar README.md se necessário
- Manter exemplos atualizados
- Documentar novas configurações

## 🔄 Processo de Review

### Checklist do Pull Request
- [ ] Código segue padrões estabelecidos
- [ ] Testes passam
- [ ] Documentação atualizada
- [ ] Sem conflitos de merge
- [ ] Descrição clara das mudanças

### Tipos de Commit
Use prefixos convencionais:
- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação
- `refactor:` Refatoração
- `test:` Testes
- `chore:` Manutenção

```bash
# Exemplos
git commit -m "feat: adicionar controle manual de válvulas"
git commit -m "fix: corrigir timeout de conexão ESP32"
git commit -m "docs: atualizar guia de instalação"
```

## 🚀 Áreas de Contribuição

### Backend (Laravel)
- Novos endpoints API
- Melhorias de performance
- Funcionalidades de administração
- Integração com outros sistemas

### Frontend
- Interface de usuário
- Dashboards
- Responsividade mobile
- Acessibilidade

### ESP32 Firmware
- Otimizações de performance
- Novos sensores/atuadores
- Protocolos de comunicação
- Gestão de energia

### DevOps/Infraestrutura
- Melhorias Docker
- Scripts de deployment
- Monitorização
- Backup/restore

### Documentação
- Guias de instalação
- Tutoriais
- Exemplos de uso
- Tradução

## 🐛 Debugging

### Informações Úteis para Reports
```bash
# Versões do sistema
docker --version
docker-compose --version
php --version

# Status dos containers
docker-compose ps

# Logs relevantes
docker-compose logs app
docker-compose logs database

# Configuração ESP32
# Incluir config.h (sem senhas)
# Logs serial do ESP32
```

## 📞 Comunicação

### Canais
- **GitHub Issues**: Bugs e funcionalidades
- **GitHub Discussions**: Perguntas gerais
- **Pull Requests**: Revisão de código

### Etiqueta
- Seja respeitoso e construtivo
- Forneça contexto suficiente
- Seja paciente com reviews
- Ajude outros contribuidores

## 🎯 Roadmap

### Próximas Funcionalidades
- [ ] Suporte a mais válvulas (>5)
- [ ] Interface mobile nativa
- [ ] Integração com sensores de umidade
- [ ] Previsão meteorológica
- [ ] Relatórios avançados
- [ ] API GraphQL
- [ ] Suporte MQTT
- [ ] Integração Home Assistant

### Melhorias Técnicas
- [ ] Testes automatizados
- [ ] CI/CD pipeline
- [ ] Monitorização avançada
- [ ] Performance optimization
- [ ] Security hardening

## 📄 Licença

Ao contribuir, você concorda que suas contribuições serão licenciadas sob a mesma licença MIT do projeto.

## 🙏 Reconhecimento

Todos os contribuidores serão reconhecidos no README.md e releases do projeto.

---

**Obrigado por contribuir para o IOTCNT! 🌱💧**
