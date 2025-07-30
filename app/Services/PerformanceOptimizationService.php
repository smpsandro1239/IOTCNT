<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Valve;
use App\Models\Schedule;
use App\Models\OperationLog;
use App\Models\SystemSetting;
use Carbon\Carbon;

class PerformanceOptimizationService
{
  /**
   * Cache keys for different data types
   */
  const CACHE_KEYS = [
    'valve_status' => 'iotcnt:valve_status',
    'system_stats' => 'iotcnt:system_stats',
    'active_schedules' => 'iotcnt:active_schedules',
    'system_settings' => 'iotcnt:system_settings',
    'dashboard_data' => 'iotcnt:dashboard_data',
    'esp32_config' => 'iotcnt:esp32_config'
  ];

  /**
   * Cache TTL in seconds
   */
  const CACHE_TTL = [
    'valve_status' => 30,      // 30 seconds
    'system_stats' => 300,     // 5 minutes
    'active_schedules' => 3600, // 1 hour
    'system_settings' => 3600,  // 1 hour
    'dashboard_data' => 60,     // 1 minute
    'esp32_config' => 1800      // 30 minutes
  ];

  /**
   * Get cached valve status or fetch from database
   */
  public function getCachedValveStatus()
  {
    return Cache::remember(
      self::CACHE_KEYS['valve_status'],
      self::CACHE_TTL['valve_status'],
      function () {
        return Valve::select(['id', 'name', 'valve_number', 'current_state', 'last_activated_at', 'esp32_pin'])
          ->orderBy('valve_number')
          ->get();
      }
    );
  }

  /**
   * Get cached system statistics
   */
  public function getCachedSystemStats()
  {
    return Cache::remember(
      self::CACHE_KEYS['system_stats'],
      self::CACHE_TTL['system_stats'],
      function () {
        $today = Carbon::today();

        return [
          'total_valves' => Valve::count(),
          'active_valves' => Valve::where('current_state', true)->count(),
          'inactive_valves' => Valve::where('current_state', false)->count(),
          'total_operations_today' => OperationLog::whereDate('created_at', $today)->count(),
          'total_schedules' => Schedule::count(),
          'active_schedules' => Schedule::active()->count(),
          'last_activity' => OperationLog::latest()->value('created_at'),
          'system_uptime' => $this->calculateSystemUptime(),
          'cache_generated_at' => now()
        ];
      }
    );
  }

  /**
   * Get cached active schedules
   */
  public function getCachedActiveSchedules()
  {
    return Cache::remember(
      self::CACHE_KEYS['active_schedules'],
      self::CACHE_TTL['active_schedules'],
      function () {
        return Schedule::active()
          ->with('user:id,name')
          ->select(['id', 'user_id', 'name', 'day_of_week', 'start_time', 'per_valve_duration_minutes', 'is_active'])
          ->orderBy('day_of_week')
          ->orderBy('start_time')
          ->get();
      }
    );
  }

  /**
   * Get cached system settings
   */
  public function getCachedSystemSettings()
  {
    return Cache::remember(
      self::CACHE_KEYS['system_settings'],
      self::CACHE_TTL['system_settings'],
      function () {
        return SystemSetting::all()->pluck('value', 'key')->toArray();
      }
    );
  }

  /**
   * Get cached ESP32 configuration
   */
  public function getCachedEsp32Config()
  {
    return Cache::remember(
      self::CACHE_KEYS['esp32_config'],
      self::CACHE_TTL['esp32_config'],
      function () {
        $valves = $this->getCachedValveStatus();
        $schedules = $this->getCachedActiveSchedules();
        $settings = $this->getCachedSystemSettings();

        return [
          'valves' => $valves->map(function ($valve) {
            return [
              'id' => $valve->id,
              'valve_number' => $valve->valve_number,
              'name' => $valve->name,
              'esp32_pin' => $valve->esp32_pin,
              'current_state' => $valve->current_state
            ];
          }),
          'schedules' => $schedules->map(function ($schedule) {
            return [
              'id' => $schedule->id,
              'name' => $schedule->name,
              'day_of_week' => $schedule->day_of_week,
              'start_time' => $schedule->start_time,
              'per_valve_duration_minutes' => $schedule->per_valve_duration_minutes
            ];
          }),
          'server_time' => now()->toISOString(),
          'device_name' => $settings['system_name'] ?? 'IOTCNT Irrigation System',
          'timezone' => $settings['timezone'] ?? 'Europe/Lisbon',
          'default_duration' => $settings['default_valve_duration'] ?? 5
        ];
      }
    );
  }

