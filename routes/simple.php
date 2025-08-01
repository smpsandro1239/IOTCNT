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

Route::get('/login-test', function () {
  return 'Página de Login - IOTCNT';
});
Route::get('/login-simple', function () {
  return view('auth.login-simple');
});
Route::get('/login-working', function () {
  return view('auth.login-working');
});
Route::get('/login-direct', function () {
  return '<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IOTCNT - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">IOTCNT</h1>
                <p class="text-gray-600">Sistema de Arrefecimento Industrial</p>
            </div>

            <form method="POST" action="/login">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Entrar
                </button>
            </form>

            <div class="mt-6 text-center">
                <div class="bg-blue-50 p-4 rounded-md">
                    <h3 class="font-semibold text-blue-900">Credenciais de Teste:</h3>
                    <p class="text-sm text-blue-700 mt-2">
                        <strong>Admin:</strong> admin@iotcnt.local<br>
                        <strong>User:</strong> user@iotcnt.local<br>
                        <strong>Password:</strong> password
                    </p>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="/" class="text-blue-600 hover:text-blue-800">← Voltar à página principal</a>
            </div>
        </div>
    </div>
</body>
</html>';
});
