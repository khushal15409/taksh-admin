<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language', 'en');
        
        // Extract language code (e.g., 'en-US' -> 'en', 'hi-IN' -> 'hi')
        $locale = strtolower(substr($locale, 0, 2));
        
        // Supported locales: en, hi, gu
        $supportedLocales = ['en', 'hi', 'gu'];
        
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en'; // Default to English
        }
        
        app()->setLocale($locale);
        
        return $next($request);
    }
}
