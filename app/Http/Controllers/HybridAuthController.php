<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class HybridAuthController extends Controller
{
  /**
   * Endpoint para autenticação híbrida (HTML + Laravel)
   */
  public function authenticate(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    $email = $request->email;
    $password = $request->password;

    // Verificar credenciais HTML hardcoded (compatibilidade)
    if ($this->validateHtmlCredentials($email, $password)) {
      return $this->createHtmlSession($email, $request);
    }

    // Verificar credenciais Laravel
    if (Auth::attempt(['email' => $email, 'password' => $password])) {
      $user = Auth::user();
      return $this->createLaravelSession($user, $request);
    }

    return response()->json([
      'success' => false,
      'message' => 'Credenciais inválidas'
    ], 401);
  }

  /**
   * Validar credenciais HTML hardcoded
   */
  private function validateHtmlCredentials($email, $password)
  {
    $validCredentials = [
      'admin@iotcnt.local' => 'password',
      'user@iotcnt.local' => 'password'
    ];

    return isset($validCredentials[$email]) &&
      $validCredentials[$email] === $password;
  }

  /**
   * Criar sessão para autenticação HTML
   */
  private function createHtmlSession($email, Request $request)
  {
    $isAdmin = $email === 'admin@iotcnt.local';

    // Criar sessão HTML
    Session::put('html_auth', [
      'authenticated' => true,
      'email' => $email,
      'role' => $isAdmin ? 'admin' : 'user',
      'login_time' => now()
    ]);

    // Determinar redirecionamento
    $redirectUrl = $isAdmin ? '/dashboard-admin.html' : '/dashboard-user.html';

    return response()->json([
      'success' => true,
      'message' => 'Login realizado com sucesso',
      'redirect' => $redirectUrl,
      'user' => [
        'email' => $email,
        'role' => $isAdmin ? 'admin' : 'user'
      ]
    ]);
  }

  /**
   * Criar sessão para autenticação Laravel
   */
  private function createLaravelSession($user, Request $request)
  {
    $request->session()->regenerate();

    // Também criar sessão HTML para compatibilidade
    Session::put('html_auth', [
      'authenticated' => true,
      'email' => $user->email,
      'role' => $user->isAdmin() ? 'admin' : 'user',
      'login_time' => now()
    ]);

    $redirectUrl = $user->isAdmin() ? '/dashboard-admin.html' : '/dashboard-user.html';

    return response()->json([
      'success' => true,
      'message' => 'Login realizado com sucesso',
      'redirect' => $redirectUrl,
      'user' => [
        'email' => $user->email,
        'role' => $user->isAdmin() ? 'admin' : 'user'
      ]
    ]);
  }

  /**
   * Logout híbrido
   */
  public function logout(Request $request)
  {
    // Logout Laravel
    Auth::logout();

    // Limpar sessão HTML
    Session::forget('html_auth');

    // Invalidar sessão
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
      'success' => true,
      'message' => 'Logout realizado com sucesso',
      'redirect' => '/login-iotcnt.html'
    ]);
  }

  /**
   * Verificar status de autenticação
   */
  public function status(Request $request)
  {
    // Verificar autenticação Laravel
    if (Auth::check()) {
      $user = Auth::user();
      return response()->json([
        'authenticated' => true,
        'type' => 'laravel',
        'user' => [
          'email' => $user->email,
          'role' => $user->isAdmin() ? 'admin' : 'user'
        ]
      ]);
    }

    // Verificar autenticação HTML
    $htmlAuth = Session::get('html_auth');
    if ($htmlAuth && $htmlAuth['authenticated']) {
      return response()->json([
        'authenticated' => true,
        'type' => 'html',
        'user' => [
          'email' => $htmlAuth['email'],
          'role' => $htmlAuth['role']
        ]
      ]);
    }

    return response()->json([
      'authenticated' => false
    ]);
  }

  /**
   * Migrar utilizador HTML para Laravel
   */
  public function migrateToLaravel(Request $request)
  {
    $htmlAuth = Session::get('html_auth');

    if (!$htmlAuth || !$htmlAuth['authenticated']) {
      return response()->json([
        'success' => false,
        'message' => 'Não autenticado via HTML'
      ], 401);
    }

    $email = $htmlAuth['email'];

    // Verificar se utilizador já existe no Laravel
    $user = User::where('email', $email)->first();

    if (!$user) {
      // Criar utilizador no Laravel
      $user = User::create([
        'name' => $email === 'admin@iotcnt.local' ? 'Administrador' : 'Utilizador',
        'email' => $email,
        'password' => Hash::make('password'), // Senha padrão
        'role' => $htmlAuth['role']
      ]);
    }

    // Fazer login no Laravel
    Auth::login($user);
    $request->session()->regenerate();

    return response()->json([
      'success' => true,
      'message' => 'Migração para Laravel concluída',
      'user' => [
        'email' => $user->email,
        'role' => $user->role
      ]
    ]);
  }
}
