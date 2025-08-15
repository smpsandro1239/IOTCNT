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

// Configuração de email (simulada)
$emailConfig = [
  'smtp_host' => 'smtp.gmail.com',
  'smtp_port' => 587,
  'smtp_user' => 'iotcnt@cnt.pt',
  'smtp_pass' => 'password',
  'from_email' => 'iotcnt@cnt.pt',
  'from_name' => 'Sistema IOTCNT'
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

// Função para criar template de email
function createEmailTemplate($type, $data)
{
  $templates = [
    'alert' => [
      'subject' => '🚨 Alerta IOTCNT: {title}',
      'body' => '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                        .header { background: linear-gradient(135deg, #e17055, #fd79a8); color: white; padding: 30px; text-align: center; }
                        .content { padding: 30px; }
                        .alert-box { background: #fff5f5; border-left: 4px solid #e17055; padding: 20px; margin: 20px 0; border-radius: 5px; }
                        .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px; }
                        .btn { display: inline-block; padding: 12px 24px; background: #74b9ff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>🚨 Alerta do Sistema IOTCNT</h1>
                            <p>Sistema de Condensadores CNT</p>
                        </div>
                        <div class="content">
                            <div class="alert-box">
                                <h2>{title}</h2>
                                <p><strong>Descrição:</strong> {message}</p>
                                <p><strong>Válvula:</strong> {valve}</p>
                                <p><strong>Prioridade:</strong> {priority}</p>
                                <p><strong>Data/Hora:</strong> {timestamp}</p>
                            </div>
                            <p>Este alerta foi gerado automaticamente pelo sistema de monitorização IOTCNT.</p>
                            <a href="http://localhost:8080/dashboard-admin.html" class="btn">Ver Dashboard</a>
                        </div>
                        <div class="footer">
                            <p>&copy; 2025 IOTCNT - Sistema de Condensadores CNT</p>
                        </div>
                    </div>
                </body>
                </html>
            '
    ],
    'report' => [
      'subject' => '📊 Relatório IOTCNT: {period}',
      'body' => '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                        .header { background: linear-gradient(135deg, #74b9ff, #0984e3); color: white; padding: 30px; text-align: center; }
                        .content { padding: 30px; }
                        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; }
                        .stat-box { background: #f8f9fa; padding: 20px; border-radius: 5px; text-align: center; }
                        .stat-value { font-size: 24px; font-weight: bold; color: #74b9ff; }
                        .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px; }
                        .btn { display: inline-block; padding: 12px 24px; background: #00b894; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>📊 Relatório do Sistema</h1>
                            <p>Período: {period}</p>
                        </div>
                        <div class="content">
                            <div class="stats">
                                <div class="stat-box">
                                    <div class="stat-value">{efficiency}%</div>
                                    <div>Eficiência Média</div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-value">{cycles}</div>
                                    <div>Total de Ciclos</div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-value">{temperature}°C</div>
                                    <div>Temperatura Média</div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-value">{uptime}h</div>
                                    <div>Tempo Online</div>
                                </div>
                            </div>
                            <p>Relatório gerado automaticamente pelo sistema IOTCNT.</p>
                            <a href="http://localhost:8080/reports-dashboard.html" class="btn">Ver Relatórios Completos</a>
                        </div>
                        <div class="footer">
                            <p>&copy; 2025 IOTCNT - Sistema de Condensadores CNT</p>
                        </div>
                    </div>
                </body>
                </html>
            '
    ],
    'maintenance' => [
      'subject' => '🔧 Manutenção IOTCNT: {title}',
      'body' => '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                        .header { background: linear-gradient(135deg, #fdcb6e, #e17055); color: white; padding: 30px; text-align: center; }
                        .content { padding: 30px; }
                        .maintenance-box { background: #fffbf0; border-left: 4px solid #fdcb6e; padding: 20px; margin: 20px 0; border-radius: 5px; }
                        .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px; }
                        .btn { display: inline-block; padding: 12px 24px; background: #fdcb6e; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>🔧 Notificação de Manutenção</h1>
                            <p>Sistema de Condensadores CNT</p>
                        </div>
                        <div class="content">
                            <div class="maintenance-box">
                                <h2>{title}</h2>
                                <p><strong>Descrição:</strong> {message}</p>
                                <p><strong>Válvula:</strong> {valve}</p>
                                <p><strong>Tipo:</strong> {type}</p>
                                <p><strong>Agendado para:</strong> {scheduled}</p>
                            </div>
                            <p>Esta notificação foi gerada automaticamente pelo sistema de manutenção IOTCNT.</p>
                            <a href="http://localhost:8080/scheduling.html" class="btn">Ver Agendamentos</a>
                        </div>
                        <div class="footer">
                            <p>&copy; 2025 IOTCNT - Sistema de Condensadores CNT</p>
                        </div>
                    </div>
                </body>
                </html>
            '
    ]
  ];

  if (!isset($templates[$type])) {
    return null;
  }

  $template = $templates[$type];

  // Substituir placeholders
  foreach ($data as $key => $value) {
    $template['subject'] = str_replace('{' . $key . '}', $value, $template['subject']);
    $template['body'] = str_replace('{' . $key . '}', $value, $template['body']);
  }

  return $template;
}

// Função para simular envio de email
function sendEmail($to, $subject, $body, $config)
{
  // Simular envio (em produção usaria PHPMailer ou similar)
  $logEntry = [
    'to' => $to,
    'subject' => $subject,
    'sent_at' => date('Y-m-d H:i:s'),
    'status' => 'sent',
    'type' => 'email'
  ];

  // Log do email "enviado"
  file_put_contents(
    __DIR__ . '/logs/email_log.txt',
    json_encode($logEntry) . "\n",
    FILE_APPEND | LOCK_EX
  );

  return true;
}

// Função para obter lista de destinatários
function getEmailRecipients($pdo, $type = 'all')
{
  try {
    // Simulação de destinatários (em produção viria da BD)
    $recipients = [
      'admin' => [
        'admin@cnt.pt',
        'manutencao@cnt.pt',
        'supervisor@cnt.pt'
      ],
      'technical' => [
        'tecnico1@cnt.pt',
        'tecnico2@cnt.pt'
      ],
      'management' => [
        'gestao@cnt.pt',
        'director@cnt.pt'
      ]
    ];

    if ($type === 'all') {
      return array_merge($recipients['admin'], $recipients['technical'], $recipients['management']);
    }

    return $recipients[$type] ?? [];
  } catch (Exception $e) {
    return [];
  }
}

// Função para registar notificação na BD
function logNotification($pdo, $type, $title, $message, $recipients)
{
  try {
    $stmt = $pdo->prepare("
            INSERT INTO notifications (type, title, message, recipients, status, created_at)
            VALUES (?, ?, ?, ?, 'sent', NOW())
        ");

    $stmt->execute([
      $type,
      $title,
      $message,
      json_encode($recipients)
    ]);

    return $pdo->lastInsertId();
  } catch (Exception $e) {
    return false;
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
        'message' => 'Sistema de email online',
        'smtp_configured' => true,
        'available_actions' => ['send_alert', 'send_report', 'send_maintenance', 'test_email'],
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'send_alert':
      $alertData = [
        'title' => $_GET['title'] ?? 'Alerta do Sistema',
        'message' => $_GET['message'] ?? 'Alerta gerado automaticamente',
        'valve' => $_GET['valve'] ?? 'Sistema Geral',
        'priority' => $_GET['priority'] ?? 'Média',
        'timestamp' => date('Y-m-d H:i:s')
      ];

      $template = createEmailTemplate('alert', $alertData);
      $recipients = getEmailRecipients($pdo, 'admin');

      $sent = 0;
      foreach ($recipients as $email) {
        if (sendEmail($email, $template['subject'], $template['body'], $emailConfig)) {
          $sent++;
        }
      }

      logNotification($pdo, 'alert', $alertData['title'], $alertData['message'], $recipients);

      echo json_encode([
        'status' => 'success',
        'message' => "Alerta enviado para {$sent} destinatários",
        'recipients' => $recipients,
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'send_report':
      $reportData = [
        'period' => $_GET['period'] ?? 'Diário',
        'efficiency' => $_GET['efficiency'] ?? '96.8',
        'cycles' => $_GET['cycles'] ?? '234',
        'temperature' => $_GET['temperature'] ?? '18.4',
        'uptime' => $_GET['uptime'] ?? '24'
      ];

      $template = createEmailTemplate('report', $reportData);
      $recipients = getEmailRecipients($pdo, 'management');

      $sent = 0;
      foreach ($recipients as $email) {
        if (sendEmail($email, $template['subject'], $template['body'], $emailConfig)) {
          $sent++;
        }
      }

      logNotification($pdo, 'report', 'Relatório ' . $reportData['period'], 'Relatório automático gerado', $recipients);

      echo json_encode([
        'status' => 'success',
        'message' => "Relatório enviado para {$sent} destinatários",
        'recipients' => $recipients,
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'send_maintenance':
      $maintenanceData = [
        'title' => $_GET['title'] ?? 'Manutenção Programada',
        'message' => $_GET['message'] ?? 'Manutenção preventiva agendada',
        'valve' => $_GET['valve'] ?? 'Condensador 1',
        'type' => $_GET['type'] ?? 'Preventiva',
        'scheduled' => $_GET['scheduled'] ?? date('Y-m-d H:i:s', strtotime('+1 day'))
      ];

      $template = createEmailTemplate('maintenance', $maintenanceData);
      $recipients = getEmailRecipients($pdo, 'technical');

      $sent = 0;
      foreach ($recipients as $email) {
        if (sendEmail($email, $template['subject'], $template['body'], $emailConfig)) {
          $sent++;
        }
      }

      logNotification($pdo, 'maintenance', $maintenanceData['title'], $maintenanceData['message'], $recipients);

      echo json_encode([
        'status' => 'success',
        'message' => "Notificação de manutenção enviada para {$sent} destinatários",
        'recipients' => $recipients,
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'test_email':
      $testData = [
        'title' => 'Teste do Sistema de Email',
        'message' => 'Este é um email de teste do sistema IOTCNT',
        'valve' => 'Sistema de Teste',
        'priority' => 'Baixa',
        'timestamp' => date('Y-m-d H:i:s')
      ];

      $template = createEmailTemplate('alert', $testData);
      $testEmail = $_GET['email'] ?? 'admin@cnt.pt';

      if (sendEmail($testEmail, $template['subject'], $template['body'], $emailConfig)) {
        echo json_encode([
          'status' => 'success',
          'message' => "Email de teste enviado para {$testEmail}",
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Falha ao enviar email de teste',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
      break;

    case 'get_logs':
      $logFile = __DIR__ . '/logs/email_log.txt';
      $logs = [];

      if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES);
        foreach (array_slice($lines, -50) as $line) { // Últimos 50 logs
          $logs[] = json_decode($line, true);
        }
      }

      echo json_encode([
        'status' => 'success',
        'logs' => array_reverse($logs),
        'count' => count($logs),
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    default:
      echo json_encode([
        'status' => 'error',
        'message' => 'Acção não reconhecida',
        'available_actions' => ['status', 'send_alert', 'send_report', 'send_maintenance', 'test_email', 'get_logs'],
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

// Criar directório de logs se não existir
if (!is_dir(__DIR__ . '/logs')) {
  mkdir(__DIR__ . '/logs', 0755, true);
}
