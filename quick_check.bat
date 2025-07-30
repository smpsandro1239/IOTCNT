@echo off
setlocal enabledelayedexpansion

:: IOTCNT Quick System Check
:: Fast verification of system status and health

title IOTCNT - Verificação Rápida

:: Colors for output
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "CYAN=[96m"
set "WHITE=[97m"
set "RESET=[0m"

echo %CYAN%
echo  ⚡ IOTCNT - Verificação Rápida
echo  ============================
echo %RESET%

:: Detect Docker Compose command
docker compose version >nul 2>&1
if %errorLevel% equ 0 (
    set "DOCKER_CMD=docker compose"
) else (
    docker-compose --version >nul 2>&1
    if %errorLevel% equ 0 (
        set "DOCKER_CMD=docker-compose"
    ) else (
        echo %RED%❌ Docker não encontrado%RESET%
        pause
        exit /b 1
    )
)

echo %WHITE%Verificação Rápida do Sistema IOTCNT%RESET%
echo.

:: Check 1: Docker
echo %BLUE%🐳 Docker:%RESET%
docker --version | findstr "version" >nul
if %errorLevel% equ 0 (
    echo   %GREEN%✅ Docker instalado%RESET%
) else (
    echo   %RED%❌ Docker não encontrado%RESET%
)

:: Check 2: Containers
echo %BLUE%📦 Containers:%RESET%
%DOCKER_CMD% ps --format "table {{.Names}}\t{{.Status}}" 2>nul | findstr "Up" >nul
if %errorLevel% equ 0 (
    echo   %GREEN%✅ Containers em execução%RESET%
    for /f "skip=1 tokens=1,2" %%a in ('%DOCKER_CMD% ps --format "{{.Names}} {{.Status}}" 2^>nul') do (
        echo %%b | findstr "Up" >nul
        if !errorLevel! equ 0 (
            echo     %GREEN%• %%a: ATIVO%RESET%
        ) else (
            echo     %RED%• %%a: INATIVO%RESET%
        )
    )
) else (
    echo   %RED%❌ Nenhum container ativo%RESET%
)

:: Check 3: Web Server
echo %BLUE%🌐 Servidor Web:%RESET%
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost' -TimeoutSec 5; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 (
    echo   %GREEN%✅ Web online: http://localhost%RESET%
) else (
    echo   %RED%❌ Web offline%RESET%
)

:: Check 4: Database
echo %BLUE%🗄️  Base de Dados:%RESET%
%DOCKER_CMD% exec -T app php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB_OK'; } catch(Exception \$e) { echo 'DB_ERROR'; }" 2>nul | findstr "DB_OK" >nul
if %errorLevel% equ 0 (
    echo   %GREEN%✅ Base de dados conectada%RESET%
) else (
    echo   %RED%❌ Base de dados desconectada%RESET%
)

:: Check 5: Redis Cache
echo %BLUE%🔧 Cache Redis:%RESET%
%DOCKER_CMD% exec -T app php artisan tinker --execute="try { Redis::ping(); echo 'REDIS_OK'; } catch(Exception \$e) { echo 'REDIS_ERROR'; }" 2>nul | findstr "REDIS_OK" >nul
if %errorLevel% equ 0 (
    echo   %GREEN%✅ Redis conectado%RESET%
) else (
    echo   %RED%❌ Redis desconectado%RESET%
)

:: Check 6: Performance System (NEW!)
echo %BLUE%📊 Sistema de Performance:%RESET%
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost/admin/performance' -TimeoutSec 5; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 (
    echo   %GREEN%✅ Performance disponível: /admin/performance%RESET%
) else (
    echo   %RED%❌ Performance indisponível%RESET%
)

:: Check 7: Admin Panel
echo %BLUE%🔧 Painel Admin:%RESET%
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost/admin/dashboard' -TimeoutSec 5; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 (
    echo   %GREEN%✅ Admin disponível: /admin/dashboard%RESET%
) else (
    echo   %RED%❌ Admin indisponível%RESET%
)

:: Check 8: API Endpoints
echo %BLUE%🔌 API ESP32:%RESET%
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost/api/ping' -TimeoutSec 5; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 (
    echo   %GREEN%✅ API ESP32 disponível%RESET%
) else (
    echo   %RED%❌ API ESP32 indisponível%RESET%
)

:: Check 9: Configuration Files
echo %BLUE%⚙️  Configuração:%RESET%
if exist ".env" (
    echo   %GREEN%✅ Ficheiro .env existe%RESET%
) else (
    echo   %RED%❌ Ficheiro .env não encontrado%RESET%
)

if exist "docker-compose.yml" (
    echo   %GREEN%✅ docker-compose.yml existe%RESET%
) else (
    echo   %RED%❌ docker-compose.yml não encontrado%RESET%
)

:: Check 10: Disk Space
echo %BLUE%💾 Espaço em Disco:%RESET%
for /f "tokens=3" %%a in ('dir /-c 2^>nul ^| findstr "bytes free"') do (
    set free_space=%%a
)
if defined free_space (
    echo   %GREEN%✅ Espaço livre: !free_space! bytes%RESET%
) else (
    echo   %YELLOW%⚠️  Não foi possível verificar espaço%RESET%
)

:: Summary
echo.
echo %CYAN%========================================%RESET%

:: Count successful checks
set success_count=0
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost' -TimeoutSec 3; if ($response.StatusCode -eq 200) { exit 0 } else { exit 1 } } catch { exit 1 }" >nul 2>&1
if %errorLevel% equ 0 set /a success_count+=1

%DOCKER_CMD% ps --format "{{.Names}}" 2>nul | findstr "app" >nul
if %errorLevel% equ 0 set /a success_count+=1

%DOCKER_CMD% exec -T app php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'ERROR'; }" 2>nul | findstr "OK" >nul
if %errorLevel% equ 0 set /a success_count+=1

if exist ".env" set /a success_count+=1

if %success_count% geq 3 (
    echo %GREEN%🎉 Sistema funcionando bem! (%success_count%/4 verificações OK)%RESET%
    echo.
    echo %WHITE%Interfaces disponíveis:%RESET%
    echo   🌐 Principal: http://localhost
    echo   🔧 Admin: http://localhost/admin/dashboard
    echo   📊 Performance: http://localhost/admin/performance
) else (
    echo %RED%⚠️  Sistema com problemas (%success_count%/4 verificações OK)%RESET%
    echo.
    echo %YELLOW%Sugestões:%RESET%
    echo   • Execute: start_iotcnt.bat para iniciar
    echo   • Execute: iotcnt_complete.bat para gestão completa
    echo   • Verifique se Docker Desktop está em execução
)

echo %CYAN%========================================%RESET%
echo.

set /p action="Abrir interface principal? (s/n): "
if /i "!action!"=="s" (
    start http://localhost
)

pause
exit /b 0
