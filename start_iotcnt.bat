@echo off
setlocal enabledelayedexpansion

:: IOTCNT System Startup and Verification Script
:: This script initializes and checks the complete IOTCNT irrigation system

title IOTCNT - Sistema de Irrigacao IoT

:: Colors for output (Windows 10+)
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

:: Function to print colored messages
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
    timeout /t 3 >nul
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

docker-compose --version >nul 2>&1
if %errorLevel% neq 0 (
    call :print_error "Docker Compose nao encontrado!"
    pause
    exit /b 1
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

:: Stop any running containers
call :print_step "Parando containers existentes..."
docker-compose down --remove-orphans >nul 2>&1

:: Build and start containers
call :print_step "Construindo e iniciando containers Docker..."
echo %YELLOW%Isto pode demorar alguns minutos na primeira execucao...%RESET%

docker-compose build --no-cache
if %errorLevel% neq 0 (
    call :print_error "Falha ao construir containers!"
    pause
    exit /b 1
)

docker-compose up -d
if %errorLevel% neq 0 (
    call :print_error "Falha ao iniciar containers!"
    pause
    exit /b 1
)

call :print_success "Containers iniciados com sucesso"

:: Wait for services to be ready
call :print_step "Aguardando servicos ficarem prontos..."
echo %YELLOW%Aguardando base de dados... (30 segundos)%RESET%
timeout /t 30 >nul

:: Check container status
call :print_step "Verificando status dos containers..."
docker-compose ps

:: Run Laravel setup
call :print_step "Configurando aplicacao Laravel..."

:: Install dependencies
call :print_info "Instalando dependencias..."
docker-compose exec -T app composer install --no-dev --optimize-autoloader
if %errorLevel% neq 0 (
    call :print_warning "Falha ao instalar dependencias, mas continuando..."
)

:: Generate app key
call :print_info "Gerando chave da aplicacao..."
docker-compose exec -T app php artisan key:generate --force

:: Run migrations
call :print_info "Executando migracoes da base de dados..."
docker-compose exec -T app php artisan migrate --force
if %errorLevel% neq 0 (
    call :print_error "Falha nas migracoes da base de dados!"
    goto :show_logs
)

:: Cache configuration
call :print_info "Otimizando configuracao..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

:: Create storage link
docker-compose exec -T app php artisan storage:link

call :print_success "Configuracao Laravel concluida"

:: System verification
call :print_step "Verificando sistema..."

:: Check web server
call :print_info "Testando servidor web..."
timeout /t 5 >nul
curl -s -o nul -w "%%{http_code}" http://localhost | findstr "200" >nul
if %errorLevel% equ 0 (
    call :print_success "Servidor web respondendo (HTTP 200)"
) else (
    call :print_warning "Servidor web pode nao estar respondendo corretamente"
)

:: Check database connection
call :print_info "Testando conexao com base de dados..."
docker-compose exec -T app php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';" 2>nul | findstr "Database OK" >nul
if %errorLevel% equ 0 (
    call :print_success "Conexao com base de dados OK"
) else (
    call :print_warning "Problema na conexao com base de dados"
)

:: Check Redis connection
call :print_info "Testando conexao com Redis..."
docker-compose exec -T app php artisan tinker --execute="Redis::ping(); echo 'Redis OK';" 2>nul | findstr "Redis OK" >nul
if %errorLevel% equ 0 (
    call :print_success "Conexao com Redis OK"
) else (
    call :print_warning "Problema na conexao com Redis"
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
echo   - Ver logs: docker-compose logs -f
echo   - Parar sistema: docker-compose down
echo   - Reiniciar: docker-compose restart
echo.

:: Ask if user wants to open web interface
set /p open_web="Abrir interface web no navegador? (s/n): "
if /i "!open_web!"=="s" (
    start http://localhost
)

:: Ask if user wants to see logs
set /p show_logs_choice="Ver logs em tempo real? (s/n): "
if /i "!show_logs_choice!"=="s" (
    goto :show_logs
)

goto :end

:show_logs
echo.
call :print_info "Mostrando logs em tempo real (Ctrl+C para sair)..."
echo.
docker-compose logs -f

:end
echo.
call :print_success "Script concluido!"
pause
exit /b 0
