<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\ValveController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OperationLogController;
use App\Http\Controllers\Admin\TelegramUserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PerformanceController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return 'IOTCNT Sistema Online';
});

Route::get('/test', function () {
    return 'IOTCNT Sistema Online - EmpresaX';
});

// Dashboard principal para utilizadores logados

Route::get('/dashboard', function () {
    return response()->json([
        'message' => 'Dashboard IOTCNT',
        'sistema' => 'Arrefecimento de Condensadores',
        'status' => 'Sistema Operacional',
        'nota' => 'Para funcionalidade completa, configure a base de dados'
    ]);
})->name('dashboard');

// API endpoint para dados do dashboard
Route::get('/dashboard/api/data', [UserDashboardController::class, 'apiData'])
    ->middleware(['auth'])
    ->name('dashboard.api.data');

// Rotas para agendamentos de utilizadores

Route::middleware(['auth'])->group(function () {
    Route::resource('schedules', ScheduleController::class);
    Route::patch('schedules/{schedule}/toggle', [ScheduleController::class, 'toggle'])->name('schedules.toggle');
    Route::post('schedules/{schedule}/execute', [ScheduleController::class, 'execute'])->name('schedules.execute');
    Route::get('api/schedules', [ScheduleController::class, 'apiIndex'])->name('schedules.api.index');
});

// Profile routes removed - not using Laravel Breeze

// Grupo de Rotas para Administração - temporariamente desactivadas
// Protegidas pelo middleware 'auth' (requer login) e 'role:admin' (requer papel de admin)
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard de Administração
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // API endpoints para dashboard de admin
    Route::get('/api/metrics', [AdminDashboardController::class, 'getMetrics'])->name('api.metrics');
    Route::get('/api/valve-usage', [AdminDashboardController::class, 'getValveUsage'])->name('api.valve-usage');
    Route::get('/api/esp32-status', [AdminDashboardController::class, 'getEsp32Status'])->name('api.esp32-status');
    Route::get('/api/dashboard-data', [AdminDashboardController::class, 'getDashboardData'])->name('api.dashboard-data');

    // Ações do sistema
    Route::post('/test-system', [AdminDashboardController::class, 'testSystem'])->name('test-system');
    Route::post('/restart-esp32', [AdminDashboardController::class, 'restartEsp32'])->name('restart-esp32');
    Route::get('/export/data', [AdminDashboardController::class, 'exportData'])->name('export.data');

    // Sistema de Configurações
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');
    Route::get('/settings/export', [SettingsController::class, 'export'])->name('settings.export');
    Route::post('/settings/import', [SettingsController::class, 'import'])->name('settings.import');
    Route::post('/settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
    Route::post('/settings/test-telegram', [SettingsController::class, 'testTelegram'])->name('settings.test-telegram');
    Route::get('/api/settings', [SettingsController::class, 'getApiSettings'])->name('api.settings');

    // Sistema de Performance e Otimização
    Route::get('/performance', function () {
        return response()->json([
            'message' => 'Sistema de Performance IOTCNT',
            'funcionalidades' => [
                'Métricas em tempo real',
                'Optimização de cache',
                'Detecção de queries lentas',
                'Recomendações automáticas'
            ],
            'status' => 'Interface implementada',
            'nota' => 'Configure a base de dados para funcionalidade completa'
        ]);
    })->name('performance.index');
    Route::get('/api/performance/metrics', [PerformanceController::class, 'getMetrics'])->name('api.performance.metrics');
    Route::post('/performance/clear-cache', [PerformanceController::class, 'clearCache'])->name('performance.clear-cache');
    Route::post('/performance/optimize', [PerformanceController::class, 'runFullOptimization'])->name('performance.optimize');
    Route::post('/performance/warm-cache', [PerformanceController::class, 'warmUpCache'])->name('performance.warm-cache');
    Route::post('/performance/optimize-database', [PerformanceController::class, 'optimizeDatabase'])->name('performance.optimize-database');
    Route::get('/api/performance/cache-stats', [PerformanceController::class, 'getCacheStats'])->name('api.performance.cache-stats');
    Route::get('/api/performance/database-stats', [PerformanceController::class, 'getDatabaseStats'])->name('api.performance.database-stats');
    Route::get('/api/performance/system-resources', [PerformanceController::class, 'getSystemResources'])->name('api.performance.system-resources');
    Route::post('/performance/clean-logs', [PerformanceController::class, 'cleanOldLogs'])->name('performance.clean-logs');
    Route::post('/performance/test', [PerformanceController::class, 'testPerformance'])->name('performance.test');
    Route::post('/performance/clean-logs', [PerformanceController::class, 'cleanOldLogs'])->name('performance.clean-logs');
    Route::post('/performance/test', [PerformanceController::class, 'testPerformance'])->name('performance.test');

    // CRUD para Válvulas
    Route::resource('valves', ValveController::class);

    // CRUD para Agendamentos
    Route::resource('schedules', AdminScheduleController::class);

    // CRUD para Utilizadores
    Route::resource('users', UserController::class);

    // Gestão de Logs de Operação
    Route::resource('logs', OperationLogController::class)->only(['index', 'show', 'destroy']);
    Route::delete('logs/bulk-delete', [OperationLogController::class, 'bulkDelete'])->name('logs.bulk-delete');

    // Gestão de Utilizadores Telegram
    Route::resource('telegram-users', TelegramUserController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::patch('telegram-users/{telegramUser}/authorize', [TelegramUserController::class, 'authorizeUser'])->name('telegram-users.authorize');
    Route::patch('telegram-users/{telegramUser}/revoke', [TelegramUserController::class, 'revoke'])->name('telegram-users.revoke');
});

// Rotas do Telegram Bot
Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');
Route::get('/telegram/set-webhook', [TelegramController::class, 'setWebhook'])->name('telegram.set-webhook');
Route::get('/telegram/remove-webhook', [TelegramController::class, 'removeWebhook'])->name('telegram.remove-webhook');


// Rotas de autenticação funcionais
Route::get('login', function () {
    return 'IOTCNT Login Page - Sistema Online';
})->name('login');

Route::post('login', function (Request $request) {
    return response()->json([
        'message' => 'Login POST recebido',
        'email' => $request->input('email'),
        'timestamp' => now()->format('Y-m-d H:i:s')
    ]);
})->name('login.post');

Route::post('logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Rotas de autenticação originais (backup) - temporariamente desactivadas
// Route::middleware('guest')->group(function () {
//     Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
//     Route::post('register', [RegisterController::class, 'register']);
//     Route::get('forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
//     Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
//     Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
//     Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
// });
