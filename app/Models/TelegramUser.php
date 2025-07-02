<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'telegram_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'telegram_chat_id',
        'telegram_username',
        'first_name',
        'last_name',
        'user_id', // Web user linkage
        'is_authorized',
        'authorization_level',
        'receive_notifications',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'telegram_chat_id' => 'integer',
        'is_authorized' => 'boolean',
        'receive_notifications' => 'boolean',
    ];

    /**
     * Get the web user associated with this Telegram user (if any).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the operation logs initiated by this Telegram user.
     */
    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class);
    }
}
