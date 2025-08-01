<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixUserPasswords extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'users:fix-passwords';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Fix user passwords to ensure they work correctly';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $this->info('Fixing user passwords...');

    // Fix admin password
    $admin = User::where('email', 'admin@iotcnt.local')->first();
    if ($admin) {
      $admin->password = Hash::make('password');
      $admin->save();
      $this->info('✅ Admin password fixed');
    }

    // Fix user password
    $user = User::where('email', 'user@iotcnt.local')->first();
    if ($user) {
      $user->password = Hash::make('password');
      $user->save();
      $this->info('✅ User password fixed');
    }

    // Test passwords
    $this->info('Testing passwords...');

    $adminTest = Hash::check('password', User::where('email', 'admin@iotcnt.local')->first()->password);
    $userTest = Hash::check('password', User::where('email', 'user@iotcnt.local')->first()->password);

    $this->info('Admin password test: ' . ($adminTest ? '✅ PASS' : '❌ FAIL'));
    $this->info('User password test: ' . ($userTest ? '✅ PASS' : '❌ FAIL'));

    if ($adminTest && $userTest) {
      $this->info('🎉 All passwords are working correctly!');
    } else {
      $this->error('❌ Some passwords are still not working');
    }

    return 0;
  }
}
