<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // 🛡️ Security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Older browsers (optional, no longer used in modern Chrome)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Strong Content Security Policy
$response->headers->set(
    'Content-Security-Policy',
    "default-src 'self'; ".
    "script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ".
    "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; ".
    "font-src 'self' data:; ". // 👈 allow local fonts and data URIs
    "img-src 'self' data: https:; ".
    "object-src 'none'; ".
    "frame-ancestors 'self'; ".
    "base-uri 'self'; ".
    "form-action 'self'; ".
    "upgrade-insecure-requests;"
);


        // HSTS only on HTTPS
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Hide PHP version (Hostinger exposes this by default)
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
