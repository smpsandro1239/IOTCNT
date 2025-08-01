<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create default admin user
    User::firstOrCreate(
      ['email' => 'admin@iotcnt.local'],
      [
        'name' => 'Administrator',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'email_verified_at' => now(),
      ]
    );

    // Create default user
    User::firstOrCreate(
      ['email' => 'user@iotcnt.local'],
      [
        'name' => 'User',
        'password' => Hash::make('password'),
        'role' => 'user',
        'email_verified_at' => now(),
      ]
    );

    // Create additional test users in development
    if (app()->environment('local')) {
      User::factory(5)->create();
      User::factory(2)->admin()->create();
    }
  }
}
