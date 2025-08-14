<?php

use Illuminate\Support\Facades\Route;

// Rota principal
Route::get('/', function () {
  return response()->json([
    'message' => 'IOTCNT - Sistema de Arrefecimento de Condensadores',
    'status' => 'Sistema Online',
    'empresa' => 'Continente',
    'proposito' => 'Prevenção de Legionela em Centrais de Frio',
    'timestamp' => now()->format('Y-m-d H:i:s'),
    'links' => [
      'login' => '/login',
      'dashboard' => '/dashboard',
      'admin' => '/admin/dashboard'
    ]
  ]);
});

// Rota de login
Route::get('login', function () {
  return response()->json([
    'message' => 'IOTCNT - Página de Login',
    'status' => 'Sistema Online',
    'credenciais_teste' => [
      'admin' => 'admin@iotcnt.local / password',
      'user' => 'user@iotcnt.local / password'
    ],
    'form_action' => '/login',
    'timestamp' => now()->format('Y-m-d H:i:s')
  ]);
})->name('login');

// Rota de teste
Route::get('/test', function () {
  return 'IOTCNT Sistema Online - Teste Funcional';
});

// Dashboard simples
Route::get('/dashboard', function () {
  return response()->json([
    'message' => 'Dashboard IOTCNT',
    'sistema' => 'Arrefecimento de Condensadores',
    'status' => 'Sistema Operacional',
    'timestamp' => now()->format('Y-m-d H:i:s')
  ]);
})->name('dashboard');
