<?php

echo "=== IOTCNT Simple Setup (File-based) ===\n";

// Create storage directories
$dirs = ['storage/app', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs'];
foreach ($dirs as $dir) {
  if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    echo "✓ Created directory: $dir\n";
  }
}

// Create simple JSON database files
$dataDir = 'storage/app/data';
if (!is_dir($dataDir)) {
  mkdir($dataDir, 0755, true);
  echo "✓ Created data directory\n";
}

// Create users.json
$users = [
  [
    'id' => 1,
    'name' => 'Administrator',
    'email' => 'admin@iotcnt.local',
    'password' => password_hash('admin123', PASSWORD_DEFAULT),
    'role' => 'admin',
    'created_at' => date('Y-m-d H:i:s')
  ]
];
file_put_contents($dataDir . '/users.json', json_encode($users, JSON_PRETTY_PRINT));
echo "✓ Created users database\n";

// Create valves.json
$valves = [
  ['id' => 1, 'name' => 'Válvula 1 - Jardim Principal', 'description' => 'Rega do jardim principal da frente', 'pin_number' => 2, 'is_active' => false, 'duration' => 5],
  ['id' => 2, 'name' => 'Válvula 2 - Horta', 'description' => 'Sistema de rega da horta', 'pin_number' => 3, 'is_active' => false, 'duration' => 5],
  ['id' => 3, 'name' => 'Válvula 3 - Relvado', 'description' => 'Aspersores do relvado', 'pin_number' => 4, 'is_active' => false, 'duration' => 5],
  ['id' => 4, 'name' => 'Válvula 4 - Vasos', 'description' => 'Rega dos vasos da varanda', 'pin_number' => 5, 'is_active' => false, 'duration' => 5],
  ['id' => 5, 'name' => 'Válvula 5 - Estufa', 'description' => 'Sistema de rega da estufa', 'pin_number' => 6, 'is_active' => false, 'duration' => 5],
];
file_put_contents($dataDir . '/valves.json', json_encode($valves, JSON_PRETTY_PRINT));
echo "✓ Created valves database\n";

// Create empty logs.json
file_put_contents($dataDir . '/logs.json', json_encode([], JSON_PRETTY_PRINT));
echo "✓ Created logs database\n";

// Update .env to use file driver
$envContent = file_get_contents('.env');
$envContent = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=file', $envContent);
file_put_contents('.env', $envContent);
echo "✓ Updated .env for file-based storage\n";

echo "\n🎉 Simple setup completed!\n\n";
echo "=== ACCESS INFORMATION ===\n";
echo "URL: http://localhost:8000\n";
echo "Login: admin@iotcnt.local\n";
echo "Password: admin123\n\n";
echo "To start the server, run:\n";
echo "php artisan serve\n\n";
echo "Note: This is a simplified version using file storage.\n";
echo "For production, configure a proper database.\n\n";
