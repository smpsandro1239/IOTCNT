<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'operation_logs';

    /**
     * Indicates if the model should be timestamped.
     *
     * We are using a custom 'logged_at' field instead of default 'created_at'/'updated_at'.
     *
     * @var bool
     */
    public $timestamps = false; // Desativa os timestamps created_at e updated_at automáticos

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'valve_id',
        'schedule_id',
        'user_id',
        'telegram_user_id',
        'event_type',
        'message',
        'source',
        'status',
        'details',
        'duration_seconds',
        'logged_at', // Adicionar logged_at aqui se quiser permitir a sua definição em massa
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array', // ou 'json' dependendo da preferência
        'logged_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     *
     * Populates logged_at when creating a new record, if not already set.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->logged_at)) {
                $log->logged_at = now();
            }
        });
    }

    /**
     * Get the valve associated with the log.
     */
    public function valve(): BelongsTo
    {
        return $this->belongsTo(Valve::class);
    }

    /**
     * Get the schedule associated with the log.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the user (web portal) associated with the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the Telegram user associated with the log.
     */
    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }
}
