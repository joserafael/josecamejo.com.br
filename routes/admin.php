<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogSubcategoryController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\BlogImageController;
use App\Http\Controllers\Admin\BlogVideoController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\AdminUserController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Aqui estão registradas todas as rotas para o painel administrativo.
| Todas as rotas são prefixadas com 'admin' e agrupadas com middleware
| de autenticação e autorização.
|
*/

// Aplicar middleware admin em todas as rotas
Route::middleware(['admin'])->group(function () {

// Dashboard principal
Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.index');

// Gerenciamento de Conteúdo - Rotas diretas (sem prefix content)
Route::resource('posts', PostController::class);
Route::resource('projects', ProjectController::class);

// Rotas adicionais para posts
Route::post('posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
Route::post('posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('posts.unpublish');

// Rotas adicionais para projetos
Route::post('projects/{project}/feature', [ProjectController::class, 'feature'])->name('projects.feature');
Route::post('projects/{project}/unfeature', [ProjectController::class, 'unfeature'])->name('projects.unfeature');

// Gerenciamento do Blog
Route::resource('blog-posts', BlogPostController::class);
Route::resource('blog-categories', BlogCategoryController::class);
Route::resource('blog-subcategories', BlogSubcategoryController::class);
Route::resource('blog-tags', BlogTagController::class);
Route::resource('blog-images', BlogImageController::class);
Route::resource('blog-videos', BlogVideoController::class);
Route::resource('blog-comments', BlogCommentController::class);

// Rotas adicionais para blog posts
Route::patch('blog-posts/{blogPost}/toggle-featured', [BlogPostController::class, 'toggleFeatured'])->name('blog-posts.toggle-featured');
Route::post('blog-posts/{blogPost}/duplicate', [BlogPostController::class, 'duplicate'])->name('blog-posts.duplicate');

// Rotas adicionais para blog comments
Route::post('blog-comments/{blogComment}/approve', [BlogCommentController::class, 'approve'])->name('blog-comments.approve');
Route::post('blog-comments/{blogComment}/reject', [BlogCommentController::class, 'reject'])->name('blog-comments.reject');
Route::post('blog-comments/bulk-action', [BlogCommentController::class, 'bulkAction'])->name('blog-comments.bulk-action');

// Habilidades (Skills)
Route::get('skills', [AdminController::class, 'skills'])->name('skills.index');
Route::get('skills/create', [AdminController::class, 'createSkill'])->name('skills.create');
Route::post('skills', [AdminController::class, 'storeSkill'])->name('skills.store');
Route::get('skills/{skill}/edit', [AdminController::class, 'editSkill'])->name('skills.edit');
Route::put('skills/{skill}', [AdminController::class, 'updateSkill'])->name('skills.update');
Route::delete('skills/{skill}', [AdminController::class, 'destroySkill'])->name('skills.destroy');

// Gerenciamento de Usuários
Route::resource('users', AdminUserController::class);
Route::get('users/{user}/change-password', [AdminUserController::class, 'changePasswordForm'])->name('users.change-password');
Route::put('users/{user}/update-password', [AdminUserController::class, 'changePassword'])->name('users.update-password');

// Mensajes
Route::resource('messages', MessageController::class);
Route::post('messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
Route::post('messages/{message}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.mark-as-read');
Route::post('messages/{message}/toggle-read', [MessageController::class, 'toggleRead'])->name('messages.toggle-read');

// Analytics
Route::get('analytics', [AdminController::class, 'analytics'])->name('analytics');

// Configurações
Route::get('settings/general', [AdminController::class, 'settingsGeneral'])->name('settings.general');
Route::get('settings/profile', [AdminController::class, 'profile'])->name('settings.profile');
Route::put('settings/profile', [AdminController::class, 'updateProfile'])->name('settings.profile.update');
Route::get('settings/security', [AdminController::class, 'security'])->name('settings.security');
Route::put('settings/security', [AdminController::class, 'updateSecurity'])->name('settings.security.update');

// Sistema
Route::get('backup', [AdminController::class, 'backup'])->name('backup');
Route::post('backup/create', [AdminController::class, 'createBackup'])->name('backup.create');
Route::get('logs', [AdminController::class, 'logs'])->name('logs');
Route::get('cache', [AdminController::class, 'cache'])->name('cache');
Route::post('cache/clear', [AdminController::class, 'clearCache'])->name('cache.clear');

// Notificações
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [AdminController::class, 'notifications'])->name('index');
    Route::post('{notification}/read', [AdminController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [AdminController::class, 'markAllAsRead'])->name('read-all');
});

// Ajuda e Suporte
Route::prefix('help')->name('help.')->group(function () {
    Route::get('/', [AdminController::class, 'help'])->name('index');
    Route::get('/documentation', [AdminController::class, 'documentation'])->name('documentation');
    Route::get('/support', [AdminController::class, 'support'])->name('support');
    Route::post('/support/ticket', [AdminController::class, 'createTicket'])->name('support.ticket');
});

// API Routes para AJAX
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/stats', [AdminController::class, 'getStats'])->name('stats');
    Route::get('/notifications/count', [AdminController::class, 'getNotificationCount'])->name('notifications.count');
    Route::post('/auto-save', [AdminController::class, 'autoSave'])->name('auto-save');
});

// Blog Comments API Routes
Route::get('/blog-comments/get-comments/{post}', [BlogCommentController::class, 'getCommentsByPost'])->name('blog-comments.get-comments');

}); // Fim do grupo de middleware admin