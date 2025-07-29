<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\SystemSetting;

class SettingsController extends Controller
{
  /**
   * Display the settings page.
   */
  public function index()
  {
    $settings = $this->getAllSettings();

    return view('admin.settings.index', compact('settings'));
  }

  /**
   * Update system settings.
   */
  public function update(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'system_name' => 'required|string|max:255',
      'timezone' => 'required|string|max:50',
      'default_valve_duration' => 'required|integer|between:1,60',
      'max_concurrent_valves' => 'required|integer|between:1,5',
      'auto_cycle_enabled' => 'boolean',
      'cycle_duration_per_valve' => 'required|integer|between:1,30',
      'telegram_notifications' => 'boolean',
      'telegram_bot_token' => 'nullable|string|max:255',
      'email_notifications' => 'boolean',
      'smtp_host' => 'nullable|string|max:255',
      'smtp_port' => 'nullable|integer|between:1,65535',
      'smtp_username' => 'nullable|string|max:255',
      'smtp_password' => 'nullable|string|max:255',
      'smtp_encryption' => 'nullable|in:tls,ssl',
      'api_rate_limit' => 'required|integer|between:10,1000',
      'esp32_timeout' => 'required|integer|between:5,300',
      'log_retention_days' => 'required|integer|between:7,365',
      'backup_enabled' => 'boolean',
      'backup_frequency' => 'required|in:daily,weekly,monthly',
      'maintenance_mode' => 'boolean',
      'debug_mode' => 'boolean'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Dados inválidos',
        'errors' => $validator->errors()
      ], 422);
    }

    try {
      foreach ($request->all() as $key => $value) {
        if ($key !== '_token' && $key !== '_method') {
          $this->setSetting($key, $value);
        }
      }

      // Limpar cache de configurações
      Cache::forget('system_settings');

      return response()->json([
        'success' => true,
        'message' => 'Configurações atualizadas com sucesso'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro ao atualizar configurações: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Reset settings to default values.
   */
  public function reset()
  {
    try {
      $defaultSettings = $this->getDefaultSettings();

      foreach ($defaultSettings as $key => $value) {
        $this->setSetting($key, $value);
      }

      Cache::forget('system_settings');

      return response()->json([
        'success' => true,
        'message' => 'Configurações restauradas para os valores padrão'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro ao restaurar configurações: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Export settings.
   */
  public function export()
  {
    try {
      $settings = $this->getAllSettings();

      $export = [
        'export_date' => now()->toISOString(),
        'version' => '1.0.0',
        'settings' => $settings
      ];

      $filename = 'iotcnt_settings_' . now()->format('Y-m-d_H-i-s') . '.json';

      return response()->json($export)
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro na exportação: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Import settings.
   */
  public function import(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'settings_file' => 'required|file|mimes:json|max:2048'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Arquivo inválido',
        'errors' => $validator->errors()
      ], 422);
    }

    try {
      $file = $request->file('settings_file');
      $content = file_get_contents($file->getPathname());
      $data = json_decode($content, true);

      if (!$data || !isset($data['settings'])) {
        throw new \Exception('Formato de arquivo inválido');
      }

      $importedCount = 0;
      foreach ($data['settings'] as $key => $value) {
        if ($this->isValidSettingKey($key)) {
          $this->setSetting($key, $value);
          $importedCount++;
        }
      }

      Cache::forget('system_settings');

      return response()->json([
        'success' => true,
        'message' => "Configurações importadas com sucesso ({$importedCount} itens)"
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro na importação: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Test email configuration.
   */
  public function testEmail()
  {
    try {
      $settings = $this->getAllSettings();

      if (!$settings['email_notifications']) {
        throw new \Exception('Notificações por email estão desativadas');
      }

      // Configurar temporariamente as configurações de email
      config([
        'mail.mailers.smtp.host' => $settings['smtp_host'],
        'mail.mailers.smtp.port' => $settings['smtp_port'],
        'mail.mailers.smtp.username' => $settings['smtp_username'],
        'mail.mailers.smtp.password' => $settings['smtp_password'],
        'mail.mailers.smtp.encryption' => $settings['smtp_encryption'],
      ]);

      // Enviar email de teste
      \Mail::raw('Este é um email de teste do sistema IOTCNT.', function ($message) {
        $message->to(auth()->user()->email)
          ->subject('Teste de Email - IOTCNT');
      });

      return response()->json([
        'success' => true,
        'message' => 'Email de teste enviado com sucesso'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro no teste de email: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Test Telegram configuration.
   */
  public function testTelegram()
  {
    try {
      $settings = $this->getAllSettings();

      if (!$settings['telegram_notifications'] || !$settings['telegram_bot_token']) {
        throw new \Exception('Configuração do Telegram incompleta');
      }

      // Testar conexão com API do Telegram
      $response = \Http::get("https://api.telegram.org/bot{$settings['telegram_bot_token']}/getMe");

      if (!$response->successful()) {
        throw new \Exception('Token do Telegram inválido');
      }

      $botInfo = $response->json();

      return response()->json([
        'success' => true,
        'message' => 'Conexão com Telegram testada com sucesso',
        'bot_info' => [
          'name' => $botInfo['result']['first_name'],
          'username' => $botInfo['result']['username']
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro no teste do Telegram: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get all system settings.
   */
  private function getAllSettings()
  {
    return Cache::remember('system_settings', 3600, function () {
      $defaults = $this->getDefaultSettings();
      $settings = [];

      foreach ($defaults as $key => $defaultValue) {
        $settings[$key] = $this->getSetting($key, $defaultValue);
      }

      return $settings;
    });
  }

  /**
   * Get a specific setting value.
   */
  private function getSetting($key, $default = null)
  {
    $setting = SystemSetting::where('key', $key)->first();

    if (!$setting) {
      return $default;
    }

    // Converter valores baseado no tipo
    switch ($setting->type) {
      case 'boolean':
        return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
      case 'integer':
        return (int) $setting->value;
      case 'float':
        return (float) $setting->value;
      default:
        return $setting->value;
    }
  }

  /**
   * Set a setting value.
   */
  private function setSetting($key, $value)
  {
    $type = $this->getSettingType($key, $value);

    SystemSetting::updateOrCreate(
      ['key' => $key],
      [
        'value' => $value,
        'type' => $type,
        'updated_by' => auth()->id()
      ]
    );
  }

  /**
   * Get setting type based on key and value.
   */
  private function getSettingType($key, $value)
  {
    if (is_bool($value)) {
      return 'boolean';
    }

    if (is_int($value)) {
      return 'integer';
    }

    if (is_float($value)) {
      return 'float';
    }

    return 'string';
  }

  /**
   * Check if setting key is valid.
   */
  private function isValidSettingKey($key)
  {
    $validKeys = array_keys($this->getDefaultSettings());
    return in_array($key, $validKeys);
  }

  /**
   * Get default settings.
   */
  private function getDefaultSettings()
  {
    return [
      // Sistema
      'system_name' => 'IOTCNT Irrigation System',
      'timezone' => 'Europe/Lisbon',
      'maintenance_mode' => false,
      'debug_mode' => false,

      // Válvulas
      'default_valve_duration' => 5,
      'max_concurrent_valves' => 1,
      'auto_cycle_enabled' => true,
      'cycle_duration_per_valve' => 5,

      // Notificações
      'telegram_notifications' => false,
      'telegram_bot_token' => '',
      'email_notifications' => false,

      // Email SMTP
      'smtp_host' => '',
      'smtp_port' => 587,
      'smtp_username' => '',
      'smtp_password' => '',
      'smtp_encryption' => 'tls',

      // API
      'api_rate_limit' => 60,
      'esp32_timeout' => 30,

      // Logs e Backup
      'log_retention_days' => 90,
      'backup_enabled' => false,
      'backup_frequency' => 'weekly'
    ];
  }

  /**
   * Get settings for API.
   */
  public function getApiSettings()
  {
    $settings = $this->getAllSettings();

    // Remover configurações sensíveis
    unset($settings['smtp_password']);
    unset($settings['telegram_bot_token']);

    return response()->json([
      'success' => true,
      'settings' => $settings
    ]);
  }
}
