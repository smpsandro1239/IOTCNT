<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'system_settings';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'key',
    'value',
    'type',
    'description',
    'updated_by'
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'updated_by' => 'integer'
  ];

  /**
   * Get the user who last updated this setting.
   */
  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  /**
   * Get the typed value of the setting.
   */
  public function getTypedValueAttribute()
  {
    switch ($this->type) {
      case 'boolean':
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
      case 'integer':
        return (int) $this->value;
      case 'float':
        return (float) $this->value;
      case 'array':
        return json_decode($this->value, true);
      default:
        return $this->value;
    }
  }

  /**
   * Set the value with automatic type detection.
   */
  public function setTypedValue($value)
  {
    if (is_bool($value)) {
      $this->type = 'boolean';
      $this->value = $value ? '1' : '0';
    } elseif (is_int($value)) {
      $this->type = 'integer';
      $this->value = (string) $value;
    } elseif (is_float($value)) {
      $this->type = 'float';
      $this->value = (string) $value;
    } elseif (is_array($value)) {
      $this->type = 'array';
      $this->value = json_encode($value);
    } else {
      $this->type = 'string';
      $this->value = (string) $value;
    }
  }

  /**
   * Scope to get settings by key pattern.
   */
  public function scopeByKeyPattern($query, $pattern)
  {
    return $query->where('key', 'LIKE', $pattern);
  }

  /**
   * Scope to get settings by type.
   */
  public function scopeByType($query, $type)
  {
    return $query->where('type', $type);
  }

  /**
   * Get all settings as key-value pairs.
   */
  public static function getAllAsArray()
  {
    return static::all()->pluck('typed_value', 'key')->toArray();
  }

  /**
   * Get a setting value by key.
   */
  public static function getValue($key, $default = null)
  {
    $setting = static::where('key', $key)->first();
    return $setting ? $setting->typed_value : $default;
  }

  /**
   * Set a setting value by key.
   */
  public static function setValue($key, $value, $description = null)
  {
    $setting = static::updateOrCreate(
      ['key' => $key],
      [
        'description' => $description,
        'updated_by' => auth()->id()
      ]
    );

    $setting->setTypedValue($value);
    $setting->save();

    return $setting;
  }

  /**
   * Remove a setting by key.
   */
  public static function removeSetting($key)
  {
    return static::where('key', $key)->delete();
  }

  /**
   * Check if a setting exists.
   */
  public static function exists($key)
  {
    return static::where('key', $key)->exists();
  }

  /**
   * Get settings for a specific category.
   */
  public static function getCategory($category)
  {
    return static::where('key', 'LIKE', $category . '.%')
      ->get()
      ->pluck('typed_value', 'key')
      ->toArray();
  }

  /**
   * Bulk update settings.
   */
  public static function bulkUpdate(array $settings)
  {
    foreach ($settings as $key => $value) {
      static::setValue($key, $value);
    }
  }

  /**
   * Get settings with their metadata.
   */
  public static function getWithMetadata($keys = null)
  {
    $query = static::with('updatedBy:id,name');

    if ($keys) {
      $query->whereIn('key', (array) $keys);
    }

    return $query->get()->map(function ($setting) {
      return [
        'key' => $setting->key,
        'value' => $setting->typed_value,
        'type' => $setting->type,
        'description' => $setting->description,
        'updated_at' => $setting->updated_at,
        'updated_by' => $setting->updatedBy?->name
      ];
    });
  }
}
