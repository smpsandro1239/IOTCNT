<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
        'user_id',
        'name',
        'description',
        'day_of_week',
        'start_time',
        'per_valve_duration_minutes',
        'is_active',
        'is_enabled', // Manter compatibilidade
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'per_valve_duration_minutes' => 'integer',
        'is_active' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns the schedule.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the operation logs associated with this schedule.
     */
    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class);
    }

    /**
     * Get the next execution time for this schedule.
     */
    public function getNextExecution($from = null)
    {
        if (!$this->is_active && !$this->is_enabled) {
            return null;
        }

        $from = $from ?: Carbon::now();
        $dayOfWeek = $this->day_of_week;
        $time = $this->start_time;

        // Criar data/hora para hoje
        $today = $from->copy()->setTimeFromTimeString($time);

        // Se é hoje e ainda não passou a hora
        if ($from->dayOfWeek === $dayOfWeek && $from->lt($today)) {
            return $today;
        }

        // Calcular próxima ocorrência
        $daysUntilNext = ($dayOfWeek - $from->dayOfWeek + 7) % 7;
        if ($daysUntilNext === 0) {
            $daysUntilNext = 7; // Próxima semana
        }

        return $from->copy()->addDays($daysUntilNext)->setTimeFromTimeString($time);
    }

    /**
     * Get the day name in Portuguese.
     */
    public function getDayNameAttribute()
    {
        $days = [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado'
        ];

        return $days[$this->day_of_week] ?? 'Desconhecido';
    }

    /**
     * Get the formatted start time.
     */
    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->start_time)->format('H:i');
    }

    /**
     * Get the total duration for all valves.
     */
    public function getTotalDurationAttribute()
    {
        // Assumindo 5 válvulas por padrão
        $totalValves = 5;
        return $this->per_valve_duration_minutes * $totalValves;
    }

    /**
     * Check if the schedule is currently active.
     */
    public function getIsCurrentlyActiveAttribute()
    {
        return $this->is_active || $this->is_enabled;
    }

    /**
     * Scope to get only active schedules.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('is_active', true)
                ->orWhere('is_enabled', true);
        });
    }

    /**
     * Scope to get schedules for a specific day.
     */
    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope to get schedules for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if this schedule conflicts with another.
     */
    public function conflictsWith(Schedule $other)
    {
        return $this->day_of_week === $other->day_of_week &&
            $this->start_time === $other->start_time &&
            $this->id !== $other->id;
    }

    /**
     * Get schedules that should run now.
     */
    public static function shouldRunNow($tolerance = 60)
    {
        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeek;
        $currentTime = $now->format('H:i:s');

        // Buscar agendamentos para hoje que devem executar agora (com tolerância)
        return static::active()
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($query) use ($currentTime, $tolerance) {
                $startTime = Carbon::parse($currentTime)->subSeconds($tolerance);
                $endTime = Carbon::parse($currentTime)->addSeconds($tolerance);

                $query->whereBetween('start_time', [
                    $startTime->format('H:i:s'),
                    $endTime->format('H:i:s')
                ]);
            })
            ->get();
    }
}
