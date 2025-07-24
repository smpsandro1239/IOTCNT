@echo off
setlocal enabledelayedexpansion

:: IOTCNT Main Menu Script
:: Central hub for all IOTCNT system operations

title IOTCNT - Sistema de Irrigacao IoT

:: Colors for output
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "CYAN=[96m"
set "WHITE=[97m"
set "MAGENTA=[95m"
set "RESET=[0m"

:main_menu
cls
echo %CYAN%
echo  ██╗ ██████╗ ████████╗ ██████╗███╗   ██╗████████╗
echo  ██║██╔═══██╗╚══██╔══╝██╔════╝████╗  ██║╚══██╔══╝
echo  ██║██║   ██║   ██║   ██║     ██╔██╗ ██║   ██║
echo  ██║██║   ██║   ██║   ██║     ██║╚██╗██║   ██║
echo  ██║╚██████╔╝   ██║   ╚██████╗██║ ╚████║   ██║
echo  ╚═╝ ╚═════╝    ╚═╝    ╚═════╝╚═╝  ╚═══╝   ╚═╝
echo.
echo           Sistema de Irrigacao IoT
echo  ========================================
echo %RESET%

:: Check system status
call :check_system_status

echo %WHITE%Menu Principal:%RESET%
echo.
echo %GREEN% 1.%RESET% 🚀 Iniciar Sistema Completo
echo %BLUE% 2.%RESET% 🔧 Verificar e Manter Sistema
echo %YELLOW% 3.%RESET% 📊 Status dos Servicos
echo %MAGENTA% 4.%RESET% 📋 Ver Logs do Sistema
echo %CYAN% 5.%RESET% 🌐 Abrir Interface Web
echo %WHITE% 6.%RESET% 📖 Documentacao e Ajuda
echo %RED% 7.%RESET% 🛑 Parar Sistema
echo %WHITE% 8.%RESET% ⚙️  Configuracao Avancada
echo %YELLOW% 9.%RESET% 🔄 Reiniciar Servicos
echo %WHITE% 0.%RESET% ❌ Sair
echo.

set /p choice="Digite sua opcao (0-9): "

if "%choice%"=="1" goto :start_system
if "%choice%"=="2" goto :check_system
if "%choice%"=="3" goto :system_status
if "%choice%"=="4" goto :view_logs
if "%choice%"=="5" goto :open_web
if "%choice%"=="6" goto :help
if "%choice%"=="7" goto :stop_system
if "%choice%"=="8" goto :advanced_config
if "%choice%"=="9" goto :restart_system
if "%choice%"=="0" goto :exit

echo %RED%Opcao invalida!%RESET%
timeout /t 2 >nul
goto :main_menu

:check_system_status
:: Quick system status check
docker-compose ps >nul 2>&1
if %errorLevel% equ 0 (
    for /f %%i in ('docker-compose ps --services') do (
        docker-compose ps %%i | findstr "Up" >nul
        if !errorLevel! equ 0 (
            set system_running=true
        )
    )
)

if defined system_running (
    echo %GREEN%Status: Sistema ATIVO%RESET%
    curl -s -o nul -w "%%{http_code}" http://localhost 2>nul | findstr "200" >nul
    if !errorLevel! equ 0 (
        echo %GREEN%Web: ONLINE%RESET% - http://localhost
    ) else (
        echo %YELLOW%Web: CARREGANDO...%RESET%
    )
) else (
    echo %RED%Status: Sistema PARADO%RESET%
)
echo.
goto :eof

:start_system
cls
echo %GREEN%Iniciando Sistema IOTCNT...%RESET%
echo.
call start_iotcnt.bat
pause
goto :main_menu

:check_system
cls
echo %BLUE%Verificacao e Manutencao do Sistema...%RESET%
echo.
call check_iotcnt.bat
goto :main_menu

:system_status
cls
echo %YELLOW%Status dos Servicos...%RESET%
echo.
echo %WHITE%Containers Docker:%RESET%
docker-compose ps
echo.
echo %WHITE%Uso de Recursos:%RESET%
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}" 2>nul
echo.
echo %WHITE%Portas em Uso:%RESET%
netstat -an | findstr ":80\|:3306\|:6379" 2>nul
echo.
pause
goto :main_menu

