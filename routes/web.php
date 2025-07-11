<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ValveController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\UserController;
// Se for criar um AdminDashboardController, descomente a linha abaixo
// use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

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
use App\Http{Controllers\UserDashboardController;

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
    // Dashboard de Administração (Exemplo, precisaria de um AdminDashboardController)
    /*
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Criar esta view: resources/views/admin/dashboard.blade.php
    })->name('dashboard');
    */
    // Ou usando um controlador:
    // Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');


    // CRUD para Válvulas
    Route::resource('valves', ValveController::class);

    // CRUD para Agendamentos
    Route::resource('schedules', ScheduleController::class);

    // CRUD para Utilizadores
    Route::resource('users', UserController::class);

    // Outras rotas de administração podem ser adicionadas aqui
    // Ex: Route::get('operation-logs', [OperationLogController::class, 'index'])->name('logs.index');
    // Ex: Route::get('telegram-users', [TelegramUserController::class, 'index'])->name('telegram.users.index');
});


// Rotas de autenticação (login, register, password reset, etc.)
// Estas são incluídas pelo Laravel Breeze e estão em routes/auth.php
require __DIR__.'/auth.php';
