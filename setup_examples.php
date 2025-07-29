<?php

echo "🔧 IOTCNT - Setup de Arquivos de Exemplo\n";
echo "=========================================\n\n";

/**
 * Lista de arquivos de exemplo e seus destinos
 */
$exampleFiles = [
  // Configurações principais
  '.env.example' => '.env',
  'esp32_irrigation_controller/config.example.h' => 'esp32_irrigation_controller/config.h',

  // Configurações avançadas
  'config/secrets.example.php' => 'config/secrets.php',

  // Dados de exemplo
  'storage/users.example.json' => 'storage/users.json',
  'storage/app/data/users.example.json' => 'storage/app/data/users.json',

  // Configurações de servidor
  '.htaccess.example' => '.htaccess',
  'robots.example.txt' => 'robots.txt',

  // Docker (opcionais)
  'docker-compose.example.yml' => 'docker-compose.yml',
  'docker-compose.override.example.yml' => 'docker-compose.override.yml',
  'docker/mysql/my.example.cnf' => 'docker/mysql/my.cnf',
  'docker/nginx/conf.d/app.example.conf' => 'docker/nginx/conf.d/app.conf',
  'docker/redis/redis.example.conf' => 'docker/redis/redis.conf',
];

$copiedFiles = [];
$skippedFiles = [];
$errors = [];

foreach ($exampleFiles as $source => $destination) {
  echo "📋 Processando: $source -> $destination\n";

  // Verificar se o arquivo de exemplo existe
  if (!file_exists($source)) {
    echo "   ⚠️  Arquivo de exemplo não encontrado: $source\n";
    $errors[] = "Arquivo de exemplo não encontrado: $source";
    continue;
  }

  // Verificar se o destino já existe
  if (file_exists($destination)) {
    echo "   ⏭️  Arquivo já existe, pulando: $destination\n";
    $skippedFiles[] = $destination;
    continue;
  }

  // Criar diretório de destino se não existir
  $destinationDir = dirname($destination);
  if (!is_dir($destinationDir)) {
    if (!mkdir($destinationDir, 0755, true)) {
      echo "   ❌ Erro ao criar diretório: $destinationDir\n";
      $errors[] = "Erro ao criar diretório: $destinationDir";
      continue;
    }
    echo "   📁 Diretório criado: $destinationDir\n";
  }

  // Copiar arquivo
  if (copy($source, $destination)) {
    echo "   ✅ Copiado com sucesso!\n";
    $copiedFiles[] = $destination;
  } else {
    echo "   ❌ Erro ao copiar arquivo\n";
    $errors[] = "Erro ao copiar: $source -> $destination";
  }

  echo "\n";
}

// Resumo
echo "📊 RESUMO DO SETUP\n";
echo "==================\n\n";

echo "✅ Arquivos copiados (" . count($copiedFiles) . "):\n";
foreach ($copiedFiles as $file) {
  echo "   - $file\n";
}

if (!empty($skippedFiles)) {
  echo "\n⏭️  Arquivos pulados (" . count($skippedFiles) . "):\n";
  foreach ($skippedFiles as $file) {
    echo "   - $file (já existe)\n";
  }
}

if (!empty($errors)) {
  echo "\n❌ Erros encontrados (" . count($errors) . "):\n";
  foreach ($errors as $error) {
    echo "   - $error\n";
  }
}

echo "\n🔐 PRÓXIMOS PASSOS:\n";
echo "===================\n";
echo "1. 🔑 Execute: php secure_setup.php (para gerar credenciais seguras)\n";
echo "2. ✏️  Edite os arquivos copiados com suas configurações reais\n";
echo "3. 🚀 Execute: php artisan serve (para iniciar o servidor)\n";
echo "4. 🌐 Acesse: http://localhost:8000\n\n";

echo "⚠️  IMPORTANTE:\n";
echo "- NUNCA commite os arquivos copiados (.env, config.h, etc.)\n";
echo "- Use senhas fortes e tokens únicos\n";
echo "- Para produção, configure HTTPS\n\n";

if (empty($errors)) {
  echo "🎉 Setup de arquivos de exemplo concluído com sucesso!\n";
  exit(0);
} else {
  echo "⚠️  Setup concluído com alguns erros. Verifique os problemas acima.\n";
  exit(1);
}
