<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // ===== STANDARD SECURITY HEADERS =====
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');                  // Clickjacking
        $response->headers->set('X-Content-Type-Options', 'nosniff');             // MIME sniffing
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin'); 
        $response->headers->set('X-XSS-Protection', '1; mode=block');             // Legacy XSS protection
        $response->headers->set('Permissions-Policy', 'geolocation=(self), camera=(), microphone=()'); // Permissions
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:;");

        // ===== HSTS =====
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // ===== SECURE COOKIES =====
        // Force Laravel session cookie to use __Secure- prefix and secure flag
        if ($request->isSecure()) {
            $cookieName = Config::get('session.cookie', 'laravel_session');
            $response->headers->setCookie(
                cookie(
                    '__Secure-' . $cookieName,
                    $request->cookie($cookieName),
                    120,              // lifetime in minutes
                    '/',
                    null,
                    true,             // Secure
                    true,             // HttpOnly
                    false,            // raw
                    'Lax'             // SameSite
                )
            );
        }

        return $response;
    }
}
