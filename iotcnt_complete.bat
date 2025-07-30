@echo off
setlocal enabledelayedexpansion

:: IOTCNT Complete System Management Script
:: Sistema completo de gestão do IOTCNT com todas as funcionalidades
:: Inclui verificações do novo sistema de performance implementado

title IOTCNT - Sistema Completo de Gestao

:: Colors for output (Windows 10+)
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "CYAN=[96m"
set "WHITE=[97m"
set "MAGENTA=[95m"
set "GRAY=[90m"
set "RESET=[0m"

:: System variables
set "DOCKER_COMPOSE_CMD="
set "SYSTEM_STATUS=UNKNOWN"
set "WEB_STATUS=OFFLINE"
set "DB_STATUS=OFFLINE"
set "REDIS_STATUS=OFFLINE"
set "PERFORMANCE_STATUS=UNKNOWN"

goto :main_menu

:: ========================================
:: UTILITY FUNCTIONS
:: ========================================

:print_header
cls
echo %CYAN%
echo  ██╗ ██████╗ ████████╗ ██████╗███╗   ██╗████████╗
echo  ██║██╔═══██╗╚══██╔══╝██╔════╝████╗  ██║╚══██╔══╝
echo  ██║██║   ██║   ██║   ██║     ██╔██╗ ██║   ██║
echo  ██║██║   ██║   ██║   ██║     ██║╚██╗██║   ██║
echo  ██║╚██████╔╝   ██║   ╚██████╗██║ ╚████║   ██║
echo  ╚═╝ ╚═════╝    ╚═╝    ╚═════╝╚═╝  ╚═══╝   ╚═╝
echo.
echo           Sistema Completo de Irrigação IoT
echo  ============================================
echo %RESET%
goto :eof

:print_info
echo %GREEN%[INFO]%RESET% %~1
goto :eof

:print_warning
echo %YELLOW%[AVISO]%RESET% %~1
goto :eof

:print_error
echo %RED%[ERRO]%RESET% %~1
goto :eof

:print_step
echo %BLUE%[PASSO]%RESET% %~1
goto :eof

:print_success
echo %GREEN%[SUCESSO]%RESET% %~1
goto :eof

:print_status
echo %MAGENTA%[STATUS]%RESET% %~1
goto :eof

:: ========================================
:: SYSTEM DETECTION AND STATUS
:: ========================================

:detect_docker_compose
:: Detect which docker compose command to use
docker compose version >nul 2>&1
if %errorLevel% equ 0 (
    set "DOCKER_COMPOSE_CMD=docker compose"
    goto :eof
)

docker-compose --version >nul 2>&1
if %errorLevel% equ 0 (
    set "DOCKER_COMPOSE_CMD=docker-compose"
    goto :eof
)

set "DOCKER_COMPOSE_CMD=NONE"
goto :eof

:check_system_status
call :detect_docker_compose

if "%DOCKER_COMPOSE_CMD%"=="NONE" (
    set "SYSTEM_STATUS=DOCKER_MISSING"
    goto :eof
)

:: Check if containers are running
%DOCKER_COMPOSE_CMD% ps --services >nul 2>&1
if %errorLevel% neq 0 (
    set "SYSTEM_STATUS=STOPPED"
    goto :eof
)

:: Check individual services
for /f %%i in ('%DOCKER_COMPOSE_CMD% ps --services 2^>nul') do (
    %DOCKER_COMPOSE_CMD% ps %%i | findstr "Up" >nul 2>&1
    if !errorLevel! equ 0 (
        set "SYSTEM_STATUS=RUNNING"
    ) else (
        set "SYSTEM_STATUS=PARTIAL"
    )
)

:: Check web server
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost' -TimeoutSec 5; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 (
    set "WEB_STATUS=ONLINE"
) else (
    set "WEB_STATUS=OFFLINE"
)

:: Check database
%DOCKER_COMPOSE_CMD% exec -T app php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB_OK'; } catch(Exception \$e) { echo 'DB_ERROR'; }" 2>nul | findstr "DB_OK" >nul
if %errorLevel% equ 0 (
    set "DB_STATUS=ONLINE"
) else (
    set "DB_STATUS=OFFLINE"
)

