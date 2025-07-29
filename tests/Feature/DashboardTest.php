<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Valve;
use App\Models\OperationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
  use RefreshDatabase;

  public function test_dashboard_requires_authentication(): void
  {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
  }

  public function test_authenticated_user_can_view_dashboard(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
  }

  public function test_dashboard_displays_valves(): void
  {
    $user = User::factory()->create();

    $valve = Valve::create([
      'name' => 'Válvula Teste',
      'valve_number' => 1,
      'description' => 'Válvula para testes',
      'current_state' => false,
      'esp32_pin' => 23,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200)
      ->assertSee('Válvula Teste')
      ->assertSee('Válvula 1')
      ->assertSee('Pino 23');
  }

  public function test_dashboard_displays_recent_logs(): void
  {
    $user = User::factory()->create();

    $valve = Valve::create([
      'name' => 'Válvula Teste',
      'valve_number' => 1,
      'esp32_pin' => 23,
    ]);

    $log = OperationLog::create([
      'valve_id' => $valve->id,
      'action' => 'manual_on',
      'source' => 'web_interface',
      'user_id' => $user->id,
      'notes' => 'Teste de ativação'
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200)
      ->assertSee('Atividade Recente')
      ->assertSee('Válvula Teste');
  }

  public function test_admin_user_can_access_admin_dashboard(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
  }

  public function test_regular_user_cannot_access_admin_dashboard(): void
  {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get('/admin/dashboard');

    $response->assertStatus(403);
  }

  public function test_dashboard_shows_correct_valve_states(): void
  {
    $user = User::factory()->create();

    $activeValve = Valve::create([
      'name' => 'Válvula Ativa',
      'valve_number' => 1,
      'current_state' => true,
      'esp32_pin' => 23,
    ]);

    $inactiveValve = Valve::create([
      'name' => 'Válvula Inativa',
      'valve_number' => 2,
      'current_state' => false,
      'esp32_pin' => 24,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200)
      ->assertSee('Válvula Ativa')
      ->assertSee('Válvula Inativa')
      ->assertSee('Ligada')
      ->assertSee('Desligada');
  }
}
