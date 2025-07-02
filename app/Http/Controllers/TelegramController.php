<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram; // Importar a Facade
use App\Models\TelegramUser;
use App\Models\OperationLog;

class TelegramController extends Controller
{
    /**
     * Handle incoming Telegram updates.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        // Obter a atualização do Telegram
        $update = Telegram::commandsHandler(true); // Processa comandos automaticamente se definidos no config, mas também retorna o objeto Update

        // Log da atualização completa para debug (opcional)
        Log::debug('Telegram Update Received:', $update->toArray());

        // Verificar se é uma mensagem e se tem texto
        if ($update->isType('message') && $update->getMessage()->has('text')) {
            $message = $update->getMessage();
            $chatId = $message->getChat()->getId();
            $text = $message->getText();
            $firstName = $message->getFrom()->getFirstName();
            $username = $message->getFrom()->getUsername();

            // Verificar se o utilizador Telegram está registado e autorizado
            $telegramUser = TelegramUser::where('telegram_chat_id', $chatId)->first();

            if (!$telegramUser) {
                // Registar novo utilizador, mas não autorizado por defeito
                $telegramUser = TelegramUser::create([
                    'telegram_chat_id' => $chatId,
                    'telegram_username' => $username,
                    'first_name' => $firstName,
                    'is_authorized' => false, // Novos utilizadores precisam ser autorizados por um admin
                ]);
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Olá {$firstName}! Parece que é a primeira vez que interage comigo. O seu ID ({$chatId}) foi registado. Por favor, peça a um administrador para autorizar o seu acesso."
                ]);
                $this->logTelegramOperation($chatId, $text, 'Utilizador não registado tentou interagir.', 'WARNING', ['username' => $username, 'firstName' => $firstName]);
                return response()->json(['status' => 'unauthorized_new_user']);
            }

            if (!$telegramUser->is_authorized) {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Olá {$firstName}. O seu acesso ainda não foi autorizado por um administrador."
                ]);
                $this->logTelegramOperation($chatId, $text, 'Utilizador não autorizado tentou interagir.', 'WARNING', ['telegramUserDbId' => $telegramUser->id]);
                return response()->json(['status' => 'unauthorized']);
            }

            // Processar comandos
            // O SDK pode lidar com comandos se estiverem configurados em config/telegram.php
            // Mas para lógica mais complexa, podemos fazer manualmente aqui.

            if (str_starts_with($text, '/')) {
                $this->handleCommand($telegramUser, $text);
            } else {
                // Lidar com mensagens normais, se necessário
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Recebi a sua mensagem, mas sou um bot de comandos. Use /start para ver os comandos disponíveis.'
                ]);
            }
        } elseif ($update->isType('callback_query')) {
            // Lidar com callback queries de botões inline, se usar
            $callbackQuery = $update->getCallbackQuery();
            $chatId = $callbackQuery->getMessage()->getChat()->getId();
            $data = $callbackQuery->getData(); // Dados do botão
            Log::info("Callback query recebida: Chat ID {$chatId}, Data: {$data}");
            // Lógica para callback queries
            // Ex: Telegram::answerCallbackQuery(['callback_query_id' => $callbackQuery->getId(), 'text' => 'Ação processada!']);
        }


        return response()->json(['status' => 'success']);
    }

    /**
     * Handle known commands.
     *
     * @param \App\Models\TelegramUser $telegramUser
     * @param string $text
     */
    protected function handleCommand(TelegramUser $telegramUser, string $text)
    {
        $chatId = $telegramUser->telegram_chat_id;
        $commandParts = explode(' ', $text);
        $command = $commandParts[0];

        $this->logTelegramOperation($chatId, $text, "Comando '{$command}' recebido.", 'INFO', ['telegramUserDbId' => $telegramUser->id]);

        switch ($command) {
            case '/start':
                $responseText = "Olá {$telegramUser->first_name}! Bem-vindo ao Sistema de Controlo de Irrigação.\n";
                $responseText .= "Comandos disponíveis:\n";
                $responseText .= "/status - Estado atual das válvulas\n";
                // Adicionar mais comandos à medida que são implementados
                // $responseText .= "/log - Últimos eventos registados\n";
                // $responseText .= "/ligar <N> - Ligar válvula N\n";
                // $responseText .= "/desligar <N> - Desligar válvula N\n";
                if ($telegramUser->authorization_level === 'admin') {
                    // $responseText .= "/iniciarciclo - Forçar início do ciclo\n";
                    // $responseText .= "/pararciclo - Parar ciclo em curso\n";
                }
                Telegram::sendMessage(['chat_id' => $chatId, 'text' => $responseText]);
                break;

            case '/status':
                // Lógica para obter o estado das válvulas (do ESP32 ou da BD)
                // Exemplo:
                // $valvesStatus = Valve::all()->map(function ($valve) {
                // return "Válvula {$valve->valve_number} ({$valve->name}): " . ($valve->current_state ? 'Ligada' : 'Desligada');
                // })->implode("\n");
                // Telegram::sendMessage(['chat_id' => $chatId, 'text' => "Estado das Válvulas:\n{$valvesStatus}"]);
                Telegram::sendMessage(['chat_id' => $chatId, 'text' => 'Funcionalidade /status ainda em desenvolvimento.']);
                break;

            // Adicionar outros casos para /log, /ligar, /desligar, etc.

            default:
                Telegram::sendMessage(['chat_id' => $chatId, 'text' => 'Comando não reconhecido. Use /start para ver a lista de comandos.']);
                break;
        }
    }

