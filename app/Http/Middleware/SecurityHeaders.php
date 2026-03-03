<?php

namespace Appttpiddleware;

use Closure;
use Illuminatettpequest;

class SecurityHeaders
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
        $response = $next($request);
        
        // Headers de segurança OWASP
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set('Content-Security-Policy', 