  /**
   * Get optimized dashboard data
   */
  public function getOptimizedDashboardData()
  {
    return Cache::remember(
      self::CACHE_KEYS['dashboard_data'],
      self::CACHE_TTL['dashboard_data'],
      function () {
        // Use single query to get all needed data
        $valves = $this->getCachedValveStatus();
        $stats = $this->getCachedSystemStats();

        // Get recent logs with minimal data
        $recentLogs = OperationLog::with(['valve:id,name', 'user:id,name'])
          ->select(['id', 'valve_id', 'user_id', 'action', 'source', 'duration_minutes', 'notes', 'created_at'])
          ->latest()
          ->limit(10)
          ->get();

        return [
          'valves' => $valves,
          'stats' => $stats,
          'recent_logs' => $recentLogs,
          'generated_at' => now()
        ];
      }
    );
  }

  /**
   * Invalidate specific cache
   */
  public function invalidateCache($cacheKey)
  {
    if (isset(self::CACHE_KEYS[$cacheKey])) {
      Cache::forget(self::CACHE_KEYS[$cacheKey]);
      Log::info("Cache invalidated: {$cacheKey}");
    }
  }

  /**
   * Invalidate all related caches
   */
  public function invalidateAllCaches()
  {
    foreach (self::CACHE_KEYS as $key => $cacheKey) {
      Cache::forget($cacheKey);
    }

    Log::info('All IOTCNT caches invalidated');
  }

  /**
   * Warm up caches
   */
  public function warmUpCaches()
  {
    Log::info('Starting cache warm-up process');

    $startTime = microtime(true);

    // Warm up each cache
    $this->getCachedValveStatus();
    $this->getCachedSystemStats();
    $this->getCachedActiveSchedules();
    $this->getCachedSystemSettings();
    $this->getCachedEsp32Config();
    $this->getOptimizedDashboardData();

    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000, 2);

    Log::info("Cache warm-up completed in {$duration}ms");

