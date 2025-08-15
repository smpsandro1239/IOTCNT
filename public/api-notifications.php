<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Configurações de notificação (normalmente viriam da base de dados)
$notificationConfig = [
  'email' => [
    'enabled' => true,
    'smtp_server' => 'smtp.cnt.pt',
    'smtp_port' => 587,
    'smtp_user' => 'iotcnt@cnt.pt',
    'smtp_password' => '••••••••',
    'alert_email' => 'admin@cnt.pt'
  ],
  'telegram' => [
    'enabled' => false,
    'bot_token' => '',
    'chat_id' => ''
  ],
  'alert_types' => [
    'critical' => true,
    'warning' => true,
    'info' => true,
    'reports' => false
  ]
];

// Histórico de notificações (normalmente seria base de dados)
$notificationHistory = [];

// Função para enviar email (simulação)
function sendEmail($to, $subject, $message)
{
  // Em produção, usar PHPMailer ou similar
  $success = rand(1, 10) > 2; // 80% de sucesso

  return [
    'success' => $success,
    'message' => $success ? 'Email enviado com sucesso' : 'Falha no envio do email',
    'timestamp' => date('Y-m-d H:i:s')
  ];
}

// Função para enviar Telegram (simulação)
function sendTelegram($botToken, $chatId, $message)
{
  // Em produção, usar API do Telegram
  $success = rand(1, 10) > 3; // 70% de sucesso

  return [
    'success' => $success,
    'message' => $success ? 'Mensagem Telegram enviada' : 'Falha no envio do Telegram',
    'timestamp' => date('Y-m-d H:i:s')
  ];
}

// Função para registar notificação
function logNotification($type, $channel, $message, $status)
{
  global $notificationHistory;

  $notification = [
    'id' => uniqid(),
    'timestamp' => date('Y-m-d H:i:s'),
    'type' => $type,
    'channel' => $channel,
    'message' => $message,
    'status' => $status
  ];

  array_unshift($notificationHistory, $notification);

  // Manter apenas os últimos 100 registos
  if (count($notificationHistory) > 100) {
    $notificationHistory = array_slice($notificationHistory, 0, 100);
  }

  return $notification;
}