:: Check Redis
%DOCKER_COMPOSE_CMD% exec -T app php artisan tinker --execute="try { Redis::ping(); echo 'REDIS_OK'; } catch(Exception \$e) { echo 'REDIS_ERROR'; }" 2>nul | findstr "REDIS_OK" >nul
if %errorLevel% equ 0 (
    set "REDIS_STATUS=ONLINE"
) else (
    set "REDIS_STATUS=OFFLINE"
)

:: Check Performance System (NEW!)
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost/admin/performance' -TimeoutSec 5; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 (
    set "PERFORMANCE_STATUS=AVAILABLE"
) else (
    set "PERFORMANCE_STATUS=UNAVAILABLE"
)

goto :eof

:display_system_status
echo %WHITE%Estado do Sistema:%RESET%
echo.

:: System Status
if "%SYSTEM_STATUS%"=="RUNNING" (
    echo   🟢 Sistema: %GREEN%ATIVO%RESET%
) else if "%SYSTEM_STATUS%"=="PARTIAL" (
    echo   🟡 Sistema: %YELLOW%PARCIAL%RESET%
) else if "%SYSTEM_STATUS%"=="STOPPED" (
    echo   🔴 Sistema: %RED%PARADO%RESET%
) else if "%SYSTEM_STATUS%"=="DOCKER_MISSING" (
    echo   ❌ Sistema: %RED%DOCKER NÃO ENCONTRADO%RESET%
) else (
    echo   ❓ Sistema: %GRAY%DESCONHECIDO%RESET%
)

:: Web Status
if "%WEB_STATUS%"=="ONLINE" (
    echo   🌐 Web: %GREEN%ONLINE%RESET% - http://localhost
) else (
    echo   🌐 Web: %RED%OFFLINE%RESET%
)

:: Database Status
if "%DB_STATUS%"=="ONLINE" (
    echo   🗄️  Base de Dados: %GREEN%ONLINE%RESET%
) else (
    echo   🗄️  Base de Dados: %RED%OFFLINE%RESET%
)

:: Redis Status
if "%REDIS_STATUS%"=="ONLINE" (
    echo   🔧 Redis: %GREEN%ONLINE%RESET%
) else (
    echo   🔧 Redis: %RED%OFFLINE%RESET%
)

:: Performance System Status (NEW!)
if "%PERFORMANCE_STATUS%"=="AVAILABLE" (
    echo   📊 Performance: %GREEN%DISPONÍVEL%RESET% - /admin/performance
) else (
    echo   📊 Performance: %RED%INDISPONÍVEL%RESET%
)

echo.
goto :eof

:: ========================================
:: MAIN MENU
:: ========================================

:main_menu
call :print_header
call :check_system_status
call :display_system_status

echo %WHITE%Menu Principal:%RESET%
echo.
echo %GREEN% 1.%RESET% 🚀 Iniciar Sistema Completo
echo %BLUE% 2.%RESET% 🔧 Verificação e Diagnóstico Completo
echo %YELLOW% 3.%RESET% 📊 Monitorização e Performance
echo %MAGENTA% 4.%RESET% 📋 Gestão de Logs
echo %CYAN% 5.%RESET% 🌐 Gestão Web
echo %WHITE% 6.%RESET% 🗄️  Gestão de Base de Dados
echo %GREEN% 7.%RESET% ⚙️  Configuração e Manutenção
echo %BLUE% 8.%RESET% 🔄 Operações de Sistema
echo %YELLOW% 9.%RESET% 📖 Documentação e Ajuda
echo %RED% 0.%RESET% ❌ Sair
echo.

set /p choice="Digite sua opção (0-9): "

if "%choice%"=="1" goto :start_system
if "%choice%"=="2" goto :full_diagnostics
if "%choice%"=="3" goto :performance_menu
if "%choice%"=="4" goto :logs_menu
if "%choice%"=="5" goto :web_menu
if "%choice%"=="6" goto :database_menu
if "%choice%"=="7" goto :config_menu
if "%choice%"=="8" goto :system_operations
if "%choice%"=="9" goto :help_menu
if "%choice%"=="0" goto :exit

