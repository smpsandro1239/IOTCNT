<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FallbackMonitoringMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        // Monitorar falhas e registrar métricas
        if ($response->status() >= 500) {
            Log::error('Server error', [
                'endpoint' => $request->path(),
                'duration' => $duration,
                'status' => $response->status(),
                'ip' => $request->ip()
            ]);
        }

        return $response;
    }
}
