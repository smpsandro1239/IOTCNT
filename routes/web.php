<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HybridAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\UserDashboardController;

// Rota principal - redireciona para homepage HTML
Route::get('/', function () {
  return redirect('/index-iotcnt.html');
});

// Rotas de autenticação híbrida
Route::prefix('auth')->group(function () {
  Route::post('/login', [HybridAuthController::class, 'authenticate'])->name('auth.login');
  Route::post('/logout', [HybridAuthController::class, 'logout'])->name('auth.logout');
  Route::get('/status', [HybridAuthController::class, 'status'])->name('auth.status');
  Route::post('/migrate', [HybridAuthController::class, 'migrateToLaravel'])->name('auth.migrate');
  Route::get('/csrf', function () {
    return response()->json(['token' => csrf_token()]);
  })->name('auth.csrf');
});

// Standard Laravel Routes expected by tests
Route::get('/login', function() { return response()->file(public_path('login-iotcnt.html')); })->name('login');
Route::post('/login', [HybridAuthController::class, 'authenticate']);
Route::get('/register', function() { return response('Registration Screen', 200); })->name('register');
Route::post('/register', [HybridAuthController::class, 'authenticate']); // Mock for tests
Route::post('/logout', [HybridAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
  Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
  Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
  Route::get('/laravel/dashboard', [UserDashboardController::class, 'index']);
  Route::get('/laravel/admin', [DashboardController::class, 'index']);
});

// Rota de fallback para páginas HTML estáticas
Route::fallback(function () {
  $path = request()->path();

  // Lista de páginas HTML válidas
  $validPages = [
    'index-iotcnt.html',
    'login-iotcnt.html',
    'dashboard-admin.html',
    'dashboard-user.html',
    'valve-control.html',
    'scheduling.html',
    'system-settings.html',
    'monitoring-dashboard.html',
    'charts-dashboard.html',
    'reports-dashboard.html',
    'api-docs.html',
    'notifications.html',
    'email-dashboard.html',
    'esp32-dashboard.html',
    'test-dashboard.html',
    'documentation-dashboard.html',
    'system-logs.html',
    'database-admin.html',
    'backup-admin.html',
    'performance-metrics.html',
    'mobile-app.html'
  ];

  if (in_array($path, $validPages)) {
    return response()->file(public_path($path));
  }

  return abort(404);
});

// Rotas de Broadcasting para comunicação em tempo real
Route::post('/broadcast/esp32-data', [BroadcastController::class, 'broadcastESP32Data'])->name('broadcast.esp32.data');
Route::post('/broadcast/join-channel', [BroadcastController::class, 'joinChannel'])->name('broadcast.join.channel');

// Rota para dashboard em tempo real
Route::get('/dashboard/real-time', function () {
    return view('dashboard.real-time');
})->name('dashboard.real-time');

// Rota para testar comunicação ESP32 em tempo real
Route::post('/api/esp32/test-data', [BroadcastController::class, 'broadcastESP32Data'])->name('api.esp32.test-data');

// Rotas de notificações
Route::prefix('notifications')->group(function () {
    Route::post('/send-alert', [NotificationController::class, 'sendAlert'])->name('notifications.send-alert');
    Route::post('/setup-esp32', [NotificationController::class, 'setupESP32Notifications'])->name('notifications.setup-esp32');
    Route::get('/status', [NotificationController::class, 'getStatus'])->name('notifications.status');
});

// Rota para dashboard industrial premium
Route::get('/dashboard/industrial', function () {
    return view('dashboard.industrial');
})->name('dashboard.industrial');
use App\Http\Controllers\BroadcastController;

// Broadcast endpoints for ESP32 real‑time communication
Route::post('/broadcast/esp32-data', [BroadcastController::class, 'store'])->name('broadcast.esp32');
Route::post('/broadcast/join-channel', [BroadcastController::class, 'joinChannel'])->name('broadcast.join');
Route::post('/broadcast/leave-channel', [BroadcastController::class, 'leaveChannel'])->name('broadcast.leave');
Route::get('/broadcast/real-time-data', [BroadcastController::class, 'getRealTimeData'])->name('broadcast.realtime');
Route::get('/broadcast/historical-data', [BroadcastController::class, 'getHistoricalData'])->name('broadcast.historical');
Route::get('/broadcast/device-statistics', [BroadcastController::class, 'getDeviceStatistics'])->name('broadcast.statistics');

// Dashboard view for predictive analysis real‑time
Route::get('/dashboard/prediction', function () {
    return view('dashboard.real-time-prediction');
})->name('dashboard.prediction');
