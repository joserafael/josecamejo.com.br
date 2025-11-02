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
            return $request->route('locale');
        }
        
        if (session('locale')) {
            return session('locale');
        }
        
        if ($request->cookie('locale')) {
            return $request->cookie('locale');
        }
        
        $preferred = $request->getPreferredLanguage(config('app.locales'));
        return $preferred ?: config('app.fallback_locale');
    }
}
