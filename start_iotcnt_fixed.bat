@echo off
setlocal enabledelayedexpansion

:: IOTCNT System Startup Script - Fixed Version
title IOTCNT - Sistema de Irrigacao IoT

:: Colors for output
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "CYAN=[96m"
set "WHITE=[97m"
set "RESET=[0m"

echo %CYAN%
echo  ========================================
echo  🌱 IOTCNT - Sistema de Irrigacao IoT
echo  ========================================
echo %RESET%

goto :main

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

:main

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    call :print_warning "Recomenda-se executar como Administrador para melhor funcionamento"
    ping 127.0.0.1 -n 4 >nul
)

:: Check Docker installation
call :print_step "Verificando instalacao do Docker..."
docker --version >nul 2>&1
if %errorLevel% neq 0 (
    call :print_error "Docker nao encontrado! Por favor instale o Docker Desktop primeiro."
    echo.
    echo Baixe em: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

docker compose version >nul 2>&1
if %errorLevel% neq 0 (
    docker-compose --version >nul 2>&1
    if %errorLevel% neq 0 (
        call :print_error "Docker Compose nao encontrado!"
        pause
        exit /b 1
    )
)

call :print_success "Docker e Docker Compose encontrados"

:: Check if .env file exists
call :print_step "Verificando configuracao do ambiente..."
if not exist ".env" (
    call :print_warning "Arquivo .env nao encontrado. Criando a partir do exemplo..."
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        call :print_info "Arquivo .env criado. Por favor configure antes de continuar."
    ) else (
        call :print_error "Arquivo .env.example nao encontrado!"
        pause
        exit /b 1
    )

    echo.
    call :print_warning "IMPORTANTE: Configure o arquivo .env com:"
    echo   - Senhas da base de dados
    echo   - Token do bot Telegram
    echo   - Token da API ESP32
    echo   - URL do servidor
    echo.
    set /p continue="Pressione Enter apos configurar o .env ou 'n' para sair: "
    if /i "!continue!"=="n" exit /b 0
)

call :print_success "Arquivo .env encontrado"

:: Copy other config files if missing
call :print_step "Verificando outros arquivos de configuracao..."

if not exist "docker\nginx\conf.d\app.conf" (
    if exist "docker\nginx\conf.d\app.example.conf" (
        copy "docker\nginx\conf.d\app.example.conf" "docker\nginx\conf.d\app.conf" >nul
        call :print_info "Configuracao Nginx criada"
    )
)

if not exist "docker\mysql\my.cnf" (
    if exist "docker\mysql\my.example.cnf" (
        copy "docker\mysql\my.example.cnf" "docker\mysql\my.cnf" >nul
        call :print_info "Configuracao MySQL criada"
    )
)

if not exist "docker\redis\redis.conf" (
    if exist "docker\redis\redis.example.conf" (
        copy "docker\redis\redis.example.conf" "docker\redis\redis.conf" >nul
        call :print_info "Configuracao Redis criada"
    )
)

:: Stop any running containers
call :print_step "Parando containers existentes..."
docker compose down --remove-orphans >nul 2>&1
if %errorLevel% neq 0 (
    docker-compose down --remove-orphans >nul 2>&1
)

:: Build and start containers
call :print_step "Construindo e iniciando containers Docker..."
echo %YELLOW%Isto pode demorar alguns minutos na primeira execucao...%RESET%

:: Try docker compose first, then docker-compose
docker compose build --no-cache >nul 2>&1
if %errorLevel% neq 0 (
    call :print_info "Tentando com docker-compose..."
    docker-compose build --no-cache
    if %errorLevel% neq 0 (
        call :print_error "Falha ao construir containers!"
        echo.
        call :print_info "Executando diagnostico..."
        docker compose config
        pause
        exit /b 1
    )
    set USE_COMPOSE=docker-compose
) else (
    set USE_COMPOSE=docker compose
)

%USE_COMPOSE% up -d
if %errorLevel% neq 0 (
    call :print_error "Falha ao iniciar containers!"
    echo.
    call :print_info "Verificando logs..."
    %USE_COMPOSE% logs
    pause
    exit /b 1
)

