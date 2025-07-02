<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Valve extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'valves';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'valve_number',
        'description',
        'current_state',
        'last_activated_at',
        'esp32_pin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'current_state' => 'boolean',
        'last_activated_at' => 'datetime',
        'valve_number' => 'integer',
        'esp32_pin' => 'integer',
    ];

    /**
     * Get the operation logs for the valve.
     */
    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class);
    }
}
