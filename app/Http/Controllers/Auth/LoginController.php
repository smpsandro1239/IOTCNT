<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
  /**
   * Mostrar o formulário de login.
   */
  public function showLoginForm()
  {
    return view('auth.login');
  }

  /**
   * Processar o login do utilizador.
   */
  public function login(Request $request)
  {
    $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);

    if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
      $request->session()->regenerate();

      // Redirecionar baseado no papel do utilizador
      if (Auth::user()->isAdmin()) {
        return redirect()->intended(route('admin.dashboard'));
      }

      return redirect()->intended(route('dashboard'));
    }

    throw ValidationException::withMessages([
      'email' => __('As credenciais fornecidas não correspondem aos nossos registos.'),
    ]);
  }

  /**
   * Fazer logout do utilizador.
   */
  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
  }
}