// Obter método HTTP
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
  case 'GET':
    switch ($action) {
      case 'config':
        echo json_encode([
          'status' => 'success',
          'data' => $notificationConfig,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'history':
        echo json_encode([
          'status' => 'success',
          'data' => $notificationHistory,
          'count' => count($notificationHistory),
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'test-email':
        $result = sendEmail(
          $notificationConfig['email']['alert_email'],
          'IOTCNT - Teste de Email',
          'Este é um email de teste do sistema IOTCNT.'
        );

        logNotification('test', 'email', 'Email de teste', $result['success'] ? 'sent' : 'failed');

        echo json_encode([
          'status' => $result['success'] ? 'success' : 'error',
          'message' => $result['message'],
          'timestamp' => $result['timestamp']
        ]);
        break;

      case 'test-telegram':
        if (!$notificationConfig['telegram']['enabled']) {
          echo json_encode([
            'status' => 'error',
            'message' => 'Telegram não está configurado',
            'timestamp' => date('Y-m-d H:i:s')
          ]);
          break;
        }

        $result = sendTelegram(
          $notificationConfig['telegram']['bot_token'],
          $notificationConfig['telegram']['chat_id'],
          '🤖 IOTCNT - Teste de notificação Telegram'
        );

        logNotification('test', 'telegram', 'Mensagem de teste Telegram', $result['success'] ? 'sent' : 'failed');

        echo json_encode([
          'status' => $result['success'] ? 'success' : 'error',
          'message' => $result['message'],
          'timestamp' => $result['timestamp']
        ]);
        break;

      default:
        echo json_encode([
          'status' => 'success',
          'message' => 'IOTCNT Notifications API',
          'version' => '1.0',
          'endpoints' => [
            'GET /api-notifications.php?action=config' => 'Obter configurações',
            'GET /api-notifications.php?action=history' => 'Histórico de notificações',
            'GET /api-notifications.php?action=test-email' => 'Testar email',
            'GET /api-notifications.php?action=test-telegram' => 'Testar Telegram',
            'POST /api-notifications.php?action=send' => 'Enviar notificação',
            'POST /api-notifications.php?action=alert' => 'Enviar alerta'
          ],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;
    }
    break;

  case 'POST':
    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
      case 'send':
        $type = $input['type'] ?? 'info';
        $message = $input['message'] ?? 'Notificação do sistema IOTCNT';
        $channels = $input['channels'] ?? ['email'];

        $results = [];

        foreach ($channels as $channel) {
          if ($channel === 'email' && $notificationConfig['email']['enabled']) {
            $result = sendEmail(
              $notificationConfig['email']['alert_email'],
              "IOTCNT - " . ucfirst($type),
              $message
            );

            $results['email'] = $result;
            logNotification($type, 'email', $message, $result['success'] ? 'sent' : 'failed');
          }

          if ($channel === 'telegram' && $notificationConfig['telegram']['enabled']) {
            $emoji = $type === 'critical' ? '🚨' : ($type === 'warning' ? '⚠️' : 'ℹ️');
            $telegramMessage = $emoji . ' IOTCNT - ' . $message;

            $result = sendTelegram(
              $notificationConfig['telegram']['bot_token'],
              $notificationConfig['telegram']['chat_id'],
              $telegramMessage
            );

            $results['telegram'] = $result;
            logNotification($type, 'telegram', $message, $result['success'] ? 'sent' : 'failed');
          }
        }

        echo json_encode([
          'status' => 'success',
          'message' => 'Notificações processadas',
          'results' => $results,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'alert':
        $alertType = $input['alert_type'] ?? 'warning';
        $valveId = $input['valve_id'] ?? null;
        $temperature = $input['temperature'] ?? null;
        $pressure = $input['pressure'] ?? null;

        // Gerar mensagem baseada no tipo de alerta
        $messages = [
          'critical' => "ALERTA CRÍTICO: Falha detectada no sistema" . ($valveId ? " - Condensador $valveId" : ""),
          'warning' => "AVISO: " . ($temperature ? "Temperatura elevada ($temperature°C)" : "Parâmetros fora do normal") . ($valveId ? " - Condensador $valveId" : ""),
          'info' => "INFO: " . ($valveId ? "Operação completada no Condensador $valveId" : "Sistema funcionando normalmente"),
          'maintenance' => "MANUTENÇÃO: " . ($valveId ? "Condensador $valveId colocado em manutenção" : "Manutenção programada iniciada")
        ];

        $message = $messages[$alertType] ?? $messages['info'];

        // Determinar canais baseado no tipo de alerta
        $channels = [];
        if ($notificationConfig['email']['enabled'] && $notificationConfig['alert_types'][$alertType] ?? true) {
          $channels[] = 'email';
        }
        if ($notificationConfig['telegram']['enabled'] && $notificationConfig['alert_types'][$alertType] ?? true) {
          $channels[] = 'telegram';
        }

        $results = [];

        foreach ($channels as $channel) {
          if ($channel === 'email') {
            $subject = "IOTCNT - " . strtoupper($alertType);
            $emailBody = $message . "\n\nTimestamp: " . date('Y-m-d H:i:s') . "\nSistema: IOTCNT - CNT";

            $result = sendEmail($notificationConfig['email']['alert_email'], $subject, $emailBody);
            $results['email'] = $result;
            logNotification($alertType, 'email', $message, $result['success'] ? 'sent' : 'failed');
          }

          if ($channel === 'telegram') {
            $emoji = $alertType === 'critical' ? '🚨' : ($alertType === 'warning' ? '⚠️' : ($alertType === 'maintenance' ? '🔧' : 'ℹ️'));
            $telegramMessage = $emoji . ' ' . $message . "\n\n📅 " . date('d/m/Y H:i:s');

            $result = sendTelegram(
              $notificationConfig['telegram']['bot_token'],
              $notificationConfig['telegram']['chat_id'],
              $telegramMessage
            );

            $results['telegram'] = $result;
            logNotification($alertType, 'telegram', $message, $result['success'] ? 'sent' : 'failed');
          }
        }

        echo json_encode([
          'status' => 'success',
          'message' => 'Alerta processado',
          'alert_type' => $alertType,
          'channels_used' => $channels,
          'results' => $results,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

      case 'daily-report':
        $reportData = $input['data'] ?? [];

        $report = "📊 RELATÓRIO DIÁRIO IOTCNT - " . date('d/m/Y') . "\n\n";
        $report .= "🏭 RESUMO DO SISTEMA:\n";
        $report .= "• Condensadores monitorizados: " . ($reportData['total_valves'] ?? 5) . "\n";
        $report .= "• Válvulas activas: " . ($reportData['active_valves'] ?? 4) . "\n";
        $report .= "• Ciclos executados: " . ($reportData['cycles_completed'] ?? 12) . "\n";
        $report .= "• Alertas críticos: " . ($reportData['critical_alerts'] ?? 0) . "\n";
        $report .= "• Uptime: " . ($reportData['uptime'] ?? '99.8%') . "\n";
        $report .= "• Temperatura média: " . ($reportData['avg_temperature'] ?? '18.5°C') . "\n\n";
        $report .= "✅ Sistema funcionando normalmente\n";
        $report .= "📅 Próxima manutenção: " . date('d/m/Y', strtotime('+7 days'));

        $results = [];

        if ($notificationConfig['email']['enabled']) {
          $result = sendEmail(
            $notificationConfig['email']['alert_email'],
            'IOTCNT - Relatório Diário ' . date('d/m/Y'),
            $report
          );
          $results['email'] = $result;
          logNotification('report', 'email', 'Relatório diário enviado', $result['success'] ? 'sent' : 'failed');
        }

        if ($notificationConfig['telegram']['enabled']) {
          $result = sendTelegram(
            $notificationConfig['telegram']['bot_token'],
            $notificationConfig['telegram']['chat_id'],
            $report
          );
          $results['telegram'] = $result;
          logNotification('report', 'telegram', 'Relatório diário enviado', $result['success'] ? 'sent' : 'failed');
        }

        echo json_encode([
          'status' => 'success',
          'message' => 'Relatório diário enviado',
          'results' => $results,
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