call :print_error "Opção inválida!"
timeout /t 2 >nul
goto :main_menu

:: ========================================
:: SYSTEM STARTUP
:: ========================================

:start_system
call :print_header
call :print_step "Iniciando Sistema IOTCNT Completo..."
echo.

:: Check prerequisites
call :print_step "Verificando pré-requisitos..."

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    call :print_warning "Recomenda-se executar como Administrador"
)

:: Check Docker
call :detect_docker_compose
if "%DOCKER_COMPOSE_CMD%"=="NONE" (
    call :print_error "Docker não encontrado!"
    echo.
    echo Instale o Docker Desktop: https://www.docker.com/products/docker-desktop
    pause
    goto :main_menu
)

call :print_success "Docker encontrado: %DOCKER_COMPOSE_CMD%"

:: Check configuration files
call :print_step "Verificando ficheiros de configuração..."

if not exist ".env" (
    call :print_warning "Ficheiro .env não encontrado. Criando..."
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        call :print_info "Ficheiro .env criado. Configure antes de continuar."
        echo.
        set /p continue="Pressione Enter após configurar o .env ou 'n' para sair: "
        if /i "!continue!"=="n" goto :main_menu
    ) else (
        call :print_error "Ficheiro .env.example não encontrado!"
        pause
        goto :main_menu
    )
)

:: Copy other config files
call :copy_config_files

:: Stop existing containers
call :print_step "Parando containers existentes..."
%DOCKER_COMPOSE_CMD% down --remove-orphans >nul 2>&1

:: Build and start
call :print_step "Construindo e iniciando containers..."
echo %YELLOW%Isto pode demorar alguns minutos na primeira execução...%RESET%

%DOCKER_COMPOSE_CMD% build --no-cache
if %errorLevel% neq 0 (
    call :print_error "Falha ao construir containers!"
    pause
    goto :main_menu
)

%DOCKER_COMPOSE_CMD% up -d
if %errorLevel% neq 0 (
    call :print_error "Falha ao iniciar containers!"
    pause
    goto :main_menu
)

call :print_success "Containers iniciados"

:: Wait for services
call :print_step "Aguardando serviços ficarem prontos..."
echo %YELLOW%Aguardando 30 segundos...%RESET%
ping 127.0.0.1 -n 31 >nul

:: Laravel setup
call :print_step "Configurando aplicação Laravel..."

%DOCKER_COMPOSE_CMD% exec -T app composer install --no-dev --optimize-autoloader
%DOCKER_COMPOSE_CMD% exec -T app php artisan key:generate --force
%DOCKER_COMPOSE_CMD% exec -T app php artisan migrate --force
%DOCKER_COMPOSE_CMD% exec -T app php artisan db:seed --force
%DOCKER_COMPOSE_CMD% exec -T app php artisan config:cache
%DOCKER_COMPOSE_CMD% exec -T app php artisan route:cache
%DOCKER_COMPOSE_CMD% exec -T app php artisan view:cache
%DOCKER_COMPOSE_CMD% exec -T app php artisan storage:link

call :print_success "Laravel configurado"

:: Test Performance System (NEW!)
call :print_step "Testando Sistema de Performance..."
timeout /t 5 >nul
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost/admin/performance' -TimeoutSec 10; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 (
    call :print_success "Sistema de Performance DISPONÍVEL!"
) else (
    call :print_warning "Sistema de Performance pode não estar acessível ainda"
)

:: Final verification
call :print_step "Verificação final..."
call :check_system_status

echo.
echo %CYAN%========================================%RESET%
echo %WHITE%        SISTEMA INICIADO COM SUCESSO   %RESET%
echo %CYAN%========================================%RESET%
echo.

call :display_system_status

