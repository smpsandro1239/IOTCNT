<?php

echo "=== IOTCNT Quick Setup ===\n";

// Try different MySQL configurations
$configs = [
  ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],
  ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root'],
  ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '123456'],
  ['host' => 'localhost', 'user' => 'root', 'pass' => ''],
  ['host' => 'localhost', 'user' => 'root', 'pass' => 'root'],
];

$pdo = null;
$workingConfig = null;

foreach ($configs as $config) {
  try {
    echo "Trying MySQL connection: {$config['user']}@{$config['host']} with password: " . ($config['pass'] ? 'YES' : 'NO') . "\n";
    $pdo = new PDO("mysql:host={$config['host']}", $config['user'], $config['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $workingConfig = $config;
    echo "✓ MySQL connection successful!\n";
    break;
  } catch (PDOException $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n";
    continue;
  }
}

if (!$pdo) {
  echo "\n❌ Could not connect to MySQL. Please:\n";
  echo "1. Start Laragon\n";
  echo "2. Make sure MySQL is running\n";
  echo "3. Check MySQL credentials\n";
  exit(1);
}

try {
  // Create database
  echo "\nCreating database 'iotcnt'...\n";
  $pdo->exec("CREATE DATABASE IF NOT EXISTS iotcnt");
  echo "✓ Database created successfully\n";

  // Update .env file
  echo "\nUpdating .env file...\n";
  $envContent = file_get_contents('.env');
  $envContent = preg_replace('/DB_HOST=.*/', "DB_HOST={$workingConfig['host']}", $envContent);
  $envContent = preg_replace('/DB_USERNAME=.*/', "DB_USERNAME={$workingConfig['user']}", $envContent);
  $envContent = preg_replace('/DB_PASSWORD=.*/', "DB_PASSWORD={$workingConfig['pass']}", $envContent);
  file_put_contents('.env', $envContent);
  echo "✓ .env file updated\n";

  // Connect to the iotcnt database
  $pdo = new PDO("mysql:host={$workingConfig['host']};dbname=iotcnt", $workingConfig['user'], $workingConfig['pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Create users table
  echo "\nCreating users table...\n";
  $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
  echo "✓ Users table created\n";

  // Create valves table
  echo "Creating valves table...\n";
  $pdo->exec("
        CREATE TABLE IF NOT EXISTS valves (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            pin_number INT NOT NULL,
            is_active BOOLEAN DEFAULT FALSE,
            duration INT DEFAULT 5,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
  echo "✓ Valves table created\n";

  // Create admin user
  echo "\nCreating admin user...\n";
  $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        password = VALUES(password),
        role = VALUES(role)
    ");

  $stmt->execute(['Administrator', 'admin@iotcnt.local', $hashedPassword, 'admin']);
  echo "✓ Admin user created/updated\n";

  // Create sample valves
  echo "\nCreating sample valves...\n";
  $valves = [
    ['Válvula 1 - Jardim Principal', 'Rega do jardim principal da frente', 2],
    ['Válvula 2 - Horta', 'Sistema de rega da horta', 3],
    ['Válvula 3 - Relvado', 'Aspersores do relvado', 4],
    ['Válvula 4 - Vasos', 'Rega dos vasos da varanda', 5],
    ['Válvula 5 - Estufa', 'Sistema de rega da estufa', 6],
  ];

  $stmt = $pdo->prepare("
        INSERT INTO valves (name, description, pin_number, duration)
        VALUES (?, ?, ?, 5)
        ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        description = VALUES(description)
    ");

  foreach ($valves as $valve) {
    $stmt->execute($valve);
  }
  echo "✓ Sample valves created\n";

  echo "\n🎉 Setup completed successfully!\n\n";
  echo "=== ACCESS INFORMATION ===\n";
  echo "URL: http://localhost:8000\n";
  echo "Login: admin@iotcnt.local\n";
  echo "Password: admin123\n\n";
  echo "To start the server, run:\n";
  echo "php artisan serve\n\n";
} catch (Exception $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
  exit(1);
}
