<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Esp32Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rota de teste pública (não requer autenticação)
Route::get('/ping', function () {
    return response()->json(['message' => 'pong', 'timestamp' => now()]);
});

// Grupo de rotas protegidas pela autenticação Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Rotas ESP32
    Route::prefix('esp32')->name('api.esp32.')->group(function () {
        Route::get('/config', [Esp32Controller::class, 'getConfig'])->name('config');
        Route::post('/valve-status', [Esp32Controller::class, 'updateValveStatus'])->name('valve.status');
        Route::post('/log', [Esp32Controller::class, 'receiveLog'])->name('log');
        Route::get('/commands', [Esp32Controller::class, 'getCommands'])->name('commands');

        // Manual control endpoints
        Route::post('/control-valve', [Esp32Controller::class, 'controlValve'])->name('control.valve');
        Route::post('/start-cycle', [Esp32Controller::class, 'startCycle'])->name('start.cycle');
        Route::post('/stop-all', [Esp32Controller::class, 'stopAll'])->name('stop.all');
    });

    // Rota para o ESP32 obter comandos pendentes (se usar polling em vez de ESP32 ser um servidor HTTP)
    // Route::get('/esp32/commands', function(Request $request) {
    // Lógica para verificar se há comandos para este ESP32
    // return response()->json(['commands' => []]); // Placeholder
    // });

    // Rotas para interface web
    Route::prefix('valve')->name('api.valve.')->group(function () {
        Route::get('/status', [App\Http\Controllers\Api\ValveStatusController::class, 'index'])->name('status');
        Route::get('/status/{valve}', [App\Http\Controllers\Api\ValveStatusController::class, 'show'])->name('show');
        Route::post('/control', [App\Http\Controllers\Api\ValveStatusController::class, 'control'])->name('control');
        Route::post('/start-cycle', [App\Http\Controllers\Api\ValveStatusController::class, 'startCycle'])->name('start-cycle');
        Route::post('/stop-all', [App\Http\Controllers\Api\ValveStatusController::class, 'stopAll'])->name('stop-all');
        Route::get('/stats', [App\Http\Controllers\Api\ValveStatusController::class, 'stats'])->name('stats');
    });

    // Rota de teste para verificar o utilizador autenticado via token
    Route::get('/user', function (Request $request) {
        return $request->user(); // Retorna o utilizador associado ao token
    });
});

// Se o ESP32 não puder usar Sanctum facilmente, pode-se considerar uma chave API simples
// (menos seguro, mas possível para cenários mais simples).
// Exemplo com middleware de chave API personalizado (não incluído aqui):
/*
Route::middleware('auth.apikey')->prefix('esp32_simple')->group(function () {
    Route::post('/valve-status', function (Request $request) {
        return response()->json(['message' => 'Simple API Key: Valve status received']);
    });
});
*/
