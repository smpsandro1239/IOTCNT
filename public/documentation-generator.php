<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Função para gerar documentação da API
function generateApiDocumentation() {
    $endpoints = [
        [
            'method' => 'GET',
            'endpoint' => '/api.php',
            'description' => 'API principal para comunicação com ESP32',
            'parameters' => [
                'action' => 'Acção a executar (status, valves, control)',
                'valve_id' => 'ID da válvula (opcional)',
                'command' => 'Comando para controlo (opcional)'
            ],
            'responses' => [
                'success' => 'Operação executada com sucesso',
                'error' => 'Erro na execução da operação'
            ]
        ],
        [
            'method' => 'GET',
            'endpoint' => '/database-manager.php',
            'description' => 'Gestão da base de dados MySQL',
            'parameters' => [
                'action' => 'Acção (status, valves, logs, settings)',
                'filter' => 'Filtro para pesquisa (opcional)',
                'limit' => 'Limite de resultados (opcional)'
            ],
            'responses' => [
                'success' => 'Dados retornados com sucesso',
                'error' => 'Erro na consulta à base de dados'
            ]
        ],
        [
            'method' => 'GET',
            'endpoint' => '/backup-manager.php',
            'description' => 'Sistema de backup automático',
            'parameters' => [
                'action' => 'Acção (status, create, list, clean, auto)',
                'max_age' => 'Idade máxima para limpeza (opcional)'
            ],
            'responses' => [
                'success' => 'Backup executado com sucesso',
                'error' => 'Erro no sistema de backup'
            ]
        ],
        [
            'method' => 'GET',
            'endpoint' =>reports-mana
           'description' => 'Geração de relatórios automáticos',
            'parameters' => [
                'action' => 'Tipo de relatório (daily, weekly, monthly)',
                'format' => 'Formato de saída (json, pdf) - opcional'
            ],
            'responses' => [
                'success' => 'Relatório gerado com sucesso',
                'error' => 'Erro na geração do relatório'
            ]
        ],
        [
            'method' => 'GET',
            'endpoint' => '/email-manager.php',
            'description' => 'Sistema de notificações por email',
            'parameters' => [
                'action' => 'Acção (send_alert, send_report, test_email)',
                'email' => 'Email de destino (para teste)',
                'type' => 'Tipo de notificação'
            ],
            'responses' => [
                'success' => 'Email enviado com sucesso',
                'error' => 'Erro no envio do email'
            ]
        ]
    ];

    return $endpoints;
}

// Função para gerar documentação do sistema
function generateSystemDocumentation() {
    return [
        'overview' => [
            'name' => 'IOTCNT - Sistema de Condensadores',
            'version' => '2.0.0',
            'description' => 'Sistema IoT completo para gestão e monitorização de condensadores industriais',
            'company' => 'CNT - Continente',
            'last_updated' => date('Y-m-d H:i:s')
        ],
        'architecture' => [
            'frontend' => 'HTML5, CSS3, JavaScript Vanilla',
            'backend' => 'PHP 8.1, MySQL 8.0',
            'hardware' => 'ESP32-WROOM-32',
            'deployment' => 'Docker Compose',
            'monitoring' => 'Sistema próprio em tempo real'
        ],
        'features' => [
            'Controlo de 5 condensadores',
            'Monitorização em tempo real',
            'Sistema de alertas automáticos',
            'Relatórios inteligentes',
            'Backup automático',
            'Gráficos interactivos',
            'Notificações por email',
            'Interface responsiva',
            'API REST completa',
            'Documentação automática'
        ],
        'urls' => [
            'dashboard_admin' => 'http://localhost:8080/dashboard-admin.html',
            'dashboard_user' => 'http://localhost:8080/dashboard-user.html',
            'valve_control' => 'http://localhost:8080/valve-control.html',
            'monitoring' => 'http://localhost:8080/monitoring-dashboard.html',
            'reports' => 'http://localhost:8080/reports-dashboard.html',
            'charts' => 'http://localhost:8080/charts-dashboard.html',
            'backups' => 'http://localhost:8080/backup-admin.html',
            'emails' => 'http://localhost:8080/email-dashboard.html',
            'api_docs' => 'http://localhost:8080/api-docs.html'
        ]
    ];
}

// Função para gerar guia de instalação
function generateInstallationGuide() {
    return [
        'requirements' => [
            'Docker & Docker Compose',
            'Git',
            'Porta 8080 disponível',
            'Mínimo 2GB RAM',
            'Mínimo 5GB espaço em disco'
        ],
        'steps' => [
            '1. Clone o repositório: git clone https://github.com/smpsandro1239/IOTCNT.git',
            '2. Entre no directório: cd IOTCNT',
            '3. Copie o ficheiro de ambiente: cp .env.example .env',
            '4. Inicie os containers: docker-compose up -d',
            '5. Execute o setup: docker-compose exec app php artisan migrate --seed',
            '6. Aceda ao sistema: http://localhost:8080'
        ],
        'credentials' => [
            'Admin: admin@iotcnt.local / password',
            'User: user@iotcnt.local / password'
        ],
        'troubleshooting' => [
            'Porta 8080 ocupada: Altere a porta no docker-compose.yml',
            'Erro de permissões: Execute com sudo ou ajuste permissões',
            'MySQL não inicia: Verifique se a porta 3307 está livre',
            'Laravel erro 500: Execute php artisan config:clear'
        ]
    ];
}

// Função para gerar changelog
function generateChangelog() {
    return [
        'v2.0.0' => [
            'date' => '2025-08-15',
            'changes' => [
                'Sistema completo de backup automático',
                'Monitorização avançada em tempo real',
                'Relatórios automáticos inteligentes',
                'Gráficos interactivos com Chart.js',
                'Sistema de notificações por email',
                'Documentação automática',
                'Interface moderna e responsiva',
                'API REST completa'
            ]
        ],
        'v1.5.0' => [
            'date' => '2025-08-14',
            'changes' => [
                'Base de dados MySQL persistente',
                'Sistema de logs avançado',
                'Performance metrics',
                'Sistema de configurações',
                'Melhorias na interface'
            ]
        ],
        'v1.0.0' => [
            'date' => '2025-08-13',
            'changes' => [
                'Sistema base funcional',
                'Controlo de válvulas',
                'Dashboard admin e user',
                'API ESP32 básica',
                'Sistema de autenticação'
            ]
        ]
    ];
}

// Processar pedidos
$action = $_GET['action'] ?? 'overview';

try {
    switch ($action) {
        case 'overview':
            $system = generateSystemDocumentation();
            echo json_encode([
                'status' => 'success',
                'documentation' => $system,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;

        case 'api':
            $api = generateApiDocumentation();
            echo json_encode([
                'status' => 'success',
                'api_documentation' => $api,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;

        case 'installation':
            $installation = generateInstallationGuide();
            echo json_encode([
                'status' => 'success',
                'installation_guide' => $installation,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;

        case 'changelog':
            $changelog = generateChangelog();
            echo json_encode([
                'status' => 'success',
                'changelog' => $changelog,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;

        case 'full':
            $full = [
                'system' => generateSystemDocumentation(),
                'api' => generateApiDocumentation(),
                'installation' => generateInstallationGuide(),
                'changelog' => generateChangelog()
            ];
            echo json_encode([
                'status' => 'success',
                'full_documentation' => $full,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;

        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Acção não reconhecida',
                'available_actions' => ['overview', 'api', 'installation', 'changelog', 'full'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro interno: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
