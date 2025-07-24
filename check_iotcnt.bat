@echo off
setlocal enabledelayedexpansion

:: IOTCNT System Check and Maintenance Script
:: This script performs comprehensive system checks and maintenance tasks

title IOTCNT - Verificacao e Manutencao do Sistema

:: Colors for output
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "CYAN=[96m"
set "WHITE=[97m"
set "RESET=[0m"

echo %CYAN%
echo  ==========================================
echo  🔧 IOTCNT - Verificacao do Sistema
echo  ==========================================
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
echo %BLUE%[VERIFICANDO]%RESET% %~1
goto :eof

:print_success
echo %GREEN%[OK]%RESET% %~1
goto :eof

:main

echo %WHITE%Escolha uma opcao:%RESET%
echo.
echo 1. Verificacao completa do sistema
echo 2. Status dos containers
echo 3. Logs do sistema
echo 4. Teste de conectividade
echo 5. Limpeza e manutencao
echo 6. Backup da base de dados
echo 7. Reiniciar servicos
echo 8. Parar sistema
echo 9. Informacoes do sistema
echo 0. Sair
echo.

set /p choice="Digite sua opcao (0-9): "

if "%choice%"=="1" goto :full_check
if "%choice%"=="2" goto :container_status
if "%choice%"=="3" goto :show_logs
if "%choice%"=="4" goto :connectivity_test
if "%choice%"=="5" goto :maintenance
if "%choice%"=="6" goto :backup
if "%choice%"=="7" goto :restart_services
if "%choice%"=="8" goto :stop_system
if "%choice%"=="9" goto :system_info
if "%choice%"=="0" goto :end

call :print_error "Opcao invalida!"
timeout /t 2 >nul
goto :main

:full_check
echo.
call :print_step "Iniciando verificacao completa do sistema..."
echo.

:: Check Docker
call :print_step "Docker e Docker Compose..."
docker --version >nul 2>&1
if %errorLevel% equ 0 (
    call :print_success "Docker instalado"
) else (
    call :print_error "Docker nao encontrado"
)

docker-compose --version >nul 2>&1
if %errorLevel% equ 0 (
    call :print_success "Docker Compose instalado"
) else (
    call :print_error "Docker Compose nao encontrado"
)

:: Check containers
call :print_step "Status dos containers..."
for /f "tokens=1,2" %%a in ('docker-compose ps --services') do (
    docker-compose ps %%a | findstr "Up" >nul
    if !errorLevel! equ 0 (
        call :print_success "Container %%a: Executando"
    ) else (
        call :print_error "Container %%a: Parado ou com problema"
    )
)

:: Check web server
call :print_step "Servidor web..."
curl -s -o nul -w "%%{http_code}" http://localhost 2>nul | findstr "200" >nul
if %errorLevel% equ 0 (
    call :print_success "Servidor web respondendo (HTTP 200)"
) else (
    call :print_warning "Servidor web nao esta respondendo"
)

:: Check API endpoints
call :print_step "Endpoints da API..."
curl -s -o nul -w "%%{http_code}" http://localhost/api/ping 2>nul | findstr "200" >nul
if %errorLevel% equ 0 (
    call :print_success "API ping endpoint OK"
) else (
    call :print_warning "API ping endpoint com problema"
)

:: Check database
call :print_step "Base de dados..."
docker-compose exec -T app php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB_OK'; } catch(Exception \$e) { echo 'DB_ERROR'; }" 2>nul | findstr "DB_OK" >nul
if %errorLevel% equ 0 (
    call :print_success "Conexao com base de dados OK"
) else (
    call :print_error "Problema na conexao com base de dados"
)

:: Check Redis
call :print_step "Cache Redis..."
docker-compose exec -T app php artisan tinker --execute="try { Redis::ping(); echo 'REDIS_OK'; } catch(Exception \$e) { echo 'REDIS_ERROR'; }" 2>nul | findstr "REDIS_OK" >nul
if %errorLevel% equ 0 (
    call :print_success "Conexao com Redis OK"
) else (
    call :print_error "Problema na conexao com Redis"
)

:: Check disk space
call :print_step "Espaco em disco..."
for /f "tokens=3" %%a in ('dir /-c ^| findstr "bytes free"') do (
    set free_space=%%a
)
call :print_info "Espaco livre em disco: !free_space! bytes"

echo.
call :print_success "Verificacao completa concluida!"
pause
goto :main

