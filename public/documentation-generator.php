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