:view_logs
cls
echo %MAGENTA%Logs do Sistema...%RESET%
echo.
echo %WHITE%Escolha o tipo de log:%RESET%
echo 1. Todos os logs (tempo real)
echo 2. Aplicacao Laravel
echo 3. Servidor Web (Nginx)
echo 4. Base de Dados
echo 5. Ultimos 50 logs
echo 6. Apenas erros
echo.
set /p log_choice="Digite sua opcao (1-6): "

if "%log_choice%"=="1" docker-compose logs -f
if "%log_choice%"=="2" docker-compose logs -f app
if "%log_choice%"=="3" docker-compose logs -f webserver
if "%log_choice%"=="4" docker-compose logs -f database
if "%log_choice%"=="5" docker-compose logs --tail=50
if "%log_choice%"=="6" docker-compose logs | findstr -i "error\|exception\|fail"

pause
goto :main_menu

:open_web
cls
echo %CYAN%Abrindo Interface Web...%RESET%
echo.

:: Check if system is running
curl -s -o nul -w "%%{http_code}" http://localhost 2>nul | findstr "200" >nul
if %errorLevel% equ 0 (
    echo %GREEN%Sistema online! Abrindo navegador...%RESET%
    start http://localhost
    echo.
    echo %WHITE%Interfaces disponiveis:%RESET%
    echo   🌐 Principal: http://localhost
    echo   🔧 Admin: http://localhost/admin/dashboard
    echo   📊 Logs: http://localhost/admin/logs
    echo   📱 Telegram: http://localhost/admin/telegram-users
) else (
    echo %RED%Sistema nao esta respondendo!%RESET%
    echo %YELLOW%Deseja iniciar o sistema? (s/n):%RESET%
    set /p start_choice=""
    if /i "!start_choice!"=="s" goto :start_system
)

pause
goto :main_menu

:help
cls
echo %WHITE%
echo  ========================================
echo           DOCUMENTACAO IOTCNT
echo  ========================================
echo %RESET%
echo.
echo %CYAN%🌱 Sobre o IOTCNT:%RESET%
echo   Sistema completo de automacao de irrigacao
echo   Controla ate 5 valvulas independentes
echo   Interface web + Bot Telegram + ESP32
echo.
echo %GREEN%🚀 Primeiros Passos:%RESET%
echo   1. Iniciar sistema (opcao 1)
echo   2. Acessar http://localhost
echo   3. Criar usuario administrador
echo   4. Configurar valvulas e agendamentos
echo   5. Configurar ESP32 e Telegram
echo.
echo %BLUE%🔧 Configuracao ESP32:%RESET%
echo   - Editar: esp32_irrigation_controller/config.h
echo   - Configurar WiFi e API endpoint
echo   - Upload via PlatformIO: pio run --target upload
echo.
echo %MAGENTA%📱 Configuracao Telegram:%RESET%
echo   - Criar bot com @BotFather
echo   - Configurar token no .env
echo   - Configurar webhook via interface web
echo.
echo %YELLOW%⚠️  Solucao de Problemas:%RESET%
echo   - Sistema nao inicia: Verificar Docker
echo   - Web nao carrega: Aguardar 30s apos inicio
echo   - ESP32 nao conecta: Verificar config.h
echo   - Telegram nao funciona: Verificar token e webhook
echo.
echo %WHITE%📁 Arquivos Importantes:%RESET%
echo   - .env: Configuracoes principais
echo   - docker-compose.yml: Servicos Docker
echo   - esp32_irrigation_controller/: Firmware ESP32
echo   - README.md: Documentacao completa
echo.
echo %CYAN%🆘 Suporte:%RESET%
echo   - Documentacao: README.md
echo   - Logs: Opcao 4 do menu
echo   - Verificacao: Opcao 2 do menu
echo.
pause
goto :main_menu

:stop_system
cls
echo %RED%Parando Sistema IOTCNT...%RESET%
echo.
call stop_iotcnt.bat
pause
goto :main_menu

:advanced_config
cls
echo %WHITE%
echo  ========================================
echo         CONFIGURACAO AVANCADA
echo  ========================================
echo %RESET%
echo.
echo %YELLOW%1.%RESET% Editar arquivo .env
echo %YELLOW%2.%RESET% Backup da base de dados
echo %YELLOW%3.%RESET% Restaurar backup
echo %YELLOW%4.%RESET% Limpar dados do sistema
echo %YELLOW%5.%RESET% Recriar containers
echo %YELLOW%6.%RESET% Ver configuracao Docker
echo %YELLOW%7.%RESET% Executar comando personalizado
echo %YELLOW%0.%RESET% Voltar ao menu principal
echo.

