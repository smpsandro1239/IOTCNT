<?php

namespace App\Services;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class Esp32BroadcastService
{
    private array $fallbackData = [];
    private bool $broadcastEnabled = true;

    public function broadcastData(array $data): array
    {
        try {
            // Broadcast para canal público
            Broadcast::channel('esp32-data', function () {
                return ['id' => 1, 'name' => 'ESP32 Channel'];
            });

            broadcast(new \App\Events\Esp32DataReceived($data))->toOthers();

            $this->storeFallback($data);

            return [
                'success' => true,
                'message' => 'Dados transmitidos com sucesso',
                'timestamp' => now()->toDateTimeString(),
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Erro no broadcast: ' . $e->getMessage());
            // Fallback: armazenar dados para recuperação posterior
            return [
                'success' => false,
                'message' => 'Broadcast falhou, dados armazenados localmente',
                'timestamp' => now()->toDateTimeString(),
                'data' => $this->storeFallback($data)
            ];
        }
    }

    public function storeFallback(array $data): array
    {
        $this->fallbackData[] = [
            'data' => $data,
            'timestamp' => now()->toDateTimeString(),
            'attempts' => 0
        ];
        // Manter apenas últimos 100 registros no fallback
        if (count($this->fallbackData) > 100) {
            array_shift($this->fallbackData);
        }
        Cache::put('esp32_fallback_data', $this->fallbackData, now()->addHours(2));
        return end($this->fallbackData);
    }

    public function getFallbackData(): array
    {
        return Cache::get('esp32_fallback_data', []);
    }

    public function clearFallback(): void
    {
        Cache::forget('esp32_fallback_data');
        $this->fallbackData = [];
    }

    public function simulateData(array $params): array
    {
        $simulated = [
            'device_id' => $params['device_id'] ?? 1,
            'temperature' => $params['temperature'] ?? rand(200, 300) / 10,
            'humidity' => $params['humidity'] ?? rand(30, 90),
            'pressure' => $params['pressure'] ?? rand(1010, 1020),
            'valve_status' => $params['valve_status'] ?? 'closed',
            'simulated' => true,
            'timestamp' => now()->toDateTimeString()
        ];
        return $this->broadcastData($simulated);
    }

    public function enableBroadcast(bool $enable): void
    {
        $this->broadcastEnabled = $enable;
    }

    public function isBroadcastEnabled(): bool
    {
        return $this->broadcastEnabled;
    }
}
