<?php

namespace Appttpontrollers;

use AppodelsredictionAnalysis;
use AppervicesredictionAnalysisService;
use Illuminatettpequest;
use IlluminatettpsonResponse;

class PredictionAnalysisController extends Controller
{
    /**
     * Display a listing of prediction analyses.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $metricType = $request->input('metric_type');
        $deviceId = $request->input('device_id');
        $status = $request->input('status');
        $alertLevel = $request->input('alert_level');
        $daysBack = $request->input('days_back', 7);

        $query = PredictionAnalysis::query();

        if ($search) {
            $query->where('current_value', 'like', '%' . $search . '%')
                  ->orWhere('predicted_value', 'like', '%' . $search . '%')
                  ->orWhere('trend_direction', 'like', '%' . $search . '%');
        }

        if ($metricType) {
            $query->where('metric_type', $metricType);
        }

        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($alertLevel) {
            $query->where('alert_level', $alertLevel);
        }

        if ($daysBack) {
            $query->where('prediction_timestamp', '>=', now()->subDays($daysBack));
        }

        $analyses = $query->orderBy('prediction_timestamp', 'desc')
                          ->paginate(15);

        return response()->json($analyses);
    }

    /**
     * Store a newly created prediction analysis.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = 
            
            
            
            
        $validator->sometimes('device_id', 'required|integer',
                             'metric_type', 'required|in:temperature,humidity,pressure,voltage')
                      ->sometimes('current_value', 'required|numeric',
                                   'threshold_min', 'nullable|numeric')
                      ->sometimes('threshold_max', 'nullable|numeric')
                      ->sometimes('moving_average', 'nullable|numeric')
                      ->sometimes('alert_level', 'in:normal,warning,critical');

        $validator->validate();

        $data = $request->all();
        $service = new PredictionAnalysisService();

        $analysis = $service->performAnalysis(
            $data['device_id'],
            $data['metric_type'],
            $data['current_value'],
            ['min' => $data['threshold_min'], 'max' => $data['threshold_max']] ?? []
        );

        if (!$analysis) {
            return response()->json(
                ['error' => 'Failed to save prediction analysis'], 
                 500
            );
        }

        return response()->json($analysis, 201);
    }

    /**
     * Display the specified prediction analysis.
     */
    public function show(int $id): JsonResponse
    {
        $analysis = PredictionAnalysis::find($id);

        if (!$analysis) {
            return response()->json(['error' => 'Analysis not found'], 404);
        }

        return response()->json($analysis);
    }

    /**
     * Update the specified prediction analysis.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $analysis = PredictionAnalysis::find($id);

        if (!$analysis) {
            return response()->json(['error' => 'Analysis not found'], 404);
        }

        $validator = 
            
            
            
            
            
            
        $validator->sometimes('current_value', 'required|numeric')
                      ->sometimes('metric_type', 'required|in:temperature,humidity,pressure,voltage')
                      ->sometimes('threshold_min', 'nullable|numeric')
                      ->sometimes('threshold_max', 'nullable|numeric')
                      ->sometimes('alert_level', 'in:normal,warning,critical');

        $validator->validate();

        $data = $request->all();
        $service = new PredictionAnalysisService();

        $analysis->update($data);

        return response()->json($analysis);
    }

    /**
     * Remove the specified prediction analysis.
     */
    public function destroy(int $id): JsonResponse
    {
        $analysis = PredictionAnalysis::find($id);

        if (!$analysis) {
            return response()->json(['error' => 'Analysis not found'], 404);
        }

        $analysis->delete();

        return response()->json(['message' => 'Analysis deleted successfully']);
    }

    /**
     * Get prediction analysis history for a specific device and metric
     */
    public function history(Request $request, int $deviceId, string $metricType)
    {
        $days = $request->input('days', 7);

        $history = PredictionAnalysis::where('device_id', $deviceId)
            ->where('metric_type', $metricType)
            ->where('prediction_timestamp', '>=', now()->subDays($days))
            ->orderBy('prediction_timestamp', 'desc')
            ->get();

        return response()->json($history);
    }

    /**
     * Get device statistics for a specific device
     */
    public function statistics(int $deviceId): JsonResponse
    {
        $service = new PredictionAnalysisService();
        $statistics = $service->getDeviceStatistics($deviceId);

        return response()->json($statistics);
    }
}
