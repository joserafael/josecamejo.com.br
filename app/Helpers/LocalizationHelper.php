<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class LocalizationHelper
{
    /**
     * Get all supported locales
     *
     * @return array
     */
    public static function getSupportedLocales(): array
    {
        return [
            'en' => 'English',
            'es' => 'Español',
            'pt' => 'Português'
        ];
    }
    
    /**
     * Get current locale
     *
     * @return string
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }
    
    /**
     * Generate localized URL
     *
     * @param string $locale
     * @param string|null $route
     * @param array $parameters
     * @return string
     */
    public static function getLocalizedUrl(string $locale, ?string $route = null, array $parameters = []): string
    {
        if (!$route) {
            $route = Route::currentRouteName();
        }
        
        // Remove locale prefix from route name if it exists
        $routeName = preg_replace('/^(en|es|pt)\./', '', $route);
        
        // Generate the localized route name
        $localizedRoute = $locale . '.' . $routeName;
        
        // Check if the localized route exists, if not use the base route
        if (Route::has($localizedRoute)) {
            return route($localizedRoute, $parameters);
        }
        
        // Fallback to adding locale prefix to URL
        $baseUrl = url('/');
        $currentPath = request()->path();
        
        // Remove existing locale from path
        $cleanPath = preg_replace('/^(en|es|pt)\//', '', $currentPath);
        $cleanPath = $cleanPath === '/' ? '' : $cleanPath;
        
        return $baseUrl . '/' . $locale . ($cleanPath ? '/' . $cleanPath : '');
    }
    
    /**
     * Get language switcher data
     *
     * @return array
     */
    public static function getLanguageSwitcher(): array
    {
        $currentLocale = self::getCurrentLocale();
        $supportedLocales = self::getSupportedLocales();
        $switcher = [];
        
        foreach ($supportedLocales as $locale => $name) {
            $switcher[] = [
                'code' => $locale,
                'name' => $name,
                'url' => self::getLocalizedUrl($locale, null, Route::current() ? Route::current()->parameters() : []),
                'active' => $locale === $currentLocale
            ];
        }
        
        return $switcher;
    }
    
    /**
     * Check if locale is supported
     *
     * @param string $locale
     * @return bool
     */
    public static function isLocaleSupported(string $locale): bool
    {
        return array_key_exists($locale, self::getSupportedLocales());
    }
}