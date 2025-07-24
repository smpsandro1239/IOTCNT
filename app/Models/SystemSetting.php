<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
  use HasFactory;

  protected $fillable = [
    'key',
    'value',
    'type',
    'description',
    'is_public'
  ];

  protected $casts = [
    'is_public' => 'boolean'
  ];

  /**
   * Get a setting value by key.
   */
  public static function get(string $key, $default = null)
  {
    $setting = static::where('key', $key)->first();

    if (!$setting) {
      return $default;
    }

    return static::castValue($setting->value, $setting->type);
  }

  /**
   * Set a setting value by key.
   */
  public static function set(string $key, $value, string $type = 'string', string $description = null): void
  {
    static::updateOrCreate(
      ['key' => $key],
      [
        'value' => $value,
        'type' => $type,
        'description' => $description
      ]
    );
  }

  /**
   * Get all public settings (accessible by ESP32).
   */
  public static function getPublicSettings(): array
  {
    return static::where('is_public', true)
      ->get()
      ->mapWithKeys(function ($setting) {
        return [$setting->key => static::castValue($setting->value, $setting->type)];
      })
      ->toArray();
  }

  /**
   * Cast value to appropriate type.
   */
  protected static function castValue($value, string $type)
  {
    return match ($type) {
      'integer' => (int) $value,
      'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
      'json' => json_decode($value, true),
      'float' => (float) $value,
      default => $value
    };
  }

  /**
   * Get the value attribute with proper casting.
   */
  public function getValueAttribute($value)
  {
    return static::castValue($value, $this->type);
  }
}
