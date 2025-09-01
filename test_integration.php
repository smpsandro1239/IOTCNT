<?php

/**
 * Script de teste para verificar integração Laravel-HTML
 */

echo "🧪 TESTE DE INTEGRAÇÃO LARAVEL-HTML\n";
echo "=====================================\n\n";

// Verificar se Laravel está funcionando
echo "1. Testando Laravel...\n";
try {
  $response = file_get_contents('http://localhost:8080/auth/status');
  if ($response !== false) {
    $data = json_decode($response, true);
    echo "   ✅ Laravel respondendo: " . ($data ? "OK" : "Erro no JSON") . "\n";
  } else {
    echo "   ❌ Laravel não está respondendo\n";
  }
} catch (Exception $e) {
  echo "   ❌ Erro ao conectar com Laravel: " . $e->getMessage() . "\n";
}

// Verificar se páginas HTML estão acessíveis
echo "\n2. Testando páginas HTML...\n";
$htmlPages = [
  'index-iotcnt.html',
  'login-iotcnt.html',
  'dashboard-admin.html',
  'dashboard-user.html'
];

foreach ($htmlPages as $page) {
  try {
    $headers = get_headers("http://localhost:8080/$page");
    if ($headers && strpos($headers[0], '200') !== false) {
      echo "   ✅ $page: Acessível\n";
    } else {
      echo "   ❌ $page: Não acessível\n";
    }
  } catch (Exception $e) {
    echo "   ❌ $page: Erro - " . $e->getMessage() . "\n";
  }
}

// Verificar se API está funcionando
echo "\n3. Testando API...\n";
try {
  $response = file_get_contents('http://localhost:8080/api.php');
  if ($response !== false) {
    echo "   ✅ API PHP: Funcionando\n";
  } else {
    echo "   ❌ API PHP: Não está respondendo\n";
  }
} catch (Exception $e) {
  echo "   ❌ API PHP: Erro - " . $e->getMessage() . "\n";
}

// Verificar CSRF endpoint
echo "\n4. Testando CSRF...\n";
try {
  $response = file_get_contents('http://localhost:8080/auth/csrf');
  if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['token'])) {
      echo "   ✅ CSRF Token: Funcionando\n";
    } else {
      echo "   ❌ CSRF Token: Formato inválido\n";
    }
  } else {
    echo "   ❌ CSRF Token: Não está respondendo\n";
  }
} catch (Exception $e) {
  echo "   ❌ CSRF Token: Erro - " . $e->getMessage() . "\n";
}

// Teste de autenticação
echo "\n5. Testando autenticação...\n";
try {
  $postData = json_encode([
    'email' => 'admin@iotcnt.local',
    'password' => 'password'
  ]);

  $context = stream_context_create([
    'http' => [
      'method' => 'POST',
      'header' => [
        'Content-Type: application/json',
        'Accept: application/json'
      ],
      'content' => $postData
    ]
  ]);

  $response = file_get_contents('http://localhost:8080/auth/login', false, $context);
  if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
      echo "   ✅ Autenticação: " . ($data['success'] ? "Funcionando" : "Credenciais rejeitadas") . "\n";
    } else {
      echo "   ❌ Autenticação: Resposta inválida\n";
    }
  } else {
    echo "   ❌ Autenticação: Não está respondendo\n";
  }
} catch (Exception $e) {
  echo "   ❌ Autenticação: Erro - " . $e->getMessage() . "\n";
}

echo "\n=====================================\n";
echo "🎯 RESUMO DO TESTE:\n";
echo "- Laravel: Verificar se está rodando\n";
echo "- HTML: Verificar se páginas carregam\n";
echo "- API: Verificar se endpoints respondem\n";
echo "- Auth: Verificar se login funciona\n\n";

echo "💡 Para testar manualmente:\n";
echo "1. Acesse http://localhost:8080/\n";
echo "2. Faça login com admin@iotcnt.local / password\n";
echo "3. Verifique se redireciona para dashboard\n";
echo "4. Teste o botão de logout\n\n";

echo "🚀 Se todos os testes passaram, a integração está funcionando!\n";