    /**
     * Log an operation initiated or related to Telegram.
     *
     * @param int|string $chatId
     * @param string $textReceived
     * @param string $message
     * @param string $status
     * @param array $details
     */
    protected function logTelegramOperation($chatId, string $textReceived, string $message, string $status = 'INFO', array $details = [])
    {
        $telegramUser = TelegramUser::where('telegram_chat_id', $chatId)->first();
        OperationLog::create([
            'telegram_user_id' => $telegramUser ? $telegramUser->id : null,
            'event_type' => 'TELEGRAM_COMMAND',
            'message' => $message,
            'source' => 'TELEGRAM_BOT',
            'status' => strtoupper($status),
            'details' => array_merge(['received_text' => $textReceived, 'chat_id' => $chatId], $details),
        ]);
    }

    /**
     * (Opcional) Método para definir o webhook. Pode ser chamado via rota ou comando Artisan.
     */
    public function setWebhook()
    {
        // A URL do webhook deve ser HTTPS e acessível publicamente.
        // Em desenvolvimento local, pode usar ngrok ou similar.
        $webhookUrl = route('telegram.webhook'); // Garanta que esta rota está definida e é HTTPS

        if (strpos($webhookUrl, 'localhost') !== false && app()->environment('production')) {
             Log::error('Tentativa de definir webhook para localhost em produção. Abortado.');
             return "ERRO: Não defina webhook para localhost em produção!";
        }
        if (strpos($webhookUrl, 'http://') !== false && strpos($webhookUrl, 'localhost') === false) {
            Log::warning('Webhook URL não é HTTPS: ' . $webhookUrl);
            // return "AVISO: Webhook URL não é HTTPS. O Telegram exige HTTPS.";
        }


        try {
            $response = Telegram::setWebhook(['url' => $webhookUrl]);
            Log::info('Webhook definido: ', $response->toArray());
            return "Webhook definido para: {$webhookUrl}. Resposta: " . $response->getDescription();
        } catch (\Throwable $e) {
            Log::error('Erro ao definir webhook: ' . $e->getMessage());
            return "Erro ao definir webhook: " . $e->getMessage();
        }
    }

    /**
     * (Opcional) Método para remover o webhook.
     */
    public function removeWebhook()
    {
        try {
            $response = Telegram::removeWebhook();
            Log::info('Webhook removido: ', $response->toArray());
            return "Webhook removido. Resposta: " . $response->getDescription();
        } catch (\Throwable $e) {
            Log::error('Erro ao remover webhook: ' . $e->getMessage());
            return "Erro ao remover webhook: " . $e->getMessage();
        }
    }
}
