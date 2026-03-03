<?php

namespace App\Http\Controllers;

use App\Services\Esp32BroadcastService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{
    public function testBroadcast(Request $request)
    {
        try {
            $service = new Esp32BroadcastService();
            $result = $service->simulateData($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Teste de broadcast executado',
                'result' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no teste: ' . $e->getMessage()
            ], 500);
        }
    }
}
