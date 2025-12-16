<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);
        
        if (!in_array($locale, config('app.locales'))) {
            $locale = config('app.fallback_locale');
        }
        
        app()->setLocale($locale);
        session(['locale' => $locale]);
        cookie()->queue('locale', $locale, 525600);
        
        return $next($request);
    }
    
    protected function getLocale(Request $request): string
    {
        if ($request->route('locale')) {
            \Illuminate\Support\Facades\Log::info('SetLocale: Found route locale: ' . $request->route('locale'));
            return $request->route('locale');
        }

        $segment = $request->segment(1);
        if ($segment && in_array($segment, config('app.locales'))) {
            \Illuminate\Support\Facades\Log::info('SetLocale: Found segment locale: ' . $segment);
            return $segment;
        }
        
        if (session('locale')) {
            \Illuminate\Support\Facades\Log::info('SetLocale: Found session locale: ' . session('locale'));
            return session('locale');
        }
        
        if ($request->cookie('locale')) {
            \Illuminate\Support\Facades\Log::info('SetLocale: Found cookie locale: ' . $request->cookie('locale'));
            return $request->cookie('locale');
        }
        
        $preferred = $request->getPreferredLanguage(config('app.locales'));
        \Illuminate\Support\Facades\Log::info('SetLocale: Using preferred/fallback locale: ' . ($preferred ?: config('app.fallback_locale')));
        return $preferred ?: config('app.fallback_locale');
    }
}
