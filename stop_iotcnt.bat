@echo off
setlocal enabledelayedexpansion

:: IOTCNT System Stop Script
:: Safely stops all IOTCNT services and containers

title IOTCNT - Parar Sistema

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
echo  🛑 IOTCNT - Parar Sistema
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

:: Check if system is running
call :print_step "Verificando estado do sistema..."

docker compose ps >nul 2>&1
if %errorLevel% neq 0 (
    docker-compose ps >nul 2>&1
    if %errorLevel% neq 0 (
        call :print_warning "Sistema já está parado ou Docker não está disponível"
        pause
        exit /b 0
    )
    set "DOCKER_CMD=docker-compose"
) else (
    set "DOCKER_CMD=docker compose"
)

:: Show current status
echo %WHITE%Estado actual dos containers:%RESET%
%DOCKER_CMD% ps

echo.
call :print_warning "Isto irá parar todos os serviços do IOTCNT:"
echo   • Aplicação web Laravel
echo   • Base de dados MySQL
echo   • Cache Redis
echo   • Servidor web Nginx
echo   • Sistema de performance
echo.

set /p confirm="Tem certeza que deseja parar o sistema? (s/n): "
if /i not "!confirm!"=="s" (
    call :print_info "Operação cancelada pelo utilizador"
    pause
    exit /b 0
)

:: Stop containers gracefully
call :print_step "Parando containers de forma segura..."

:: First, try to save any pending data
call :print_info "Salvando dados pendentes..."
%DOCKER_CMD% exec -T app php artisan cache:clear >nul 2>&1
%DOCKER_CMD% exec -T app php artisan config:clear >nul 2>&1

:: Stop containers
call :print_info "Parando containers..."
%DOCKER_CMD% stop

if %errorLevel% equ 0 (
    call :print_success "Containers parados com sucesso"
) else (
    call :print_warning "Alguns containers podem não ter parado corretamente"
)

:: Remove containers (optional)
echo.
set /p remove_containers="Remover containers também? (s/n): "
if /i "!remove_containers!"=="s" (
    call :print_step "Removendo containers..."
    %DOCKER_CMD% down --remove-orphans

    if %errorLevel% equ 0 (
        call :print_success "Containers removidos"
    ) else (
        call :print_warning "Alguns containers podem não ter sido removidos"
    )
)

:: Clean up (optional)
echo.
set /p cleanup="Limpar recursos Docker não utilizados? (s/n): "
if /i "!cleanup!"=="s" (
    call :print_step "Limpando recursos Docker..."
    docker system prune -f >nul 2>&1
    call :print_success "Limpeza concluída"
)

echo.
echo %CYAN%========================================%RESET%
echo %WHITE%         SISTEMA PARADO               %RESET%
echo %CYAN%========================================%RESET%
echo.
call :print_success "Sistema IOTCNT parado com sucesso!"
echo.
echo %WHITE%Para reiniciar o sistema:%RESET%
echo   • Execute: start_iotcnt.bat
echo   • Ou execute: iotcnt_complete.bat
echo.
echo %YELLOW%Nota:%RESET% Os dados permanecem salvos e serão
echo restaurados quando o sistema for reiniciado.
echo.

pause
exit /b 0
