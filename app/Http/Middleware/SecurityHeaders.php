<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers to protect against common web vulnerabilities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking attacks
        // Use SAMEORIGIN to allow embedding in same-origin frames (needed for OBS widgets)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS filter in browsers (legacy, but still useful)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - restrict powerful features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy
        // Note: Relaxed CSP to allow:
        // - 'unsafe-inline' for styles (Blade templates use inline styles)
        // - 'unsafe-eval' disabled for security
        // - External sources: fonts.googleapis.com, fonts.gstatic.com, code.iconify.design
        // - blob: and data: for audio/images
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://code.iconify.design https://api.iconify.design",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: blob: https:",
            "media-src 'self' blob: data:",
            "connect-src 'self' https://api.iconify.design",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
