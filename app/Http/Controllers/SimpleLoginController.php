<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SimpleLoginController extends Controller
{
  /**
   * Processar login sem middleware complexo
   */
  public function login(Request $request)
  {
    // Validação básica
    $email = trim($request->input('email'));
    $password = $request->input('password');

    // Debug: Verificar dados recebidos
    if (!$email || !$password) {
      $allEmails = User::pluck('email')->toArray();
      return response()->json([
        'error' => 'Email e password são obrigatórios',
        'debug' => [
          'email_received' => $email,
          'password_received' => !empty($password),
          'available_emails' => $allEmails
        ]
      ], 400);
    }

    // Tentar encontrar o utilizador
    $user = User::where('email', $email)->first();

    if (!$user) {
      $allEmails = User::pluck('email')->toArray();
      return response()->json([
        'error' => 'Utilizador não encontrado',
        'debug' => [
          'email_searched' => $email,
          'available_emails' => $allEmails,
          'total_users' => User::count()
        ]
      ], 401);
    }

    // Verificar password
    if (!Hash::check($password, $user->password)) {
      return response()->json([
        'error' => 'Password incorrecta',
        'debug' => [
          'user_found' => $user->email,
          'password_test' => 'Hash check failed'
        ]
      ], 401);
    }

    // Login bem-sucedido
    Auth::login($user);

    // Redirecionar baseado no papel
    if ($user->isAdmin()) {
      return redirect('/admin/dashboard-test');
    }

    return redirect('/dashboard-test');
  }

  /**
   * Logout
   */
  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
  }
}
