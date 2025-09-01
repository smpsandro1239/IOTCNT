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

// Rotas Laravel (para futuro)
Route::middleware(['auth'])->group(function () {
  Route::get('/laravel/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
  Route::get('/laravel/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
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