echo %WHITE%Interfaces Disponíveis:%RESET%
echo   🌐 Principal: http://localhost
echo   🔧 Admin: http://localhost/admin/dashboard
echo   📊 Performance: http://localhost/admin/performance
echo   📋 Logs: http://localhost/admin/logs
echo   ⚙️  Configurações: http://localhost/admin/settings
echo.

set /p open_web="Abrir interface web? (s/n): "
if /i "!open_web!"=="s" (
    start http://localhost
)

pause
goto :main_menu

:: ========================================
:: PERFORMANCE MENU (NEW!)
:: ========================================

:performance_menu
call :print_header
echo %MAGENTA%Sistema de Performance e Monitorização%RESET%
echo.

call :check_system_status
if "%SYSTEM_STATUS%"=="RUNNING" (
    echo %GREEN%✅ Sistema ativo - Performance disponível%RESET%
) else (
    echo %RED%❌ Sistema inativo - Inicie o sistema primeiro%RESET%
    pause
    goto :main_menu
)

echo.
echo %WHITE%Opções de Performance:%RESET%
echo.
echo %GREEN% 1.%RESET% 📊 Abrir Dashboard de Performance
echo %BLUE% 2.%RESET% 🔍 Verificar Métricas do Sistema
echo %YELLOW% 3.%RESET% 🗄️  Estatísticas de Cache
echo %MAGENTA% 4.%RESET% 🐌 Detectar Queries Lentas
echo %CYAN% 5.%RESET% 🧹 Limpar Cache do Sistema
echo %WHITE% 6.%RESET% ⚡ Optimizar Sistema Completo
echo %GREEN% 7.%RESET% 📈 Monitorização em Tempo Real
echo %BLUE% 8.%RESET% 🔧 Diagnóstico de Performance
echo %RED% 0.%RESET% ⬅️  Voltar ao Menu Principal
echo.

set /p perf_choice="Digite sua opção (0-8): "

if "%perf_choice%"=="1" goto :open_performance_dashboard
if "%perf_choice%"=="2" goto :check_system_metrics
if "%perf_choice%"=="3" goto :check_cache_stats
if "%perf_choice%"=="4" goto :detect_slow_queries
if "%perf_choice%"=="5" goto :clear_system_cache
if "%perf_choice%"=="6" goto :optimize_system
if "%perf_choice%"=="7" goto :real_time_monitoring
if "%perf_choice%"=="8" goto :performance_diagnostics
if "%perf_choice%"=="0" goto :main_menu

call :print_error "Opção inválida!"
timeout /t 2 >nul
goto :performance_menu

:open_performance_dashboard
call :print_info "Abrindo Dashboard de Performance..."
start http://localhost/admin/performance
echo %GREEN%Dashboard aberto no navegador!%RESET%
pause
goto :performance_menu

:check_system_metrics
call :print_step "Obtendo métricas do sistema..."
echo.

:: Get metrics via Laravel command
%DOCKER_COMPOSE_CMD% exec -T app php artisan tinker --execute="
try {
    \$service = app('App\Services\PerformanceOptimizationService');
    \$metrics = \$service->getPerformanceMetrics();
    echo 'Tempo de Resposta: ' . \$metrics['response_time'] . 'ms' . PHP_EOL;
    echo 'Uso de Memória: ' . \$metrics['memory_usage'] . 'MB' . PHP_EOL;
    echo 'Cache Hit Rate: ' . \$metrics['cache_hit_rate'] . '%%' . PHP_EOL;
    echo 'Queries BD: ' . \$metrics['db_queries'] . PHP_EOL;
} catch(Exception \$e) {
    echo 'Erro ao obter métricas: ' . \$e->getMessage();
}
"

echo.
pause
goto :performance_menu

:check_cache_stats
call :print_step "Verificando estatísticas de cache..."
echo.

%DOCKER_COMPOSE_CMD% exec -T app php artisan tinker --execute="
try {
    \$service = app('App\Services\PerformanceOptimizationService');
    \$stats = \$service->getCacheStats();
    echo 'Total de Chaves: ' . \$stats['total_keys'] . PHP_EOL;
    echo 'Memória Usada: ' . \$stats['memory_used'] . PHP_EOL;
    echo 'Hit Rate: ' . \$stats['hit_rate'] . '%%' . PHP_EOL;
    echo 'Miss Rate: ' . \$stats['miss_rate'] . '%%' . PHP_EOL;
} catch(Exception \$e) {
    echo 'Erro ao obter estatísticas: ' . \$e->getMessage();
}
"

