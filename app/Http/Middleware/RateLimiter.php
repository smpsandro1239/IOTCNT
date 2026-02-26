<?php

namespace Appttpiddleware;

use Closure;
use Illuminatettpequest;
use IlluminateacheateLimiter;
use IlluminateupportacadesateLimiter as FacadesRateLimiter;
use SymfonyomponentttpFoundationesponse;

class RateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  lluminatettpequest  $request
     * @param  losure(lluminatettpequest): (lluminatettpesponse|lluminatettpedirectResponse)  $next
     * @return lluminatettpesponse|lluminatettpedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Definir limites de taxa para diferentes rotas
        $key = $this->resolveRequestSignature($request);
        
        // Limites de taxa diferentes para diferentes endpoints
        $limit = $this->getRateLimit($request);
        
        if (FacadesRateLimiter::tooManyAttempts($key, $limit['attempts'])) {
            $seconds = FacadesRateLimiter::availableIn($key);
            
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Você está fazendo muitas requisições. Por favor, aguarde ' . $seconds . ' segundos.',
                'retry_after' => $seconds
            ], 429);
        }
        
        FacadesRateLimiter::hit($key, $limit['decay']);
        
        return $next($request);
    }
    
    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        // Usar IP + rota como identificador
        return sha1(
            $request->ip() . '|' . $request->route()->getName()
        );
    }
    
    /**
     * Get rate limit for specific request.
     */
    protected function getRateLimit(Request $request): array
    {
        $routeName = $request->route()->getName();
        
        // Limites diferentes para diferentes rotas
        $limits = [
            'api.esp32.test-data' => ['attempts' => 60, 'decay' => 60], // 60 requisições por minuto
            'broadcast.esp32.data' => ['attempts' => 30, 'decay' => 60], // 30 requisições por minuto
            'broadcast.join.channel' => ['attempts' => 10, 'decay' => 60], // 10 requisições por minuto
            'default' => ['attempts' => 100, 'decay' => 60], // 100 requisições por minuto
        ];
        
        return $limits[$routeName] ?? $limits['default'];
    }
}