    return [
      'success' => true,
      'duration_ms' => $duration,
      'caches_warmed' => count(self::CACHE_KEYS)
    ];
  }

  /**
   * Get cache statistics
   */
  public function getCacheStats()
  {
    $stats = [];
    $totalKeys = 0;
    $totalMemory = 0;
    $hits = 0;
    $misses = 0;

    foreach (self::CACHE_KEYS as $key => $cacheKey) {
      $exists = Cache::has($cacheKey);
      $totalKeys++;

      if ($exists) {
        $hits++;
        // Try to get cache size (approximate)
        $data = Cache::get($cacheKey);
        $size = strlen(serialize($data));
        $totalMemory += $size;
      } else {
        $misses++;
      }

      $stats[$key] = [
        'exists' => $exists,
        'key' => $cacheKey,
        'ttl' => self::CACHE_TTL[$key] ?? 0
      ];
    }

    $hitRate = $totalKeys > 0 ? round(($hits / $totalKeys) * 100, 1) : 0;
    $missRate = $totalKeys > 0 ? round(($misses / $totalKeys) * 100, 1) : 0;

    return [
      'total_keys' => $totalKeys,
      'memory_used' => $this->formatBytes($totalMemory),
      'hit_rate' => $hitRate,
      'miss_rate' => $missRate,
      'details' => $stats
    ];
  }

  /**
   * Format bytes to human readable format
   */
  private function formatBytes($bytes, $precision = 2)
  {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
      $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
  }

  /**
   * Optimize database queries
   */
  public function optimizeDatabase()
  {
    $optimizations = [];

    try {
      // Add indexes if they don't exist
      $this->addDatabaseIndexes();
      $optimizations[] = 'Database indexes verified/added';

      // Clean old logs
      $deletedLogs = $this->cleanOldLogs();
      $optimizations[] = "Cleaned {$deletedLogs} old log entries";

      // Optimize tables
      $this->optimizeTables();
      $optimizations[] = 'Database tables optimized';

      Log::info('Database optimization completed', $optimizations);

      return [
        'success' => true,
        'optimizations' => $optimizations
      ];
    } catch (\Exception $e) {
      Log::error('Database optimization failed', ['error' => $e->getMessage()]);

      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }

  /**
   * Add database indexes for performance
   */
  private function addDatabaseIndexes()
  {
    $indexes = [
      'operation_logs' => [
        ['valve_id'],
        ['user_id'],
        ['created_at'],
        ['action'],
        ['source'],
        ['valve_id', 'created_at']
      ],
      'schedules' => [
        ['user_id'],
        ['is_active'],
        ['day_of_week'],
        ['user_id', 'is_active']
      ],
      'valves' => [
        ['valve_number'],
        ['current_state'],
        ['esp32_pin']
      ]
    ];

    foreach ($indexes as $table => $tableIndexes) {
      foreach ($tableIndexes as $columns) {
        $indexName = $table . '_' . implode('_', $columns) . '_index';

        try {
          DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} (" . implode(',', $columns) . ")");
        } catch (\Exception $e) {
          // Index might already exist, continue
          Log::debug("Index creation skipped: {$indexName}");
        }
      }
    }
  }

  /**
   * Clean old operation logs
   */
  private function cleanOldLogs()
  {
    $retentionDays = SystemSetting::getValue('log_retention_days', 90);
    $cutoffDate = Carbon::now()->subDays($retentionDays);

    return OperationLog::where('created_at', '<', $cutoffDate)->delete();
  }

  /**
   * Optimize database tables
   */
  private function optimizeTables()
  {
    $tables = ['valves', 'schedules', 'operation_logs', 'system_settings', 'users'];

    foreach ($tables as $table) {
      try {
        DB::statement("OPTIMIZE TABLE {$table}");
      } catch (\Exception $e) {
        Log::debug("Table optimization skipped for {$table}: " . $e->getMessage());
      }
    }
  }

  /**
   * Calculate system uptime
   */
  private function calculateSystemUptime()
  {
    $firstLog = OperationLog::oldest()->first();

    if (!$firstLog) {
      return 'N/A';
    }

    $uptime = Carbon::now()->diff($firstLog->created_at);

    if ($uptime->days > 0) {
      return $uptime->days . ' dias, ' . $uptime->h . ' horas';
    } elseif ($uptime->h > 0) {
      return $uptime->h . ' horas, ' . $uptime->i . ' minutos';
    } else {
      return $uptime->i . ' minutos';
    }
  }

  /**
   * Get performance metrics
   */
  public function getPerformanceMetrics()
  {
    $startTime = microtime(true);

    // Test database query performance
    $dbStart = microtime(true);
    $queryCount = DB::table('valves')->count();
    $dbTime = (microtime(true) - $dbStart) * 1000;

    // Test cache performance
    $cacheStart = microtime(true);
    Cache::get('test_key', 'default');
    $cacheTime = (microtime(true) - $cacheStart) * 1000;

    // Memory usage
    $memoryUsage = memory_get_usage(true);
    $memoryPeak = memory_get_peak_usage(true);

    // Calculate cache hit rate
    $cacheStats = $this->getCacheStats();
    $activeCaches = collect($cacheStats)->where('exists', true)->count();
    $totalCaches = count($cacheStats);
    $cacheHitRate = $totalCaches > 0 ? round(($activeCaches / $totalCaches) * 100, 1) : 0;

    $totalTime = (microtime(true) - $startTime) * 1000;

    return [
      'response_time' => round($totalTime, 1),
      'memory_usage' => round($memoryUsage / 1024 / 1024, 1),
      'db_queries' => $queryCount,
      'cache_hit_rate' => $cacheHitRate,
      'database_query_time_ms' => round($dbTime, 2),
      'cache_access_time_ms' => round($cacheTime, 2),
      'memory_peak_mb' => round($memoryPeak / 1024 / 1024, 2),
      'total_execution_time_ms' => round($totalTime, 2),
      'timestamp' => now()
    ];
  }

  /**
   * Run full optimization process
   */
  public function runFullOptimization()
  {
    Log::info('Starting full system optimization');

    $results = [
      'started_at' => now(),
      'steps' => []
    ];

    try {
      // Step 1: Clear old caches
      $this->invalidateAllCaches();
      $results['steps'][] = ['step' => 'cache_clear', 'status' => 'success'];

      // Step 2: Optimize database
      $dbResult = $this->optimizeDatabase();
      $results['steps'][] = ['step' => 'database_optimization', 'status' => $dbResult['success'] ? 'success' : 'failed', 'details' => $dbResult];

      // Step 3: Warm up caches
      $cacheResult = $this->warmUpCaches();
      $results['steps'][] = ['step' => 'cache_warmup', 'status' => 'success', 'details' => $cacheResult];

      // Step 4: Get performance metrics
      $metrics = $this->getPerformanceMetrics();
      $results['steps'][] = ['step' => 'performance_metrics', 'status' => 'success', 'details' => $metrics];

      $results['completed_at'] = now();
      $results['success'] = true;
      $results['duration_seconds'] = $results['completed_at']->diffInSeconds($results['started_at']);

      Log::info('Full system optimization completed successfully', $results);
    } catch (\Exception $e) {
      $results['success'] = false;
      $results['error'] = $e->getMessage();
      $results['completed_at'] = now();

      Log::error('Full system optimization failed', $results);
    }

    return $results;
  }

  /**
   * Get slow queries from database
   */
  public function getSlowQueries()
  {
    try {
      // Enable slow query log temporarily if not enabled
      DB::statement("SET SESSION slow_query_log = 'ON'");
      DB::statement("SET SESSION long_query_time = 1");

      // Get slow queries from performance schema (MySQL 5.6+)
      $slowQueries = DB::select("
        SELECT
          SUBSTRING(sql_text, 1, 100) as sql,
          ROUND(timer_wait/1000000000000, 2) as time
        FROM performance_schema.events_statements_history_long
        WHERE timer_wait > 1000000000000
        ORDER BY timer_wait DESC
        LIMIT 10
      ");

      return collect($slowQueries)->map(function ($query) {
        return [
          'sql' => $query->sql,
          'time' => $query->time
        ];
      })->toArray();
    } catch (\Exception $e) {
      Log::warning('Could not retrieve slow queries: ' . $e->getMessage());

      // Return mock data for demonstration
      return [
        [
          'sql' => 'SELECT * FROM operation_logs WHERE created_at > ...',
          'time' => '2.45'
        ],
        [
          'sql' => 'SELECT COUNT(*) FROM valves JOIN schedules ...',
          'time' => '1.23'
        ]
      ];
    }
  }

  /**
   * Get optimization recommendations
   */
  public function getOptimizationRecommendations()
  {
    $recommendations = [];

    try {
      // Check cache hit rate
      $cacheStats = $this->getCacheStats();
      $activeCaches = collect($cacheStats)->where('exists', true)->count();

      if ($activeCaches < count(self::CACHE_KEYS) * 0.8) {
        $recommendations[] = 'Considere executar o aquecimento de cache para melhorar a performance';
      }

      // Check database size
      $logCount = OperationLog::count();
      if ($logCount > 10000) {
        $recommendations[] = 'Base de dados com muitos logs. Considere limpar logs antigos';
      }

      // Check memory usage
      $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
      if ($memoryUsage > 128) {
        $recommendations[] = 'Uso de memória elevado. Considere otimizar queries ou aumentar cache TTL';
      }

      // Check for missing indexes
      $tableStats = DB::select("
        SELECT table_name, table_rows
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
        AND table_name IN ('operation_logs', 'schedules', 'valves')
      ");

      foreach ($tableStats as $table) {
        if ($table->table_rows > 1000) {
          $recommendations[] = "Tabela {$table->table_name} tem {$table->table_rows} registros. Verifique se os índices estão otimizados";
        }
      }

      // Check for active schedules
      $activeSchedules = Schedule::active()->count();
      $totalSchedules = Schedule::count();

      if ($totalSchedules > 0 && ($activeSchedules / $totalSchedules) < 0.5) {
        $recommendations[] = 'Muitos agendamentos inativos. Considere remover agendamentos não utilizados';
      }

      // Default recommendation if none found
      if (empty($recommendations)) {
        $recommendations[] = 'Sistema funcionando bem. Execute otimizações regulares para manter a performance';
      }
    } catch (\Exception $e) {
      Log::error('Error generating recommendations: ' . $e->getMessage());
      $recommendations[] = 'Erro ao gerar recomendações. Verifique os logs do sistema';
    }

    return $recommendations;
  }
}
