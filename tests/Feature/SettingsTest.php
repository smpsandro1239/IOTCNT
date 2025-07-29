<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingsTest extends TestCase
{
  use RefreshDatabase;

  protected $adminUser;
  protected $regularUser;

  protected function setUp(): void
  {
    parent::setUp();

    $this->adminUser = User::factory()->create([
      'is_admin' => true,
      'name' => 'Admin User',
      'email' => 'admin@test.com'
    ]);

    $this->regularUser = User::factory()->create([
      'is_admin' => false,
      'name' => 'Regular User',
      'email' => 'user@test.com'
    ]);

    // Create some default settings
    SystemSetting::create([
      'key' => 'system_name',
      'value' => 'IOTCNT Test System',
      'type' => 'string',
      'updated_by' => $this->adminUser->id
    ]);

    SystemSetting::create([
      'key' => 'default_valve_duration',
      'value' => '5',
      'type' => 'integer',
      'updated_by' => $this->adminUser->id
    ]);

    SystemSetting::create([
      'key' => 'telegram_notifications',
      'value' => '0',
      'type' => 'boolean',
      'updated_by' => $this->adminUser->id
    ]);
  }

  public function test_admin_can_view_settings_page()
  {
    Sanctum::actingAs($this->adminUser);

    $response = $this->get('/admin/settings');

    $response->assertStatus(200);
    $response->assertSee('Configurações do Sistema');
    $response->assertSee('IOTCNT Test System');
  }

  public function test_regular_user_cannot_access_settings()
  {
    Sanctum::actingAs($this->regularUser);

    $response = $this->get('/admin/settings');

    $response->assertStatus(403);
  }

  public function test_admin_can_update_settings()
  {
    Sanctum::actingAs($this->adminUser);

    $settingsData = [
      'system_name' => 'Updated IOTCNT System',
      'timezone' => 'Europe/Madrid',
      'default_valve_duration' => 7,
      'max_concurrent_valves' => 2,
      'auto_cycle_enabled' => true,
      'telegram_notifications' => true,
      'api_rate_limit' => 120,
      'esp32_timeout' => 45,
      'log_retention_days' => 60,
      'backup_enabled' => true,
      'maintenance_mode' => false,
      'debug_mode' => true
    ];

    $response = $this->put('/admin/settings', $settingsData);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    // Verify settings were updated
    $this->assertEquals('Updated IOTCNT System', SystemSetting::getValue('system_name'));
    $this->assertEquals('Europe/Madrid', SystemSetting::getValue('timezone'));
    $this->assertEquals(7, SystemSetting::getValue('default_valve_duration'));
    $this->assertTrue(SystemSetting::getValue('auto_cycle_enabled'));
    $this->assertTrue(SystemSetting::getValue('telegram_notifications'));
    $this->assertEquals(120, SystemSetting::getValue('api_rate_limit'));
  }

  public function test_settings_validation()
  {
    Sanctum::actingAs($this->adminUser);

    // Test invalid system name (too long)
    $response = $this->put('/admin/settings', [
      'system_name' => str_repeat('a', 300),
      'timezone' => 'Europe/Lisbon',
      'default_valve_duration' => 5,
      'max_concurrent_valves' => 1,
      'api_rate_limit' => 60,
      'esp32_timeout' => 30,
      'log_retention_days' => 90
    ]);

    $response->assertStatus(422);

    // Test invalid valve duration
    $response = $this->put('/admin/settings', [
      'system_name' => 'Test System',
      'timezone' => 'Europe/Lisbon',
      'default_valve_duration' => 0, // Invalid: must be at least 1
      'max_concurrent_valves' => 1,
      'api_rate_limit' => 60,
      'esp32_timeout' => 30,
      'log_retention_days' => 90
    ]);

    $response->assertStatus(422);

    // Test invalid concurrent valves
    $response = $this->put('/admin/settings', [
      'system_name' => 'Test System',
      'timezone' => 'Europe/Lisbon',
      'default_valve_duration' => 5,
      'max_concurrent_valves' => 10, // Invalid: max is 5
      'api_rate_limit' => 60,
      'esp32_timeout' => 30,
      'log_retention_days' => 90
    ]);

    $response->assertStatus(422);

    // Test invalid API rate limit
    $response = $this->put('/admin/settings', [
      'system_name' => 'Test System',
      'timezone' => 'Europe/Lisbon',
      'default_valve_duration' => 5,
      'max_concurrent_valves' => 1,
      'api_rate_limit' => 5, // Invalid: min is 10
      'esp32_timeout' => 30,
      'log_retention_days' => 90
    ]);

    $response->assertStatus(422);
  }

  public function test_admin_can_reset_settings_to_defaults()
  {
    Sanctum::actingAs($this->adminUser);

    // First, update some settings
    SystemSetting::setValue('system_name', 'Modified System');
    SystemSetting::setValue('default_valve_duration', 10);

    $response = $this->post('/admin/settings/reset');

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    // Verify settings were reset to defaults
    $this->assertEquals('IOTCNT Irrigation System', SystemSetting::getValue('system_name'));
    $this->assertEquals(5, SystemSetting::getValue('default_valve_duration'));
  }

  public function test_admin_can_export_settings()
  {
    Sanctum::actingAs($this->adminUser);

    $response = $this->get('/admin/settings/export');

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/json');

    $data = $response->json();
    $this->assertArrayHasKey('export_date', $data);
    $this->assertArrayHasKey('version', $data);
    $this->assertArrayHasKey('settings', $data);
    $this->assertEquals('IOTCNT Test System', $data['settings']['system_name']);
  }

  public function test_admin_can_import_settings()
  {
    Sanctum::actingAs($this->adminUser);

    Storage::fake('local');

    // Create a valid settings JSON file
    $settingsData = [
      'export_date' => now()->toISOString(),
      'version' => '1.0.0',
      'settings' => [
        'system_name' => 'Imported System Name',
        'default_valve_duration' => 8,
        'telegram_notifications' => true,
        'api_rate_limit' => 100
      ]
    ];

    $file = UploadedFile::fake()->createWithContent(
      'settings.json',
      json_encode($settingsData)
    );

    $response = $this->post('/admin/settings/import', [
      'settings_file' => $file
    ]);

    $response->assertStatus(200)
      ->assertJson(['success' => true]);

    // Verify settings were imported
    $this->assertEquals('Imported System Name', SystemSetting::getValue('system_name'));
    $this->assertEquals(8, SystemSetting::getValue('default_valve_duration'));
    $this->assertTrue(SystemSetting::getValue('telegram_notifications'));
    $this->assertEquals(100, SystemSetting::getValue('api_rate_limit'));
  }

  public function test_import_validates_file_format()
  {
    Sanctum::actingAs($this->adminUser);

    // Test with non-JSON file
    $file = UploadedFile::fake()->create('settings.txt', 100);

    $response = $this->post('/admin/settings/import', [
      'settings_file' => $file
    ]);

    $response->assertStatus(422);

    // Test with invalid JSON content
    $file = UploadedFile::fake()->createWithContent(
      'settings.json',
      'invalid json content'
    );

    $response = $this->post('/admin/settings/import', [
      'settings_file' => $file
    ]);

    $response->assertStatus(500); // Should fail during processing

    // Test with missing settings key
    $file = UploadedFile::fake()->createWithContent(
      'settings.json',
      json_encode(['export_date' => now()->toISOString()])
    );

    $response = $this->post('/admin/settings/import', [
      'settings_file' => $file
    ]);

    $response->assertStatus(500); // Should fail due to missing settings key
  }

  public function test_system_setting_model_typed_values()
  {
    // Test string setting
    $stringSetting = SystemSetting::create([
      'key' => 'test_string',
      'value' => 'test value',
      'type' => 'string'
    ]);
    $this->assertEquals('test value', $stringSetting->typed_value);

    // Test integer setting
    $intSetting = SystemSetting::create([
      'key' => 'test_int',
      'value' => '42',
      'type' => 'integer'
    ]);
    $this->assertEquals(42, $intSetting->typed_value);
    $this->assertIsInt($intSetting->typed_value);

    // Test boolean setting (true)
    $boolTrueSetting = SystemSetting::create([
      'key' => 'test_bool_true',
      'value' => '1',
      'type' => 'boolean'
    ]);
    $this->assertTrue($boolTrueSetting->typed_value);

    // Test boolean setting (false)
    $boolFalseSetting = SystemSetting::create([
      'key' => 'test_bool_false',
      'value' => '0',
      'type' => 'boolean'
    ]);
    $this->assertFalse($boolFalseSetting->typed_value);

    // Test float setting
    $floatSetting = SystemSetting::create([
      'key' => 'test_float',
      'value' => '3.14',
      'type' => 'float'
    ]);
    $this->assertEquals(3.14, $floatSetting->typed_value);
    $this->assertIsFloat($floatSetting->typed_value);

    // Test array setting
    $arrayData = ['item1', 'item2', 'item3'];
    $arraySetting = SystemSetting::create([
      'key' => 'test_array',
      'value' => json_encode($arrayData),
      'type' => 'array'
    ]);
    $this->assertEquals($arrayData, $arraySetting->typed_value);
    $this->assertIsArray($arraySetting->typed_value);
  }

  public function test_system_setting_static_methods()
  {
    // Test getValue with existing setting
    $this->assertEquals('IOTCNT Test System', SystemSetting::getValue('system_name'));

    // Test getValue with non-existing setting and default
    $this->assertEquals('default_value', SystemSetting::getValue('non_existing', 'default_value'));

    // Test setValue
    SystemSetting::setValue('new_setting', 'new_value', 'Test description');
    $this->assertEquals('new_value', SystemSetting::getValue('new_setting'));

    $setting = SystemSetting::where('key', 'new_setting')->first();
    $this->assertEquals('Test description', $setting->description);

    // Test exists
    $this->assertTrue(SystemSetting::exists('system_name'));
    $this->assertFalse(SystemSetting::exists('non_existing_setting'));

    // Test removeSetting
    SystemSetting::removeSetting('new_setting');
    $this->assertFalse(SystemSetting::exists('new_setting'));
  }

  public function test_system_setting_bulk_operations()
  {
    $bulkSettings = [
      'bulk_setting_1' => 'value1',
      'bulk_setting_2' => 42,
      'bulk_setting_3' => true
    ];

    SystemSetting::bulkUpdate($bulkSettings);

    $this->assertEquals('value1', SystemSetting::getValue('bulk_setting_1'));
    $this->assertEquals(42, SystemSetting::getValue('bulk_setting_2'));
    $this->assertTrue(SystemSetting::getValue('bulk_setting_3'));
  }

  public function test_system_setting_scopes()
  {
    // Create settings with pattern
    SystemSetting::setValue('email.host', 'smtp.example.com');
    SystemSetting::setValue('email.port', 587);
    SystemSetting::setValue('telegram.token', 'test_token');

    // Test byKeyPattern scope
    $emailSettings = SystemSetting::byKeyPattern('email.%')->get();
    $this->assertCount(2, $emailSettings);

    // Test byType scope
    $stringSettings = SystemSetting::byType('string')->get();
    $this->assertGreaterThan(0, $stringSettings->count());

    // Test getCategory
    $emailCategory = SystemSetting::getCategory('email');
    $this->assertArrayHasKey('email.host', $emailCategory);
    $this->assertArrayHasKey('email.port', $emailCategory);
    $this->assertEquals('smtp.example.com', $emailCategory['email.host']);
  }

  public function test_api_settings_endpoint_excludes_sensitive_data()
  {
    Sanctum::actingAs($this->adminUser);

    // Create sensitive settings
    SystemSetting::setValue('smtp_password', 'secret_password');
    SystemSetting::setValue('telegram_bot_token', 'secret_token');

    $response = $this->get('/admin/api/settings');

    $response->assertStatus(200);
    $settings = $response->json('settings');

    // Verify sensitive data is excluded
    $this->assertArrayNotHasKey('smtp_password', $settings);
    $this->assertArrayNotHasKey('telegram_bot_token', $settings);

    // Verify non-sensitive data is included
    $this->assertArrayHasKey('system_name', $settings);
    $this->assertArrayHasKey('default_valve_duration', $settings);
  }

  public function test_settings_with_metadata()
  {
    $settingsWithMetadata = SystemSetting::getWithMetadata(['system_name', 'default_valve_duration']);

    $this->assertCount(2, $settingsWithMetadata);

    $systemNameSetting = $settingsWithMetadata->firstWhere('key', 'system_name');
    $this->assertNotNull($systemNameSetting);
    $this->assertEquals('IOTCNT Test System', $systemNameSetting['value']);
    $this->assertEquals('string', $systemNameSetting['type']);
    $this->assertArrayHasKey('updated_at', $systemNameSetting);
    $this->assertArrayHasKey('updated_by', $systemNameSetting);
  }

  public function test_regular_user_cannot_modify_settings()
  {
    Sanctum::actingAs($this->regularUser);

    $response = $this->put('/admin/settings', [
      'system_name' => 'Hacked System'
    ]);

    $response->assertStatus(403);

    $response = $this->post('/admin/settings/reset');
    $response->assertStatus(403);

    $response = $this->get('/admin/settings/export');
    $response->assertStatus(403);
  }

  public function test_settings_cache_is_cleared_on_update()
  {
    Sanctum::actingAs($this->adminUser);

    // First request should cache the settings
    $response1 = $this->get('/admin/api/settings');
    $response1->assertStatus(200);

    // Update settings
    $this->put('/admin/settings', [
      'system_name' => 'Updated via Cache Test',
      'timezone' => 'Europe/Lisbon',
      'default_valve_duration' => 5,
      'max_concurrent_valves' => 1,
      'api_rate_limit' => 60,
      'esp32_timeout' => 30,
      'log_retention_days' => 90
    ]);

    // Second request should return updated data (cache should be cleared)
    $response2 = $this->get('/admin/api/settings');
    $response2->assertStatus(200);

    $settings = $response2->json('settings');
    $this->assertEquals('Updated via Cache Test', $settings['system_name']);
  }
}
