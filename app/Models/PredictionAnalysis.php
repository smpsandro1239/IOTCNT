<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PredictionAnalysis extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prediction_analyses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'metric_type',
        'current_value',
        'moving_average',
        'trend_direction',
        'predicted_value',
        'threshold_min',
        'threshold_max',
        'alert_level',
        'status',
        'prediction_timestamp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'current_value' => 'float',
        'moving_average' => 'float',
        'predicted_value' => 'float',
        'threshold_min' => 'float',
        'threshold_max' => 'float',
        'prediction_timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the device that owns the prediction analysis.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Calculate moving average for a given metric
     *
     * @param string $metricType
     * @param int $deviceId
     * @param int $windowSize
     * @return float
     */
    public static function calculateMovingAverage($metricType, $deviceId, $windowSize = 10)
    {
        return self::where('metric_type', $metricType)
            ->where('device_id', $deviceId)
            ->orderBy('created_at', 'desc')
            ->limit($windowSize)
            ->avg('current_value');
    }

    /**
     * Determine trend direction based on historical data
     *
     * @param string $metricType
     * @param int $deviceId
     * @return string
     */
    public static function determineTrendDirection($metricType, $deviceId)
    {
        $recentData = self::where('metric_type', $metricType)
            ->where('device_id', $deviceId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['current_value', 'created_at']);

        if ($recentData->count() < 3) {
            return 'insufficient_data';
        }

        $values = $recentData->pluck('current_value')->toArray();
        $trend = self::calculateLinearTrend($values);

        return $trend > 0.1 ? 'increasing' : ($trend < -0.1 ? 'decreasing' : 'stable');
    }

    /**
     * Calculate linear trend using simple linear regression
     *
     * @param array $values
     * @return float
     */
    private static function calculateLinearTrend($values)
    {
        $n = count($values);
        if ($n < 2) return 0;

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $values[$i];
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
        }

        return ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
    }

    /**
     * Generate prediction based on trend and moving average
     *
     * @param float $currentValue
     * @param float $movingAverage
     * @param string $trendDirection
     * @param float $weight
     * @return float
     */
    public static function generatePrediction($currentValue, $movingAverage, $trendDirection, $weight = 0.3)
    {
        $trendMultiplier = match ($trendDirection) {
            'increasing' => 1.05,
            'decreasing' => 0.95,
            'stable' => 1.0,
            default => 1.0,
        };

        return ($movingAverage * (1 - $weight)) + ($currentValue * $weight) * $trendMultiplier;
    }

    /**
     * Check if value is within normal thresholds
     *
     * @param float $value
     * @param float $minThreshold
     * @param float $maxThreshold
     * @return string
     */
    public static function checkThreshold($value, $minThreshold, $maxThreshold)
    {
        if ($value < $minThreshold) {
            return 'warning';
        }

        if ($value > $maxThreshold) {
            return 'critical';
        }

        return 'normal';
    }

    /**
     * Create or update prediction analysis
     *
     * @param array $data
     * @return self
     */
    public static function createOrUpdateAnalysis($data)
    {
        $analysis = self::where('device_id', $data['device_id'])
            ->where('metric_type', $data['metric_type'])
            ->where('prediction_timestamp', '>=', now()->subHour())
            ->first();

        if ($analysis) {
            $analysis->update($data);
            return $analysis;
        }

        return self::create($data);
    }
}
