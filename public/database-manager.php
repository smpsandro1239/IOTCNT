<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Configuração da base de dados
$dbConfig = [
  'host' => 'iotcnt_mysql',
  'dbname' => 'iotcnt',
  'username' => 'root',
  'password' => '1234567890aa',
  'charset' => 'utf8mb4'
];

// Função para conectar à base de dados
function getConnection($config)
{
  try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
  } catch (PDOException $e) {
    return null;
  }
}

// Função para criar tabelas se não existirem
function createTables($pdo)
{
  $tables = [
    'system_logs' => "
            CREATE TABLE IF NOT EXISTS system_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                level ENUM('info', 'success', 'warning', 'error', 'critical') DEFAULT 'info',
                component VARCHAR(50) NOT NULL,
                message TEXT NOT NULL,
                user_email VARCHAR(100),
                valve_id INT,
                temperature DECIMAL(5,2),
                pressure DECIMAL(5,2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
    'notifications' => "
            CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                type ENUM('info', 'warning', 'critical', 'maintenance', 'report') DEFAULT 'info',
                channel ENUM('email', 'telegram') NOT NULL,
                message TEXT NOT NULL,
                status ENUM('sent', 'pending', 'failed') DEFAULT 'pending',
                recipient VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
    'valve_status' => "
            CREATE TABLE IF NOT EXISTS valve_status (
                id INT AUTO_INCREMENT PRIMARY KEY,
                valve_id INT NOT NULL,
                valve_name VARCHAR(100) NOT NULL,
                status ENUM('active', 'inactive', 'maintenance') DEFAULT 'inactive',
                temperature DECIMAL(5,2) DEFAULT 0.00,
                pressure DECIMAL(5,2) DEFAULT 0.00,
                last_cycle DATETIME,
                cycles_completed INT DEFAULT 0,
                efficiency DECIMAL(5,2) DEFAULT 0.00,
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_valve (valve_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
    'system_settings' => "
            CREATE TABLE IF NOT EXISTS system_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) NOT NULL UNIQUE,
                setting_value TEXT,
                setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
                description TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
    'schedules' => "
            CREATE TABLE IF NOT EXISTS schedules (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                schedule_time TIME NOT NULL,
                frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'daily',
                valve_ids JSON,
                active BOOLEAN DEFAULT TRUE,
                last_run DATETIME,
                next_run DATETIME,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        "
  ];

  foreach ($tables as $tableName => $sql) {
    try {
      $pdo->exec($sql);
    } catch (PDOException $e) {
      error_log("Erro ao criar tabela $tableName: " . $e->getMessage());
    }
  }
}

// Função para inserir dados iniciais
function insertInitialData($pdo)
{
  // Verificar se já existem dados
  $stmt = $pdo->query("SELECT COUNT(*) as count FROM valve_status");
  $result = $stmt->fetch();

  if ($result['count'] == 0) {
    // Inserir válvulas iniciais
    $valves = [
      [1, 'Condensador 1', 'active', 18.0, 2.1, 156, 98.5],
      [2, 'Condensador 2', 'active', 17.0, 2.3, 142, 97.8],
      [3, 'Condensador 3', 'active', 19.0, 2.0, 134, 94.2],
      [4, 'Condensador 4', 'maintenance', 20.0, 1.8, 89, 85.1],
      [5, 'Condensador 5', 'active', 18.0, 2.2, 167, 99.1]
    ];

    $stmt = $pdo->prepare("
            INSERT INTO valve_status (valve_id, valve_name, status, temperature, pressure, cycles_completed, efficiency, last_cycle)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW() - INTERVAL 2 HOUR)
        ");

    foreach ($valves as $valve) {
      $stmt->execute($valve);
    }

    // Inserir configurações iniciais
    $settings = [
      ['system_name', 'IOTCNT - CNT', 'string', 'Nome do sistema'],
      ['location', 'Central de Frio - CNT', 'string', 'Localização do sistema'],
      ['monitoring_interval', '30', 'number', 'Intervalo de monitorização em segundos'],
      ['alert_temperature', '25', 'number', 'Temperatura de alerta em °C'],
      ['cleaning_duration', '15', 'number', 'Duração do ciclo de limpeza em minutos'],
      ['min_pressure', '1.5', 'number', 'Pressão mínima em bar'],
      ['auto_mode', 'true', 'boolean', 'Modo automático activado'],
      ['email_notifications', 'true', 'boolean', 'Notificações por email'],
      ['telegram_notifications', 'false', 'boolean', 'Notificações Telegram']
    ];

    $stmt = $pdo->prepare("
            INSERT INTO system_settings (setting_key, setting_value, setting_type, description)
            VALUES (?, ?, ?, ?)
        ");

    foreach ($settings as $setting) {
      $stmt->execute($setting);
    }

    // Inserir agendamentos iniciais
    $schedules = [
      ['Ciclo Diário Manhã', '06:00:00', 'daily', '[1,2,3,5]', 1],
      ['Ciclo Semanal Profundo', '02:00:00', 'weekly', '[1,2,3,4,5]', 1],
      ['Manutenção Mensal', '00:00:00', 'monthly', '[4]', 0]
    ];

    $stmt = $pdo->prepare("
            INSERT INTO schedules (name, schedule_time, frequency, valve_ids, active)
            VALUES (?, ?, ?, ?, ?)
        ");

    foreach ($schedules as $schedule) {
      $stmt->execute($schedule);
    }

    // Inserir logs iniciais
    $logs = [
      ['info', 'system', 'Sistema iniciado com sucesso', 'sistema'],
      ['success', 'valve', 'Todas as válvulas verificadas', 'admin@iotcnt.local'],
      ['info', 'api', 'API ESP32 online e funcional', 'sistema']
    ];

    $stmt = $pdo->prepare("
            INSERT INTO system_logs (level, component, message, user_email)
            VALUES (?, ?, ?, ?)
        ");

    foreach ($logs as $log) {
      $stmt->execute($log);
    }
  }
}

// Obter método HTTP e acção
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Conectar à base de dados
$pdo = getConnection($dbConfig);

if (!$pdo) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Erro de conexão à base de dados',
    'fallback' => 'Sistema a funcionar em modo local',
    'timestamp' => date('Y-m-d H:i:s')
  ]);
  exit;
}

// Criar tabelas se necessário
createTables($pdo);
insertInitialData($pdo);

switch ($method) {
  case 'GET':
    switch ($action) {
      case 'status':
        echo json_encode([
          'status' => 'success',
          'message' => 'Base de dados IOTCNT online',
          'database' => $dbConfig['dbname'],
          'host' => $dbConfig['host'],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'valves':
        $stmt = $pdo->query("SELECT * FROM valve_status ORDER BY valve_id");
        $valves = $stmt->fetchAll();

        echo json_encode([
          'status' => 'success',
          'data' => $valves,
          'count' => count($valves),
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'logs':
        $limit = $_GET['limit'] ?? 50;
        $level = $_GET['level'] ?? '';
        $component = $_GET['component'] ?? '';

        $sql = "SELECT * FROM system_logs WHERE 1=1";
        $params = [];

        if ($level) {
          $sql .= " AND level = ?";
          $params[] = $level;
        }

        if ($component) {
          $sql .= " AND component = ?";
          $params[] = $component;
        }

        $sql .= " ORDER BY timestamp DESC LIMIT ?";
        $params[] = (int)$limit;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();

        echo json_encode([
          'status' => 'success',
          'data' => $logs,
          'count' => count($logs),
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'settings':
        $stmt = $pdo->query("SELECT * FROM system_settings ORDER BY setting_key");
        $settings = $stmt->fetchAll();

        $settingsArray = [];
        foreach ($settings as $setting) {
          $value = $setting['setting_value'];

          // Converter tipos
          switch ($setting['setting_type']) {
            case 'number':
              $value = is_numeric($value) ? (float)$value : $value;
              break;
            case 'boolean':
              $value = $value === 'true' || $value === '1';
              break;
            case 'json':
              $value = json_decode($value, true);
              break;
          }

          $settingsArray[$setting['setting_key']] = $value;
        }

        echo json_encode([
          'status' => 'success',
          'data' => $settingsArray,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'schedules':
        $stmt = $pdo->query("SELECT * FROM schedules ORDER BY schedule_time");
        $schedules = $stmt->fetchAll();

        // Converter valve_ids de JSON para array
        foreach ($schedules as &$schedule) {
          $schedule['valve_ids'] = json_decode($schedule['valve_ids'], true);
        }

        echo json_encode([
          'status' => 'success',
          'data' => $schedules,
          'count' => count($schedules),
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'notifications':
        $limit = $_GET['limit'] ?? 50;

        $stmt = $pdo->prepare("SELECT * FROM notifications ORDER BY timestamp DESC LIMIT ?");
        $stmt->execute([(int)$limit]);
        $notifications = $stmt->fetchAll();

        echo json_encode([
          'status' => 'success',
          'data' => $notifications,
          'count' => count($notifications),
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      default:
        echo json_encode([
          'status' => 'success',
          'message' => 'IOTCNT Database Manager API',
          'version' => '1.0',
          'endpoints' => [
            'GET ?action=status' => 'Status da base de dados',
            'GET ?action=valves' => 'Estado das válvulas',
            'GET ?action=logs' => 'Logs do sistema',
            'GET ?action=settings' => 'Configurações',
            'GET ?action=schedules' => 'Agendamentos',
            'GET ?action=notifications' => 'Notificações',
            'POST ?action=log' => 'Adicionar log',
            'POST ?action=update-valve' => 'Actualizar válvula',
            'POST ?action=save-setting' => 'Guardar configuração'
          ],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;
    }
    break;

  case 'POST':
    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
      case 'log':
        $level = $input['level'] ?? 'info';
        $component = $input['component'] ?? 'system';
        $message = $input['message'] ?? '';
        $userEmail = $input['user_email'] ?? null;
        $valveId = $input['valve_id'] ?? null;
        $temperature = $input['temperature'] ?? null;
        $pressure = $input['pressure'] ?? null;

        $stmt = $pdo->prepare("
                    INSERT INTO system_logs (level, component, message, user_email, valve_id, temperature, pressure)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

        $result = $stmt->execute([$level, $component, $message, $userEmail, $valveId, $temperature, $pressure]);

        echo json_encode([
          'status' => $result ? 'success' : 'error',
          'message' => $result ? 'Log adicionado' : 'Erro ao adicionar log',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'update-valve':
        $valveId = $input['valve_id'] ?? null;
        $status = $input['status'] ?? null;
        $temperature = $input['temperature'] ?? null;
        $pressure = $input['pressure'] ?? null;

        if (!$valveId) {
          echo json_encode([
            'status' => 'error',
            'message' => 'ID da válvula é obrigatório',
            'timestamp' => date('Y-m-d H:i:s')
          ]);
          break;
        }

        $updates = [];
        $params = [];

        if ($status) {
          $updates[] = "status = ?";
          $params[] = $status;
        }

        if ($temperature !== null) {
          $updates[] = "temperature = ?";
          $params[] = $temperature;
        }

        if ($pressure !== null) {
          $updates[] = "pressure = ?";
          $params[] = $pressure;
        }

        if (empty($updates)) {
          echo json_encode([
            'status' => 'error',
            'message' => 'Nenhum campo para actualizar',
            'timestamp' => date('Y-m-d H:i:s')
          ]);
          break;
        }

        $params[] = $valveId;
        $sql = "UPDATE valve_status SET " . implode(', ', $updates) . " WHERE valve_id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);

        echo json_encode([
          'status' => $result ? 'success' : 'error',
          'message' => $result ? 'Válvula actualizada' : 'Erro ao actualizar válvula',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'save-setting':
        $key = $input['key'] ?? null;
        $value = $input['value'] ?? null;
        $type = $input['type'] ?? 'string';

        if (!$key) {
          echo json_encode([
            'status' => 'error',
            'message' => 'Chave da configuração é obrigatória',
            'timestamp' => date('Y-m-d H:i:s')
          ]);
          break;
        }

        // Converter valor baseado no tipo
        switch ($type) {
          case 'boolean':
            $value = $value ? 'true' : 'false';
            break;
          case 'json':
            $value = json_encode($value);
            break;
          default:
            $value = (string)$value;
        }

        $stmt = $pdo->prepare("
                    INSERT INTO system_settings (setting_key, setting_value, setting_type)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    setting_value = VALUES(setting_value),
                    setting_type = VALUES(setting_type)
                ");

        $result = $stmt->execute([$key, $value, $type]);

        echo json_encode([
          'status' => $result ? 'success' : 'error',
          'message' => $result ? 'Configuração guardada' : 'Erro ao guardar configuração',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'add-notification':
        $type = $input['type'] ?? 'info';
        $channel = $input['channel'] ?? 'email';
        $message = $input['message'] ?? '';
        $status = $input['status'] ?? 'sent';
        $recipient = $input['recipient'] ?? null;

        $stmt = $pdo->prepare("
                    INSERT INTO notifications (type, channel, message, status, recipient)
                    VALUES (?, ?, ?, ?, ?)
                ");

        $result = $stmt->execute([$type, $channel, $message, $status, $recipient]);

        echo json_encode([
          'status' => $result ? 'success' : 'error',
          'message' => $result ? 'Notificação registada' : 'Erro ao registar notificação',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      default:
        echo json_encode([
          'status' => 'error',
          'message' => 'Acção não reconhecida',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;
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
