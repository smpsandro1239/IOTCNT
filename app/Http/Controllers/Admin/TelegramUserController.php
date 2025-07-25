<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramUser;
use App\Models\User;
use Illuminate\Http\Request;

class TelegramUserController extends Controller
{
  /**
   * Display a listing of Telegram users.
   */
  public function index()
  {
    $telegramUsers = TelegramUser::with('user')
      ->orderBy('created_at', 'desc')
      ->paginate(15);

    return view('admin.telegram-users.index', compact('telegramUsers'));
  }

  /**
   * Show the form for editing the specified Telegram user.
   */
  public function edit(TelegramUser $telegramUser)
  {
    $webUsers = User::orderBy('name')->get();
    return view('admin.telegram-users.edit', compact('telegramUser', 'webUsers'));
  }

  /**
   * Update the specified Telegram user.
   */
  public function update(Request $request, TelegramUser $telegramUser)
  {
    $validated = $request->validate([
      'user_id' => 'nullable|exists:users,id',
      'is_authorized' => 'boolean',
      'authorization_level' => 'nullable|in:admin,user',
      'receive_notifications' => 'boolean'
    ]);

    $telegramUser->update($validated);

    return redirect()->route('admin.telegram-users.index')
      ->with('success', 'Utilizador Telegram atualizado com sucesso.');
  }

  /**
   * Remove the specified Telegram user.
   */
  public function destroy(TelegramUser $telegramUser)
  {
    $telegramUser->delete();

    return redirect()->route('admin.telegram-users.index')
      ->with('success', 'Utilizador Telegram eliminado com sucesso.');
  }

  /**
   * Authorize a Telegram user.
   */
  public function authorizeUser(TelegramUser $telegramUser)
  {
    $telegramUser->update([
      'is_authorized' => true,
      'authorization_level' => 'user'
    ]);

    return redirect()->route('admin.telegram-users.index')
      ->with('success', 'Utilizador autorizado com sucesso.');
  }

  /**
   * Revoke authorization for a Telegram user.
   */
  public function revoke(TelegramUser $telegramUser)
  {
    $telegramUser->update([
      'is_authorized' => false,
      'authorization_level' => null
    ]);

    return redirect()->route('admin.telegram-users.index')
      ->with('success', 'Autorização revogada com sucesso.');
  }
}
