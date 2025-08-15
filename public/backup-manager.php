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

// Directório de backups
$backupDir = __DIR__ . '/backups/';
if (!is_dir($backupDir)) {
  mkdir($backupDir, 0755, true);
}

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

// Função para criar backup usando PDO
function createBackup($config, $backupDir)
{
  try {
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "iotcnt_backup_{$timestamp}.sql";
    $filepath = $backupDir . $filename;

    // Conectar à base de dados
    $pdo = getConnection($config);
    if (!$pdo) {
      return ['success' => false, 'error' => 'Erro de conexão à base de dados'];
    }

    // Obter lista de tabelas
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
      $tables[] = $row[0];
    }

    // Criar conteúdo do backup
    $backup_content = "-- IOTCNT Database Backup\n";
    $backup_content .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
    $backup_content .= "-- Database: {$config['dbname']}\n\n";
    $backup_content .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $backup_content .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $backup_content .= "SET time_zone = \"+00:00\";\n\n";

    foreach ($tables as $table) {
      try {
        // Estrutura da tabela
        $result = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $result->fetch(PDO::FETCH_NUM);
        $backup_content .= "-- Estrutura da tabela `$table`\n";
        $backup_content .= "DROP TABLE IF EXISTS `$table`;\n";
        $backup_content .= $row[1] . ";\n\n";

        // Dados da tabela
        $result = $pdo->query("SELECT * FROM `$table`");
        $rowCount = $result->rowCount();

        if ($rowCount > 0) {
          $backup_content .= "-- Dados da tabela `$table`\n";
          $backup_content .= "INSERT INTO `$table` VALUES ";

          $rows = [];
          while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $values = [];
            foreach ($row as $value) {
              if ($value === null) {
                $values[] = "NULL";
              } else {
                $values[] = "'" . addslashes($value) . "'";
              }
            }
            $rows[] = "(" . implode(",", $values) . ")";
          }

          $backup_content .= implode(",\n", $rows) . ";\n\n";
        } else {
          $backup_content .= "-- Tabela `$table` está vazia\n\n";
        }
      } catch (Exception $e) {
        $backup_content .= "-- Erro ao processar tabela `$table`: " . $e->getMessage() . "\n\n";
      }
    }

    $backup_content .= "SET FOREIGN_KEY_CHECKS=1;\n";
    $backup_content .= "-- Backup concluído em: " . date('Y-m-d H:i:s') . "\n";

    // Escrever ficheiro
    if (file_put_contents($filepath, $backup_content)) {
      return [
        'success' => true,
        'filename' => $filename,
        'filepath' => $filepath,
        'size' => filesize($filepath),
        'timestamp' => $timestamp,
        'tables' => count($tables)
      ];
    } else {
      return ['success' => false, 'error' => 'Erro ao escrever ficheiro de backup'];
    }
  } catch (Exception $e) {
    return ['success' => false, 'error' => $e->getMessage()];
  }
}

// Função para listar backups
function listBackups($backupDir)
{
  $backups = [];
  $files = glob($backupDir . 'iotcnt_backup_*.sql');

  foreach ($files as $file) {
    $filename = basename($file);
    $backups[] = [
      'filename' => $filename,
      'size' => filesize($file),
      'created' => date('Y-m-d H:i:s', filemtime($file)),
      'age_hours' => round((time() - filemtime($file)) / 3600, 1)
    ];
  }

  // Ordenar por data (mais recente primeiro)
  usort($backups, function ($a, $b) {
    return strcmp($b['created'], $a['created']);
  });

  return $backups;
}

// Função para limpar backups antigos
function cleanOldBackups($backupDir, $maxAge = 168)
{ // 7 dias em horas
  $cleaned = 0;
  $files = glob($backupDir . 'iotcnt_backup_*.sql');

  foreach ($files as $file) {
    $ageHours = (time() - filemtime($file)) / 3600;
    if ($ageHours > $maxAge) {
      if (unlink($file)) {
        $cleaned++;
      }
    }
  }

  return $cleaned;
}

// Processar pedidos
$action = $_GET['action'] ?? 'status';

try {
  switch ($action) {
    case 'status':
      $pdo = getConnection($dbConfig);
      if ($pdo) {
        $backups = listBackups($backupDir);
        echo json_encode([
          'status' => 'success',
          'message' => 'Sistema de backup online',
          'backup_count' => count($backups),
          'latest_backup' => $backups[0] ?? null,
          'backup_directory' => $backupDir,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Erro de conexão à base de dados',
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
      break;

    case 'create':
      $result = createBackup($dbConfig, $backupDir);
      if ($result['success']) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Backup criado com sucesso',
          'backup' => $result,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => $result['error'],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
      break;

    case 'list':
      $backups = listBackups($backupDir);
      echo json_encode([
        'status' => 'success',
        'data' => $backups,
        'count' => count($backups),
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'clean':
      $maxAge = $_GET['max_age'] ?? 168; // 7 dias por defeito
      $cleaned = cleanOldBackups($backupDir, $maxAge);
      echo json_encode([
        'status' => 'success',
        'message' => "Limpeza concluída: {$cleaned} backups removidos",
        'cleaned_count' => $cleaned,
        'timestamp' => date('Y-m-d H:i:s')
      ]);
      break;

    case 'auto':
      // Backup automático com limpeza
      $result = createBackup($dbConfig, $backupDir);
      $cleaned = cleanOldBackups($backupDir);

      if ($result['success']) {
        echo json_encode([
          'status' => 'success',
          'message' => 'Backup automático concluído',
          'backup' => $result,
          'cleaned_count' => $cleaned,
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => $result['error'],
          'timestamp' => date('Y-m-d H:i:s')
        ]);
      }
      break;

    default:
      echo json_encode([
        'status' => 'error',
        'message' => 'Acção não reconhecida',
        'available_actions' => ['status', 'create', 'list', 'clean', 'auto'],
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