set /p adv_choice="Digite sua opcao (0-7): "

if "%adv_choice%"=="1" goto :edit_env
if "%adv_choice%"=="2" goto :backup_db
if "%adv_choice%"=="3" goto :restore_db
if "%adv_choice%"=="4" goto :clean_data
if "%adv_choice%"=="5" goto :recreate_containers
if "%adv_choice%"=="6" goto :show_docker_config
if "%adv_choice%"=="7" goto :custom_command
if "%adv_choice%"=="0" goto :main_menu

echo %RED%Opcao invalida!%RESET%
timeout /t 2 >nul
goto :advanced_config

:edit_env
echo %YELLOW%Abrindo arquivo .env para edicao...%RESET%
if exist ".env" (
    notepad .env
) else (
    echo %RED%Arquivo .env nao encontrado!%RESET%
)
pause
goto :advanced_config

:backup_db
echo %BLUE%Criando backup da base de dados...%RESET%
set backup_date=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set backup_date=%backup_date: =0%
set backup_file=backup_iotcnt_%backup_date%.sql

docker-compose exec -T database mysqldump -u root -proot_password_here iotcnt > %backup_file%
if %errorLevel% equ 0 (
    echo %GREEN%Backup criado: %backup_file%%RESET%
) else (
    echo %RED%Falha ao criar backup%RESET%
)
pause
goto :advanced_config

:restore_db
echo %YELLOW%Restaurar backup da base de dados%RESET%
echo.
dir *.sql 2>nul
echo.
set /p backup_file="Digite o nome do arquivo de backup: "
if exist "%backup_file%" (
    echo %YELLOW%AVISO: Isto ira substituir todos os dados atuais!%RESET%
    set /p confirm="Continuar? (s/n): "
    if /i "!confirm!"=="s" (
        docker-compose exec -T database mysql -u root -proot_password_here iotcnt < %backup_file%
        echo %GREEN%Backup restaurado!%RESET%
    )
) else (
    echo %RED%Arquivo nao encontrado!%RESET%
)
pause
goto :advanced_config

:clean_data
echo %RED%AVISO: Isto ira remover TODOS os dados do sistema!%RESET%
echo %YELLOW%Incluindo: usuarios, valvulas, agendamentos, logs%RESET%
echo.
set /p confirm="Tem CERTEZA ABSOLUTA? Digite 'CONFIRMAR': "
if "!confirm!"=="CONFIRMAR" (
    docker-compose down -v
    docker volume rm iotcnt_mysql_data iotcnt_redis_data 2>nul
    echo %GREEN%Dados limpos! Reinicie o sistema.%RESET%
) else (
    echo %GREEN%Operacao cancelada.%RESET%
)
pause
goto :advanced_config

:recreate_containers
echo %YELLOW%Recriando todos os containers...%RESET%
set /p confirm="Continuar? (s/n): "
if /i "!confirm!"=="s" (
    docker-compose down
    docker-compose build --no-cache
    docker-compose up -d
    echo %GREEN%Containers recriados!%RESET%
)
pause
goto :advanced_config

:show_docker_config
echo %WHITE%Configuracao Docker:%RESET%
echo.
type docker-compose.yml
echo.
pause
goto :advanced_config

:custom_command
echo %WHITE%Executar comando personalizado no container da aplicacao:%RESET%
echo %YELLOW%Exemplo: php artisan migrate%RESET%
echo.
set /p custom_cmd="Digite o comando: "
if not "!custom_cmd!"=="" (
    docker-compose exec app !custom_cmd!
)
pause
goto :advanced_config

:restart_system
cls
echo %YELLOW%Reiniciando Sistema IOTCNT...%RESET%
echo.
set /p confirm="Confirmar reinicio? (s/n): "
if /i "!confirm!"=="s" (
    docker-compose restart
    echo %GREEN%Sistema reiniciado!%RESET%
    echo %YELLOW%Aguardando servicos ficarem prontos...%RESET%
    timeout /t 15 >nul
) else (
    echo %GREEN%Operacao cancelada.%RESET%
)
pause
goto :main_menu

:exit
cls
echo %CYAN%
echo  Obrigado por usar o IOTCNT!
echo  Sistema de Irrigacao IoT
echo
echo  🌱 Mantenha suas plantas felizes! 💧
echo %RESET%
timeout /t 3 >nul
exit /b 0
