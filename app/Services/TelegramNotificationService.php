<?php

namespace App\Services;

use App\Models\TelegramUser;
use App\Models\SystemSetting;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
  /**
   * Send notification to all authorized users.
   */
  public function sendToAll(string $message, string $parseMode = 'Markdown'): void
  {
    if (!SystemSetting::get('telegram_notifications_enabled', true)) {
      return;
    }

    $users = TelegramUser::where('is_authorized', true)
      ->where('receive_notifications', true)
      ->get();

    foreach ($users as $user) {
      $this->sendToUser($user->telegram_chat_id, $message, $parseMode);
    }
  }

  /**
   * Send notification to admin users only.
   */
  public function sendToAdmins(string $message, string $parseMode = 'Markdown'): void
  {
    if (!SystemSetting::get('telegram_notifications_enabled', true)) {
      return;
    }

    $admins = TelegramUser::where('is_authorized', true)
      ->where('authorization_level', 'admin')
      ->where('receive_notifications', true)
      ->get();

    foreach ($admins as $admin) {
      $this->sendToUser($admin->telegram_chat_id, $message, $parseMode);
    }
  }

  /**
   * Send notification to specific user.
   */
  public function sendToUser(int $chatId, string $message, string $parseMode = 'Markdown'): bool
  {
    try {
      Telegram::sendMessage([
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => $parseMode
      ]);

      Log::info("Telegram notification sent to chat ID: {$chatId}");
      return true;
    } catch (\Exception $e) {
      Log::error("Failed to send Telegram notification to chat ID {$chatId}: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Send irrigation cycle start notification.
   */
  public function notifyCycleStart(string $source = 'SYSTEM'): void
  {
    $message = "🌱 *Ciclo de Irrigação Iniciado*\n\n";
    $message .= "📅 " . now()->format('d/m/Y H:i') . "\n";
    $message .= "🔧 Origem: {$source}\n";
    $message .= "💧 Todas as válvulas serão ativadas sequencialmente";

    $this->sendToAll($message);
  }

  /**
   * Send irrigation cycle completion notification.
   */
  public function notifyCycleComplete(int $duration = null): void
  {
    $message = "✅ *Ciclo de Irrigação Concluído*\n\n";
    $message .= "📅 " . now()->format('d/m/Y H:i') . "\n";

    if ($duration) {
      $message .= "⏱️ Duração: {$duration} minutos\n";
    }

    $message .= "💧 Todas as válvulas foram desativadas";

    $this->sendToAll($message);
  }

  /**
   * Send error notification.
   */
  public function notifyError(string $error, string $context = ''): void
  {
    $message = "❌ *Erro no Sistema de Irrigação*\n\n";
    $message .= "📅 " . now()->format('d/m/Y H:i') . "\n";
    $message .= "🚨 Erro: {$error}\n";

    if ($context) {
      $message .= "📝 Contexto: {$context}";
    }

    $this->sendToAdmins($message);
  }

  /**
   * Send valve status change notification.
   */
  public function notifyValveChange(int $valveNumber, bool $state, string $source = 'SYSTEM'): void
  {
    $status = $state ? '🟢 Ligada' : '🔴 Desligada';
    $action = $state ? 'ativada' : 'desativada';

    $message = "💧 *Válvula {$valveNumber} {$action}*\n\n";
    $message .= "📅 " . now()->format('d/m/Y H:i') . "\n";
    $message .= "🔧 Origem: {$source}\n";
    $message .= "📊 Estado: {$status}";

    // Only notify admins for manual changes, all users for system changes
    if (in_array($source, ['WEB_PORTAL', 'TELEGRAM_BOT'])) {
      $this->sendToAdmins($message);
    } else {
      $this->sendToAll($message);
    }
  }

  /**
   * Send system status summary.
   */
  public function sendSystemStatus(): void
  {
    $valves = \App\Models\Valve::orderBy('valve_number')->get();
    $activeSchedules = \App\Models\Schedule::where('is_enabled', true)->count();

    $message = "📊 *Estado do Sistema de Irrigação*\n\n";
    $message .= "📅 " . now()->format('d/m/Y H:i') . "\n\n";

    $message .= "💧 *Válvulas:*\n";
    foreach ($valves as $valve) {
      $status = $valve->current_state ? '🟢' : '🔴';
      $message .= "   {$status} Válvula {$valve->valve_number}: {$valve->name}\n";
    }

    $message .= "\n⏰ Agendamentos ativos: {$activeSchedules}\n";
    $message .= "🔧 Sistema: Operacional";

    $this->sendToAdmins($message);
  }
}
