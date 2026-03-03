<?php

namespace App\Http\Controllers;

use App\Services\Esp32BroadcastService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class BroadcastController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|integer',
            'metric_type' => 'required|in:temperature,humidity,pressure',
            'current_value' => 'required|numeric',
            'threshold_min' => 'nullable|numeric',
            'threshold_max' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $analysis = (new Esp32BroadcastService())->broadcastData(
                $request->all()
            );
            return response()->json($analysis, 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao enviar dados: ' . $e->getMessage()
            ], 500);
        }
    }
}
