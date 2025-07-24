<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
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

// Rotas de Profile (Breeze default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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
    Route::patch('telegram-users/{telegramUser}/authorize', [TelegramUserController::class, 'authorize'])->name('telegram-users.authorize');
    Route::patch('telegram-users/{telegramUser}/revoke', [TelegramUserController::class, 'revoke'])->name('telegram-users.revoke');
});

// Rotas do Telegram Bot
Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');
Route::get('/telegram/set-webhook', [TelegramController::class, 'setWebhook'])->name('telegram.set-webhook');
Route::get('/telegram/remove-webhook', [TelegramController::class, 'removeWebhook'])->name('telegram.remove-webhook');


// Rotas de autenticação (login, register, password reset, etc.)
// Estas são incluídas pelo Laravel Breeze e estão em routes/auth.php
require __DIR__ . '/auth.php';
