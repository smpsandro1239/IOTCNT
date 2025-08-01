<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
  use RefreshDatabase;

  public function test_login_screen_can_be_rendered(): void
  {
    $response = $this->get('/login');

    $response->assertStatus(200);
  }

  public function test_users_can_authenticate_using_the_login_screen(): void
  {
    $user = User::factory()->create();

    $response = $this->post('/login', [
      'email' => $user->email,
      'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
  }

  public function test_admin_users_redirect_to_admin_dashboard(): void
  {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->post('/login', [
      'email' => $admin->email,
      'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard'));
  }

  public function test_users_can_not_authenticate_with_invalid_password(): void
  {
    $user = User::factory()->create();

    $this->post('/login', [
      'email' => $user->email,
      'password' => 'wrong-password',
    ]);

    $this->assertGuest();
  }

  public function test_users_can_logout(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
  }

  public function test_registration_screen_can_be_rendered(): void
  {
    $response = $this->get('/register');

    $response->assertStatus(200);
  }

  public function test_new_users_can_register(): void
  {
    $response = $this->post('/register', [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
  }
}
