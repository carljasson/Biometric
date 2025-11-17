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
        | SECURITY HEADERS
        |--------------------------------------------------------------------------
        */

        // HSTS (Force HTTPS)
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains'
        );

        // Prevent Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer Policy
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        // Permissions Policy
        $response->headers->set(
            'Permissions-Policy',
            "geolocation=(), microphone=(), camera=(), fullscreen=(self), payment=()"
        );

        // Cross-Origin Isolation
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        /*
        |--------------------------------------------------------------------------
        | CONTENT SECURITY POLICY (CSP)
        |--------------------------------------------------------------------------
        | Compatible with Laravel, JS, SweetAlert, Bootstrap,
        | reCAPTCHA, CDNJS, and JSDelivr.
        |--------------------------------------------------------------------------
        */

        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self';

             img-src 'self' data: https:;

             script-src 
                'self'
                https://www.gstatic.com
                https://www.google.com
                https://www.google-analytics.com
                https://cdn.jsdelivr.net
                https://cdnjs.cloudflare.com;

             style-src
                'self'
                https://cdn.jsdelivr.net
                https://cdnjs.cloudflare.com
                'unsafe-inline';

             frame-src 
                https://www.google.com 
                https://www.gstatic.com;

             font-src 'self' data: https:;

             connect-src 'self' https:;

             frame-ancestors 'self';

             base-uri 'self';
             form-action 'self';"
        );

        // Remove X-Powered-By (security)
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        return $response;
    }
}