:container_status
echo.
call :print_step "Status detalhado dos containers..."
echo.
docker-compose ps
echo.
call :print_info "Uso de recursos:"
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}"
echo.
pause
goto :main

:show_logs
echo.
echo %WHITE%Escolha o tipo de log:%RESET%
echo 1. Todos os logs
echo 2. Aplicacao Laravel
echo 3. Nginx
echo 4. Base de dados
echo 5. Redis
echo 6. Logs de erro apenas
echo.
set /p log_choice="Digite sua opcao (1-6): "

if "%log_choice%"=="1" docker-compose logs -f
if "%log_choice%"=="2" docker-compose logs -f app
if "%log_choice%"=="3" docker-compose logs -f webserver
if "%log_choice%"=="4" docker-compose logs -f database
if "%log_choice%"=="5" docker-compose logs -f redis
if "%log_choice%"=="6" docker-compose logs -f | findstr -i "error\|exception\|fail"

goto :main

:connectivity_test
echo.
call :print_step "Testando conectividade..."
echo.

:: Test web server
call :print_info "Testando servidor web (localhost:80)..."
curl -I http://localhost 2>nul | findstr "HTTP" || call :print_error "Falha na conexao web"

:: Test database
call :print_info "Testando base de dados (localhost:3306)..."
telnet localhost 3306 2>nul | findstr "mysql" >nul || call :print_warning "Base de dados pode nao estar acessivel externamente"

:: Test Redis
call :print_info "Testando Redis (localhost:6379)..."
telnet localhost 6379 2>nul || call :print_warning "Redis pode nao estar acessivel externamente"

:: Test ESP32 API endpoints
call :print_info "Testando endpoints ESP32..."
curl -s http://localhost/api/ping | findstr "pong" >nul && call :print_success "API ping OK" || call :print_warning "API ping falhou"

echo.
pause
goto :main

:maintenance
echo.
call :print_step "Executando tarefas de manutencao..."
echo.

:: Clear Laravel caches
call :print_info "Limpando caches do Laravel..."
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan view:clear

:: Optimize Laravel
call :print_info "Otimizando Laravel..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

:: Clean old logs (optional)
set /p clean_logs="Limpar logs antigos da aplicacao? (s/n): "
if /i "!clean_logs!"=="s" (
    docker-compose exec -T app php artisan tinker --execute="DB::table('operation_logs')->where('logged_at', '<', now()->subDays(30))->delete(); echo 'Logs antigos removidos';"
)

:: Docker cleanup
call :print_info "Limpando recursos Docker nao utilizados..."
docker system prune -f

call :print_success "Manutencao concluida!"
pause
goto :main

:backup
echo.
call :print_step "Criando backup da base de dados..."

set backup_date=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set backup_date=%backup_date: =0%

set backup_file=backup_iotcnt_%backup_date%.sql

docker-compose exec -T database mysqldump -u root -proot_password_here iotcnt > %backup_file%

if %errorLevel% equ 0 (
    call :print_success "Backup criado: %backup_file%"
) else (
    call :print_error "Falha ao criar backup"
)

pause
goto :main

:restart_services
echo.
call :print_step "Reiniciando servicos..."

set /p confirm="Tem certeza que deseja reiniciar todos os servicos? (s/n): "
if /i "!confirm!"=="s" (
    docker-compose restart
    call :print_success "Servicos reiniciados!"
) else (
    call :print_info "Operacao cancelada"
)

pause
goto :main

:stop_system
echo.
call :print_warning "Isto ira parar todo o sistema IOTCNT!"
set /p confirm="Tem certeza? (s/n): "
if /i "!confirm!"=="s" (
    docker-compose down
    call :print_success "Sistema parado!"
) else (
    call :print_info "Operacao cancelada"
)

pause
goto :main

:system_info
echo.
call :print_step "Informacoes do sistema..."
echo.

echo %WHITE%Sistema Operacional:%RESET%
systeminfo | findstr /C:"OS Name" /C:"OS Version"
echo.

echo %WHITE%Docker:%RESET%
docker --version
docker-compose --version
echo.

echo %WHITE%Containers IOTCNT:%RESET%
docker-compose ps
echo.

echo %WHITE%Uso de Recursos:%RESET%
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}"
echo.

echo %WHITE%Portas em Uso:%RESET%
netstat -an | findstr ":80\|:3306\|:6379"
echo.

echo %WHITE%Espaco em Disco:%RESET%
dir /-c | findstr "bytes free"
echo.

pause
goto :main

:end
echo.
call :print_success "Obrigado por usar IOTCNT!"
exit /b 0
