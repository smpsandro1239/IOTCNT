<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PredictionAnalysis;
use App\Models\Device;

class Esp32IntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o fluxo completo: ESP32 envia dados -> broadcast controller processa -> análise preditiva é criada -> dashboard tem registro.
     */
    public function test_esp32_data_flow_creates_prediction()
    {
        // Cria um device fictício
        $device = Device::factory()->create(['name' => 'ESP32-TEST']);

        // Payload simulado
        $payload = [
            'device_id' => $device->id,
            'metric_type' => 'temperature',
            'current_value' => 27.5,
            'threshold_min' => 15,
            'threshold_max' => 30,
        ];

        // Envia request ao endpoint broadcast (autenticado via Sanctum)
        $user = $this->createUserWithSanctum();
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson(route('broadcast.esp32'), $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);

        // Verifica se a análise preditiva foi criada
        $this->assertDatabaseHas('prediction_analyses', [
            'device_id' => $device->id,
            'metric_type' => 'temperature',
            'current_value' => 27.5,
        ]);

        // Opcional: verifica se o dashboard view carrega sem erro (simulação simples)
        $dashboardResponse = $this->actingAs($user, 'sanctum')
                                 ->get(route('dashboard.prediction'));
        $dashboardResponse->assertStatus(200);
    }

    /**
     * Helper para criar um usuário com token Sanctum.
     */
    protected function createUserWithSanctum()
    {
        $user = \App\Models\User::factory()->create();
        $user->createToken('test-token')->plainTextToken;
        return $user;
    }
}