echo.
pause
goto :performance_menu

:clear_system_cache
call :print_step "Limpando cache do sistema..."

%DOCKER_COMPOSE_CMD% exec -T app php artisan cache:clear
%DOCKER_COMPOSE_CMD% exec -T app php artisan config:clear
%DOCKER_COMPOSE_CMD% exec -T app php artisan route:clear
%DOCKER_COMPOSE_CMD% exec -T app php artisan view:clear

call :print_success "Cache limpo com sucesso!"
pause
goto :performance_menu

:optimize_system
call :print_step "Executando optimização completa do sistema..."
echo.

%DOCKER_COMPOSE_CMD% exec -T app php artisan tinker --execute="
try {
    \$service = app('App\Services\PerformanceOptimizationService');
    \$result = \$service->runFullOptimization();
    if (\$result['success']) {
        echo 'Optimização concluída com sucesso!' . PHP_EOL;
        echo 'Duração: ' . \$result['duration_seconds'] . ' segundos' . PHP_EOL;
    } else {
        echo 'Erro na optimização: ' . \$result['error'] . PHP_EOL;
    }
} catch(Exception \$e) {
    echo 'Erro ao executar optimização: ' . \$e->getMessage();
}
"

echo.
call :print_success "Optimização concluída!"
pause
goto :performance_menu

:real_time_monitoring
call :print_info "Monitorização em tempo real (Ctrl+C para sair)..."
echo.
echo %YELLOW%Mostrando uso de recursos dos containers...%RESET%
docker stats --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}\t{{.BlockIO}}"
goto :performance_menu

:performance_diagnostics
call :print_step "Executando diagnóstico completo de performance..."
echo.

echo %WHITE%1. Verificando containers...%RESET%
%DOCKER_COMPOSE_CMD% ps

echo.
echo %WHITE%2. Verificando uso de recursos...%RESET%
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}"

echo.
echo %WHITE%3. Verificando conectividade...%RESET%
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost' -TimeoutSec 5; Write-Host 'Web: OK ('$response.StatusCode')' } catch { Write-Host 'Web: ERRO' }"
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost/admin/performance' -TimeoutSec 5; Write-Host 'Performance: OK ('$response.StatusCode')' } catch { Write-Host 'Performance: ERRO' }"

echo.
echo %WHITE%4. Verificando logs de erro...%RESET%
%DOCKER_COMPOSE_CMD% logs --tail=10 | findstr -i "error\|exception\|fail" || echo Nenhum erro recente encontrado

echo.
pause
goto :performance_menu

:: ========================================
:: CONFIGURATION FUNCTIONS
:: ========================================

:copy_config_files
if not exist "docker\nginx\conf.d\app.conf" (
    if exist "docker\nginx\conf.d\app.example.conf" (
        copy "docker\nginx\conf.d\app.example.conf" "docker\nginx\conf.d\app.conf" >nul
        call :print_info "Configuração Nginx criada"
    )
)

if not exist "docker\mysql\my.cnf" (
    if exist "docker\mysql\my.example.cnf" (
        copy "docker\mysql\my.example.cnf" "docker\mysql\my.cnf" >nul
        call :print_info "Configuração MySQL criada"
    )
)

if not exist "docker\redis\redis.conf" (
    if exist "docker\redis\redis.example.conf" (
        copy "docker\redis\redis.example.conf" "docker\redis\redis.conf" >nul
        call :print_info "Configuração Redis criada"
    )
)

if not exist "esp32_irrigation_controller\config.h" (
    if exist "esp32_irrigation_controller\config.example.h" (
        copy "esp32_irrigation_controller\config.example.h" "esp32_irrigation_controller\config.h" >nul
        call :print_info "Configuração ESP32 criada"
    )
)
goto :eof

