<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LocaleTest extends TestCase
{
    /**
     * Test browser language detection (Spanish).
     */
    public function test_browser_language_detection_es()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'es',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('es', App::getLocale());
    }

    /**
     * Test browser language detection (Portuguese).
     */
    public function test_browser_language_detection_pt()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'pt',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('pt', App::getLocale());
    }

    /**
     * Test browser language detection (English).
     */
    public function test_browser_language_detection_en()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('en', App::getLocale());
    }

    /**
     * Test fallback to English for unsupported language.
     */
    public function test_fallback_to_english_for_unsupported_language()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('en', App::getLocale());
    }

    /**
     * Test URL prefix overrides browser language.
     */
    public function test_url_prefix_overrides_browser_language()
    {
        // Request /es/ but browser says English
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
        ])->get('/es'); // Assuming /es redirects to /es/ or is valid

        // Note: The route is defined as prefix($locale).
        // If /es hits the home controller, it should have set locale to es.
        
        $response->assertStatus(200);
        $this->assertEquals('es', App::getLocale());
    }
}
