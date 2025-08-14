<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Simular base de dados em ficheiro JSON
$dataFile = __DIR__ . '/esp32-data.json';

// Inicializar dados se não existirem
if (!file_exists($dataFile)) {
  $initialData = [
    'valves' => [
      ['id' => 1, 'name' => 'Condensador 1', 'status' => 'active', 'temperature' => 18, 'last_cycle' => '2025-08-14 06:00:00'],
      ['id' => 2, 'name' => 'Condensador 2', 'status' => 'active', 'temperature' => 17, 'last_cycle' => '2025-08-14 06:00:00'],
      ['id' => 3, 'name' => 'Condensador 3', 'status' => 'active', 'temperature' => 19, 'last_cycle' => '2025-08-14 06:00:00'],
      ['id' => 4, 'name' => 'Condensador 4', 'status' => 'maintenance', 'temperature' => 20, 'last_cycle' => '2025-08-14 02:00:00'],
      ['id' => 5, 'name' => 'Condensador 5', 'status' => 'active', 'temperature' => 18, 'last_cycle' => '2025-08-14 06:00:00']
    ],
    'system' => [
      'status' => 'online',
      'uptime' => '99.9%',
      'efficiency' => '95%',
      'last_update' => date('Y-m-d H:i:s')
    ],
    'logs' => []
  ];
  file_put_contents($dataFile, json_encode($initialData, JSON_PRETTY_PRINT));
}

// Carregar dados
$data = json_decode(file_get_contents($dataFile), true);

// Função para salvar dados
function saveData($data, $dataFile)
{
  $data['system']['last_update'] = date('Y-m-d H:i:s');
  file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

// Função para adicionar log
function addLog($data, $message, $type = 'info')
{
  $data['logs'][] = [
    'timestamp' => date('Y-m-d H:i:s'),
    'message' => $message,
    'type' => $type
  ];

  // Manter apenas os últimos 50 logs
  if (count($data['logs']) > 50) {
    $data['logs'] = array_slice($data['logs'], -50);
  }

  return $data;
}

// Obter método HTTP
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$path = parse_url($path, PHP_URL_PATH);
$path = str_replace('/api-esp32.php', '', $path);

// Routing
switch ($method) {
  case 'GET':
    if ($path === '' || $path === '/') {
      // Status geral do sistema
      echo json_encode([
        'status' => 'success',
        'message' => 'IOTCNT ESP32 API Online',
        'system' => $data['system'],
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoints' => [
          'GET /' => 'Status do sistema',
          'GET /valves' => 'Lista de válvulas',
          'GET /valves/{id}' => 'Detalhes de uma válvula',
          'POST /valves/{id}/toggle' => 'Activar/desactivar válvula',
          'POST /valves/{id}/cycle' => 'Iniciar ciclo de limpeza',
          'GET /logs' => 'Logs do sistema',
          'POST /ping' => 'Ping do ESP32'
        ]
      ]);
    } elseif ($path === '/valves') {
      // Lista de válvulas
      echo json_encode([
        'status' => 'success',
        'data' => $data['valves'],
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    } elseif (preg_match('/\/valves\/(\d+)/', $path, $matches)) {
      // Detalhes de uma válvula específica
      $valveId = (int)$matches[1];
      $valve = null;

      foreach ($data['valves'] as $v) {
        if ($v['id'] === $valveId) {
          $valve = $v;
          break;
        }
      }

      if ($valve) {
        echo json_encode([
          'status' => 'success',
          'data' => $valve,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        http_response_code(404);
        echo json_encode([
          'status' => 'error',
          'message' => 'Válvula não encontrada',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
    } elseif ($path === '/logs') {
      // Logs do sistema
      echo json_encode([
        'status' => 'success',
        'data' => array_reverse($data['logs']), // Mais recentes primeiro
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    } else {
      http_response_code(404);
      echo json_encode([
        'status' => 'error',
        'message' => 'Endpoint não encontrado',
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    }
    break;

  case 'POST':
    $input = json_decode(file_get_contents('php://input'), true);

    if ($path === '/ping') {
      // Ping do ESP32
      $data = addLog($data, 'ESP32 ping recebido', 'info');
      saveData($data, $dataFile);

      echo json_encode([
        'status' => 'success',
        'message' => 'pong',
        'timestamp' => date('Y-m-d H:i:s'),
        'system_status' => $data['system']['status']
      ]);
    } elseif (preg_match('/\/valves\/(\d+)\/toggle/', $path, $matches)) {
      // Toggle válvula
      $valveId = (int)$matches[1];
      $valveFound = false;

      foreach ($data['valves'] as &$valve) {
        if ($valve['id'] === $valveId) {
          $oldStatus = $valve['status'];
          $valve['status'] = ($valve['status'] === 'active') ? 'inactive' : 'active';
          $valveFound = true;

          $data = addLog($data, "Válvula {$valve['name']} alterada de {$oldStatus} para {$valve['status']}", 'action');
          break;
        }
      }

      if ($valveFound) {
        saveData($data, $dataFile);
        echo json_encode([
          'status' => 'success',
          'message' => 'Estado da válvula alterado',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        http_response_code(404);
        echo json_encode([
          'status' => 'error',
          'message' => 'Válvula não encontrada',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
    } elseif (preg_match('/\/valves\/(\d+)\/cycle/', $path, $matches)) {
      // Iniciar ciclo de limpeza
      $valveId = (int)$matches[1];
      $valveFound = false;

      foreach ($data['valves'] as &$valve) {
        if ($valve['id'] === $valveId) {
          $valve['last_cycle'] = date('Y-m-d H:i:s');
          $valve['temperature'] = rand(16, 20); // Simular nova temperatura
          $valveFound = true;

          $data = addLog($data, "Ciclo de limpeza iniciado para {$valve['name']}", 'cycle');
          break;
        }
      }

      if ($valveFound) {
        saveData($data, $dataFile);
        echo json_encode([
          'status' => 'success',
          'message' => 'Ciclo de limpeza iniciado',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        http_response_code(404);
        echo json_encode([
          'status' => 'error',
          'message' => 'Válvula não encontrada',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
    } else {
      http_response_code(404);
      echo json_encode([
        'status' => 'error',
        'message' => 'Endpoint não encontrado',
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    }
    break;

  default:
    http_response_code(405);
    echo json_encode([
      'status' => 'error',
      'message' => 'Método não permitido',
      'timestamp' => date('Y-m-d H:i:s')
    ]);
    break;
}
