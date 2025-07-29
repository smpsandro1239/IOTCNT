<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ValveController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OperationLogController;
use App\Http\Controllers\Admin\TelegramUserController;
use App\Http\Controllers\TelegramController;

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
    return view('welcome'); // Página inicial pública
});

// Dashboard principal para utilizadores logados
use App\Http\Controllers\UserDashboardController;

Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// API endpoint para dados do dashboard
Route::get('/dashboard/api/data', [UserDashboardController::class, 'apiData'])
    ->middleware(['auth'])
    ->name('dashboard.api.data');

// Rotas para agendamentos de utilizadores
use App\Http\Controllers\ScheduleController;

Route::middleware(['auth'])->group(function () {
    Route::resource('schedules', ScheduleController::class);
    Route::patch('schedules/{schedule}/toggle', [ScheduleController::class, 'toggle'])->name('schedules.toggle');
    Route::post('schedules/{schedule}/execute', [ScheduleController::class, 'execute'])->name('schedules.execute');
    Route::get('api/schedules', [ScheduleController::class, 'apiIndex'])->name('schedules.api.index');
});

// Profile routes removed - not using Laravel Breeze

// Grupo de Rotas para Administração
// Protegidas pelo middleware 'auth' (requer login) e 'role:admin' (requer papel de admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard de Administração
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD para Válvulas
    Route::resource('valves', ValveController::class);

    // CRUD para Agendamentos
    Route::resource('schedules', ScheduleController::class);

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


// Rotas de autenticação
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
