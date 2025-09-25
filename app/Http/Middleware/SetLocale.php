<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['en', 'es', 'pt'];
        $defaultLocale = 'pt'; // Portuguese as default since it's a .com.br domain
        
        // Check if locale is provided in URL
        $locale = $request->segment(1);
        
        if (in_array($locale, $supportedLocales)) {
            // Store locale in session
            Session::put('locale', $locale);
            App::setLocale($locale);
        } else {
            // Check if locale is stored in session
            $sessionLocale = Session::get('locale');
            
            if ($sessionLocale && in_array($sessionLocale, $supportedLocales)) {
                App::setLocale($sessionLocale);
            } else {
                // Detect browser language
                $browserLocale = $this->detectBrowserLanguage($request, $supportedLocales);
                
                if ($browserLocale) {
                    Session::put('locale', $browserLocale);
                    App::setLocale($browserLocale);
                } else {
                    // Use default locale
                    Session::put('locale', $defaultLocale);
                    App::setLocale($defaultLocale);
                }
            }
        }
        
        return $next($request);
    }
    
    /**
     * Detect browser language preference
     *
     * @param Request $request
     * @param array $supportedLocales
     * @return string|null
     */
    private function detectBrowserLanguage(Request $request, array $supportedLocales)
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }
        
        // Parse Accept-Language header
        $languages = [];
        $parts = explode(',', $acceptLanguage);
        
        foreach ($parts as $part) {
            $part = trim($part);
            $langParts = explode(';', $part);
            $lang = trim($langParts[0]);
            
            // Extract just the language code (e.g., 'pt' from 'pt-BR')
            $langCode = substr($lang, 0, 2);
            
            if (in_array($langCode, $supportedLocales)) {
                return $langCode;
            }
        }
        
        return null;
    }
}