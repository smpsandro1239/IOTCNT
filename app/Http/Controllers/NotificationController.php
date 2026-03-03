<?php

namespace Appttpontrollers;

use Illuminatettpequest;
use Illuminateupportacadesttp;
use Illuminateupportacadesail;
use Illuminateupportacadesueue;
use AppaillertNotification;
use AppodelsperationLog;
use Appodelsser;
use AppodelselegramUser;

class NotificationController extends Controller
{
    /**
     * Envia notificação para Telegram com fallback para email
     */
    public function sendAlert(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'level' => 'required|in:info,warning,error,critical',
            'recipient_type' => 'required|in:telegram,email,both',
            'device_id' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        $message = $validated['message'];
        $level = $validated['level'];
        $recipientType = $validated['recipient_type'];
        $deviceId = $validated['device_id'] ?? 'system';
        $priority = $validated['priority'] ?? 5;

        // Log da notificação
        $this->logNotification($message, $level, $recipientType, $deviceId);

        // Enviar notificações
        $results = [
            'telegram' => false,
            'email' => false,
        ];

        if ($recipientType === 'telegram' || $recipientType === 'both') {
            $results['telegram'] = $this->sendTelegramNotification($message, $level, $priority);
        }

        if ($recipientType === 'email' || $recipientType === 'both') {
            $results['email'] = $this->sendEmailNotification($message, $level);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notificação enviada com sucesso',
            'results' => $results,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Envia notificação para Telegram
     */
    private function sendTelegramNotification($message, $level, $priority)
    {
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if (!$botToken || !$chatId) {
                return false;
            }

            $emoji = $this->getEmojiByLevel($level);
            $formattedMessage = sprintf(
                
