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

// Rota principal do site
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rotas para formulário de mensagem
Route::post('/send-message', [HomeController::class, 'sendMessage'])->name('send.message');
Route::get('/generate-captcha', [HomeController::class, 'generateCaptcha'])->name('generate.captcha');

// Política de privacidade
Route::get('/politica-de-privacidade', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas do painel administrativo
Route::prefix('admin')->name('admin.')->group(function () {
    // Incluir todas as rotas do admin do arquivo separado
    require __DIR__.'/admin.php';
});
