<?php

use Illuminate\Support\Facades\Route;

Route::get('/simple', function () {
  return 'FUNCIONANDO!';
});

Route::get('/test-web', function () {
  return 'TESTE COM WEB MIDDLEWARE!';
})->middleware('web');
