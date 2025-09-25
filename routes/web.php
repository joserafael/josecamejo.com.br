<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui estão registradas as rotas web para sua aplicação. Estas rotas
| são carregadas pelo RouteServiceProvider e todas elas serão atribuídas
| ao grupo de middleware "web".
|
*/

// Language switcher route (without prefix)
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'es', 'pt'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

// Default routes (without language prefix) - redirect to Portuguese
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/send-message', [HomeController::class, 'sendMessage'])->name('send.message');
Route::get('/generate-captcha', [HomeController::class, 'generateCaptcha'])->name('generate.captcha');
Route::get('/politica-de-privacidade', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');

// Localized routes for each language
foreach (['en', 'es', 'pt'] as $locale) {
    Route::prefix($locale)->name($locale . '.')->group(function () use ($locale) {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::post('/send-message', [HomeController::class, 'sendMessage'])->name('send.message');
        Route::get('/generate-captcha', [HomeController::class, 'generateCaptcha'])->name('generate.captcha');
        
        // Privacy policy with different URLs per language
        if ($locale === 'pt') {
            Route::get('/politica-de-privacidade', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
        } elseif ($locale === 'es') {
            Route::get('/politica-de-privacidad', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
        } else {
            Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
        }
    });
}

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas do painel administrativo
Route::prefix('admin')->name('admin.')->group(function () {
    // Incluir todas as rotas do admin do arquivo separado
    require __DIR__.'/admin.php';
});
