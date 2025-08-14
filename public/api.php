<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Obter parâmetros
$action = $_GET['action'] ?? 'status';
$id = $_GET['id'] ?? null;

// Dados simulados
$valves = [
  ['id' => 1, 'name' => 'Condensador 1', 'status' => 'active', 'temperature' => 18],
  ['id' => 2, 'name' => 'Condensador 2', 'status' => 'active', 'temperature' => 17],
  ['id' => 3, 'name' => 'Condensador 3', 'status' => 'active', 'temperature' => 19],
  ['id' => 4, 'name' => 'Condensador 4', 'status' => 'maintenance', 'temperature' => 20],
  ['id' => 5, 'name' => 'Condensador 5', 'status' => 'active', 'temperature' => 18]
];

$system = [
  'status' => 'online',
  'uptime' => '99.9%',
  'efficiency' => '95%',
  'active_valves' => 4,
  'temperature_avg' => 18.4,
  'last_update' => date('Y-m-d H:i:s')
];

// Routing baseado em action
switch ($action) {
  case 'status':
    echo json_encode([
      'status' => 'success',
      'message' => 'IOTCNT ESP32 API Online',
      'system' => $system,
      'timestamp' => date('Y-m-d H:i:s')
    ]);
    break;

  case 'valves':
    echo json_encode([
      'status' => 'success',
      'data' => $valves,
      'count' => count($valves),
      'timestamp' => date('Y-m-d H:i:s')
    ]);
    break;

  case 'valve':
    if ($id) {
      $valve = null;
      foreach ($valves as $v) {
        if ($v['id'] == $id) {
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
        echo json_encode([
          'status' => 'error',
          'message' => 'Válvula não encontrada',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'ID da válvula é obrigatório',
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    }
    break;

  case 'ping':
    echo json_encode([
      'status' => 'success',
      'message' => 'pong',
      'timestamp' => date('Y-m-d H:i:s'),
      'system_status' => 'online'
    ]);
    break;

  case 'toggle':
    if ($id) {
      echo json_encode([
        'status' => 'success',
        'message' => "Válvula {$id} estado alterado",
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'ID da válvula é obrigatório',
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    }
    break;

  case 'cycle':
    if ($id) {
      echo json_encode([
        'status' => 'success',
        'message' => "Ciclo de limpeza iniciado para válvula {$id}",
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'ID da válvula é obrigatório',
        'timestamp' => date('Y-m-d H:i:s')
      ]);
    }
    break;

  default:
    echo json_encode([
      'status' => 'error',
      'message' => 'Acção não reconhecida',
      'available_actions' => ['status', 'valves', 'valve', 'ping', 'toggle', 'cycle'],
      'timestamp' => date('Y-m-d H:i:s')
    ]);
    break;
}
