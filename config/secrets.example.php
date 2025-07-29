<?php

/**
 * IOTCNT - Secrets Configuration Example
 *
 * IMPORTANTE:
 * 1. Copie este arquivo para config/secrets.php
 * 2. Configure com suas credenciais reais
 * 3. NUNCA commite o arquivo secrets.php
 */

return [
  /*
    |--------------------------------------------------------------------------
    | API Keys e Tokens Sensíveis
    |--------------------------------------------------------------------------
    */

  'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN', 'SEU_TOKEN_TELEGRAM_AQUI'),
    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET', 'webhook_secret_muito_seguro'),
  ],

  'esp32' => [
    'api_token' => env('ESP32_API_TOKEN', 'esp32_token_muito_seguro_aqui'),
    'encryption_key' => env('ESP32_ENCRYPTION_KEY', 'chave_criptografia_esp32'),
  ],

  'external_apis' => [
    'weather_api_key' => env('WEATHER_API_KEY', ''),
    'notification_service_key' => env('NOTIFICATION_SERVICE_KEY', ''),
  ],

  /*
    |--------------------------------------------------------------------------
    | Chaves de Criptografia Adicionais
    |--------------------------------------------------------------------------
    */

  'encryption' => [
    'master_key' => env('MASTER_ENCRYPTION_KEY', 'master_key_muito_segura'),
    'jwt_secret' => env('JWT_SECRET', 'jwt_secret_muito_seguro'),
    'api_signature_key' => env('API_SIGNATURE_KEY', 'signature_key_segura'),
  ],

  /*
    |--------------------------------------------------------------------------
    | Configurações de Produção
    |--------------------------------------------------------------------------
    */

  'production' => [
    'ssl_cert_path' => env('SSL_CERT_PATH', '/path/to/ssl/cert.pem'),
    'ssl_key_path' => env('SSL_KEY_PATH', '/path/to/ssl/private.key'),
    'backup_encryption_key' => env('BACKUP_ENCRYPTION_KEY', 'backup_key_segura'),
  ],

  /*
    |--------------------------------------------------------------------------
    | Configurações de Monitorização
    |--------------------------------------------------------------------------
    */

  'monitoring' => [
    'sentry_dsn' => env('SENTRY_DSN', ''),
    'log_webhook_url' => env('LOG_WEBHOOK_URL', ''),
    'alert_email_password' => env('ALERT_EMAIL_PASSWORD', ''),
  ],
];
