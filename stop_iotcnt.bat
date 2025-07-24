@echo off
setlocal enabledelayedexpansion

:: IOTCNT System Stop Script
:: This script safely stops the IOTCNT irrigation system

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

echo %WHITE%Este script ira parar todos os servicos do IOTCNT.%RESET%
echo.
echo %YELLOW%AVISO: Isto ira interromper:%RESET%
echo   - Todos os agendamentos de irrigacao
echo   - Interface web
echo   - API para ESP32
echo   - Bot Telegram
echo   - Base de dados e cache
echo.

set /p confirm="Tem certeza que deseja parar o sistema? (s/n): "
if /i not "!confirm!"=="s" (
    echo %GREEN%Operacao cancelada.%RESET%
    pause
    exit /b 0
)

echo.
echo %BLUE%[PASSO]%RESET% Parando containers Docker...

:: Stop all containers gracefully
docker-compose down

if %errorLevel% equ 0 (
    echo %GREEN%[SUCESSO]%RESET% Sistema IOTCNT parado com sucesso!
    echo.
    echo %WHITE%Para reiniciar o sistema:%RESET%
    echo   - Execute: start_iotcnt.bat
    echo   - Ou use: docker-compose up -d
) else (
    echo %RED%[ERRO]%RESET% Erro ao parar alguns containers.
    echo.
    echo %YELLOW%Tentando parada forcada...%RESET%
    docker-compose kill
    docker-compose rm -f

    if !errorLevel! equ 0 (
        echo %GREEN%[SUCESSO]%RESET% Sistema parado forcadamente.
    ) else (
        echo %RED%[ERRO]%RESET% Falha ao parar sistema. Verifique manualmente.
    )
)

echo.
echo %WHITE%Status final dos containers:%RESET%
docker-compose ps

echo.
pause
exit /b 0
