<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | SECURITY HEADERS (ALL FIXED)
        |--------------------------------------------------------------------------
        */

        // HSTS - Required for HTTPS security
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains'
        );

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Control referrer behavior
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy – safer version to avoid blocking browser features
        $response->headers->set(
            'Permissions-Policy',
            "geolocation=(), microphone=(), camera=(), fullscreen=(self), payment=()"
        );

        // Required for cross-origin isolation
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        /*
        |--------------------------------------------------------------------------
        | SAFE CONTENT SECURITY POLICY
        |--------------------------------------------------------------------------
        | Your previous CSP was too strict and would break Laravel, Bootstrap,
        | JS libraries, images, and sometimes Hostinger itself.
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self';
             img-src 'self' data: https:;
             script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;
             style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;
             font-src 'self' data: https:;
             connect-src 'self' https:;
             frame-ancestors 'self';
             base-uri 'self';
             form-action 'self';"
        );

        // Remove PHP version leak
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        return $response;
    }
}
