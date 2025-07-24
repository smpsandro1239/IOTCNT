<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram; // Importar a Facade
use App\Models\TelegramUser;
use App\Models\OperationLog;
use App\Models\Valve;
use App\Models\Schedule;
use App\Services\TelegramNotificationService;

class TelegramController extends Controller
{
    protected $notificationService;

    public function __construct(TelegramNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
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
                $responseText .= "/logs - Últimos eventos do sistema\n";
                $responseText .= "/schedules - Ver agendamentos ativos\n";

                if ($telegramUser->authorization_level === 'admin') {
                    $responseText .= "\n🔧 *Comandos de Admin:*\n";
                    $responseText .= "/emergency_stop - Parar todas as válvulas\n";
                    $responseText .= "/start_cycle - Iniciar ciclo manual\n";
                    $responseText .= "/system_status - Estado detalhado do sistema\n";
                    $responseText .= "/valve_on [N] - Ligar válvula N\n";
                    $responseText .= "/valve_off [N] - Desligar válvula N\n";
                }
                Telegram::sendMessage(['chat_id' => $chatId, 'text' => $responseText]);
                break;

            case '/status':
                $valves = Valve::orderBy('valve_number')->get();
                $statusText = "🌱 *Estado das Válvulas*\n\n";

                foreach ($valves as $valve) {
                    $status = $valve->current_state ? '🟢 Ligada' : '🔴 Desligada';
                    $lastActivated = $valve->last_activated_at ?
                        $valve->last_activated_at->diffForHumans() : 'Nunca';

                    $statusText .= "💧 *Válvula {$valve->valve_number}* ({$valve->name})\n";
                    $statusText .= "   Estado: {$status}\n";
                    $statusText .= "   Última ativação: {$lastActivated}\n\n";
                }

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => $statusText,
                    'parse_mode' => 'Markdown'
                ]);
                break;

            case '/logs':
                $recentLogs = OperationLog::orderBy('logged_at', 'desc')
                    ->take(5)
                    ->get();

                $logsText = "📋 *Últimos Eventos*\n\n";

                foreach ($recentLogs as $log) {
                    $icon = match ($log->status) {
                        'SUCCESS' => '✅',
                        'ERROR' => '❌',
                        'WARNING' => '⚠️',
                        default => 'ℹ️'
                    };

                    $logsText .= "{$icon} *{$log->event_type}*\n";
                    $logsText .= "   {$log->message}\n";
                    $logsText .= "   📅 {$log->logged_at->format('d/m H:i')}\n\n";
                }

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => $logsText,
                    'parse_mode' => 'Markdown'
                ]);
                break;

            case '/schedules':
                $schedules = Schedule::where('is_enabled', true)->get();
                $schedulesText = "⏰ *Agendamentos Ativos*\n\n";

                foreach ($schedules as $schedule) {
                    $dayName = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'][$schedule->day_of_week];
                    $schedulesText .= "📅 *{$schedule->name}*\n";
                    $schedulesText .= "   Dia: {$dayName}\n";
                    $schedulesText .= "   Hora: {$schedule->start_time}\n";
                    $schedulesText .= "   Duração: {$schedule->per_valve_duration_minutes}min/válvula\n\n";
                }

                if ($schedules->isEmpty()) {
                    $schedulesText .= "Nenhum agendamento ativo.";
                }

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => $schedulesText,
                    'parse_mode' => 'Markdown'
                ]);
                break;

            case '/emergency_stop':
                if ($telegramUser->authorization_level !== 'admin') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => '❌ Acesso negado. Apenas administradores podem usar este comando.'
                    ]);
                    break;
                }

                // Parar todas as válvulas
                Valve::query()->update(['current_state' => false]);

                // Log da operação de emergência
                OperationLog::create([
                    'telegram_user_id' => $telegramUser->id,
                    'event_type' => 'EMERGENCY_STOP_TELEGRAM',
                    'message' => "Paragem de emergência ativada via Telegram por {$telegramUser->first_name}",
                    'source' => 'TELEGRAM_BOT',
                    'status' => 'WARNING',
                    'details' => [
                        'telegram_user_id' => $telegramUser->id,
                        'chat_id' => $chatId,
                        'stopped_valves' => Valve::count()
                    ]
                ]);

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '🚨 *PARAGEM DE EMERGÊNCIA ATIVADA*\n\nTodas as válvulas foram desligadas imediatamente.',
                    'parse_mode' => 'Markdown'
                ]);

                // Notify all admins about emergency stop
                $this->notificationService->sendToAdmins(
                    "🚨 *PARAGEM DE EMERGÊNCIA*\n\nAtivada por {$telegramUser->first_name} via Telegram\n📅 " . now()->format('d/m/Y H:i')
                );
                break;

            case '/start_cycle':
                if ($telegramUser->authorization_level !== 'admin') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => '❌ Acesso negado. Apenas administradores podem usar este comando.'
                    ]);
                    break;
                }

                // Log the manual cycle start
                OperationLog::create([
                    'telegram_user_id' => $telegramUser->id,
                    'event_type' => 'MANUAL_CYCLE_START_TELEGRAM',
                    'message' => "Ciclo manual iniciado via Telegram por {$telegramUser->first_name}",
                    'source' => 'TELEGRAM_BOT',
                    'status' => 'INFO',
                    'details' => [
                        'telegram_user_id' => $telegramUser->id,
                        'chat_id' => $chatId
                    ]
                ]);

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '🌱 *Ciclo de Irrigação Iniciado*\n\nO ciclo manual foi iniciado com sucesso.',
                    'parse_mode' => 'Markdown'
                ]);

                $this->notificationService->notifyCycleStart('TELEGRAM');
                break;

            case '/system_status':
                if ($telegramUser->authorization_level !== 'admin') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => '❌ Acesso negado. Apenas administradores podem usar este comando.'
                    ]);
                    break;
                }

                $this->notificationService->sendSystemStatus();
                break;

            default:
                // Check for valve control commands
                if (preg_match('/^\/valve_(on|off)\s+(\d+)$/', $text, $matches)) {
                    if ($telegramUser->authorization_level !== 'admin') {
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => '❌ Acesso negado. Apenas administradores podem controlar válvulas.'
                        ]);
                        break;
                    }

                    $action = $matches[1]; // 'on' or 'off'
                    $valveNumber = (int) $matches[2];

                    if ($valveNumber < 1 || $valveNumber > 5) {
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => '❌ Número de válvula inválido. Use números de 1 a 5.'
                        ]);
                        break;
                    }

                    $valve = Valve::where('valve_number', $valveNumber)->first();
                    if (!$valve) {
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => '❌ Válvula não encontrada.'
                        ]);
                        break;
                    }

                    $newState = $action === 'on';
                    $valve->update([
                        'current_state' => $newState,
                        'last_activated_at' => $newState ? now() : $valve->last_activated_at
                    ]);

                    // Log the operation
                    OperationLog::create([
                        'valve_id' => $valve->id,
                        'telegram_user_id' => $telegramUser->id,
                        'event_type' => $newState ? 'MANUAL_VALVE_ON_TELEGRAM' : 'MANUAL_VALVE_OFF_TELEGRAM',
                        'message' => "Válvula {$valve->name} " . ($newState ? 'ligada' : 'desligada') . " via Telegram por {$telegramUser->first_name}",
                        'source' => 'TELEGRAM_BOT',
                        'status' => 'SUCCESS',
                        'details' => [
                            'valve_number' => $valveNumber,
                            'new_state' => $newState,
                            'telegram_user_id' => $telegramUser->id
                        ]
                    ]);

                    $statusText = $newState ? '🟢 ligada' : '🔴 desligada';
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "💧 Válvula {$valveNumber} ({$valve->name}) foi {$statusText} com sucesso.",
                        'parse_mode' => 'Markdown'
                    ]);

                    $this->notificationService->notifyValveChange($valveNumber, $newState, 'TELEGRAM_BOT');
                    break;
                }
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
