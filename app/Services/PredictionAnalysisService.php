<?php

namespace App\Services;

use App\Models\PredictionAnalysis;
use App\Models\Valve;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class PredictionAnalysisService
{
    /**
     * Perform complete analysis cycle for a device
     *
     * @param int $deviceId
     * @param string $metricType
     * @param float $currentValue
     * @param array $thresholds
     * @return PredictionAnalysis
     */
    public function performAnalysis($deviceId, $metricType, $currentValue, $thresholds = [])
    {
        try {
            // Get historical data for moving average
            $historicalData = $this->getRecentHistoricalData($deviceId, $metricType, 10);
            
            // Calculate moving average
            $movingAverage = $this->calculateMovingAverage($historicalData);
            
            // Determine trend direction
            $trendDirection = $this->determineTrendDirection($historicalData);
            
            // Generate prediction
            $predictedValue = $this->generatePrediction($currentValue, $movingAverage, $trendDirection);
            
            // Check thresholds
            $alertLevel = $this->checkThresholds($currentValue, $thresholds);
            
            // Create or update analysis
            $analysisData = [
                'device_id' => $deviceId,
                'metric_type' => $metricType,
                'current_value' => $currentValue,
                'moving_average' => $movingAverage,
                'trend_direction' => $trendDirection,
                'predicted_value' => $predictedValue,
                'threshold_min' => $thresholds['min'] ?? null,
                'threshold_max' => $thresholds['max'] ?? null,
                'alert_level' => $alertLevel,
                'status' => 'active',
            ];

            return PredictionAnalysis::createOrUpdateAnalysis($analysisData);
            
        } catch (\Exception $e) {
            \Log::error('PredictionAnalysisService error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get recent historical data for a device and metric
     *
     * @param int $deviceId
     * @param string $metricType
     * @param int $limit
     * @return array
     */
    private function getRecentHistoricalData($deviceId, $metricType, $limit = 10)
    {
        $logs = Valve::where('device_id', $deviceId)
            ->where('metric_type', $metricType)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get(['value', 'created_at']);

        return $logs->map(function ($log) {
            return [
                'value' => $log->value,
                'timestamp' => $log->created_at->timestamp,
            ];
        })->toArray();
    }

    /**
     * Calculate moving average from historical data
     *
     * @param array $historicalData
     * @return float
     */
    private function calculateMovingAverage($historicalData)
    {
        if (empty($historicalData)) {
            return 0;
        }

        $values = array_column($historicalData, 'value');
        return array_sum($values) / count($values);
    }

    /**
     * Determine trend direction from historical data
     *
     * @param array $historicalData
     * @return string
     */
    private function determineTrendDirection($historicalData)
    {
        if (count($historicalData) < 3) {
            return 'insufficient_data';
        }

        $values = array_column($historicalData, 'value');
        $trend = $this->calculateLinearTrend($values);

        return $trend > 0.1 ? 'increasing' : ($trend < -0.1 ? 'decreasing' : 'stable');
    }

    /**
     * Calculate linear trend using simple linear regression
     *
     * @param array $values
     * @return float
     */
    private function calculateLinearTrend($values)
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
     * @return float
     */
    private function generatePrediction($currentValue, $movingAverage, $trendDirection)
    {
        $trendMultiplier = match ($trendDirection) {
            'increasing' => 1.05,
            'decreasing' => 0.95,
            'stable' => 1.0,
            default => 1.0,
        };

        $weight = 0.3; // 30% current value, 70% moving average
        return ($movingAverage * (1 - $weight)) + ($currentValue * $weight) * $trendMultiplier;
    }

    /**
     * Check if value is within normal thresholds
     *
     * @param float $value
     * @param array $thresholds
     * @return string
     */
    private function checkThresholds($value, $thresholds)
    {
        $minThreshold = $thresholds['min'] ?? null;
        $maxThreshold = $thresholds['max'] ?? null;

        if ($minThreshold && $value < $minThreshold) {
            return 'warning';
        }

        if ($maxThreshold && $value > $maxThreshold) {
            return 'critical';
        }

        return 'normal';
    }

    /**
     * Get prediction analysis for a device and metric
     *
     * @param int $deviceId
     * @param string $metricType
     * @return PredictionAnalysis|null
     */
    public function getLatestAnalysis($deviceId, $metricType)
    {
        return PredictionAnalysis::where('device_id', $deviceId)
            ->where('metric_type', $metricType)
            ->where('status', 'active')
            ->latest('prediction_timestamp')
            ->first();
    }

    /**
     * Get prediction analysis history for a device and metric
     *
     * @param int $deviceId
     * @param string $metricType
     * @param int $days
     * @return array
     */
    public function getAnalysisHistory($deviceId, $metricType, $days = 7)
    {
        return PredictionAnalysis::where('device_id', $deviceId)
            ->where('metric_type', $metricType)
            ->where('prediction_timestamp', '>=', now()->subDays($days))
            ->orderBy('prediction_timestamp', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Archive old prediction analyses
     *
     * @param int $days
     * @return int
     */
    public function archiveOldAnalyses($days = 30)
    {
        return PredictionAnalysis::where('prediction_timestamp', '<', now()->subDays($days))
            ->where('status', 'active')
            ->update(['status' => 'archived']);
    }

    /**
     * Get prediction analysis statistics for a device
     *
     * @param int $deviceId
     * @return array
     */
    public function getDeviceStatistics($deviceId)
    {
        return [
            'total_analyses' => PredictionAnalysis::where('device_id', $deviceId)->count(),
            'active_analyses' => PredictionAnalysis::where('device_id', $deviceId)
                ->where('status', 'active')
                ->count(),
            'critical_alerts' => PredictionAnalysis::where('device_id', $deviceId)
                ->where('alert_level', 'critical')
                ->count(),
            'warning_alerts' => PredictionAnalysis::where('device_id', $deviceId)
                ->where('alert_level', 'warning')
                ->count(),
            'average_prediction_accuracy' => $this->calculateAveragePredictionAccuracy($deviceId),
        ];
    }

    /**
     * Calculate average prediction accuracy for a device
     *
     * @param int $deviceId
     * @return float
     */
    private function calculateAveragePredictionAccuracy($deviceId)
    {
        $analyses = PredictionAnalysis::where('device_id', $deviceId)
            ->where('predicted_value', '!=', null)
            ->where('status', 'active')
            ->get();

        if ($analyses->count() === 0) {
            return 0;
        }

        $totalError = 0;
        foreach ($analyses as $analysis) {
            $error = abs($analysis->predicted_value - $analysis->current_value);
            $totalError += $error;
        }

        return 100 - (($totalError / $analyses->count()) / 100 * 100);
    }
}
