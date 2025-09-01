<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HybridAuth
{
  /**
   * Handle an incoming request.
   * Suporta tanto autenticação Laravel quanto HTML/JavaScript
   */
  public function handle(Request $request, Closure $next)
  {
    // Verificar se é uma requisição para páginas HTML estáticas
    if ($this->isStaticHtmlRequest($request)) {
      return $next($request);
    }

    // Verificar autenticação Laravel
    if (Auth::check()) {
      return $next($request);
    }

    // Verificar autenticação HTML via session
    if ($this->isHtmlAuthenticated($request)) {
      return $next($request);
    }

    // Redirecionar para login baseado no tipo de requisição
    if ($request->expectsJson() || $request->is('api/*')) {
      return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    return redirect()->guest(route('login'));
  }

  /**
   * Verificar se é uma requisição para páginas HTML estáticas
   */
  private function isStaticHtmlRequest(Request $request): bool
  {
    $staticPages = [
      'index-iotcnt.html',
      'login-iotcnt.html',
      'dashboard-admin.html',
      'dashboard-user.html',
      'valve-control.html',
      'scheduling.html',
      'system-settings.html',
      'monitoring-dashboard.html',
      'charts-dashboard.html',
      'reports-dashboard.html',
      'api-docs.html',
      'notifications.html',
      'email-dashboard.html',
      'esp32-dashboard.html',
      'test-dashboard.html',
      'documentation-dashboard.html',
      'system-logs.html',
      'database-admin.html',
      'backup-admin.html',
      'performance-metrics.html',
      'mobile-app.html'
    ];

    $path = $request->path();

    return in_array($path, $staticPages) ||
      str_ends_with($path, '.html') ||
      str_ends_with($path, '.css') ||
      str_ends_with($path, '.js') ||
      str_ends_with($path, '.png') ||
      str_ends_with($path, '.jpg') ||
      str_ends_with($path, '.ico');
  }

  /**
   * Verificar autenticação HTML via session/localStorage simulation
   */
  private function isHtmlAuthenticated(Request $request): bool
  {
    // Verificar se existe uma session HTML válida
    $htmlAuth = Session::get('html_auth');

    if ($htmlAuth && isset($htmlAuth['authenticated']) && $htmlAuth['authenticated']) {
      return true;
    }

    // Verificar headers de autenticação HTML (para AJAX)
    $authHeader = $request->header('X-HTML-Auth');
    if ($authHeader) {
      $authData = json_decode(base64_decode($authHeader), true);
      if ($authData && isset($authData['email']) && isset($authData['authenticated'])) {
        return $authData['authenticated'] === true;
      }
    }

    return false;
  }
}
