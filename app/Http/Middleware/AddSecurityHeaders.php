<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // --- Fixing A05: Security Misconfiguration ---
        
        // 1. X-Content-Type-Options: Prevents MIME-sniffing attacks.
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);

        // 2. X-Frame-Options: Prevents Clickjacking. Use DENY or SAMEORIGIN.
        $response->headers->set('X-Frame-Options', 'DENY', false);

        // 3. Referrer-Policy: Prevents leaking sensitive data to external sites.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);
        
        // --- Fixing A02: Cryptographic Failures ---

        // 4. Strict-Transport-Security (HSTS): Forces HTTPS after the first visit.
        // NOTE: Only set this if your application is ONLY served over HTTPS.
        if ($request->isSecure()) {
            // max-age=1 year (31536000 seconds)
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains', false);
        }

        // 5. Content-Security-Policy (CSP) - Simple Example
        // NOTE: CSP is complex and often needs specific sources added.
        // This is a minimal secure policy (default-src 'self').
        $response->headers->set(
            'Content-Security-Policy', 
            "default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-eval'", 
            false
        );


        // OPTIONAL: Hide server information (A05)
        $response->headers->remove('Server');


        return $response;
    }
}