<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PerformanceOptimizationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
{
  protected $performanceService;

  public function __construct(PerformanceOptimizationService $performanceService)
  {
    $this->performanceService = $performanceService;
  }

  /**
   * Display performance dashboard.
   */
  public function index()
  {
    $metrics = $this->performanceService->getPerformanceMetrics();
    $cacheStats = $this->performanceService->getCacheStats();
    $slowQueries = $this->performanceService->getSlowQueries();
    $recommendations = $this->performanceService->getOptimizationRecommendations();

    // Get performance logs (últimos 50 registros)
    $performanceLogs = DB::table('operation_logs')
      ->where('action', 'LIKE', '%performance%')
      ->orWhere('action', 'LIKE', '%optimization%')
      ->orWhere('action', 'LIKE', '%cache%')
      ->orderBy('created_at', 'desc')
      ->limit(50)
      ->get()
      ->map(function ($log) {
        $log->status = str_contains($log->details, 'success') ? 'success' : 'error';
        $log->created_at = \Carbon\Carbon::parse($log->created_at);
        return $log;
      });

    return view('admin.performance.index', compact(
      'metrics',
      'cacheStats',
      'slowQueries',
      'recommendations',
      'performanceLogs'
    ));
  }

  /**
   * Get performance metrics via API.
   */
  public function getMetrics()
  {
    try {
      $metrics = $this->performanceService->getPerformanceMetrics();

      return response()->json([
        'success' => true,
        'metrics' => $metrics
      ]);
    } catch (\Exception $e) {
      Log::error('Error getting performance metrics', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao obter métricas de performance'
      ], 500);
    }
  }

  /**
   * Clear all caches.
   */
  public function clearCache()
  {
    try {
      $this->performanceService->invalidateAllCaches();

      // Also clear Laravel caches
      Artisan::call('cache:clear');
      Artisan::call('config:clear');
      Artisan::call('route:clear');
      Artisan::call('view:clear');

      Log::info('All caches cleared by admin', ['user_id' => auth()->id()]);

      return response()->json([
        'success' => true,
        'message' => 'Todas as caches foram limpas com sucesso'
      ]);
    } catch (\Exception $e) {
      Log::error('Error clearing caches', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao limpar caches: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Warm up caches.
   */
  public function warmUpCache()
  {
    try {
      $result = $this->performanceService->warmUpCaches();

      Log::info('Cache warm-up initiated by admin', ['user_id' => auth()->id()]);

      return response()->json([
        'success' => true,
        'message' => 'Caches aquecidas com sucesso',
        'details' => $result
      ]);
    } catch (\Exception $e) {
      Log::error('Error warming up caches', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao aquecer caches: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Optimize database.
   */
  public function optimizeDatabase()
  {
    try {
      $result = $this->performanceService->optimizeDatabase();

      Log::info('Database optimization initiated by admin', [
        'user_id' => auth()->id(),
        'result' => $result
      ]);

      return response()->json([
        'success' => $result['success'],
        'message' => $result['success'] ?
          'Base de dados otimizada com sucesso' :
          'Erro na otimização da base de dados',
        'details' => $result
      ]);
    } catch (\Exception $e) {
      Log::error('Error optimizing database', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao otimizar base de dados: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Run full optimization.
   */
  public function runFullOptimization()
  {
    try {
      $result = $this->performanceService->runFullOptimization();

      Log::info('Full optimization initiated by admin', [
        'user_id' => auth()->id(),
        'result' => $result
      ]);

      return response()->json([
        'success' => $result['success'],
        'message' => $result['success'] ?
          'Otimização completa executada com sucesso' :
          'Erro na otimização completa',
        'details' => $result
      ]);
    } catch (\Exception $e) {
      Log::error('Error running full optimization', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro na otimização completa: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get cache statistics.
   */
  public function getCacheStats()
  {
    try {
      $stats = $this->performanceService->getCacheStats();

      return response()->json([
        'success' => true,
        'cache_stats' => $stats
      ]);
    } catch (\Exception $e) {
      Log::error('Error getting cache stats', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao obter estatísticas de cache'
      ], 500);
    }
  }

  /**
   * Get database statistics.
   */
  public function getDatabaseStats()
  {
    try {
      $stats = [
        'tables' => [],
        'total_size_mb' => 0,
        'total_records' => 0
      ];

      $tables = ['users', 'valves', 'schedules', 'operation_logs', 'system_settings', 'telegram_users'];

      foreach ($tables as $table) {
        try {
          $count = DB::table($table)->count();
          $size = DB::select("SELECT
                        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                        FROM information_schema.TABLES
                        WHERE table_schema = DATABASE()
                        AND table_name = ?", [$table]);

          $sizeInMb = $size[0]->size_mb ?? 0;

          $stats['tables'][$table] = [
            'records' => $count,
            'size_mb' => $sizeInMb
          ];

          $stats['total_records'] += $count;
          $stats['total_size_mb'] += $sizeInMb;
        } catch (\Exception $e) {
          $stats['tables'][$table] = [
            'records' => 'N/A',
            'size_mb' => 'N/A',
            'error' => $e->getMessage()
          ];
        }
      }

      $stats['total_size_mb'] = round($stats['total_size_mb'], 2);

      return response()->json([
        'success' => true,
        'database_stats' => $stats
      ]);
    } catch (\Exception $e) {
      Log::error('Error getting database stats', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao obter estatísticas da base de dados'
      ], 500);
    }
  }

  /**
   * Clean old logs.
   */
  public function cleanOldLogs()
  {
    try {
      $retentionDays = request('retention_days', 90);

      if ($retentionDays < 7) {
        return response()->json([
          'success' => false,
          'message' => 'Período de retenção deve ser pelo menos 7 dias'
        ], 422);
      }

      $cutoffDate = now()->subDays($retentionDays);
      $deletedCount = DB::table('operation_logs')
        ->where('created_at', '<', $cutoffDate)
        ->delete();

      Log::info('Old logs cleaned by admin', [
        'user_id' => auth()->id(),
        'retention_days' => $retentionDays,
        'deleted_count' => $deletedCount
      ]);

      return response()->json([
        'success' => true,
        'message' => "Foram eliminados {$deletedCount} registos antigos",
        'deleted_count' => $deletedCount
      ]);
    } catch (\Exception $e) {
      Log::error('Error cleaning old logs', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao limpar logs antigos: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get system resource usage.
   */
  public function getSystemResources()
  {
    try {
      $resources = [
        'memory' => [
          'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
          'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
          'limit' => ini_get('memory_limit')
        ],
        'php' => [
          'version' => PHP_VERSION,
          'max_execution_time' => ini_get('max_execution_time'),
          'upload_max_filesize' => ini_get('upload_max_filesize'),
          'post_max_size' => ini_get('post_max_size')
        ],
        'server' => [
          'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
          'php_sapi' => PHP_SAPI,
          'os' => PHP_OS
        ],
        'laravel' => [
          'version' => app()->version(),
          'environment' => app()->environment(),
          'debug' => config('app.debug'),
          'timezone' => config('app.timezone')
        ]
      ];

      // Try to get disk usage (may not work on all systems)
      try {
        $diskFree = disk_free_space('.');
        $diskTotal = disk_total_space('.');

        if ($diskFree !== false && $diskTotal !== false) {
          $resources['disk'] = [
            'free_gb' => round($diskFree / 1024 / 1024 / 1024, 2),
            'total_gb' => round($diskTotal / 1024 / 1024 / 1024, 2),
            'used_percent' => round((($diskTotal - $diskFree) / $diskTotal) * 100, 1)
          ];
        }
      } catch (\Exception $e) {
        $resources['disk'] = ['error' => 'Unable to get disk usage'];
      }

      return response()->json([
        'success' => true,
        'resources' => $resources
      ]);
    } catch (\Exception $e) {
      Log::error('Error getting system resources', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao obter recursos do sistema'
      ], 500);
    }
  }

  /**
   * Test system performance.
   */
  public function testPerformance()
  {
    try {
      $tests = [];

      // Test 1: Database query performance
      $start = microtime(true);
      DB::table('valves')->count();
      $tests['database_query'] = [
        'name' => 'Database Query Test',
        'duration_ms' => round((microtime(true) - $start) * 1000, 2),
        'status' => 'completed'
      ];

      // Test 2: Cache performance
      $start = microtime(true);
      Cache::put('performance_test', 'test_value', 60);
      $value = Cache::get('performance_test');
      Cache::forget('performance_test');
      $tests['cache_operations'] = [
        'name' => 'Cache Operations Test',
        'duration_ms' => round((microtime(true) - $start) * 1000, 2),
        'status' => $value === 'test_value' ? 'completed' : 'failed'
      ];

      // Test 3: File system performance
      $start = microtime(true);
      $testFile = storage_path('app/performance_test.txt');
      file_put_contents($testFile, 'performance test content');
      $content = file_get_contents($testFile);
      unlink($testFile);
      $tests['filesystem'] = [
        'name' => 'File System Test',
        'duration_ms' => round((microtime(true) - $start) * 1000, 2),
        'status' => $content === 'performance test content' ? 'completed' : 'failed'
      ];

      // Test 4: JSON processing
      $start = microtime(true);
      $data = ['test' => 'data', 'numbers' => range(1, 1000)];
      $json = json_encode($data);
      $decoded = json_decode($json, true);
      $tests['json_processing'] = [
        'name' => 'JSON Processing Test',
        'duration_ms' => round((microtime(true) - $start) * 1000, 2),
        'status' => $decoded['test'] === 'data' ? 'completed' : 'failed'
      ];

      $totalDuration = array_sum(array_column($tests, 'duration_ms'));
      $passedTests = count(array_filter($tests, fn($test) => $test['status'] === 'completed'));

      Log::info('Performance test completed by admin', [
        'user_id' => auth()->id(),
        'total_duration_ms' => $totalDuration,
        'passed_tests' => $passedTests,
        'total_tests' => count($tests)
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Teste de performance concluído',
        'summary' => [
          'total_duration_ms' => round($totalDuration, 2),
          'passed_tests' => $passedTests,
          'total_tests' => count($tests),
          'success_rate' => round(($passedTests / count($tests)) * 100, 1) . '%'
        ],
        'tests' => $tests
      ]);
    } catch (\Exception $e) {
      Log::error('Error running performance test', ['error' => $e->getMessage()]);

      return response()->json([
        'success' => false,
        'message' => 'Erro no teste de performance: ' . $e->getMessage()
      ], 500);
    }
  }
}