call :print_success "Containers iniciados com sucesso"

:: Wait for services to be ready
call :print_step "Aguardando servicos ficarem prontos..."
echo %YELLOW%Aguardando base de dados... (30 segundos)%RESET%
ping 127.0.0.1 -n 31 >nul

:: Check container status
call :print_step "Verificando status dos containers..."
%USE_COMPOSE% ps

:: Run Laravel setup
call :print_step "Configurando aplicacao Laravel..."

:: Install dependencies
call :print_info "Instalando dependencias..."
%USE_COMPOSE% exec -T app composer install --no-dev --optimize-autoloader
if %errorLevel% neq 0 (
    call :print_warning "Falha ao instalar dependencias, mas continuando..."
)

:: Generate app key
call :print_info "Gerando chave da aplicacao..."
%USE_COMPOSE% exec -T app php artisan key:generate --force

:: Run migrations
call :print_info "Executando migracoes da base de dados..."
%USE_COMPOSE% exec -T app php artisan migrate --force
if %errorLevel% neq 0 (
    call :print_error "Falha nas migracoes da base de dados!"
    echo.
    call :print_info "Verificando logs da base de dados..."
    %USE_COMPOSE% logs database
    pause
    exit /b 1
)

:: Seed database
call :print_info "Populando base de dados..."
%USE_COMPOSE% exec -T app php artisan db:seed --force

:: Cache configuration
call :print_info "Otimizando configuracao..."
%USE_COMPOSE% exec -T app php artisan config:cache
%USE_COMPOSE% exec -T app php artisan route:cache
%USE_COMPOSE% exec -T app php artisan view:cache

:: Create storage link
%USE_COMPOSE% exec -T app php artisan storage:link

call :print_success "Configuracao Laravel concluida"

:: System verification
call :print_step "Verificando sistema..."

:: Check web server
call :print_info "Testando servidor web..."
ping 127.0.0.1 -n 6 >nul

:: Try to test web server
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost' -TimeoutSec 10; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 (
    call :print_success "Servidor web respondendo (HTTP 200)"
) else (
    call :print_warning "Servidor web pode nao estar respondendo corretamente"
)

:: Check database connection
call :print_info "Testando conexao com base de dados..."
%USE_COMPOSE% exec -T app php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database OK'; } catch(Exception \$e) { echo 'Database Error'; exit(1); }" 2>nul | findstr "Database OK" >nul
if %errorLevel% equ 0 (
    call :print_success "Conexao com base de dados OK"
) else (
    call :print_warning "Problema na conexao com base de dados"
)

:: Show system information
echo.
echo %CYAN%========================================%RESET%
echo %WHITE%           SISTEMA INICIADO           %RESET%
echo %CYAN%========================================%RESET%
echo.
call :print_success "Sistema IOTCNT iniciado com sucesso!"
echo.
echo %WHITE%Acesso ao Sistema:%RESET%
echo   🌐 Interface Web: http://localhost
echo   🗄️  Base de Dados: localhost:3306
echo   🔧 Redis: localhost:6379
echo.
echo %WHITE%Proximos Passos:%RESET%
echo   1. Aceder a interface web: http://localhost
echo   2. Criar utilizador administrador
echo   3. Configurar valvulas no painel admin
echo   4. Configurar agendamentos de irrigacao
echo   5. Configurar ESP32 com endpoint da API
echo   6. Configurar webhook do Telegram
echo.
echo %WHITE%Comandos Uteis:%RESET%
echo   - Ver logs: %USE_COMPOSE% logs -f
echo   - Parar sistema: %USE_COMPOSE% down
echo   - Reiniciar: %USE_COMPOSE% restart
echo.

:: Ask if user wants to open web interface
set /p open_web="Abrir interface web no navegador? (s/n): "
if /i "!open_web!"=="s" (
    start http://localhost
)

:: Ask if user wants to see logs
set /p show_logs_choice="Ver logs em tempo real? (s/n): "
if /i "!show_logs_choice!"=="s" (
    %USE_COMPOSE% logs -f
)

echo.
call :print_success "Script concluido!"
pause
exit /b 0
