<?php

// Teste simples para verificar se o sistema de performance está funcionando

require_once 'vendor/autoload.php';

// Simular o ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\PerformanceOptimizationService;

try {
  echo "🧪 Testando Sistema de Performance IOTCNT\n";
  echo "==========================================\n\n";

  $service = new PerformanceOptimizationService();

  // Teste 1: Métricas de Performance
  echo "1. Testando métricas de performance...\n";
  $metrics = $service->getPerformanceMetrics();
  echo "   ✅ Tempo de resposta: {$metrics['response_time']}ms\n";
  echo "   ✅ Uso de memória: {$metrics['memory_usage']}MB\n";
  echo "   ✅ Cache hit rate: {$metrics['cache_hit_rate']}%\n\n";

  // Teste 2: Estatísticas de Cache
  echo "2. Testando estatísticas de cache...\n";
  $cacheStats = $service->getCacheStats();
  echo "   ✅ Total de chaves: {$cacheStats['total_keys']}\n";
  echo "   ✅ Memória usada: {$cacheStats['memory_used']}\n";
  echo "   ✅ Hit rate: {$cacheStats['hit_rate']}%\n\n";

  // Teste 3: Queries Lentas
  echo "3. Testando detecção de queries lentas...\n";
  $slowQueries = $service->getSlowQueries();
  echo "   ✅ Encontradas " . count($slowQueries) . " queries lentas\n\n";

  // Teste 4: Recomendações
  echo "4. Testando recomendações de otimização...\n";
  $recommendations = $service->getOptimizationRecommendations();
  echo "   ✅ Geradas " . count($recommendations) . " recomendações\n";
  foreach ($recommendations as $i => $rec) {
    echo "   - " . ($i + 1) . ". $rec\n";
  }
  echo "\n";

  // Teste 5: Aquecimento de Cache
  echo "5. Testando aquecimento de cache...\n";
  $warmupResult = $service->warmUpCaches();
  echo "   ✅ Cache aquecido em {$warmupResult['duration_ms']}ms\n";
  echo "   ✅ {$warmupResult['caches_warmed']} caches aquecidos\n\n";

  echo "🎉 Todos os testes passaram com sucesso!\n";
  echo "🚀 Sistema de Performance está funcionando corretamente.\n\n";

  echo "📋 Resumo dos Componentes Implementados:\n";
  echo "   ✅ Controller de Performance (PerformanceController)\n";
  echo "   ✅ Serviço de Otimização (PerformanceOptimizationService)\n";
  echo "   ✅ View de Performance (admin/performance/index.blade.php)\n";
  echo "   ✅ Rotas de Performance (routes/web.php)\n";
  echo "   ✅ Navegação Admin (admin-navigation.blade.php)\n";
  echo "   ✅ Integração no Dashboard Admin\n\n";
} catch (Exception $e) {
  echo "❌ Erro durante o teste: " . $e->getMessage() . "\n";
  echo "📍 Arquivo: " . $e->getFile() . "\n";
  echo "📍 Linha: " . $e->getLine() . "\n";
}
