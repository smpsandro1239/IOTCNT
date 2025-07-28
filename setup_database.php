<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\User;

try {
  // Create users table
  if (!Schema::hasTable('users')) {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->enum('role', ['admin', 'user'])->default('user');
      $table->rememberToken();
      $table->timestamps();
    });
    echo "✓ Users table created successfully\n";
  }

  // Create valves table
  if (!Schema::hasTable('valves')) {
    Schema::create('valves', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->text('description')->nullable();
      $table->integer('pin_number');
      $table->boolean('is_active')->default(false);
      $table->integer('duration')->default(5);
      $table->timestamps();
    });
    echo "✓ Valves table created successfully\n";
  }

  // Create admin user
  $admin = User::firstOrCreate(
    ['email' => 'admin@iotcnt.local'],
    [
      'name' => 'Administrator',
      'password' => bcrypt('admin123'),
      'role' => 'admin'
    ]
  );
  echo "✓ Admin user created: {$admin->email}\n";
  echo "✓ Password: admin123\n";

  echo "\n=== SETUP COMPLETE ===\n";
  echo "Access the site at: http://localhost:8080\n";
  echo "Login: admin@iotcnt.local\n";
  echo "Password: admin123\n";
} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
}
