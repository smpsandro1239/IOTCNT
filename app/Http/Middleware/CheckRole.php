<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  O papel necessário para aceder à rota.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            // Se o utilizador não estiver logado, redirecionar para a página de login.
            return redirect('login');
        }

        // Auth::user() nunca será null aqui porque Auth::check() foi verificado.
        $user = Auth::user();

        if ($user->role === $role) {
            return $next($request);
        }

        // Se o utilizador não tiver o papel necessário, abortar com um 403 Forbidden.
        // Poderia também redirecionar para uma página específica de "não autorizado".
        abort(403, 'Acesso não autorizado.');
    }
}