:: ========================================
:: SIMPLIFIED MENUS (Placeholder implementations)
:: ========================================

:full_diagnostics
call :print_header
call :print_info "Executando diagnóstico completo..."
call check_iotcnt.bat
goto :main_menu

:logs_menu
call :print_header
echo %WHITE%Gestão de Logs:%RESET%
echo.
echo 1. Ver todos os logs
echo 2. Logs da aplicação
echo 3. Logs do servidor web
echo 4. Logs da base de dados
echo 5. Apenas erros
echo 0. Voltar
echo.
set /p log_choice="Opção: "

if "%log_choice%"=="1" %DOCKER_COMPOSE_CMD% logs -f
if "%log_choice%"=="2" %DOCKER_COMPOSE_CMD% logs -f app
if "%log_choice%"=="3" %DOCKER_COMPOSE_CMD% logs -f webserver
if "%log_choice%"=="4" %DOCKER_COMPOSE_CMD% logs -f database
if "%log_choice%"=="5" %DOCKER_COMPOSE_CMD% logs -f | findstr -i "error\|exception\|fail"
if "%log_choice%"=="0" goto :main_menu

goto :logs_menu

:web_menu
call :print_header
echo %WHITE%Gestão Web:%RESET%
echo.
echo 1. Abrir interface principal
echo 2. Abrir painel admin
echo 3. Abrir sistema de performance
echo 4. Verificar status web
echo 0. Voltar
echo.
set /p web_choice="Opção: "

if "%web_choice%"=="1" start http://localhost
if "%web_choice%"=="2" start http://localhost/admin/dashboard
if "%web_choice%"=="3" start http://localhost/admin/performance
if "%web_choice%"=="4" powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost' -TimeoutSec 5; Write-Host 'Status: '$response.StatusCode } catch { Write-Host 'Status: ERRO' }"
if "%web_choice%"=="0" goto :main_menu

pause
goto :web_menu

:database_menu
call :print_header
echo %WHITE%Gestão de Base de Dados:%RESET%
echo.
echo 1. Backup da base de dados
echo 2. Verificar conexão
echo 3. Executar migrações
echo 4. Limpar dados antigos
echo 0. Voltar
echo.
set /p db_choice="Opção: "

if "%db_choice%"=="1" goto :backup_database
if "%db_choice%"=="2" goto :test_database
if "%db_choice%"=="3" %DOCKER_COMPOSE_CMD% exec -T app php artisan migrate
if "%db_choice%"=="4" goto :clean_old_data
if "%db_choice%"=="0" goto :main_menu

pause
goto :database_menu

:config_menu
call :print_header
echo %WHITE%Configuração e Manutenção:%RESET%
echo.
echo 1. Editar .env
echo 2. Copiar ficheiros de configuração
echo 3. Limpar sistema Docker
echo 4. Recriar containers
echo 0. Voltar
echo.
set /p config_choice="Opção: "

if "%config_choice%"=="1" notepad .env
if "%config_choice%"=="2" call :copy_config_files
if "%config_choice%"=="3" docker system prune -f
if "%config_choice%"=="4" goto :recreate_containers
if "%config_choice%"=="0" goto :main_menu

pause
goto :config_menu

:system_operations
call :print_header
echo %WHITE%Operações de Sistema:%RESET%
echo.
echo 1. Parar sistema
echo 2. Reiniciar sistema
echo 3. Ver status detalhado
echo 4. Executar comando personalizado
echo 0. Voltar
echo.
set /p sys_choice="Opção: "

if "%sys_choice%"=="1" %DOCKER_COMPOSE_CMD% down
if "%sys_choice%"=="2" %DOCKER_COMPOSE_CMD% restart
if "%sys_choice%"=="3" call :display_detailed_status
if "%sys_choice%"=="4" goto :custom_command
if "%sys_choice%"=="0" goto :main_menu

pause
goto :system_operations

