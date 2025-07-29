<?php

echo "🔒 IOTCNT - Setup Seguro\n";
echo "========================\n\n";

// Verificar se .env existe
if (!file_exists('.env')) {
  echo "⚠️  Arquivo .env não encontrado!\n";
  echo "📋 Copiando .env.example para .env...\n";

  if (file_exists('.env.example')) {
    copy('.env.example', '.env');
    echo "✅ Arquivo .env criado com sucesso!\n\n";
  } else {
    echo "❌ Arquivo .env.example não encontrado!\n";
    exit(1);
  }
} else {
  echo "✅ Arquivo .env já existe\n\n";
}

// Verificar se config.h existe
if (!file_exists('esp32_irrigation_controller/config.h')) {
  echo "⚠️  Arquivo ESP32 config.h não encontrado!\n";
  echo "📋 Copiando config.example.h para config.h...\n";

  if (file_exists('esp32_irrigation_controller/config.example.h')) {
    copy('esp32_irrigation_controller/config.example.h', 'esp32_irrigation_controller/config.h');
    echo "✅ Arquivo config.h criado com sucesso!\n\n";
  } else {
    echo "❌ Arquivo config.example.h não encontrado!\n";
    exit(1);
  }
} else {
  echo "✅ Arquivo ESP32 config.h já existe\n\n";
}

// Gerar tokens seguros
echo "🔐 Gerando credenciais seguras...\n";

function generateSecureToken($length = 64)
{
  return bin2hex(random_bytes($length / 2));
}

function generateBase64Key($length = 32)
{
  return base64_encode(random_bytes($length));
}

// Gerar credenciais
$appKey = 'base64:' . generateBase64Key();
$telegramToken = generateSecureToken(32) . ':' . generateSecureToken(32);
$esp32Token = generateSecureToken(64);
$encryptionKey = generateSecureToken(64);

echo "✅ Credenciais geradas com sucesso!\n\n";

// Atualizar .env com credenciais seguras
$envContent = file_get_contents('.env');

// Substituir valores vazios por credenciais geradas
$envContent = preg_replace('/^APP_KEY=$/m', "APP_KEY=$appKey", $envContent);
$envContent = preg_replace('/^TELEGRAM_BOT_TOKEN=$/m', "TELEGRAM_BOT_TOKEN=$telegramToken", $envContent);
$envContent = preg_replace('/^ESP32_API_TOKEN=$/m', "ESP32_API_TOKEN=$esp32Token", $envContent);
$envContent = preg_replace('/^ENCRYPTION_KEY=$/m', "ENCRYPTION_KEY=$encryptionKey", $envContent);

file_put_contents('.env', $envContent);

echo "🔧 Configurações aplicadas no .env\n\n";

// Criar diretórios necessários
$directories = [
  'storage/app/data',
  'storage/framework/cache',
  'storage/framework/sessions',
  'storage/framework/views',
  'storage/logs'
];

foreach ($directories as $dir) {
  if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    echo "📁 Criado diretório: $dir\n";
  }
}

// Criar utilizador admin com senha aleatória
$adminPassword = generateSecureToken(16);
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

$users = [
  [
    'id' => 1,
    'name' => 'Administrator',
    'email' => 'admin@iotcnt.local',
    'password' => $hashedPassword,
    'role' => 'admin',
    'created_at' => date('Y-m-d H:i:s')
  ]
];

// Criar arquivo de utilizadores seguro
if (!is_dir('storage/app/data')) {
  mkdir('storage/app/data', 0755, true);
}

file_put_contents('storage/app/data/users.json', json_encode($users, JSON_PRETTY_PRINT));

// Criar outros arquivos de dados
file_put_contents('storage/app/data/valves.json', json_encode([
  ['id' => 1, 'name' => 'Válvula 1 - Jardim Principal', 'description' => 'Rega do jardim principal da frente', 'pin_number' => 2, 'is_active' => false, 'duration' => 5],
  ['id' => 2, 'name' => 'Válvula 2 - Horta', 'description' => 'Sistema de rega da horta', 'pin_number' => 3, 'is_active' => false, 'duration' => 5],
  ['id' => 3, 'name' => 'Válvula 3 - Relvado', 'description' => 'Aspersores do relvado', 'pin_number' => 4, 'is_active' => false, 'duration' => 5],
  ['id' => 4, 'name' => 'Válvula 4 - Vasos', 'description' => 'Rega dos vasos da varanda', 'pin_number' => 5, 'is_active' => false, 'duration' => 5],
  ['id' => 5, 'name' => 'Válvula 5 - Estufa', 'description' => 'Sistema de rega da estufa', 'pin_number' => 6, 'is_active' => false, 'duration' => 5],
], JSON_PRETTY_PRINT));

file_put_contents('storage/app/data/logs.json', json_encode([], JSON_PRETTY_PRINT));

echo "\n🎉 SETUP SEGURO CONCLUÍDO!\n";
echo "==========================\n\n";

echo "📋 CREDENCIAIS GERADAS:\n";
echo "------------------------\n";
echo "🌐 URL: http://localhost:8000\n";
echo "👤 Login: admin@iotcnt.local\n";
echo "🔑 Password: $adminPassword\n\n";

echo "🔐 TOKENS GERADOS:\n";
echo "-------------------\n";
echo "📱 Telegram Bot Token: $telegramToken\n";
echo "🤖 ESP32 API Token: $esp32Token\n\n";

echo "⚠️  IMPORTANTE - PRÓXIMOS PASSOS:\n";
echo "==================================\n";
echo "1. 📝 ANOTE as credenciais acima em local seguro\n";
echo "2. 🤖 Configure o bot Telegram com @BotFather\n";
echo "3. 🔧 Edite esp32_irrigation_controller/config.h com suas credenciais WiFi\n";
echo "4. 🚀 Execute: php artisan serve\n";
echo "5. 🔒 NUNCA commite arquivos .env ou config.h\n\n";

echo "📖 Para mais informações de segurança, consulte SECURITY_SETUP.md\n\n";

// Salvar credenciais em arquivo temporário seguro
$credentialsFile = 'CREDENTIALS_' . date('Y-m-d_H-i-s') . '.txt';
$credentialsContent = "IOTCNT - Credenciais Geradas em " . date('Y-m-d H:i:s') . "\n";
$credentialsContent .= "=================================================\n\n";
$credentialsContent .= "Login Web:\n";
$credentialsContent .= "URL: http://localhost:8000\n";
$credentialsContent .= "Email: admin@iotcnt.local\n";
$credentialsContent .= "Password: $adminPassword\n\n";
$credentialsContent .= "Tokens:\n";
$credentialsContent .= "Telegram Bot Token: $telegramToken\n";
$credentialsContent .= "ESP32 API Token: $esp32Token\n\n";
$credentialsContent .= "IMPORTANTE: Guarde este arquivo em local seguro e delete após anotar as credenciais!\n";

file_put_contents($credentialsFile, $credentialsContent);

echo "💾 Credenciais salvas em: $credentialsFile\n";
echo "⚠️  DELETE este arquivo após anotar as credenciais!\n\n";
