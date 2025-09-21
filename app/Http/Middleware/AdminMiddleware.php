<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar o painel administrativo.');
        }

        // Verificar se o usuário tem permissão de admin
        // Aqui você pode implementar sua lógica de verificação de permissões
        // Por exemplo, verificar se o usuário tem um campo 'is_admin' ou um role específico
        
        $user = Auth::user();
        
        // Exemplo de verificação (adapte conforme sua estrutura de usuários)
        if (!$user->is_admin ?? false) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar o painel administrativo.');
        }

        return $next($request);
    }
}