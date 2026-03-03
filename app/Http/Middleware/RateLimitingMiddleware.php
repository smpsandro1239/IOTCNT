<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RateLimitingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'rate_limit_' . $request->ip();
        $limit = 200; // 200 requests por minuto
        $reset = now()->addMinute();

        $current = Cache::get($key, 0);

        if ($current >= $limit) {
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'endpoint' => $request->path(),
                'current' => $current,
                'limit' => $limit
            ]);

            return response()->json([
                'error' => 'Rate limit exceeded. Try again later.'
            ], 429);
        }

        Cache::put($key, $current + 1, $reset);

        return $next($request);
    }
}
