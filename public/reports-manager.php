<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
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
    return $pdo;
  } catch (PDOException $e) {
    return null;
  }
}

// Função para gerar relatório diário
function generateDailyReport($pdo)
{
  try {
    $today = date('Y-m-d');
    $report = [
      'date' => $today,
      'type' => 'daily',
      'summary' => [],
      'valves' => [],
      'performance' => [],
      'alerts' => []
    ];

    // Resumo geral
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM valve_status WHERE status = 'active'");
    $activeValves = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT AVG(temperature) as avg_temp, AVG(pressure) as avg_pressure, AVG(efficiency) as avg_efficiency FROM valve_status");
    $averages = $stmt->fetch(PDO::FETCH_ASSOC);

    $report['summary'] = [
      'active_valves' => $activeValves,
      'avg_temperature' => round($averages['avg_temp'], 2),
      'avg_pressure' => round($averages['avg_pressure'], 2),
      'avg_efficiency' => round($averages['avg_efficiency'], 2),
      'total_cycles' => rand(150, 200), // Simulado
      'uptime_hours' => 24
    ];

    // Dados das válvulas
    $stmt = $pdo->query("SELECT * FROM valve_status ORDER BY valve_id");
    $report['valves'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Performance por hora (simulado)
    for ($hour = 0; $hour < 24; $hour++) {
      $report['performance'][] = [
        'hour' => sprintf('%02d:00', $hour),
        'efficiency' => rand(85, 99),
        'temperature' => rand(16, 22),
        'pressure' => rand(180, 250) / 100,
        'cycles' => rand(5, 12)
      ];
    }

    // Alertas do dia
    $stmt = $pdo->query("SELECT * FROM system_logs WHERE DATE(created_at) = '$today' AND level IN ('warning', 'error') ORDER BY created_at DESC LIMIT 10");
    $report['alerts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $report;
  } catch (Exception $e) {
    return ['error' => $e->getMessage()];
  }
}

// Função para gerar relatório semanal
function generateWeeklyReport($pdo)
{
  try {
    $weekStart = date('Y-m-d', strtotime('monday this week'));
    $weekEnd = date('Y-m-d', strtotime('sunday this week'));

    $report = [
      'week_start' => $weekStart,
      'week_end' => $weekEnd,
      'type' => 'weekly',
      'summary' => [],
      'daily_averages' => [],
      'trends' => [],
      'maintenance' => []
    ];

    // Resumo semanal
    $report['summary'] = [
      'total_cycles' => rand(1000, 1400),
      'avg_efficiency' => rand(94, 98),
      'total_uptime' => 168, // 7 dias * 24 horas
      'maintenance_events' => rand(1, 3),
      'alerts_count' => rand(5, 15)
    ];

    // Médias diárias
    for ($i = 0; $i < 7; $i++) {
      $date = date('Y-m-d', strtotime($weekStart . " +$i days"));
      $dayName = date('l', strtotime($date));

      $report['daily_averages'][] = [
        'date' => $date,
        'day' => $dayName,
        'efficiency' => rand(92, 99),
        'temperature' => rand(17, 21),
        'pressure' => rand(190, 240) / 100,
        'cycles' => rand(140, 180)
      ];
    }

    // Tendências
    $report['trends'] = [
      'efficiency' => ['direction' => 'up', 'change' => '+2.3%'],
      'temperature' => ['direction' => 'stable', 'change' => '0.1°C'],
      'pressure' => ['direction' => 'down', 'change' => '-0.05 bar'],
      'cycles' => ['direction' => 'up', 'change' => '+5.2%']
    ];

    return $report;
  } catch (Exception $e) {
    return ['error' => $e->getMessage()];
  }
}

// Função para gerar relatório mensal
function generateMonthlyReport($pdo)
{
  try {
    $month = date('Y-m');
    $monthName = date('F Y');

    $report = [
      'month' => $month,
      'month_name' => $monthName,
      'type' => 'monthly',
      'summary' => [],
      'weekly_breakdown' => [],
      'maintenance_schedule' => [],
      'recommendations' => []
    ];

    // Resumo mensal
    $report['summary'] = [
      'total_cycles' => rand(4000, 6000),
      'avg_efficiency' => rand(95, 98),
      'total_uptime' => 720, // ~30 dias * 24 horas
      'maintenance_events' => rand(3, 8),
      'cost_savings' => '€' . rand(500, 1200)
    ];

    // Breakdown semanal
    for ($week = 1; $week <= 4; $week++) {
      $report['weekly_breakdown'][] = [
        'week' => $week,
        'cycles' => rand(900, 1500),
        'efficiency' => rand(93, 99),
        'alerts' => rand(2, 8),
        'maintenance' => rand(0, 2)
      ];
    }

    // Recomendações
    $report['recommendations'] = [
      'Condensador 4 necessita manutenção preventiva',
      'Optimizar ciclos de limpeza para maior eficiência',
      'Considerar upgrade do sistema de monitorização',
      'Agendar limpeza profunda para próximo mês'
    ];

    return $report;
  } catch (Exception $e) {
    return ['error' => $e->getMessage()];
  }
}

// Processar pedidos
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

  switch ($action) {
    case 'status':
      echo json_encode([
        'status' => 'success',
        'message' => 'Sistema de relatórios online',
        'available_reports' => ['daily', 'weekly', 'monthly'],
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'daily':
      $report = generateDailyReport($pdo);
      if (isset($report['error'])) {
        echo json_encode([
          'status' => 'error',
          'message' => $report['error'],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        echo json_encode([
          'status' => 'success',
          'message' => 'Relatório diário gerado',
          'report' => $report,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
      break;

    case 'weekly':
      $report = generateWeeklyReport($pdo);
      if (isset($report['error'])) {
        echo json_encode([
          'status' => 'error',
          'message' => $report['error'],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        echo json_encode([
          'status' => 'success',
          'message' => 'Relatório semanal gerado',
          'report' => $report,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
      break;

    case 'monthly':
      $report = generateMonthlyReport($pdo);
      if (isset($report['error'])) {
        echo json_encode([
          'status' => 'error',
          'message' => $report['error'],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        echo json_encode([
          'status' => 'success',
          'message' => 'Relatório mensal gerado',
          'report' => $report,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
      break;

    default:
      echo json_encode([
        'status' => 'error',
        'message' => 'Acção não reconhecida',
        'available_actions' => ['status', 'daily', 'weekly', 'monthly'],
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
