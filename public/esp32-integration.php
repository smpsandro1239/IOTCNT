<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Configuração da base de dados
$dbConfig = [
  'host' => 'iotcnt_mysql',
  'dbname' => 'iotcnt',
  'username' => 'root',
  'password' => '1234567890aa',
  'charset' => 'utf8mb4'
];

// Configuração ESP32
$esp32Config = [
  'timeout' => 30,
  'retry_attempts' => 3,
  'heartbeat_interval' => 60,
  'command_timeout' => 10
];

// Função para conectar à base de dados
function getConnection($config)
{
  try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  } catch (PDOException $e) {
    return null;
  }
}

// Função para registar dispositivo ESP32
function registerESP32Device($pdo, $deviceData)
{
  try {
    // Verificar se o dispositivo já existe
    $stmt = $pdo->prepare("SELECT id FROM esp32_devices WHERE mac_address = ?");
    $stmt->execute([$deviceData['mac_address']]);

    if ($stmt->fetch()) {
      // Actualizar dispositivo existente
      $stmt = $pdo->prepare("
                UPDATE esp32_devices
                SET ip_address = ?, firmware_version = ?, last_seen = NOW(), status = 'online'
                WHERE mac_address = ?
            ");
      $stmt->execute([
        $deviceData['ip_address'],
        $deviceData['firmware_version'],
        $deviceData['mac_address']
      ]);

      return ['success' => true, 'action' => 'updated'];
    } else {
      // Registar novo dispositivo
      $stmt = $pdo->prepare("
                INSERT INTO esp32_devices (mac_address, ip_address, firmware_version, device_name, status, registered_at, last_seen)
                VALUES (?, ?, ?, ?, 'online', NOW(), NOW())
            ");
      $stmt->execute([
        $deviceData['mac_address'],
        $deviceData['ip_address'],
        $deviceData['firmware_version'],
        $deviceData['device_name'] ?? 'ESP32-' . substr($deviceData['mac_address'], -6)
      ]);

      return ['success' => true, 'action' => 'registered', 'id' => $pdo->lastInsertId()];
    }
  } catch (Exception $e) {
    return ['success' => false, 'error' => $e->getMessage()];
  }
}

// Função para obter comandos pendentes para ESP32
function getPendingCommands($pdo, $macAddress)
{
  try {
    $stmt = $pdo->prepare("
            SELECT id, command, parameters, priority, created_at
            FROM esp32_commands
            WHERE target_device = ? AND status = 'pending'
            ORDER BY priority DESC, created_at ASC
            LIMIT 10
        ");
    $stmt->execute([$macAddress]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    return [];
  }
}

// Função para marcar comando como executado
function markCommandExecuted($pdo, $commandId, $result)
{
  try {
    $stmt = $pdo->prepare("
            UPDATE esp32_commands
            SET status = 'executed', result = ?, executed_at = NOW()
            WHERE id = ?
        ");
    $stmt->execute([json_encode($result), $commandId]);

    return true;
  } catch (Exception $e) {
    return false;
  }
}

// Função para enviar comando para ESP32
function sendCommandToESP32($pdo, $macAddress, $command, $parameters = [], $priority = 1)
{
  try {
    $stmt = $pdo->prepare("
            INSERT INTO esp32_commands (target_device, command, parameters, priority, status, created_at)
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
    $stmt->execute([
      $macAddress,
      $command,
      json_encode($parameters),
      $priority
    ]);

    return ['success' => true, 'command_id' => $pdo->lastInsertId()];
  } catch (Exception $e) {
    return ['success' => false, 'error' => $e->getMessage()];
  }
}

// Função para actualizar dados de sensores
function updateSensorData($pdo, $macAddress, $sensorData)
{
  try {
    // Registar dados dos sensores
    $stmt = $pdo->prepare("
            INSERT INTO sensor_readings (device_mac, sensor_type, value, unit, timestamp)
            VALUES (?, ?, ?, ?, NOW())
        ");

    foreach ($sensorData as $sensor) {
      $stmt->execute([
        $macAddress,
        $sensor['type'],
        $sensor['value'],
        $sensor['unit'] ?? ''
      ]);
    }

    // Actualizar estado das válvulas se aplicável
    if (isset($sensorData['valves'])) {
      foreach ($sensorData['valves'] as $valve) {
        $stmt = $pdo->prepare("
                    UPDATE valve_status
                    SET temperature = ?, pressure = ?, status = ?, last_updated = NOW()
                    WHERE valve_id = ?
                ");
        $stmt->execute([
          $valve['temperature'] ?? null,
          $valve['pressure'] ?? null,
          $valve['status'] ?? 'unknown',
          $valve['id']
        ]);
      }
    }

    return ['success' => true];
  } catch (Exception $e) {
    return ['success' => false, 'error' => $e->getMessage()];
  }
}

// Função para obter lista de dispositivos
function getESP32Devices($pdo)
{
  try {
    $stmt = $pdo->query("
            SELECT *,
                   TIMESTAMPDIFF(MINUTE, last_seen, NOW()) as minutes_offline
            FROM esp32_devices
            ORDER BY last_seen DESC
        ");

    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Actualizar status baseado no último contacto
    foreach ($devices as &$device) {
      if ($device['minutes_offline'] > 5) {
        $device['status'] = 'offline';
      } elseif ($device['minutes_offline'] > 2) {
        $device['status'] = 'warning';
      }
    }

    return $devices;
  } catch (Exception $e) {
    return [];
  }
}

// Função para criar tabelas se não existirem
function createESP32Tables($pdo)
{
  try {
    // Tabela de dispositivos ESP32
    $pdo->exec("
            CREATE TABLE IF NOT EXISTS esp32_devices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                mac_address VARCHAR(17) UNIQUE NOT NULL,
                ip_address VARCHAR(15),
                device_name VARCHAR(100),
                firmware_version VARCHAR(20),
                status ENUM('online', 'offline', 'warning') DEFAULT 'offline',
                registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

    // Tabela de comandos para ESP32
    $pdo->exec("
            CREATE TABLE IF NOT EXISTS esp32_commands (
                id INT AUTO_INCREMENT PRIMARY KEY,
                target_device VARCHAR(17) NOT NULL,
                command VARCHAR(50) NOT NULL,
                parameters JSON,
                priority INT DEFAULT 1,
                status ENUM('pending', 'executed', 'failed') DEFAULT 'pending',
                result JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                executed_at TIMESTAMP NULL
            )
        ");

    // Tabela de leituras de sensores
    $pdo->exec("
            CREATE TABLE IF NOT EXISTS sensor_readings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                device_mac VARCHAR(17) NOT NULL,
                sensor_type VARCHAR(50) NOT NULL,
                value DECIMAL(10,2) NOT NULL,
                unit VARCHAR(10),
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_device_time (device_mac, timestamp),
                INDEX idx_sensor_type (sensor_type)
            )
        ");

    return true;
  } catch (Exception $e) {
    return false;
  }
}

// Processar pedidos
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'status';

try {
  $pdo = getConnection($dbConfig);
  if (!$pdo) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Erro de conexão à base de dados',
      'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
  }

  // Criar tabelas se necessário
  createESP32Tables($pdo);

  switch ($action) {
    case 'status':
      $devices = getESP32Devices($pdo);
      echo json_encode([
        'status' => 'success',
        'message' => 'Sistema de integração ESP32 online',
        'devices_count' => count($devices),
        'devices' => $devices,
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'register':
      if ($method !== 'POST') {
        throw new Exception('Método não permitido');
      }

      $input = json_decode(file_get_contents('php://input'), true);
      if (!$input || !isset($input['mac_address'])) {
        throw new Exception('Dados de registo inválidos');
      }

      $result = registerESP32Device($pdo, $input);
      if ($result['success']) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Dispositivo ' . $result['action'],
          'device_id' => $result['id'] ?? null,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        throw new Exception($result['error']);
      }
      break;

    case 'heartbeat':
      if ($method !== 'POST') {
        throw new Exception('Método não permitido');
      }

      $input = json_decode(file_get_contents('php://input'), true);
      if (!$input || !isset($input['mac_address'])) {
        throw new Exception('MAC address obrigatório');
      }

      // Actualizar último contacto
      $stmt = $pdo->prepare("
                UPDATE esp32_devices
                SET last_seen = NOW(), status = 'online'
                WHERE mac_address = ?
            ");
      $stmt->execute([$input['mac_address']]);

      // Obter comandos pendentes
      $commands = getPendingCommands($pdo, $input['mac_address']);

      // Actualizar dados de sensores se fornecidos
      if (isset($input['sensor_data'])) {
        updateSensorData($pdo, $input['mac_address'], $input['sensor_data']);
      }

      echo json_encode([
        'status' => 'success',
        'commands' => $commands,
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'command_result':
      if ($method !== 'POST') {
        throw new Exception('Método não permitido');
      }

      $input = json_decode(file_get_contents('php://input'), true);
      if (!$input || !isset($input['command_id']) || !isset($input['result'])) {
        throw new Exception('Dados de resultado inválidos');
      }

      if (markCommandExecuted($pdo, $input['command_id'], $input['result'])) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Resultado do comando registado',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        throw new Exception('Erro ao registar resultado');
      }
      break;

    case 'send_command':
      if ($method !== 'POST') {
        throw new Exception('Método não permitido');
      }

      $input = json_decode(file_get_contents('php://input'), true);
      if (!$input || !isset($input['mac_address']) || !isset($input['command'])) {
        throw new Exception('Dados de comando inválidos');
      }

      $result = sendCommandToESP32(
        $pdo,
        $input['mac_address'],
        $input['command'],
        $input['parameters'] ?? [],
        $input['priority'] ?? 1
      );

      if ($result['success']) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Comando enviado',
          'command_id' => $result['command_id'],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        throw new Exception($result['error']);
      }
      break;

    case 'sensor_data':
      $macAddress = $_GET['device'] ?? null;
      $sensorType = $_GET['sensor'] ?? null;
      $limit = min(100, max(1, intval($_GET['limit'] ?? 50)));

      $sql = "SELECT * FROM sensor_readings WHERE 1=1";
      $params = [];

      if ($macAddress) {
        $sql .= " AND device_mac = ?";
        $params[] = $macAddress;
      }

      if ($sensorType) {
        $sql .= " AND sensor_type = ?";
        $params[] = $sensorType;
      }

      $sql .= " ORDER BY timestamp DESC LIMIT ?";
      $params[] = $limit;

      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);

      echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    default:
      echo json_encode([
        'status' => 'error',
        'message' => 'Acção não reconhecida',
        'available_actions' => ['status', 'register', 'heartbeat', 'command_result', 'send_command', 'sensor_data'],
        'timestamp' => date('Y-m-d H:i:s')
      ]);
  }
} catch (Exception $e) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Erro: ' . $e->getMessage(),
    'timestamp' => date('Y-m-d H:i:s')
  ]);
}
