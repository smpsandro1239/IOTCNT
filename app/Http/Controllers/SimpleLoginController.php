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
    $email = $request->input('email');
    $password = $request->input('password');

    if (!$email || !$password) {
      return response()->json([
        'error' => 'Email e password são obrigatórios'
      ], 400);
    }

    // Tentar encontrar o utilizador
    $user = User::where('email', $email)->first();

    if (!$user) {
      return response()->json([
        'error' => 'Utilizador não encontrado'
      ], 401);
    }

    // Verificar password
    if (!Hash::check($password, $user->password)) {
      return response()->json([
        'error' => 'Password incorrecta'
      ], 401);
    }

    // Login bem-sucedido
    Auth::login($user);

    // Redirecionar baseado no papel
    if ($user->isAdmin()) {
      return redirect('/admin/dashboard');
    }

    return redirect('/dashboard');
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
