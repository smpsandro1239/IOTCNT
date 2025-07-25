@echo off
setlocal enabledelayedexpansion

:: IOTCNT Quick Fix Script
:: This script fixes common issues with the IOTCNT system

title IOTCNT - Correcao Rapida

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
echo  🔧 IOTCNT - Correcao Rapida
echo  ========================================
echo %RESET%

echo %WHITE%Este script corrige problemas comuns do IOTCNT%RESET%
echo.

:: Check if running in correct directory
if not exist "docker-compose.yml" (
    echo %RED%[ERRO]%RESET% Arquivo docker-compose.yml nao encontrado!
    echo %YELLOW%Execute este script no diretorio raiz do IOTCNT%RESET%
    pause
    exit /b 1
)

:: Fix 1: Copy configuration files if missing
echo %BLUE%[PASSO 1]%RESET% Verificando arquivos de configuracao...

if not exist ".env" (
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        echo %GREEN%[OK]%RESET% Arquivo .env criado
    ) else (
        echo %RED%[ERRO]%RESET% Arquivo .env.example nao encontrado!
    )
) else (
    echo %GREEN%[OK]%RESET% Arquivo .env existe
)

if not exist "docker\nginx\conf.d\app.conf" (
    if exist "docker\nginx\conf.d\app.example.conf" (
        copy "docker\nginx\conf.d\app.example.conf" "docker\nginx\conf.d\app.conf" >nul
        echo %GREEN%[OK]%RESET% Configuracao Nginx criada
    )
) else (
    echo %GREEN%[OK]%RESET% Configuracao Nginx existe
)

if not exist "docker\mysql\my.cnf" (
    if exist "docker\mysql\my.example.cnf" (
        copy "docker\mysql\my.example.cnf" "docker\mysql\my.cnf" >nul
        echo %GREEN%[OK]%RESET% Configuracao MySQL criada
    )
) else (
    echo %GREEN%[OK]%RESET% Configuracao MySQL existe
)

if not exist "docker\redis\redis.conf" (
    if exist "docker\redis\redis.example.conf" (
        copy "docker\redis\redis.example.conf" "docker\redis\redis.conf" >nul
        echo %GREEN%[OK]%RESET% Configuracao Redis criada
    )
) else (
    echo %GREEN%[OK]%RESET% Configuracao Redis existe
)

:: Fix 2: Stop any running containers
echo.
echo %BLUE%[PASSO 2]%RESET% Parando containers existentes...
docker compose down --remove-orphans >nul 2>&1
if %errorLevel% neq 0 (
    docker-compose down --remove-orphans >nul 2>&1
)
echo %GREEN%[OK]%RESET% Containers parados

:: Fix 3: Clean Docker system
echo.
echo %BLUE%[PASSO 3]%RESET% Limpando sistema Docker...
docker system prune -f >nul 2>&1
echo %GREEN%[OK]%RESET% Sistema Docker limpo

:: Fix 4: Remove problematic volumes
echo.
echo %BLUE%[PASSO 4]%RESET% Removendo volumes problematicos...
docker volume rm iotcnt_mysql_data >nul 2>&1
docker volume rm iotcnt_redis_data >nul 2>&1
echo %GREEN%[OK]%RESET% Volumes removidos

:: Fix 5: Recreate docker-compose.yml if corrupted
echo.
echo %BLUE%[PASSO 5]%RESET% Verificando docker-compose.yml...
docker compose config >nul 2>&1
if %errorLevel% neq 0 (
    echo %YELLOW%[AVISO]%RESET% docker-compose.yml tem problemas, tentando corrigir...
    if exist "docker-compose.example.yml" (
        copy "docker-compose.example.yml" "docker-compose.yml" >nul
        echo %GREEN%[OK]%RESET% docker-compose.yml corrigido
    ) else (
        echo %RED%[ERRO]%RESET% Nao foi possivel corrigir docker-compose.yml
    )
) else (
    echo %GREEN%[OK]%RESET% docker-compose.yml esta correto
)

:: Fix 6: Create necessary directories
echo.
echo %BLUE%[PASSO 6]%RESET% Criando diretorios necessarios...
if not exist "storage\app\public\uploads" mkdir "storage\app\public\uploads" >nul 2>&1
if not exist "backups" mkdir "backups" >nul 2>&1
if not exist "docker\nginx\ssl" mkdir "docker\nginx\ssl" >nul 2>&1
echo %GREEN%[OK]%RESET% Diretorios criados

:: Fix 7: Set correct permissions (Windows equivalent)
echo.
echo %BLUE%[PASSO 7]%RESET% Configurando permissoes...
attrib -r "storage\*" /s >nul 2>&1
attrib -r "bootstrap\cache\*" /s >nul 2>&1
echo %GREEN%[OK]%RESET% Permissoes configuradas

echo.
echo %GREEN%========================================%RESET%
echo %WHITE%           CORRECAO CONCLUIDA          %RESET%
echo %GREEN%========================================%RESET%
echo.
echo %GREEN%[SUCESSO]%RESET% Problemas comuns corrigidos!
echo.
echo %WHITE%Proximos passos:%RESET%
echo   1. Execute: start_iotcnt.bat
echo   2. Ou execute: iotcnt.bat
echo.
echo %YELLOW%Se ainda houver problemas:%RESET%
echo   - Verifique se Docker Desktop esta executando
echo   - Configure o arquivo .env com suas credenciais
echo   - Execute como Administrador
echo.

pause
exit /b 0
