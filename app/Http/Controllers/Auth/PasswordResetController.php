<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
  /**
   * Mostrar o formulário de pedido de reset de password.
   */
  public function showLinkRequestForm()
  {
    return view('auth.forgot-password');
  }

  /**
   * Enviar link de reset de password.
   */
  public function sendResetLinkEmail(Request $request)
  {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
      $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
      ? back()->with(['status' => __($status)])
      : back()->withErrors(['email' => __($status)]);
  }

  /**
   * Mostrar o formulário de reset de password.
   */
  public function showResetForm(Request $request, $token = null)
  {
    return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
  }

  /**
   * Processar o reset de password.
   */
  public function reset(Request $request)
  {
    $request->validate([
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user, $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->save();
      }
    );

    return $status === Password::PASSWORD_RESET
      ? redirect()->route('login')->with('status', __($status))
      : back()->withErrors(['email' => [__($status)]]);
  }
}
