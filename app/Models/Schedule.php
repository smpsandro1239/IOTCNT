<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'day_of_week',
        'start_time',
        'per_valve_duration_minutes',
        'is_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'per_valve_duration_minutes' => 'integer',
        'is_enabled' => 'boolean',
        // start_time é uma string 'HH:MM:SS', não precisa de cast especial para atribuição,
        // mas pode precisar para manipulação com Carbon se for datetime completo.
        // Para Time apenas, o Laravel trata bem como string.
    ];

    /**
     * Get the operation logs associated with this schedule.
     */
    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class);
    }
}
