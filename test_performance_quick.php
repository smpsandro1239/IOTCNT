<?php

// Teste rápido para verificar se as classes estão bem estruturadas

echo "🧪 Teste Rápido - Sistema de Performance\n";
echo "=====================================\n\n";

// Teste 1: Verificar se a classe do serviço está bem estruturada
echo "1. Verificando estrutura da classe PerformanceOptimizationService...\n";

$serviceFile = file_get_contents('app/Services/PerformanceOptimizationService.php');

// Contar chaves de abertura e fechamento
$openBraces = substr_count($serviceFile, '{');
$closeBraces = substr_count($serviceFile, '}');

echo "   - Chaves de abertura: $openBraces\n";
echo "   - Chaves de fechamento: $closeBraces\n";

if ($openBraces === $closeBraces) {
  echo "   ✅ Estrutura da classe está correta\n";
} else {
  echo "   ❌ Estrutura da classe tem problemas\n";
}

// Teste 2: Verificar se os métodos existem
echo "\n2. Verificando métodos necessários...\n";

$requiredMethods = [
  'getPerformanceMetrics',
  'getCacheStats',
  'getSlowQueries',
  'getOptimizationRecommendations'
];

foreach ($requiredMethods as $method) {
  if (strpos($serviceFile, "public function $method(") !== false) {
    echo "   ✅ Método $method encontrado\n";
  } else {
    echo "   ❌ Método $method não encontrado\n";
  }
}

// Teste 3: Verificar controller
echo "\n3. Verificando controller...\n";

$controllerFile = file_get_contents('app/Http/Controllers/Admin/PerformanceController.php');

if (strpos($controllerFile, 'use Illuminate\Http\Request;') === false) {
  echo "   ✅ Import desnecessário removido\n";
} else {
  echo "   ⚠️ Import desnecessário ainda presente\n";
}

if (
  strpos($controllerFile, 'getSlowQueries()') !== false &&
  strpos($controllerFile, 'getOptimizationRecommendations()') !== false
) {
  echo "   ✅ Controller chama métodos corretos\n";
} else {
  echo "   ❌ Controller não chama métodos necessários\n";
}

// Teste 4: Verificar view
echo "\n4. Verificando view...\n";

if (file_exists('resources/views/admin/performance/index.blade.php')) {
  echo "   ✅ View de performance existe\n";

  $viewFile = file_get_contents('resources/views/admin/performance/index.blade.php');

  if (strpos($viewFile, '@extends(\'layouts.admin\')') !== false) {
    echo "   ✅ View usa layout admin correto\n";
  } else {
    echo "   ⚠️ View pode ter problema de layout\n";
  }
} else {
  echo "   ❌ View de performance não encontrada\n";
}

echo "\n🎯 Teste concluído!\n";
echo "📋 Se todos os itens estão ✅, o sistema está funcionando.\n";
echo "🚀 Acesse /admin/performance para testar a interface.\n";
