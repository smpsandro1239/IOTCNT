<?php

use Illuminate\Support\Facades\Route;

Route::get('/simple', function () {
  return 'FUNCIONANDO!';
});

Route::get('/test-web', function () {
  return 'TESTE COM WEB MIDDLEWARE!';
})->middleware('web');
Route::get('/test-encrypt', function () {
  return 'TESTE ENCRYPT COOKIES!';
})->middleware(\App\Http\Middleware\EncryptCookies::class);

Route::get('/test-session', function () {
  return 'TESTE SESSION!';
})->middleware(\Illuminate\Session\Middleware\StartSession::class);
