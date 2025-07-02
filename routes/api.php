<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Adicionar aqui os controladores da API quando forem criados
// Ex: use App\Http\Controllers\Api\ValveStatusController;
// Ex: use App\Http\Controllers\Api\Esp32LogController;

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
    // Rota para o ESP32 obter a sua configuração inicial ou verificar a ligação
    Route::get('/esp32/config', function (Request $request) {
        // Retornar configurações relevantes para o ESP32 que fez a requisição
        // Poderia incluir lista de válvulas, horários, etc.
        // $user = $request->user(); // O dispositivo autenticado (se o token estiver associado a um user)
        return response()->json([
            'message' => 'Configuração para ESP32 (placeholder)',
            'device_name' => $request->user()->name ?? 'ESP32 Device', // Exemplo se o token tiver um nome
            // Adicionar dados de configuração aqui
        ]);
    })->name('api.esp32.config');

    // Rota para o ESP32 reportar o estado de uma válvula
    Route::post('/esp32/valve-status', function (Request $request) {
        // Validar os dados recebidos: valve_id, status (on/off), timestamp_esp32
        // Ex: $validated = $request->validate([
        // 'valve_number' => 'required|integer|min:1|max:5',
        // 'state' => 'required|boolean', // true para ligado, false para desligado
        // 'timestamp' => 'sometimes|integer' // Timestamp Unix do ESP32
        // ]);
        // Lógica para atualizar o estado da válvula na BD (ex: tabela `valves`)
        // Lógica para criar um log em `operation_logs`
        // return response()->json(['message' => 'Estado da válvula recebido', 'data' => $validated]);
        return response()->json(['message' => 'Estado da válvula recebido (placeholder)', 'data' => $request->all()]);
    })->name('api.esp32.valve.status');

    // Rota para o ESP32 enviar um log genérico
    Route::post('/esp32/log', function (Request $request) {
        // Validar os dados: level (info, error, debug), message, details_json
        // Ex: $validated = $request->validate([
        // 'level' => 'required|string|in:info,error,warning,debug',
        // 'message' => 'required|string',
        // 'details' => 'nullable|json'
        // ]);
        // Lógica para guardar o log em `operation_logs`
        // return response()->json(['message' => 'Log do ESP32 recebido', 'data' => $validated]);
        return response()->json(['message' => 'Log do ESP32 recebido (placeholder)', 'data' => $request->all()]);
    })->name('api.esp32.log');

    // Rota para o ESP32 obter comandos pendentes (se usar polling em vez de ESP32 ser um servidor HTTP)
    // Route::get('/esp32/commands', function(Request $request) {
        // Lógica para verificar se há comandos para este ESP32
        // return response()->json(['commands' => []]); // Placeholder
    // });

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