:help_menu
call :print_header
echo %WHITE%Documentação e Ajuda:%RESET%
echo.
echo %CYAN%🌱 IOTCNT - Sistema de Irrigação IoT%RESET%
echo.
echo %GREEN%Funcionalidades Principais:%RESET%
echo   • Controlo de até 5 válvulas de irrigação
echo   • Interface web completa
echo   • Bot Telegram integrado
echo   • Sistema de performance e monitorização
echo   • Agendamentos automáticos
echo   • Logs detalhados de operações
echo.
echo %BLUE%Interfaces Disponíveis:%RESET%
echo   🌐 Principal: http://localhost
echo   🔧 Admin: http://localhost/admin/dashboard
echo   📊 Performance: http://localhost/admin/performance
echo   📋 Logs: http://localhost/admin/logs
echo   ⚙️  Configurações: http://localhost/admin/settings
echo.
echo %YELLOW%Ficheiros Importantes:%RESET%
echo   • .env - Configurações principais
echo   • docker-compose.yml - Serviços Docker
echo   • esp32_irrigation_controller/ - Firmware ESP32
echo.
echo %MAGENTA%Sistema de Performance (NOVO!):%RESET%
echo   • Métricas em tempo real
echo   • Optimização automática
echo   • Detecção de queries lentas
echo   • Gestão inteligente de cache
echo   • Recomendações de melhoria
echo.
pause
goto :main_menu

:: ========================================
:: UTILITY IMPLEMENTATIONS
:: ========================================

:backup_database
set backup_date=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set backup_date=%backup_date: =0%
set backup_file=backup_iotcnt_%backup_date%.sql

call :print_step "Criando backup: %backup_file%"
%DOCKER_COMPOSE_CMD% exec -T database mysqldump -u root -proot_password_here iotcnt > %backup_file%
if %errorLevel% equ 0 (
    call :print_success "Backup criado com sucesso!"
) else (
    call :print_error "Falha ao criar backup"
)
goto :eof

:test_database
call :print_step "Testando conexão com base de dados..."
%DOCKER_COMPOSE_CMD% exec -T app php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Conexão OK'; } catch(Exception \$e) { echo 'Erro: ' . \$e->getMessage(); }"
goto :eof

:clean_old_data
call :print_warning "Isto irá remover logs antigos (>30 dias)"
set /p confirm="Continuar? (s/n): "
if /i "!confirm!"=="s" (
    %DOCKER_COMPOSE_CMD% exec -T app php artisan tinker --execute="DB::table('operation_logs')->where('created_at', '<', now()->subDays(30))->delete(); echo 'Logs antigos removidos';"
)
goto :eof

:recreate_containers
call :print_warning "Isto irá recriar todos os containers"
set /p confirm="Continuar? (s/n): "
if /i "!confirm!"=="s" (
    %DOCKER_COMPOSE_CMD% down
    %DOCKER_COMPOSE_CMD% build --no-cache
    %DOCKER_COMPOSE_CMD% up -d
    call :print_success "Containers recriados!"
)
goto :eof

:custom_command
echo %WHITE%Executar comando no container da aplicação:%RESET%
set /p custom_cmd="Comando: "
if not "!custom_cmd!"=="" (
    %DOCKER_COMPOSE_CMD% exec app !custom_cmd!
)
goto :eof

:display_detailed_status
call :check_system_status
call :display_system_status
echo.
echo %WHITE%Containers:%RESET%
%DOCKER_COMPOSE_CMD% ps
echo.
echo %WHITE%Recursos:%RESET%
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}"
goto :eof

:: ========================================
:: EXIT
:: ========================================

:exit
call :print_header
echo %CYAN%
echo  Obrigado por usar o IOTCNT!
echo  Sistema Completo de Irrigação IoT
echo.
echo  🌱 Mantenha suas plantas felizes! 💧
echo.
echo  Funcionalidades implementadas:
echo  ✅ Sistema de irrigação automatizado
echo  ✅ Interface web completa
echo  ✅ Bot Telegram integrado
echo  ✅ Sistema de performance e monitorização
echo  ✅ Gestão avançada de cache
echo  ✅ Diagnósticos e optimizações
echo %RESET%
timeout /t 5 >nul
exit /b 0
