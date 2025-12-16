<?php

use Illuminate\Http\Request;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Initialize facades
$app->instance('config', new \Illuminate\Config\Repository([
    'app' => [
        'locales' => ['en', 'es', 'pt'],
        'fallback_locale' => 'en'
    ]
]));

// Test cases
$urls = ['/en', '/es', '/pt', 'http://localhost/pt'];

foreach ($urls as $url) {
    if (strpos($url, 'http') === 0) {
        $request = Request::create($url, 'GET');
    } else {
        $request = Request::create('http://localhost' . $url, 'GET');
    }
    
    // Simulate middleware logic manually for testing
    $middleware = new SetLocale();
    
    // We need to reflect the protected getLocale method
    $reflection = new ReflectionClass($middleware);
    $method = $reflection->getMethod('getLocale');
    $method->setAccessible(true);
    
    $detectedLocale = $method->invokeArgs($middleware, [$request]);
    
    echo "URL: $url -> Detected Locale: $detectedLocale\n";
}
